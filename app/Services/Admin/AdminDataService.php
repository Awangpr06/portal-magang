<?php

namespace App\Services\Admin;

use App\Models\Attendance;
use App\Models\Activity;
use App\Models\Assignment;
use App\Models\Internship;
use App\Models\Document;
use App\Models\Mentor;
use App\Models\Pembimbing;
use App\Models\PerguruanTinggi;
use App\Models\Peserta;
use App\Models\Report;
use App\Models\VerificationHistory;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminDataService
{
    private const DISPLAY_TIMEZONE = 'Asia/Jakarta';

    public function context(): array
    {
        $users = User::query()
            ->with(['peserta.perguruanTinggi', 'mentor', 'pembimbing', 'documents', 'verifiedBy'])
            ->latest()
            ->get();
        $verificationAccounts = $users
            ->filter(fn (User $user) => in_array($user->role, ['peserta', 'mentor', 'pembimbing'], true))
            ->sortByDesc('created_at')
            ->values();

        $participants = Peserta::query()
            ->with(['user.documents', 'perguruanTinggi', 'internship.mentor.user'])
            ->latest()
            ->get();

        $mentors = Mentor::query()->with('user')->latest()->get();
        $advisors = Pembimbing::query()->with('user')->latest()->get();
        $campuses = PerguruanTinggi::query()->latest()->get();

        $documents = Schema::hasTable('documents')
            ? Document::query()->with('user')->latest()->get()
            : collect();
        $documentTypes = $this->documentTypes();
        $activities = Schema::hasTable('activities')
            ? Activity::query()->with('user')->latest()->get()
            : collect();

        $documentsCount = $documents->count();
        $reportsCount = Schema::hasTable('reports') ? Report::count() : 0;
        $placements = Internship::query()
            ->with(['peserta.user', 'peserta.perguruanTinggi', 'mentor.user'])
            ->latest()
            ->get();
        $attendances = Schema::hasTable('attendances')
            ? Attendance::query()->with(['peserta.user', 'peserta.internship'])->latest('tanggal')->get()
            : collect();
        $attendanceRecaps = $attendances
            ->groupBy('peserta_id')
            ->map(function ($items) {
                $first = $items->first();
                $peserta = $first?->peserta;
                $presentCount = $items->whereIn('status', ['hadir', 'terlambat'])->count();
                $permitCount = $items->where('status', 'izin')->count();
                $absentCount = $items->whereIn('status', ['alpa', 'tidak_hadir'])->count();
                $workdays = max($items->count(), 1);
                $percent = (int) round(($presentCount / $workdays) * 100);
                $status = match (true) {
                    $percent >= 90 => 'sangat baik',
                    $percent >= 75 => 'baik',
                    default => 'perlu perhatian',
                };

                return [
                    'peserta_id' => $peserta?->id,
                    'nim' => $peserta?->nim ?? '-',
                    'nama' => $peserta?->user?->name ?? '-',
                    'prodi' => $peserta?->jurusan ?? '-',
                    'instansi' => 'LLDIKTI Wilayah V Yogyakarta',
                    'penempatan' => $peserta?->internship?->divisi
                        ?? $peserta?->internship?->unit_kerja
                        ?? $peserta?->internship?->posisi
                        ?? '-',
                    'periode' => $peserta?->program_magang ?? '-',
                    'workdays' => $workdays,
                    'present' => $presentCount,
                    'permit' => $permitCount,
                    'absent' => $absentCount,
                    'attendance' => $percent,
                    'status' => $status,
                    'last_active' => optional($items->sortByDesc('tanggal')->first()?->tanggal)->translatedFormat('d F Y') ?? '-',
                ];
            })
            ->values();
        $assignments = Schema::hasTable('assignments')
            ? Assignment::query()
                ->with(['peserta.user', 'peserta.perguruanTinggi', 'peserta.internship', 'mentor.user'])
                ->latest('deadline')
                ->latest()
                ->get()
            : collect();
        $reports = Report::query()
            ->with(['peserta.user', 'peserta.internship', 'peserta.certificates', 'peserta.assessments'])
            ->latest()
            ->get();
        $verificationHistories = Schema::hasTable('verification_histories')
            ? VerificationHistory::query()
                ->with(['user.peserta.perguruanTinggi', 'admin'])
                ->latest('verified_at')
                ->latest()
                ->get()
            : collect();
        $monitoringSources = collect()
            ->concat($reports)
            ->concat($assignments)
            ->concat($attendances)
            ->concat($activities);
        $monitoringProgramMonthly = collect(range(5, 0, -1))->map(function (int $offset) use ($monitoringSources) {
            $month = now()->startOfMonth()->subMonthsNoOverflow($offset);
            $count = $monitoringSources->filter(function ($item) use ($month) {
                $date = $item->created_at ?? null;

                return $date && $date->format('Y-m') === $month->format('Y-m');
            })->count();

            return [
                'label' => $month->translatedFormat('M'),
                'count' => $count,
            ];
        })->values();
        $legacyVerificationHistories = $users
            ->filter(function (User $user) use ($verificationHistories) {
                if (! in_array($user->role, ['peserta', 'mentor', 'pembimbing'], true)) {
                    return false;
                }

                if (! in_array($user->account_status, ['disetujui', 'ditolak'], true)) {
                    return false;
                }

                return ! $verificationHistories->contains(function (VerificationHistory $history) use ($user) {
                    return (int) $history->user_id === (int) $user->id
                        && $history->jenis === 'akun';
                });
            })
            ->map(function (User $user) {
                $date = $user->verified_at ?? $user->updated_at ?? $user->created_at;

                return [
                    'id' => -1 * (int) $user->id,
                    'nama' => $user->name ?? '-',
                    'email' => $user->email ?? '-',
                    'jenis' => 'akun',
                    'jenis_label' => 'Akun',
                    'status' => $this->verificationStatus($user->account_status),
                    'tanggal' => optional($date)->translatedFormat('d M Y') ?? '-',
                    'periode' => $this->periodLabel($date),
                    'admin' => $user->verifiedBy?->name ?? '-',
                    'keterangan' => $user->account_status === 'ditolak'
                        ? ($user->rejection_reason ?: 'Akun ditolak sebelum riwayat verifikasi otomatis tersedia.')
                        : 'Akun disetujui sebelum riwayat verifikasi otomatis tersedia.',
                    'verified_at' => optional($date)->translatedFormat('d M Y H:i') ?? '-',
                    'sort_at' => optional($date)->toDateTimeString(),
                    'role' => $user->role ?? '-',
                    'role_label' => $this->roleLabel($user->role),
                ];
            });
        $verificationHistoryItems = collect($verificationHistories->map(fn (VerificationHistory $history) => [
                'id' => $history->id,
                'nama' => $history->user?->name ?? '-',
                'email' => $history->user?->email ?? '-',
                'jenis' => $history->jenis,
                'jenis_label' => $this->verificationTypeLabel($history->jenis),
                'status' => $this->verificationStatus($history->status),
                'tanggal' => optional($history->verified_at ?? $history->created_at)->translatedFormat('d M Y') ?? '-',
                'periode' => $this->periodLabel($history->verified_at ?? $history->created_at),
                'admin' => $history->admin?->name ?? '-',
                'keterangan' => $history->keterangan ?: '-',
                'verified_at' => optional($history->verified_at)->translatedFormat('d M Y H:i') ?? '-',
                'sort_at' => optional($history->verified_at ?? $history->created_at)->toDateTimeString(),
                'role' => $history->user?->role ?? '-',
                'role_label' => $this->roleLabel($history->user?->role),
            ])->all())
            ->merge($legacyVerificationHistories)
            ->sortByDesc(function (array $history) {
                return strtotime($history['sort_at'] ?? now()->toDateTimeString());
            })
            ->values();
        $documentParticipants = $participants->map(function (Peserta $peserta) use ($documentTypes) {
            $documents = $peserta->user?->documents?->values() ?? collect();

            $documentsByType = $documents->mapWithKeys(function (Document $document) {
                return [$this->documentKey($document) => $document];
            });

            $documentMap = [];

            foreach ($documentTypes as $type) {
                $document = $documentsByType->get($type['key']);

                $documentMap[$type['key']] = [
                    'key' => $type['key'],
                    'label' => $type['label'],
                    'uploaded' => (bool) $document,
                    'status' => $document ? $this->documentStatus($document) : 'belum diunggah',
                    'nama_dokumen' => $document?->nama_dokumen,
                    'file' => $document?->file,
                    'ukuran_file' => $document?->ukuran_file,
                    'tanggal' => $this->formatDateOnly($document?->created_at),
                    'download_url' => $document
                        ? route('admin.magang.dokumen.download', [
                            'user' => $peserta->user_id,
                            'jenisDokumen' => $type['key'],
                        ])
                        : null,
                ];
            }

            $uploadedCount = $documentsByType->count();
            $requiredCount = count($documentTypes);

            return [
                'id' => $peserta->id,
                'user_id' => $peserta->user_id,
                'nama' => $peserta->user?->name ?? '-',
                'email' => $peserta->user?->email ?? '-',
                'nim' => $peserta->nim ?: '-',
                'prodi' => $peserta->jurusan ?: '-',
                'instansi' => $peserta->perguruanTinggi?->nama_pt ?? '-',
                'periode' => $peserta->program_magang ?: '-',
                'status' => $this->uiStatus($peserta->user?->account_status ?? $peserta->status),
                'uploaded_count' => $uploadedCount,
                'required_count' => $requiredCount,
                'completion' => match (true) {
                    $uploadedCount === 0 => 'Belum Upload',
                    $uploadedCount >= $requiredCount => 'Lengkap',
                    default => 'Sebagian',
                },
                'completion_class' => match (true) {
                    $uploadedCount === 0 => 'danger',
                    $uploadedCount >= $requiredCount => 'success',
                    default => 'warning',
                },
                'documents' => $documentMap,
                'last_upload' => $this->formatDateOnly($documents->sortByDesc('created_at')->first()?->created_at),
            ];
        })->values();

        return [
            'adminStats' => [
                'total_users' => $users->count(),
                'active_users' => $users->where('account_status', 'disetujui')->count(),
                'waiting_users' => $users->where('account_status', 'menunggu')->count(),
                'rejected_users' => $users->where('account_status', 'ditolak')->count(),
                'active_participants' => $participants->where('status', 'aktif')->count(),
                'total_participants' => $participants->count(),
                'total_campuses' => $campuses->count(),
                'total_documents' => $documentsCount,
                'total_reports' => $reportsCount,
            ],
            'adminRecentActivities' => $activities->take(5)->map(fn (Activity $activity) => [
                'id' => $activity->id,
                'nama' => $activity->user?->name ?? '-',
                'role' => $activity->user?->role ?? '-',
                'aktivitas' => $activity->aktivitas,
                'waktu' => optional($activity->created_at)->diffForHumans() ?? '-',
                'tanggal' => optional($activity->created_at)->translatedFormat('d M Y H:i') ?? '-',
            ])->values(),
            'adminRecentDocuments' => $documents->take(5)->map(fn (Document $document) => [
                'id' => $document->id,
                'nama' => $document->nama_dokumen,
                'pemilik' => $document->user?->name ?? '-',
                'status' => $this->documentStatus($document),
                'tanggal' => optional($document->created_at)->translatedFormat('d M Y') ?? '-',
                'waktu' => optional($document->created_at)->diffForHumans() ?? '-',
            ])->values(),
            'adminVerificationAccounts' => $verificationAccounts->map(fn (User $user) => [
                'id' => $user->id,
                'nama' => $user->name ?? '-',
                'email' => $user->email ?? '-',
                'role' => $user->role,
                'role_label' => $this->roleLabel($user->role),
                'instansi' => $this->institution($user),
                'status' => $this->uiStatus($user->account_status),
                'tanggal' => optional($user->created_at)->translatedFormat('d M Y') ?? '-',
                'verified_at' => optional($user->verified_at)->translatedFormat('d M Y H:i') ?? '-',
                'rejection_reason' => $user->rejection_reason ?: '-',
            ])->values(),
            'adminUsers' => $users->map(fn (User $user) => $this->mapUser($user))->values(),
            'adminParticipants' => $participants->map(fn (Peserta $peserta) => [
                'id' => $peserta->id,
                'user_id' => $peserta->user_id,
                'nama' => $peserta->user?->name ?? '-',
                'username' => $peserta->user?->username ?? '-',
                'role' => 'peserta',
                'email' => $peserta->user?->email ?? '-',
                'instansi' => $peserta->perguruanTinggi?->nama_pt ?? '-',
                'nim' => $peserta->nim ?: '-',
                'tempat_lahir' => $peserta->tempat_lahir ?: '-',
                'tanggal_lahir' => $this->formatDate($peserta->tanggal_lahir),
                'tanggal_lahir_raw' => optional($peserta->tanggal_lahir)->format('Y-m-d') ?? '',
                'jenis_kelamin' => $peserta->jenis_kelamin ?: '-',
                'no_hp' => $peserta->no_hp ?: '-',
                'alamat' => $peserta->alamat ?: '-',
                'program_studi' => $peserta->jurusan ?: '-',
                'fakultas' => $peserta->fakultas ?: '-',
                'program_magang' => $peserta->program_magang ?: '-',
                'pembimbing_akademik' => $peserta->pembimbing_akademik ?: '-',
                'tanggal_mulai_magang' => $this->formatDate($peserta->tanggal_mulai_magang),
                'tanggal_mulai_magang_raw' => optional($peserta->tanggal_mulai_magang)->format('Y-m-d') ?? '',
                'tanggal_selesai_magang' => $this->formatDate($peserta->tanggal_selesai_magang),
                'tanggal_selesai_magang_raw' => optional($peserta->tanggal_selesai_magang)->format('Y-m-d') ?? '',
                'foto' => $peserta->user?->foto ? asset('storage/'.$peserta->user->foto) : null,
                'status' => $this->uiStatus($peserta->user?->account_status ?? $peserta->status),
                'tanggal' => optional($peserta->created_at)->translatedFormat('d F Y') ?? '-',
            ])->values(),
            'adminMentors' => $mentors->map(fn (Mentor $mentor) => [
                'id' => $mentor->id,
                'user_id' => $mentor->user_id,
                'nama' => $mentor->user?->name ?? '-',
                'username' => $mentor->user?->username ?? '-',
                'instansi' => $mentor->perguruan_tinggi ?: ($mentor->divisi ?: '-'),
                'unit_kerja' => $mentor->divisi ?: '-',
                'email' => $mentor->user?->email ?? '-',
                'nip' => $mentor->nip ?: '-',
                'jenis_kelamin' => $mentor->jenis_kelamin ?: '-',
                'no_hp' => $mentor->no_hp ?: '-',
                'alamat' => $mentor->alamat ?: '-',
                'perguruan_tinggi_raw' => $mentor->perguruan_tinggi ?: '',
                'jabatan' => $mentor->jabatan ?: '-',
                'divisi' => $mentor->divisi ?: '-',
                'foto' => $mentor->user?->foto ? asset('storage/'.$mentor->user->foto) : null,
                'status' => $this->uiStatus($mentor->user?->account_status),
                'tanggal' => optional($mentor->created_at)->translatedFormat('d F Y') ?? '-',
            ])->values(),
            'adminAdvisors' => $advisors->map(fn (Pembimbing $advisor) => [
                'id' => $advisor->id,
                'user_id' => $advisor->user_id,
                'nama' => $advisor->user?->name ?? '-',
                'username' => $advisor->user?->username ?? '-',
                'nidn' => $advisor->nidn_nip ?: '-',
                'kampus' => $advisor->perguruan_tinggi ?: ($advisor->instansi ?: '-'),
                'email' => $advisor->user?->email ?? '-',
                'tempat_lahir' => $advisor->tempat_lahir ?: '-',
                'tanggal_lahir' => $this->formatDate($advisor->tanggal_lahir),
                'tanggal_lahir_raw' => optional($advisor->tanggal_lahir)->format('Y-m-d') ?? '',
                'jenis_kelamin' => $advisor->jenis_kelamin ?: '-',
                'no_hp' => $advisor->no_hp ?: '-',
                'alamat' => $advisor->alamat ?: '-',
                'program_studi' => $advisor->program_studi ?: '-',
                'jabatan' => $advisor->jabatan ?: '-',
                'perguruan_tinggi_raw' => $advisor->perguruan_tinggi ?: '',
                'foto' => $advisor->user?->foto ? asset('storage/'.$advisor->user->foto) : null,
                'status' => $this->uiStatus($advisor->user?->account_status),
                'tanggal' => optional($advisor->created_at)->translatedFormat('d F Y') ?? '-',
            ])->values(),
            'adminCampuses' => $campuses->map(function (PerguruanTinggi $campus) use ($participants) {
                $campusParticipants = $participants->where('perguruan_tinggi_id', $campus->id);

                return [
                    'id' => $campus->id,
                    'nama' => $campus->nama_pt,
                    'pic' => $campus->pic ?: '-',
                    'pic_nip' => $campus->pic_nip ?: '-',
                    'fakultas' => $campus->fakultas ?: '-',
                    'program_studi' => $campus->program_studi ?: '-',
                    'jenis' => $campus->jenis ?: (str_contains(strtolower($campus->nama_pt), 'negeri') ? 'Negeri' : 'Swasta'),
                    'provinsi' => $this->provinceFromAddress($campus->alamat),
                    'alamat' => $campus->alamat ?: '-',
                    'email' => $campus->email ?: '-',
                    'status' => $this->campusStatus($campus->status_kerja_sama),
                    'mahasiswa_count' => $campusParticipants->count(),
                    'program_studi_count' => $campusParticipants->pluck('jurusan')->filter()->unique()->count(),
                    'instansi_mitra_count' => $campusParticipants->count(),
                    'terakhir_aktif' => optional($campusParticipants->sortByDesc('updated_at')->first()?->updated_at ?? $campus->updated_at)->translatedFormat('d M Y') ?? '-',
                    'tanggal' => optional($campus->created_at)->translatedFormat('d F Y') ?? '-',
                ];
            })->values(),
            'adminPlacements' => $placements->map(fn (Internship $internship) => [
                'id' => $internship->id,
                'peserta_id' => $internship->peserta_id,
                'nama' => $internship->peserta?->user?->name ?? '-',
                'nim' => $internship->peserta?->nim ?? '-',
                'prodi' => $internship->peserta?->jurusan ?? '-',
                'fakultas' => $internship->peserta?->fakultas ?? '-',
                'perguruan_tinggi' => $internship->peserta?->perguruanTinggi?->nama_pt ?? '-',
                'instansi' => $internship->instansi ?: '-',
                'penempatan' => $internship->divisi ?: ($internship->unit_kerja ?: ($internship->posisi ?: '-')),
                'posisi' => $internship->posisi ?: ($internship->divisi ?: '-'),
                'periode' => $internship->peserta?->program_magang ?? '-',
                'tanggal_penempatan_raw' => optional($internship->tanggal_mulai)->format('Y-m-d') ?? '',
                'tanggal_penempatan' => optional($internship->tanggal_mulai)->translatedFormat('d F Y') ?? '-',
                'tanggal' => optional($internship->tanggal_mulai ?? $internship->created_at)->translatedFormat('d F Y') ?? '-',
                'status' => $this->placementStatus($internship->status),
                'mentor' => $internship->mentor?->user?->name ?? '-',
                'mentor_nip' => $internship->mentor?->nip ?? '-',
                'mentor_email' => $internship->mentor?->user?->email ?? '-',
                'pembimbing_id' => $internship->pembimbing_id,
            ])->values(),
            'adminAttendances' => $attendances->map(fn (Attendance $attendance) => [
                'id' => $attendance->id,
                'nama' => $attendance->peserta?->user?->name ?? '-',
                'nim' => $attendance->peserta?->nim ?? '-',
                'instansi' => $attendance->peserta?->internship?->instansi ?? '-',
                'tanggal' => optional($attendance->tanggal)->translatedFormat('d F Y') ?? '-',
                'periodeTanggal' => $this->periodLabel($attendance->tanggal),
                'masuk' => $attendance->jam_masuk ? $attendance->jam_masuk->format('H:i') : '-',
                'keluar' => $attendance->jam_pulang ? $attendance->jam_pulang->format('H:i') : '-',
                'status' => str_replace('_', ' ', $attendance->status),
                'periode' => $attendance->peserta?->program_magang ?? '-',
                'keterangan' => $attendance->keterangan ?: '-',
            ])->values(),
            'adminAttendanceRecaps' => $attendanceRecaps,
            'adminMagangActivities' => $assignments->map(function (Assignment $assignment) {
                $statusRaw = Str::lower(trim((string) $assignment->status));
                $hasFile = filled($assignment->file_hasil);
                $deadline = $assignment->deadline;
                $createdAt = $assignment->created_at;

                $status = match (true) {
                    in_array($statusRaw, ['selesai', 'done', 'completed', 'disetujui'], true) => 'selesai',
                    in_array($statusRaw, ['terlambat', 'late'], true) => 'berlangsung',
                    in_array($statusRaw, ['review', 'finalisasi', 'dikerjakan', 'aktif', 'on_progress', 'in_progress'], true) => 'berlangsung',
                    in_array($statusRaw, ['belum_dikerjakan', 'belum dikerjakan', 'draft', 'pending', 'menunggu', 'belum dimulai'], true) => 'belum dimulai',
                    default => $assignment->progress >= 100
                        ? 'selesai'
                        : (($assignment->progress ?? 0) > 0 ? 'berlangsung' : 'belum dimulai'),
                };

                $penempatan = $assignment->peserta?->internship?->divisi
                    ?? $assignment->peserta?->internship?->unit_kerja
                    ?? $assignment->peserta?->internship?->posisi
                    ?? '-';

                return [
                    'id' => $assignment->id,
                    'peserta_id' => $assignment->peserta_id,
                    'tanggal' => optional($deadline ?? $createdAt)->translatedFormat('d F Y') ?? '-',
                    'tanggal_mulai' => optional($createdAt)->translatedFormat('d F Y') ?? '-',
                    'tanggal_selesai' => optional($deadline)->translatedFormat('d F Y') ?? '-',
                    'deadline' => optional($deadline)->translatedFormat('d F Y') ?? '-',
                    'deadline_raw' => optional($deadline)->format('Y-m-d') ?? '',
                    'nama' => $assignment->peserta?->user?->name ?? '-',
                    'nim' => $assignment->peserta?->nim ?? '-',
                    'prodi' => $assignment->peserta?->jurusan ?? '-',
                    'penempatan' => $penempatan,
                    'judul' => $assignment->judul ?: ($assignment->deskripsi ?: '-'),
                    'kegiatan' => $assignment->judul ?: ($assignment->deskripsi ?: '-'),
                    'kategori' => $assignment->prioritas ?: 'Administrasi',
                    'kategori_key' => $assignment->prioritas ?: 'Administrasi',
                    'pemberi' => $assignment->mentor?->user?->name ?? 'Admin',
                    'status' => $status,
                    'progress' => (int) ($assignment->progress ?? 0),
                    'laporan' => $hasFile ? 'dibuat' : 'belum dibuat',
                    'periode' => $assignment->peserta?->program_magang ?? '-',
                    'catatan' => $assignment->catatan ?: '-',
                    'download_url' => filled($assignment->file_hasil) ? route('mentor.monitoring.penugasan.download', $assignment) : null,
                    'submission_download_url' => filled($assignment->file_pengumpulan) ? route('mentor.monitoring.penugasan.submission.download', $assignment) : null,
                ];
            })->values(),
            'adminMagangReports' => $reports->map(function (Report $report) {
                $certificate = $this->certificatePayload($report);
                $latestAssessment = $report->peserta?->assessments?->sortByDesc('created_at')->first();
                $internship = $report->peserta?->internship;
                $internshipFinished = $internship
                    && (
                        $internship->status === 'selesai'
                        || ($internship->tanggal_selesai && now()->startOfDay()->greaterThanOrEqualTo($internship->tanggal_selesai->copy()->startOfDay()))
                    );
                $certificateEligible = $internshipFinished
                    && $latestAssessment
                    && in_array($latestAssessment->status, ['final', 'selesai', 'disetujui'], true)
                    && (float) $latestAssessment->nilai_akhir > 0;
                $isFinalReport = str_contains(strtolower(trim((string) ($report->jenis ?? 'berkala'))), 'akhir')
                    || str_contains(strtolower(trim((string) ($report->jenis ?? 'berkala'))), 'final');
                $mentorAndPembimbingApproved = filled($report->catatan_mentor) && filled($report->catatan_pembimbing);
                $adminApproved = $isFinalReport
                    && $mentorAndPembimbingApproved
                    && in_array(strtolower((string) ($report->status ?? '')), ['approved', 'disetujui'], true);
                $statusLabel = $this->reportStatus($report->status);

                if ($isFinalReport && $mentorAndPembimbingApproved) {
                    $statusLabel = $adminApproved ? 'Disetujui' : 'Disetujui oleh Mentor dan Pembimbing';
                }

                return [
                    'id' => $report->id,
                    'tanggal' => optional($report->created_at)->translatedFormat('d F Y') ?? '-',
                    'nama' => $report->peserta?->user?->name ?? '-',
                    'nim' => $report->peserta?->nim ?? '-',
                    'prodi' => $report->peserta?->jurusan ?? '-',
                    'instansi' => $report->peserta?->internship?->instansi ?? '-',
                    'periode' => $report->periode ?: ($report->peserta?->program_magang ?? '-'),
                    'judul' => $report->judul,
                    'status_raw' => strtolower((string) ($report->status ?? 'pending')),
                    'jenis_raw' => str_contains(strtolower(trim((string) ($report->jenis ?? 'berkala'))), 'akhir')
                        || str_contains(strtolower(trim((string) ($report->jenis ?? 'berkala'))), 'final')
                        ? 'akhir'
                        : 'berkala',
                    'jenis' => str_contains(strtolower(trim((string) ($report->jenis ?? 'berkala'))), 'akhir')
                        || str_contains(strtolower(trim((string) ($report->jenis ?? 'berkala'))), 'final')
                        ? 'Akhir'
                        : 'Berkala',
                    'status' => $this->reportStatus($report->status),
                    'status_label' => $statusLabel,
                    'status_raw' => strtolower((string) ($report->status ?? 'pending')),
                    'catatan_mentor' => $report->catatan_mentor ?: '-',
                    'catatan_pembimbing' => $report->catatan_pembimbing ?: '-',
                    'admin_approved_at' => optional($report->admin_approved_at)->toDateTimeString(),
                    'approval_ready' => $isFinalReport && $mentorAndPembimbingApproved,
                    'approval_done' => $adminApproved,
                    'sertifikat_exists' => filled($certificate['download_url'] ?? null),
                    'dokumen' => $report->file,
                    'download_url' => route('reports.download', $report),
                    'sertifikat' => $certificate,
                    'sertifikat_url' => $certificate['download_url'],
                    'sertifikat_file' => $certificate['file_name'],
                    'sertifikat_eligible' => $certificateEligible,
                ];
            })->values(),
            'adminVerificationHistories' => $verificationHistoryItems,
            'adminVerificationHistoryStats' => [
                'total' => $verificationHistoryItems->count(),
                'disetujui' => $verificationHistoryItems->whereIn('status', ['disetujui', 'approved'])->count(),
                'ditolak' => $verificationHistoryItems->whereIn('status', ['ditolak', 'rejected'])->count(),
                'hari_ini' => $verificationHistoryItems->filter(function (array $history) {
                    return \Carbon\Carbon::parse($history['sort_at'] ?? now()->toDateTimeString())->isToday();
                })->count(),
            ],
            'adminDocumentTypes' => $documentTypes,
            'adminDocumentParticipants' => $documentParticipants,
            'adminDocumentStats' => [
                'total_participants' => $documentParticipants->count(),
                'complete' => $documentParticipants->where('completion', 'Lengkap')->count(),
                'partial' => $documentParticipants->where('completion', 'Sebagian')->count(),
                'empty' => $documentParticipants->where('completion', 'Belum Upload')->count(),
                'uploaded_documents' => $documents->count(),
            ],
            'adminMonitoringProgram' => $monitoringProgramMonthly,
        ];
    }

    private function mapUser(User $user): array
    {
        return [
            'id' => $user->id,
            'user_id' => $user->id,
            'nama' => $user->name,
            'username' => $user->username ?? '-',
            'role' => $user->role,
            'role_label' => $this->roleLabel($user->role),
            'email' => $user->email,
            'instansi' => $this->institution($user),
            'status' => $this->uiStatus($user->account_status),
            'tanggal' => optional($user->created_at)->translatedFormat('d F Y') ?? '-',
            'verified_at' => optional($user->verified_at)->translatedFormat('d F Y H:i') ?? '-',
            'foto' => $user->avatar_url,
            'phone' => $user->phone ?: '-',
            'address' => $user->address ?: '-',
            'nim' => $user->peserta?->nim ?: '-',
            'tempat_lahir' => $user->peserta?->tempat_lahir ?: ($user->pembimbing?->tempat_lahir ?: '-'),
            'tanggal_lahir' => $this->formatDate($user->peserta?->tanggal_lahir ?? $user->pembimbing?->tanggal_lahir),
            'tanggal_lahir_raw' => optional($user->peserta?->tanggal_lahir ?? $user->pembimbing?->tanggal_lahir)->format('Y-m-d'),
            'jenis_kelamin' => $user->peserta?->jenis_kelamin ?: ($user->mentor?->jenis_kelamin ?: $user->pembimbing?->jenis_kelamin ?: '-'),
            'no_hp' => $user->peserta?->no_hp ?: ($user->mentor?->no_hp ?: $user->pembimbing?->no_hp ?: ($user->phone ?: '-')),
            'alamat' => $user->peserta?->alamat ?: ($user->mentor?->alamat ?: $user->pembimbing?->alamat ?: ($user->address ?: '-')),
            'perguruan_tinggi' => $user->peserta?->perguruanTinggi?->nama_pt ?: ($user->mentor?->perguruan_tinggi ?: $user->pembimbing?->perguruan_tinggi ?: 'LLDIKTI Wilayah V'),
            'program_studi' => $user->peserta?->jurusan ?: ($user->pembimbing?->program_studi ?: '-'),
            'fakultas' => $user->peserta?->fakultas ?: '-',
            'program_magang' => $user->peserta?->program_magang ?: '-',
            'pembimbing_akademik' => $user->peserta?->pembimbing_akademik ?: '-',
            'tanggal_mulai_magang' => $this->formatDate($user->peserta?->tanggal_mulai_magang),
            'tanggal_selesai_magang' => $this->formatDate($user->peserta?->tanggal_selesai_magang),
            'tanggal_mulai_magang_raw' => optional($user->peserta?->tanggal_mulai_magang)->format('Y-m-d'),
            'tanggal_selesai_magang_raw' => optional($user->peserta?->tanggal_selesai_magang)->format('Y-m-d'),
            'nip' => $user->mentor?->nip ?: '-',
            'jabatan' => $user->mentor?->jabatan ?: ($user->pembimbing?->jabatan ?: '-'),
            'divisi' => $user->mentor?->divisi ?: '-',
            'nidn' => $user->pembimbing?->nidn_nip ?: '-',
            'kampus' => $user->pembimbing?->perguruan_tinggi ?: '-',
        ];
    }

    private function institution(User $user): string
    {
        if ($user->peserta?->perguruanTinggi) {
            return $user->peserta->perguruanTinggi->nama_pt;
        }

        if ($user->mentor) {
            return $user->mentor->perguruan_tinggi ?: ($user->mentor->divisi ?: '-');
        }

        if ($user->pembimbing) {
            return $user->pembimbing->perguruan_tinggi ?: ($user->pembimbing->instansi ?: '-');
        }

        return 'LLDIKTI Wilayah V';
    }

    private function uiStatus(?string $status): string
    {
        return match ($status) {
            'disetujui', 'aktif', 'approved' => 'aktif',
            'ditolak', 'rejected' => 'ditolak',
            'nonaktif' => 'nonaktif',
            default => 'menunggu',
        };
    }

    private function roleLabel(?string $role): string
    {
        return match ($role) {
            'peserta' => 'Peserta',
            'mentor' => 'Mentor',
            'pembimbing' => 'Pembimbing Akademik',
            'super_admin' => 'Super Admin',
            default => ucfirst((string) $role),
        };
    }

    private function provinceFromAddress(?string $address): string
    {
        if (! $address) {
            return '-';
        }

        return str_contains(strtolower($address), 'yogyakarta') ? 'DI Yogyakarta' : $address;
    }

    private function placementStatus(?string $status): string
    {
        return match ($status) {
            'berjalan' => 'aktif',
            'selesai' => 'selesai',
            default => 'menunggu konfirmasi',
        };
    }

    private function reportStatus(?string $status): string
    {
        return match ($status) {
            'approved', 'disetujui' => 'disetujui',
            'revisi' => 'perlu diperbaiki',
            'rejected', 'ditolak' => 'ditolak',
            default => 'review',
        };
    }

    private function activityStatus(?string $status): string
    {
        return match ($status) {
            'approved', 'disetujui' => 'selesai',
            'rejected', 'ditolak' => 'belum dimulai',
            default => 'berlangsung',
        };
    }

    private function certificatePayload(Report $report): array
    {
        $certificate = $report->peserta?->certificates?->sortByDesc('tanggal_terbit')->first();

        if (! $certificate) {
            return [
                'nomor' => null,
                'file_name' => null,
                'download_url' => null,
                'status' => null,
                'predikat' => null,
            ];
        }

        $downloadUrl = null;

        if ($certificate->file && Storage::disk('public')->exists($certificate->file)) {
            $downloadUrl = Storage::disk('public')->url($certificate->file);
        }

        return [
            'nomor' => $certificate->nomor,
            'file_name' => basename($certificate->file ?: $certificate->nomor.'.pdf'),
            'download_url' => $downloadUrl,
            'status' => $certificate->status,
            'predikat' => $certificate->predikat,
        ];
    }

    private function documentStatus(Document $document): string
    {
        if (strtolower((string) $document->kategori) === 'dokumen pendukung') {
            return 'disetujui';
        }

        $status = strtolower((string) $document->status);

        return match ($status) {
            'disetujui', 'approved' => 'disetujui',
            'ditolak', 'rejected' => 'ditolak',
            'revisi' => 'revisi',
            default => 'menunggu',
        };
    }

    private function verificationStatus(?string $status): string
    {
        return match ($status) {
            'disetujui', 'approved' => 'disetujui',
            'ditolak', 'rejected' => 'ditolak',
            default => 'menunggu',
        };
    }

    private function verificationTypeLabel(?string $type): string
    {
        return match ($type) {
            'akun' => 'Akun',
            'dokumen' => 'Dokumen',
            'perguruan_tinggi', 'perguruan tinggi' => 'Perguruan Tinggi',
            default => ucfirst(str_replace('_', ' ', (string) $type)),
        };
    }

    private function documentTypes(): array
    {
        return [
            ['key' => 'proposal', 'label' => 'Proposal'],
            ['key' => 'ktm', 'label' => 'KTM'],
            ['key' => 'transkip', 'label' => 'Transkip'],
            ['key' => 'cv', 'label' => 'CV'],
            ['key' => 'surat_pengantar', 'label' => 'Surat Pengantar'],
            ['key' => 'sertifikat_pendukung', 'label' => 'Sertifikat pendukung'],
        ];
    }

    private function documentKey(Document $document): string
    {
        $source = Str::lower(trim((string) ($document->jenis_dokumen ?: $document->nama_dokumen ?: $document->file ?: '')));

        return match (true) {
            Str::contains($source, ['proposal']) => 'proposal',
            Str::contains($source, ['ktm']) => 'ktm',
            Str::contains($source, ['transkip', 'transkrip']) => 'transkip',
            Str::contains($source, ['cv']) => 'cv',
            Str::contains($source, ['surat pengantar', 'surat-pengantar', 'surat_pengantar']) => 'surat_pengantar',
            Str::contains($source, ['sertifikat']) => 'sertifikat_pendukung',
            default => Str::slug($source) ?: 'dokumen',
        };
    }

    private function periodLabel($date): string
    {
        if (! $date) {
            return 'bulan ini';
        }

        if ($date->isToday()) {
            return 'hari ini';
        }

        return $date->isCurrentWeek() ? 'minggu ini' : 'bulan ini';
    }

    private function formatDate($date): string
    {
        if (! $date) {
            return '-';
        }

        return \Carbon\Carbon::parse($date)->translatedFormat('d F Y');
    }

    private function formatDateOnly($date): string
    {
        if (! $date) {
            return '-';
        }

        return $date->copy()
            ->timezone(self::DISPLAY_TIMEZONE)
            ->translatedFormat('d M Y');
    }

    private function campusStatus(?string $status): string
    {
        return match ($status) {
            'proses', 'dalam proses', 'pending' => 'proses',
            'tidak aktif', 'nonaktif', 'inactive' => 'tidak aktif',
            default => 'aktif',
        };
    }
}
