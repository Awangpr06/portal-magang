<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Attendance;
use App\Models\Notification;
use App\Models\Peserta;
use App\Models\User;
use App\Services\Peserta\PesertaDataService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    private const TIMEZONE = 'Asia/Jakarta';

    public function index(Request $request)
    {
        $pesertaContext = app(PesertaDataService::class)->forUser($request->user());
        $peserta = Peserta::query()
            ->where('user_id', $request->user()->id)
            ->first();

        $attendances = $peserta
            ? Attendance::query()
                ->where('peserta_id', $peserta->id)
                ->orderByDesc('tanggal')
                ->orderByDesc('id')
                ->get()
            : collect();
        $now = now(self::TIMEZONE);
        $todayAttendance = $attendances->first(
            fn (Attendance $attendance) => $attendance->tanggal->isSameDay($now)
        );
        $validAttendances = $attendances->whereIn('status', ['hadir', 'terlambat']);

        return view('peserta.aktivitas-magang.absensi', [
            'pesertaContext' => $pesertaContext,
            'peserta' => $peserta,
            'attendances' => $attendances,
            'todayAttendance' => $todayAttendance,
            'now' => $now,
            'attendanceStats' => [
                'total' => $attendances->count(),
                'present' => $attendances->where('status', 'hadir')->count(),
                'late' => $attendances->where('status', 'terlambat')->count(),
                'permit' => $attendances->where('status', 'izin')->count(),
                'sick' => $attendances->where('status', 'sakit')->count(),
                'absent' => $attendances->whereIn('status', ['alpa', 'tidak_hadir'])->count(),
                'percentage' => $attendances->count()
                    ? (int) round(($validAttendances->count() / $attendances->count()) * 100)
                    : 0,
            ],
        ]);
    }

    public function checkIn(Request $request)
    {
        $peserta = $this->peserta($request);
        $now = now(self::TIMEZONE);
        $attendance = Attendance::firstOrNew([
            'peserta_id' => $peserta->id,
            'tanggal' => $now->toDateString(),
        ]);

        if ($attendance->exists && in_array($attendance->status, ['izin', 'sakit'], true)) {
            return back()->withErrors(['absensi' => 'Hari ini sudah tercatat sebagai '.$attendance->status.'.']);
        }

        if ($attendance->jam_masuk) {
            return back()->withErrors(['absensi' => 'Absensi masuk hari ini sudah tercatat.']);
        }

        $isLate = $now->gt($now->copy()->setTime(8, 15, 0));
        $attendance->fill([
            'jam_masuk' => $now->format('H:i:s'),
            'status' => $isLate ? 'terlambat' : 'hadir',
            'keterangan' => $isLate
                ? 'Terlambat '.$now->diffInMinutes($now->copy()->setTime(8, 15)).' menit'
                : 'Absensi masuk tepat waktu',
        ])->save();

        if ($isLate) {
            $this->notifyLateCheckIn($peserta, $attendance, $now);
        }

        return back()->with('success', 'Absensi masuk berhasil dicatat pada '.$now->format('H:i').' WIB.');
    }

    public function checkOut(Request $request)
    {
        $peserta = $this->peserta($request);
        $now = now(self::TIMEZONE);
        $attendance = $peserta->attendances()
            ->whereDate('tanggal', $now->toDateString())
            ->first();

        if (! $attendance || ! $attendance->jam_masuk) {
            return back()->withErrors(['absensi' => 'Silakan melakukan absensi masuk terlebih dahulu.']);
        }

        if ($attendance->jam_pulang) {
            return back()->withErrors(['absensi' => 'Absensi pulang hari ini sudah tercatat.']);
        }

        $checkIn = Carbon::parse($now->toDateString().' '.$attendance->jam_masuk->format('H:i:s'), self::TIMEZONE);
        $attendance->update([
            'jam_pulang' => $now->format('H:i:s'),
            'durasi_menit' => $checkIn->diffInMinutes($now),
        ]);

        return back()->with('success', 'Absensi pulang berhasil dicatat pada '.$now->format('H:i').' WIB.');
    }

    public function permit(Request $request)
    {
        $data = $request->validate([
            'keterangan' => ['required', 'string', 'max:1000'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'lampiran' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);
        $peserta = $this->peserta($request);
        $attachment = $request->file('lampiran')?->store('attendance-letters', 'public');

        foreach (CarbonPeriod::create($data['tanggal_mulai'], $data['tanggal_selesai']) as $date) {
            Attendance::updateOrCreate(
                ['peserta_id' => $peserta->id, 'tanggal' => $date->toDateString()],
                [
                    'jam_masuk' => null,
                    'jam_pulang' => null,
                    'status' => 'izin',
                    'durasi_menit' => null,
                    'keterangan' => $data['keterangan'],
                    'tanggal_selesai' => $data['tanggal_selesai'],
                    'lampiran' => $attachment,
                ]
            );
        }

        return back()->with('success', 'Pengajuan izin berhasil disimpan.');
    }

    public function sick(Request $request)
    {
        $data = $request->validate([
            'keterangan' => ['required', 'string', 'max:1000'],
            'tanggal' => ['required', 'date'],
            'lampiran' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);
        $peserta = $this->peserta($request);
        $attachment = $request->file('lampiran')?->store('attendance-letters', 'public');

        Attendance::updateOrCreate(
            ['peserta_id' => $peserta->id, 'tanggal' => $data['tanggal']],
            [
                'jam_masuk' => null,
                'jam_pulang' => null,
                'status' => 'sakit',
                'durasi_menit' => null,
                'keterangan' => $data['keterangan'],
                'tanggal_selesai' => $data['tanggal'],
                'lampiran' => $attachment,
            ]
        );

        return back()->with('success', 'Pengajuan sakit untuk satu hari berhasil disimpan.');
    }

    private function peserta(Request $request)
    {
        abort_unless($request->user()->peserta, 403, 'Profil peserta belum tersedia.');

        return $request->user()->peserta;
    }

    private function notifyLateCheckIn($peserta, Attendance $attendance, Carbon $now): void
    {
        $peserta->loadMissing(['user', 'internship.mentor.user', 'internship.pembimbing.user']);

        $recipientIds = collect([
            $peserta->internship?->mentor?->user_id,
            $peserta->internship?->pembimbing?->user_id,
        ])
            ->merge(
                User::query()
                    ->whereIn('role', ['admin', 'super_admin'])
                    ->pluck('id')
            )
            ->filter()
            ->unique()
            ->values();

        $message = 'Peserta '.$peserta->user?->name.' melakukan absensi masuk pukul '.$now->format('H:i').' WIB dan tercatat terlambat.';

        foreach ($recipientIds as $recipientId) {
            Notification::create([
                'user_id' => $recipientId,
                'judul' => 'Absensi terlambat',
                'pesan' => $message,
                'dibaca' => false,
            ]);
        }

        Activity::create([
            'user_id' => $peserta->user_id,
            'aktivitas' => 'Melakukan absensi masuk terlambat pada '.$now->format('H:i').' WIB.',
        ]);
    }
}
