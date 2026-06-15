<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mentor;
use App\Models\Pembimbing;
use App\Models\PerguruanTinggi;
use App\Models\Peserta;
use App\Models\VerificationHistory;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class DashboardActionController extends Controller
{
    public function storePeserta(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('users', 'username')],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'nim' => ['required', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:255'],
            'tempat_lahir' => ['required', 'string', 'max:100'],
            'tanggal_lahir' => ['required', 'date'],
            'jenis_kelamin' => ['required', 'in:Laki-laki,Perempuan'],
            'no_hp' => ['required', 'string', 'max:30'],
            'alamat' => ['required', 'string'],
            'perguruan_tinggi' => ['required', 'string', 'max:255'],
            'program_studi' => ['required', 'string', 'max:255'],
            'fakultas' => ['required', 'string', 'max:255'],
            'program_magang' => ['required', 'string', 'max:255'],
            'pembimbing_akademik' => ['required', 'string', 'max:255'],
            'tanggal_mulai_magang' => ['required', 'date'],
            'tanggal_selesai_magang' => ['required', 'date', 'after_or_equal:tanggal_mulai_magang'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $fotoPath = $request->file('foto')?->store('foto-peserta', 'public');

        DB::transaction(function () use ($validated, $fotoPath) {
            $perguruanTinggi = PerguruanTinggi::firstOrCreate(
                ['nama_pt' => $validated['perguruan_tinggi']],
                [
                    'alamat' => $validated['alamat'],
                    'telepon' => $validated['no_hp'],
                    'email' => $validated['email'],
                ]
            );

            $user = User::create([
                'name' => $validated['name'],
                'username' => $validated['username'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'peserta',
                'foto' => $fotoPath,
                'account_status' => 'disetujui',
                'verified_at' => now(),
                'verified_by' => auth()->id(),
            ]);

            VerificationHistory::create([
                'user_id' => $user->id,
                'admin_id' => auth()->id(),
                'jenis' => 'akun',
                'status' => 'disetujui',
                'keterangan' => 'Akun peserta baru '.$validated['name'].' berhasil dibuat dan disetujui.',
                'verified_at' => now(),
            ]);

            Peserta::create([
                'user_id' => $user->id,
                'perguruan_tinggi_id' => $perguruanTinggi->id,
                'nim' => $validated['nim'],
                'tempat_lahir' => $validated['tempat_lahir'],
                'tanggal_lahir' => $validated['tanggal_lahir'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'jurusan' => $validated['program_studi'],
                'fakultas' => $validated['fakultas'],
                'program_magang' => $validated['program_magang'],
                'pembimbing_akademik' => $validated['pembimbing_akademik'],
                'tanggal_mulai_magang' => $validated['tanggal_mulai_magang'],
                'tanggal_selesai_magang' => $validated['tanggal_selesai_magang'],
                'semester' => '-',
                'no_hp' => $validated['no_hp'],
                'alamat' => $validated['alamat'],
                'status' => 'aktif',
            ]);
        });

        return redirect()
            ->route('admin.pengguna.peserta')
            ->with('success', 'Peserta berhasil ditambahkan dan tersimpan ke database.');
    }

    public function storePerguruanTinggi(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_pt' => ['required', 'string', 'max:255', Rule::unique('perguruan_tinggi', 'nama_pt')],
            'jenis' => ['required', Rule::in(['Negeri', 'Swasta'])],
            'provinsi' => ['required', 'string', 'max:255'],
            'pic' => ['required', 'string', 'max:255'],
            'pic_nip' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'fakultas' => ['required', 'string', 'max:255'],
            'program_studi' => ['required', 'string', 'max:255'],
        ]);

        PerguruanTinggi::create([
            'nama_pt' => $validated['nama_pt'],
            'jenis' => $validated['jenis'],
            'status_kerja_sama' => 'aktif',
            'pic' => $validated['pic'],
            'pic_nip' => $validated['pic_nip'],
            'alamat' => $validated['provinsi'],
            'telepon' => '-',
            'email' => $validated['email'],
            'fakultas' => $validated['fakultas'],
            'program_studi' => $validated['program_studi'],
        ]);

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Perguruan tinggi berhasil ditambahkan dan tersimpan ke database.');
    }

    public function storeMentor(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('users', 'username')],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'nip' => ['required', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:255'],
            'jenis_kelamin' => ['required', 'in:Laki-laki,Perempuan'],
            'no_hp' => ['required', 'string', 'max:30'],
            'alamat' => ['required', 'string'],
            'perguruan_tinggi' => ['required', 'in:LLDIKTI Wilayah V Yogyakarta'],
            'jabatan' => ['required', 'string', 'max:255'],
            'divisi' => ['required', 'string', 'max:255'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $fotoPath = $request->file('foto')?->store('foto-mentor', 'public');

        DB::transaction(function () use ($validated, $fotoPath) {
            $user = User::create([
                'name' => $validated['name'],
                'username' => $validated['username'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'mentor',
                'foto' => $fotoPath,
                'account_status' => 'disetujui',
                'verified_at' => now(),
                'verified_by' => auth()->id(),
            ]);

            VerificationHistory::create([
                'user_id' => $user->id,
                'admin_id' => auth()->id(),
                'jenis' => 'akun',
                'status' => 'disetujui',
                'keterangan' => 'Akun mentor baru '.$validated['name'].' berhasil dibuat oleh admin.',
                'verified_at' => now(),
            ]);

            Mentor::create([
                'user_id' => $user->id,
                'nip' => $validated['nip'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'jabatan' => $validated['jabatan'],
                'divisi' => $validated['divisi'],
                'no_hp' => $validated['no_hp'],
                'alamat' => $validated['alamat'],
                'perguruan_tinggi' => $validated['perguruan_tinggi'],
            ]);
        });

        return redirect()
            ->route('admin.pengguna.mentor')
            ->with('success', 'Mentor berhasil ditambahkan dan tersimpan ke database.');
    }

    public function storePembimbing(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('users', 'username')],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'nidn_nip' => ['required', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:255'],
            'tempat_lahir' => ['required', 'string', 'max:100'],
            'tanggal_lahir' => ['required', 'date'],
            'jenis_kelamin' => ['required', 'in:Laki-laki,Perempuan'],
            'no_hp' => ['required', 'string', 'max:30'],
            'alamat' => ['required', 'string'],
            'perguruan_tinggi' => ['required', 'string', 'max:255'],
            'program_studi' => ['required', 'string', 'max:255'],
            'jabatan' => ['required', 'string', 'max:255'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $fotoPath = $request->file('foto')?->store('foto-pembimbing', 'public');

        DB::transaction(function () use ($validated, $fotoPath) {
            $user = User::create([
                'name' => $validated['name'],
                'username' => $validated['username'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'pembimbing',
                'foto' => $fotoPath,
                'account_status' => 'disetujui',
                'verified_at' => now(),
                'verified_by' => auth()->id(),
            ]);

            VerificationHistory::create([
                'user_id' => $user->id,
                'admin_id' => auth()->id(),
                'jenis' => 'akun',
                'status' => 'disetujui',
                'keterangan' => 'Akun pembimbing akademik baru '.$validated['name'].' berhasil dibuat oleh admin.',
                'verified_at' => now(),
            ]);

            Pembimbing::create([
                'user_id' => $user->id,
                'nidn_nip' => $validated['nidn_nip'],
                'tempat_lahir' => $validated['tempat_lahir'],
                'tanggal_lahir' => $validated['tanggal_lahir'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'no_hp' => $validated['no_hp'],
                'alamat' => $validated['alamat'],
                'perguruan_tinggi' => $validated['perguruan_tinggi'],
                'instansi' => $validated['perguruan_tinggi'],
                'program_studi' => $validated['program_studi'],
                'jabatan' => $validated['jabatan'],
            ]);
        });

        return redirect()
            ->route('admin.pengguna.pembimbing')
            ->with('success', 'Pembimbing akademik berhasil ditambahkan dan tersimpan ke database.');
    }

    public function updateUserStatus(Request $request, User $user): JsonResponse
    {
        abort_unless(in_array($user->role, ['peserta', 'mentor', 'pembimbing'], true), 404);

        $validated = $request->validate([
            'action' => ['required', Rule::in(['setujui', 'tolak'])],
            'rejection_reason' => ['nullable', 'required_if:action,tolak', 'string', 'max:1000'],
        ]);

        $approved = $validated['action'] === 'setujui';

        $user->update([
            'account_status' => $approved ? 'disetujui' : 'ditolak',
            'rejection_reason' => $approved ? null : $validated['rejection_reason'],
            'verified_at' => now(),
            'verified_by' => auth()->id(),
        ]);

        return response()->json([
            'message' => "Akun {$user->name} berhasil ".($approved ? 'disetujui' : 'ditolak').'.',
            'status' => $approved ? 'aktif' : 'ditolak',
        ]);
    }

    public function updateMonitoredUser(Request $request, User $user): JsonResponse
    {
        abort_unless(in_array($user->role, ['peserta', 'mentor', 'pembimbing', 'admin', 'super_admin'], true), 404);

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:50', 'alpha_dash', Rule::unique('users', 'username')->ignore($user->id)],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'account_status' => ['required', Rule::in(['aktif', 'nonaktif', 'menunggu', 'ditolak'])],
        ];

        if ($user->role === 'peserta') {
            $rules = array_merge($rules, [
                'nim' => ['required', 'string', 'max:50'],
                'tempat_lahir' => ['required', 'string', 'max:100'],
                'tanggal_lahir' => ['required', 'date'],
                'jenis_kelamin' => ['required', Rule::in(['Laki-laki', 'Perempuan'])],
                'no_hp' => ['required', 'string', 'max:30'],
                'alamat' => ['required', 'string', 'max:1000'],
                'perguruan_tinggi' => ['required', 'string', 'max:255'],
                'program_studi' => ['required', 'string', 'max:255'],
                'fakultas' => ['required', 'string', 'max:255'],
                'program_magang' => ['required', 'string', 'max:255'],
                'pembimbing_akademik' => ['required', 'string', 'max:255'],
                'tanggal_mulai_magang' => ['required', 'date'],
                'tanggal_selesai_magang' => ['required', 'date', 'after_or_equal:tanggal_mulai_magang'],
            ]);
        } elseif ($user->role === 'mentor') {
            $rules = array_merge($rules, [
                'nip' => ['required', 'string', 'max:50'],
                'jenis_kelamin' => ['required', Rule::in(['Laki-laki', 'Perempuan'])],
                'jabatan' => ['required', 'string', 'max:255'],
                'divisi' => ['required', 'string', 'max:255'],
                'no_hp' => ['required', 'string', 'max:30'],
                'alamat' => ['required', 'string', 'max:1000'],
                'perguruan_tinggi' => ['required', 'string', 'max:255'],
            ]);
        } elseif ($user->role === 'pembimbing') {
            $rules = array_merge($rules, [
                'nidn' => ['required', 'string', 'max:50'],
                'tempat_lahir' => ['required', 'string', 'max:100'],
                'tanggal_lahir' => ['required', 'date'],
                'jenis_kelamin' => ['required', Rule::in(['Laki-laki', 'Perempuan'])],
                'no_hp' => ['required', 'string', 'max:30'],
                'alamat' => ['required', 'string', 'max:1000'],
                'perguruan_tinggi' => ['required', 'string', 'max:255'],
                'program_studi' => ['required', 'string', 'max:255'],
                'jabatan' => ['required', 'string', 'max:255'],
            ]);
        } else {
            $rules = array_merge($rules, [
                'phone' => ['nullable', 'string', 'max:30'],
                'address' => ['nullable', 'string', 'max:1000'],
            ]);
        }

        $validated = $request->validate($rules);

        $resolvedStatus = match ($validated['account_status']) {
            'aktif' => 'disetujui',
            'nonaktif' => 'nonaktif',
            'menunggu' => 'menunggu',
            'ditolak' => 'ditolak',
        };

        abort_if($user->role === 'super_admin' && $resolvedStatus !== 'disetujui', 403, 'Super admin harus tetap aktif.');

        DB::transaction(function () use ($user, $validated, $resolvedStatus) {
            $user->forceFill([
                'name' => $validated['name'],
                'username' => $validated['username'] ?: null,
                'email' => $validated['email'],
                'account_status' => $resolvedStatus,
                'verified_at' => in_array($resolvedStatus, ['disetujui', 'nonaktif'], true) ? now() : $user->verified_at,
                'verified_by' => in_array($resolvedStatus, ['disetujui', 'nonaktif'], true) ? auth()->id() : $user->verified_by,
                'rejection_reason' => $resolvedStatus === 'ditolak' ? ($user->rejection_reason ?: 'Status akun diperbarui melalui statistik pengguna.') : null,
            ])->save();

            if ($user->role === 'peserta' && $user->peserta) {
                $campus = PerguruanTinggi::firstOrCreate([
                    'nama_pt' => $validated['perguruan_tinggi'],
                ], [
                    'alamat' => $validated['alamat'],
                    'telepon' => $validated['no_hp'],
                    'email' => $validated['email'],
                ]);

                $user->phone = $validated['no_hp'];
                $user->address = $validated['alamat'];
                $user->save();

                $user->peserta->forceFill([
                    'perguruan_tinggi_id' => $campus->id,
                    'nim' => $validated['nim'],
                    'tempat_lahir' => $validated['tempat_lahir'],
                    'tanggal_lahir' => $validated['tanggal_lahir'],
                    'jenis_kelamin' => $validated['jenis_kelamin'],
                    'no_hp' => $validated['no_hp'],
                    'alamat' => $validated['alamat'],
                    'jurusan' => $validated['program_studi'],
                    'fakultas' => $validated['fakultas'],
                    'program_magang' => $validated['program_magang'],
                    'pembimbing_akademik' => $validated['pembimbing_akademik'],
                    'tanggal_mulai_magang' => $validated['tanggal_mulai_magang'],
                    'tanggal_selesai_magang' => $validated['tanggal_selesai_magang'],
                ])->save();
            } elseif ($user->role === 'mentor' && $user->mentor) {
                $user->phone = $validated['no_hp'];
                $user->address = $validated['alamat'];
                $user->save();

                $user->mentor->forceFill([
                    'nip' => $validated['nip'],
                    'jenis_kelamin' => $validated['jenis_kelamin'],
                    'jabatan' => $validated['jabatan'],
                    'divisi' => $validated['divisi'],
                    'no_hp' => $validated['no_hp'],
                    'alamat' => $validated['alamat'],
                    'perguruan_tinggi' => $validated['perguruan_tinggi'],
                ])->save();
            } elseif ($user->role === 'pembimbing' && $user->pembimbing) {
                $user->phone = $validated['no_hp'];
                $user->address = $validated['alamat'];
                $user->save();

                $user->pembimbing->forceFill([
                    'nidn_nip' => $validated['nidn'],
                    'tempat_lahir' => $validated['tempat_lahir'],
                    'tanggal_lahir' => $validated['tanggal_lahir'],
                    'jenis_kelamin' => $validated['jenis_kelamin'],
                    'no_hp' => $validated['no_hp'],
                    'alamat' => $validated['alamat'],
                    'perguruan_tinggi' => $validated['perguruan_tinggi'],
                    'program_studi' => $validated['program_studi'],
                    'jabatan' => $validated['jabatan'],
                ])->save();
            } else {
                $user->forceFill([
                    'phone' => $validated['phone'] ?? $user->phone,
                    'address' => $validated['address'] ?? $user->address,
                ])->save();
            }
        });

        $user->loadMissing(['peserta.perguruanTinggi', 'mentor', 'pembimbing']);

        return response()->json([
            'message' => "Data pengguna {$user->name} berhasil diperbarui.",
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'status' => $this->statusToUi($user->account_status),
                'nim' => $user->peserta?->nim,
                'tempat_lahir' => $user->peserta?->tempat_lahir ?? $user->pembimbing?->tempat_lahir,
                'tanggal_lahir' => optional($user->peserta?->tanggal_lahir ?? $user->pembimbing?->tanggal_lahir)->format('Y-m-d'),
                'jenis_kelamin' => $user->peserta?->jenis_kelamin ?? $user->mentor?->jenis_kelamin ?? $user->pembimbing?->jenis_kelamin,
                'no_hp' => $user->peserta?->no_hp ?? $user->mentor?->no_hp ?? $user->pembimbing?->no_hp,
                'alamat' => $user->peserta?->alamat ?? $user->mentor?->alamat ?? $user->pembimbing?->alamat,
                'perguruan_tinggi' => $user->peserta?->perguruanTinggi?->nama_pt ?? $user->mentor?->perguruan_tinggi ?? $user->pembimbing?->perguruan_tinggi,
                'program_studi' => $user->peserta?->jurusan ?? $user->pembimbing?->program_studi,
                'fakultas' => $user->peserta?->fakultas,
                'program_magang' => $user->peserta?->program_magang,
                'pembimbing_akademik' => $user->peserta?->pembimbing_akademik,
                'tanggal_mulai_magang' => optional($user->peserta?->tanggal_mulai_magang)->format('Y-m-d'),
                'tanggal_selesai_magang' => optional($user->peserta?->tanggal_selesai_magang)->format('Y-m-d'),
                'nip' => $user->mentor?->nip,
                'jabatan' => $user->mentor?->jabatan ?? $user->pembimbing?->jabatan,
                'divisi' => $user->mentor?->divisi,
                'nidn' => $user->pembimbing?->nidn_nip,
                'kampus' => $user->pembimbing?->perguruan_tinggi,
                'phone' => $user->phone,
                'address' => $user->address,
            ],
        ]);
    }

    public function toggleMonitoredUserStatus(User $user): JsonResponse
    {
        abort_unless(in_array($user->role, ['peserta', 'mentor', 'pembimbing', 'admin', 'super_admin'], true), 404);
        abort_if($user->role === 'super_admin', 403, 'Super admin tidak dapat dinonaktifkan.');

        $nextStatus = $user->account_status === 'nonaktif' ? 'disetujui' : 'nonaktif';

        $user->forceFill([
            'account_status' => $nextStatus,
            'verified_at' => now(),
            'verified_by' => auth()->id(),
            'rejection_reason' => null,
        ])->save();

        return response()->json([
            'message' => $nextStatus === 'nonaktif'
                ? "Akun {$user->name} berhasil dinonaktifkan."
                : "Akun {$user->name} berhasil diaktifkan kembali.",
            'status' => $this->statusToUi($user->account_status),
        ]);
    }

    public function destroyMonitoredUser(User $user): JsonResponse
    {
        abort_unless(in_array($user->role, ['peserta', 'mentor', 'pembimbing', 'admin', 'super_admin'], true), 404);
        abort_if($user->id === auth()->id(), 422, 'Anda tidak dapat menghapus akun yang sedang digunakan.');
        abort_if($user->role === 'super_admin', 403, 'Super admin tidak dapat dihapus.');

        $name = $user->name;
        $user->delete();

        return response()->json([
            'message' => "Akun {$name} berhasil dihapus.",
        ]);
    }

    public function updateCampusMonitoring(Request $request, \App\Models\PerguruanTinggi $campus): JsonResponse
    {
        abort_unless(in_array($request->user()?->role, ['admin', 'super_admin'], true), 403);

        $validated = $request->validate([
            'nama_pt' => ['required', 'string', 'max:255', Rule::unique('perguruan_tinggi', 'nama_pt')->ignore($campus->id)],
            'jenis' => ['required', Rule::in(['Negeri', 'Swasta'])],
            'alamat' => ['required', 'string', 'max:1000'],
            'email' => ['required', 'email', 'max:255'],
            'pic' => ['nullable', 'string', 'max:255'],
            'pic_nip' => ['nullable', 'string', 'max:100'],
            'fakultas' => ['nullable', 'string', 'max:255'],
            'program_studi' => ['nullable', 'string', 'max:255'],
            'status_kerja_sama' => ['required', Rule::in(['aktif', 'proses', 'tidak aktif'])],
        ]);

        $campus->forceFill($validated)->save();

        return response()->json([
            'message' => "Data perguruan tinggi {$campus->nama_pt} berhasil diperbarui.",
            'campus' => [
                'id' => $campus->id,
                'nama' => $campus->nama_pt,
                'status' => $this->campusStatusToUi($campus->status_kerja_sama),
            ],
        ]);
    }

    public function toggleCampusMonitoringStatus(\App\Models\PerguruanTinggi $campus): JsonResponse
    {
        abort_unless(in_array(request()->user()?->role, ['admin', 'super_admin'], true), 403);

        $nextStatus = $campus->status_kerja_sama === 'tidak aktif' ? 'aktif' : 'tidak aktif';

        $campus->forceFill([
            'status_kerja_sama' => $nextStatus,
        ])->save();

        return response()->json([
            'message' => $nextStatus === 'tidak aktif'
                ? "Perguruan tinggi {$campus->nama_pt} berhasil dinonaktifkan."
                : "Perguruan tinggi {$campus->nama_pt} berhasil diaktifkan kembali.",
            'status' => $this->campusStatusToUi($campus->status_kerja_sama),
        ]);
    }

    public function destroyCampusMonitoring(\App\Models\PerguruanTinggi $campus): JsonResponse
    {
        abort_unless(in_array(request()->user()?->role, ['admin', 'super_admin'], true), 403);

        $name = $campus->nama_pt;

        if ($campus->pesertas()->exists()) {
            return response()->json([
                'message' => 'Perguruan tinggi tidak dapat dihapus karena masih memiliki peserta terkait.',
            ], 422);
        }

        $campus->delete();

        return response()->json([
            'message' => "Perguruan tinggi {$name} berhasil dihapus.",
        ]);
    }

    private function statusToUi(string $status): string
    {
        return match ($status) {
            'disetujui' => 'aktif',
            'nonaktif' => 'nonaktif',
            'ditolak' => 'ditolak',
            default => 'menunggu',
        };
    }

    private function campusStatusToUi(string $status): string
    {
        return match ($status) {
            'aktif' => 'aktif',
            'proses' => 'proses',
            default => 'tidak aktif',
        };
    }

}
