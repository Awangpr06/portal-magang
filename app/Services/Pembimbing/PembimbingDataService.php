<?php

namespace App\Services\Pembimbing;

use App\Models\Assessment;
use App\Models\Attendance;
use App\Models\Document;
use App\Models\Internship;
use App\Models\Logbook;
use App\Models\Report;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class PembimbingDataService
{
    public function forUser(?User $user): array
    {
        if (! $user) {
            return $this->emptyContext();
        }

        try {
            $user->loadMissing(['pembimbing']);
        } catch (\Throwable $exception) {
            report($exception);

            return $this->emptyContext();
        }

        $pembimbing = $user->pembimbing;

        if (! $pembimbing) {
            return $this->emptyContext();
        }

        try {
            $internships = $this->whenTableExists('internships', fn () => Internship::query()
                ->with([
                    'peserta.user',
                    'peserta.perguruanTinggi',
                    'peserta.reports' => fn ($query) => $query->orderByDesc('created_at')->orderByDesc('id'),
                    'peserta.logbooks' => fn ($query) => $query->orderByDesc('tanggal')->orderByDesc('id'),
                    'peserta.attendances' => fn ($query) => $query->orderByDesc('tanggal')->orderByDesc('id'),
                    'peserta.assessments' => fn ($query) => $query->orderByDesc('created_at')->orderByDesc('id'),
                ])
                ->where('pembimbing_id', $pembimbing->id)
                ->orderByDesc('updated_at')
                ->get());

            $students = $internships->map(function (Internship $internship) {
                return $this->buildStudentRow($internship);
            })->values();

            $reports = $this->whenTableExists('reports', fn () => Report::query()
                ->with(['peserta.user', 'peserta.internship', 'reviewer'])
                ->whereHas('peserta.internship.pembimbing', fn ($query) => $query->where('user_id', $user->id))
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->get());

            $logbooks = $this->whenTableExists('logbooks', fn () => Logbook::query()
                ->with(['peserta.user', 'peserta.internship'])
                ->whereHas('peserta.internship.pembimbing', fn ($query) => $query->where('user_id', $user->id))
                ->orderByDesc('tanggal')
                ->orderByDesc('id')
                ->get());

            $attendances = $this->whenTableExists('attendances', fn () => Attendance::query()
                ->with(['peserta.user', 'peserta.internship'])
                ->whereHas('peserta.internship.pembimbing', fn ($query) => $query->where('user_id', $user->id))
                ->orderByDesc('tanggal')
                ->orderByDesc('id')
                ->get());

            $assessments = $this->whenTableExists('assessments', fn () => Assessment::query()
                ->with(['peserta.user', 'peserta.internship'])
                ->whereHas('peserta.internship.pembimbing', fn ($query) => $query->where('user_id', $user->id))
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->get());

            $dashboardStats = [
                [
                    'label' => 'Mahasiswa Bimbingan',
                    'value' => $students->count(),
                    'icon' => 'bi-people-fill',
                    'color' => 'primary',
                ],
                [
                    'label' => 'Laporan Menunggu',
                    'value' => $reports->whereIn('status', ['pending', 'menunggu', 'review', 'draft'])->count(),
                    'icon' => 'bi-file-earmark-text',
                    'color' => 'info',
                ],
                [
                    'label' => 'Logbook Menunggu',
                    'value' => $logbooks->whereIn('status', ['pending', 'menunggu', 'revisi', 'ditolak'])->count(),
                    'icon' => 'bi-journal-check',
                    'color' => 'warning',
                ],
                [
                    'label' => 'Perlu Tindak Lanjut',
                    'value' => $students->where('status', 'perlu tindak lanjut')->count(),
                    'icon' => 'bi-exclamation-triangle',
                    'color' => 'danger',
                ],
            ];

            $dashboardAlerts = $this->buildAlerts($students, $reports, $logbooks, $attendances, $assessments);

            return [
                'user' => $user,
                'pembimbing' => $pembimbing,
                'internships' => $internships,
                'students' => $students,
                'reports' => $reports,
                'logbooks' => $logbooks,
                'attendances' => $attendances,
                'assessments' => $assessments,
                'dashboardStats' => $dashboardStats,
                'dashboardAlerts' => $dashboardAlerts,
            ];
        } catch (\Throwable $exception) {
            report($exception);

            return $this->emptyContext();
        }
    }

    public function monitoringActivitiesForUser(?User $user): array
    {
        if (! $user) {
            return $this->emptyMonitoringContext();
        }

        try {
            $user->loadMissing(['pembimbing']);
        } catch (\Throwable $exception) {
            report($exception);

            return $this->emptyMonitoringContext();
        }

        $pembimbing = $user->pembimbing;

        if (! $pembimbing) {
            return $this->emptyMonitoringContext();
        }

        try {
            $internships = $this->whenTableExists('internships', fn () => Internship::query()
                ->with([
                    'peserta.user',
                    'peserta.perguruanTinggi',
                    'peserta.assignments' => fn ($query) => $query->orderByDesc('deadline')->orderByDesc('created_at')->orderByDesc('id'),
                ])
                ->where('pembimbing_id', $pembimbing->id)
                ->orderByDesc('updated_at')
                ->get());

            $activities = $internships->flatMap(function (Internship $internship) {
                $peserta = $internship->peserta;
                $assignments = $peserta?->assignments ?? collect();

                $penempatan = $this->resolvePenempatan($internship);

                return $assignments->map(function ($assignment) use ($internship, $peserta, $penempatan) {
                    $statusKey = strtolower((string) ($assignment->status ?? 'belum_dikerjakan'));
                    $progress = (int) ($assignment->progress ?? 0);
                    $deadline = $assignment->deadline?->copy() ?? $assignment->created_at?->copy();
                    $submittedAt = $assignment->submitted_at ?? $assignment->updated_at ?? $assignment->created_at;

                    $status = match (true) {
                        in_array($statusKey, ['selesai', 'disetujui'], true) => 'terverifikasi',
                        $statusKey === 'terlambat' => 'terlambat',
                        in_array($statusKey, ['aktif', 'belum_dikerjakan'], true) && $progress > 0 => 'menunggu',
                        in_array($statusKey, ['aktif', 'belum_dikerjakan'], true) => 'menunggu',
                        default => 'perlu catatan',
                    };

                    return [
                        'id' => $assignment->id,
                        'nama' => $peserta?->user?->name ?? '-',
                        'nim' => $peserta?->nim ?? '-',
                        'prodi' => $peserta?->jurusan ?? '-',
                        'penempatan' => $penempatan,
                        'kegiatan' => $assignment->judul ?: 'Penugasan',
                        'status' => $status,
                        'progress' => max(0, min(100, $progress)),
                        'updated' => optional($submittedAt)->translatedFormat('d M Y H:i') ?? '-',
                        'periode' => $peserta?->program_magang ?? '-',
                        'kategori' => 'penugasan',
                        'catatan' => $assignment->catatan ?: $assignment->deskripsi ?: 'Belum ada catatan terbaru.',
                    ];
                })->values();
            })->values();

            return [
                'activitiesData' => $activities,
                'penempatanOptions' => $activities
                    ->pluck('penempatan')
                    ->filter(fn ($value) => filled($value) && $value !== '-')
                    ->unique()
                    ->values(),
            ];
        } catch (\Throwable $exception) {
            report($exception);

            return $this->emptyMonitoringContext();
        }
    }

    public function monitoringOverviewForUser(?User $user): array
    {
        if (! $user) {
            return $this->emptyMonitoringOverviewContext();
        }

        try {
            $user->loadMissing(['pembimbing']);
        } catch (\Throwable $exception) {
            report($exception);

            return $this->emptyMonitoringOverviewContext();
        }

        $pembimbing = $user->pembimbing;

        if (! $pembimbing) {
            return $this->emptyMonitoringOverviewContext();
        }

        try {
            $internships = $this->whenTableExists('internships', fn () => Internship::query()
                ->with([
                    'peserta.user',
                    'peserta.perguruanTinggi',
                    'peserta.reports' => fn ($query) => $query->orderByDesc('created_at')->orderByDesc('id'),
                    'peserta.logbooks' => fn ($query) => $query->orderByDesc('tanggal')->orderByDesc('id'),
                    'peserta.attendances' => fn ($query) => $query->orderByDesc('tanggal')->orderByDesc('id'),
                    'peserta.assignments' => fn ($query) => $query->orderByDesc('deadline')->orderByDesc('created_at')->orderByDesc('id'),
                ])
                ->where('pembimbing_id', $pembimbing->id)
                ->orderByDesc('updated_at')
                ->get());

            $monitoringRows = $internships->map(function (Internship $internship) {
                $student = $this->buildStudentRow($internship);
                $peserta = $internship->peserta;
                $reports = $peserta?->reports ?? collect();
                $logbooks = $peserta?->logbooks ?? collect();
                $attendances = $peserta?->attendances ?? collect();
                $assignments = $peserta?->assignments ?? collect();

                $latestLogbook = $logbooks->first();
                $latestReport = $reports->first();
                $latestAssignment = $assignments->first();
                $latestAttendance = $attendances->first();

                $updatedAt = collect([
                    $latestLogbook?->updated_at,
                    $latestLogbook?->created_at,
                    $latestReport?->updated_at,
                    $latestReport?->created_at,
                    $latestAttendance?->updated_at,
                    $latestAttendance?->created_at,
                    $latestAssignment?->updated_at,
                    $latestAssignment?->created_at,
                    $internship->updated_at,
                ])->filter()->sortDesc()->first();

                return [
                    'id' => $internship->id,
                    'nama' => $student['nama'],
                    'nim' => $student['nim'],
                    'prodi' => $student['prodi'],
                    'penempatan' => $student['penempatan'],
                    'kampus' => $peserta?->perguruanTinggi?->nama_pt ?? '-',
                    'kehadiran' => $this->monitoringAttendanceLabel($attendances),
                    'progress' => $student['progress'],
                    'logbook' => $student['logbook'],
                    'laporan' => $student['laporan'],
                    'aktivitas' => $latestLogbook?->kegiatan
                        ?: $latestReport?->judul
                        ?: $latestAssignment?->judul
                        ?: ('Penempatan '.$student['penempatan'].' di LLDIKTI Wilayah V Yogyakarta.'),
                    'status' => $student['status'],
                    'catatan' => $student['catatan'],
                    'periode' => $student['periode'],
                    'terakhir' => optional($updatedAt)->translatedFormat('d M Y, H:i') ?? '-',
                ];
            })->values();

            return [
                'monitoringRows' => $monitoringRows,
                'monitoringPlacementOptions' => $monitoringRows
                    ->pluck('penempatan')
                    ->filter(fn ($value) => filled($value) && $value !== '-')
                    ->unique()
                    ->values(),
                'monitoringSummary' => [
                    'total' => $monitoringRows->count(),
                    'aktif' => $monitoringRows->where('status', 'aktif')->count(),
                    'perlu_tindak_lanjut' => $monitoringRows->where('status', 'perlu tindak lanjut')->count(),
                    'selesai' => $monitoringRows->where('status', 'selesai')->count(),
                    'rata_rata' => $monitoringRows->count() ? (int) round($monitoringRows->avg('progress')) : 0,
                ],
            ];
        } catch (\Throwable $exception) {
            report($exception);

            return $this->emptyMonitoringOverviewContext();
        }
    }

    public function statusMagangForUser(?User $user): array
    {
        if (! $user) {
            return $this->emptyStatusMagangContext();
        }

        try {
            $user->loadMissing(['pembimbing']);
        } catch (\Throwable $exception) {
            report($exception);

            return $this->emptyStatusMagangContext();
        }

        $pembimbing = $user->pembimbing;

        if (! $pembimbing) {
            return $this->emptyStatusMagangContext();
        }

        try {
            $internships = $this->whenTableExists('internships', fn () => Internship::query()
                ->with([
                    'peserta.user',
                    'peserta.perguruanTinggi',
                    'peserta.attendances' => fn ($query) => $query->orderByDesc('tanggal')->orderByDesc('id'),
                ])
                ->where('pembimbing_id', $pembimbing->id)
                ->orderByDesc('updated_at')
                ->get());

            $statusRows = $internships->map(function (Internship $internship) {
                $peserta = $internship->peserta;
                $attendances = $peserta?->attendances ?? collect();

                $tanggalMulai = $peserta?->tanggal_mulai_magang?->copy() ?? $internship->tanggal_mulai?->copy();
                $tanggalSelesai = $peserta?->tanggal_selesai_magang?->copy() ?? $internship->tanggal_selesai?->copy();
                $today = now('Asia/Jakarta');
                $totalDays = ($tanggalMulai && $tanggalSelesai) ? max(1, $tanggalMulai->diffInDays($tanggalSelesai)) : 0;
                $elapsedDays = ($tanggalMulai && $totalDays > 0) ? max(0, min($totalDays, $tanggalMulai->diffInDays($today))) : 0;
                $progress = $totalDays > 0 ? (int) round(($elapsedDays / $totalDays) * 100) : 0;

                $daysLeft = $tanggalSelesai
                    ? max(0, (int) $today->copy()->startOfDay()->diffInDays($tanggalSelesai->copy()->startOfDay(), false))
                    : null;

                $status = 'sedang magang';
                if ($internship->status === 'selesai' || ($tanggalSelesai && $today->greaterThanOrEqualTo($tanggalSelesai))) {
                    $status = 'selesai';
                } elseif ($daysLeft !== null && $daysLeft <= 7) {
                    $status = 'akan selesai';
                }

                $attendanceLast = $attendances->first();
                $perguruanTinggi = $peserta?->perguruanTinggi?->nama_pt ?? '-';
                $penempatan = $this->resolvePenempatan($internship);

                return [
                    'id' => $internship->id,
                    'nama' => $peserta?->user?->name ?? '-',
                    'nim' => $peserta?->nim ?? '-',
                    'prodi' => $peserta?->jurusan ?? '-',
                    'kampus' => $perguruanTinggi,
                    'penempatan' => $penempatan,
                    'periode' => $peserta?->program_magang ?? '-',
                    'pembimbing_akademik' => $peserta?->pembimbing_akademik ?? '-',
                    'status' => $status,
                    'progress' => $progress,
                    'tanggal_mulai' => optional($tanggalMulai)->translatedFormat('d M Y') ?? '-',
                    'tanggal_selesai' => optional($tanggalSelesai)->translatedFormat('d M Y') ?? '-',
                    'hari_tersisa' => $daysLeft,
                    'absensi_terakhir' => $attendanceLast?->status
                        ? ucfirst(str_replace('_', ' ', (string) $attendanceLast->status))
                        : 'Belum ada absensi',
                    'catatan' => $attendanceLast?->keterangan
                        ?: $internship->deskripsi
                        ?: 'Status magang tersinkron dari database.',
                ];
            })->values();

            return [
                'statusMagangData' => $statusRows,
                'statusMagangPlacementOptions' => $statusRows
                    ->pluck('penempatan')
                    ->filter(fn ($value) => filled($value) && $value !== '-')
                    ->unique()
                    ->values(),
                'statusMagangCampusOptions' => $statusRows
                    ->pluck('kampus')
                    ->filter(fn ($value) => filled($value) && $value !== '-')
                    ->unique()
                    ->values(),
                'statusMagangSummary' => [
                    'total' => $statusRows->count(),
                    'sedang' => $statusRows->where('status', 'sedang magang')->count(),
                    'akan_selesai' => $statusRows->where('status', 'akan selesai')->count(),
                    'selesai' => $statusRows->where('status', 'selesai')->count(),
                    'rata_rata' => $statusRows->count() ? (int) round($statusRows->avg('progress')) : 0,
                ],
            ];
        } catch (\Throwable $exception) {
            report($exception);

            return $this->emptyStatusMagangContext();
        }
    }

    public function cooperationDataForUser(?User $user): array
    {
        if (! $user) {
            return $this->emptyCooperationContext();
        }

        try {
            $user->loadMissing(['pembimbing']);
        } catch (\Throwable $exception) {
            report($exception);

            return $this->emptyCooperationContext();
        }

        $pembimbing = $user->pembimbing;

        if (! $pembimbing) {
            return $this->emptyCooperationContext();
        }

        try {
            $documents = $this->whenTableExists('documents', fn () => Document::query()
                ->with(['user.peserta.perguruanTinggi', 'user.peserta.internship'])
                ->where('kategori', 'Dokumen Kerja Sama')
                ->whereHas('user.peserta.internship', fn ($query) => $query->where('pembimbing_id', $pembimbing->id))
                ->latest()
                ->get());

            $cooperationRows = $documents
                ->map(function (Document $document) {
                    $peserta = $document->user?->peserta;
                    $status = strtolower((string) ($document->status ?? 'menunggu'));
                    $fileName = $document->file ? basename((string) $document->file) : ($document->nama_dokumen ?? '-');

                    return [
                        'id' => $document->id,
                        'user_id' => $document->user_id,
                        'nama' => $document->nama_dokumen ?? '-',
                        'pemilik' => $document->user?->name ?? '-',
                        'nim' => $peserta?->nim ?? '-',
                        'kampus' => $peserta?->perguruanTinggi?->nama_pt ?? '-',
                        'jenis' => strtoupper((string) $document->jenis_dokumen ?: '-'),
                        'jenis_key' => $document->jenis_dokumen,
                        'file' => $document->file,
                        'status' => $status,
                        'status_label' => match (true) {
                            in_array($status, ['disetujui', 'approved', 'aktif'], true) => 'Disetujui',
                            in_array($status, ['ditolak', 'rejected', 'nonaktif'], true) => 'Ditolak',
                            in_array($status, ['revisi'], true) => 'Revisi',
                            default => 'Menunggu',
                        },
                        'catatan' => $document->catatan ?: '-',
                        'uploaded_at' => optional($document->created_at)->translatedFormat('d M Y H:i') ?? '-',
                        'size' => $document->ukuran_file ? number_format(((float) $document->ukuran_file) / 1024, 1) . ' KB' : '-',
                        'download_url' => $document->file && Storage::disk('public')->exists($document->file)
                            ? Storage::disk('public')->url($document->file)
                            : null,
                        'file_name' => $fileName,
                    ];
                })
                ->values();

            $cooperationStats = [
                'total' => $cooperationRows->count(),
                'menunggu' => $cooperationRows->where('status', 'menunggu')->count(),
                'disetujui' => $cooperationRows->whereIn('status', ['disetujui', 'approved', 'aktif'])->count(),
                'ditolak' => $cooperationRows->whereIn('status', ['ditolak', 'rejected', 'nonaktif'])->count(),
                'revisi' => $cooperationRows->where('status', 'revisi')->count(),
            ];

            return [
                'cooperationRows' => $cooperationRows,
                'cooperationUploads' => $cooperationRows,
                'cooperationStats' => $cooperationStats,
            ];
        } catch (\Throwable $exception) {
            report($exception);

            return $this->emptyCooperationContext();
        }
    }

    private function buildStudentRow(Internship $internship): array
    {
        $peserta = $internship->peserta;
        $reports = $peserta?->reports ?? collect();
        $logbooks = $peserta?->logbooks ?? collect();
        $attendances = $peserta?->attendances ?? collect();
        $assessments = $peserta?->assessments ?? collect();

        $tanggalMulai = $peserta?->tanggal_mulai_magang?->copy() ?? $internship->tanggal_mulai?->copy();
        $tanggalSelesai = $peserta?->tanggal_selesai_magang?->copy() ?? $internship->tanggal_selesai?->copy();
        $durasi = ($tanggalMulai && $tanggalSelesai) ? max(1, $tanggalMulai->diffInDays($tanggalSelesai)) : 0;
        $berjalan = ($tanggalMulai && $durasi > 0) ? max(0, min($durasi, $tanggalMulai->diffInDays(now()))) : 0;
        $progress = $durasi > 0 ? (int) round(($berjalan / $durasi) * 100) : 0;

        $attendanceValid = $attendances->whereIn('status', ['hadir', 'terlambat']);
        $attendanceAbsent = $attendances->whereIn('status', ['alpa', 'tidak_hadir']);
        $attendanceLate = $attendances->where('status', 'terlambat');
        $attendancePercent = $attendances->count() > 0
            ? (int) round(($attendanceValid->count() / $attendances->count()) * 100)
            : 0;

        $latestReport = $reports->first();
        $latestLogbook = $logbooks->first();
        $latestAssessment = $assessments->first();

        $laporanStatus = $this->latestReportStatus($latestReport?->status);
        $nilaiStatus = ($latestAssessment && in_array($latestAssessment->status, ['final', 'selesai', 'disetujui'], true))
            || (($latestAssessment?->nilai_akhir ?? 0) > 0)
            ? 'selesai'
            : 'belum';

        $absensiStatus = $attendances->count() === 0
            ? 'Belum lengkap'
            : (
                $attendanceAbsent->count() > 0
                    ? 'Tidak hadir '.$attendanceAbsent->count().' kali'
                    : (
                        $attendanceLate->count() > 0
                            ? 'Terlambat '.$attendanceLate->count().' kali'
                            : 'Hadir '.$attendanceValid->count().' hari'
                    )
            );

        $logbookStatus = $logbooks->count() > 0 && in_array($latestLogbook?->status, ['approved', 'disetujui', 'selesai'], true)
            ? 'Lengkap'
            : ($logbooks->count() > 0 ? 'Perlu cek' : 'Belum ada');

        $status = 'aktif';

        if ($internship->status === 'selesai' || ($tanggalSelesai && now()->greaterThanOrEqualTo($tanggalSelesai))) {
            $status = 'selesai';
        } elseif (($peserta?->status ?? 'aktif') !== 'aktif' || $internship->status === 'pending') {
            $status = 'belum aktif';
        } elseif (
            ($attendancePercent > 0 && $attendancePercent < 70)
            || in_array($laporanStatus, ['review', 'perlu revisi', 'draft'], true)
            || in_array($logbookStatus, ['Perlu cek', 'Belum ada'], true)
            || ($latestAssessment && ! in_array($latestAssessment->status, ['final', 'selesai', 'disetujui'], true))
        ) {
            $status = 'perlu tindak lanjut';
        }

        $catatan = $latestAssessment?->catatan
            ?? $latestReport?->catatan
            ?? $latestLogbook?->deskripsi
            ?? $internship->deskripsi
            ?? 'Data bimbingan belum memiliki catatan terbaru.';

        return [
            'id' => $internship->id,
            'nama' => $peserta?->user?->name ?? '-',
            'nim' => $peserta?->nim ?? '-',
            'prodi' => $peserta?->jurusan ?? '-',
            'penempatan' => $this->resolvePenempatan($internship),
            'periode' => $peserta?->program_magang ?? '-',
            'status' => $status,
            'progress' => $progress,
            'laporan' => $laporanStatus,
            'nilai' => $nilaiStatus,
            'absensi' => $absensiStatus,
            'logbook' => $logbookStatus,
            'catatan' => $catatan,
        ];
    }

    private function latestReportStatus(?string $status): string
    {
        return match ($status) {
            'approved', 'disetujui', 'selesai' => 'selesai',
            'review' => 'review',
            'pending', 'menunggu', 'draft' => 'review',
            'revisi', 'rejected', 'ditolak' => 'perlu revisi',
            default => 'belum',
        };
    }

    private function buildAlerts(Collection $students, Collection $reports, Collection $logbooks, Collection $attendances, Collection $assessments): array
    {
        $alerts = [];

        if ($students->isEmpty()) {
            $alerts[] = 'Belum ada mahasiswa bimbingan yang terhubung di database.';
        } else {
            $followUpStudent = $students->firstWhere('status', 'perlu tindak lanjut');
            if ($followUpStudent) {
                $alerts[] = $followUpStudent['nama'].' perlu tindak lanjut pada '.$followUpStudent['laporan'].' dan progres magang.';
            }
        }

        $reportWaiting = $reports->whereIn('status', ['pending', 'menunggu', 'review', 'draft'])->count();
        if ($reportWaiting > 0) {
            $alerts[] = $reportWaiting.' laporan masih menunggu review pembimbing.';
        }

        $logbookWaiting = $logbooks->whereIn('status', ['pending', 'menunggu', 'revisi', 'ditolak'])->count();
        if ($logbookWaiting > 0) {
            $alerts[] = $logbookWaiting.' logbook masih perlu validasi atau perbaikan.';
        }

        $attendanceIssue = $attendances->whereIn('status', ['alpa', 'tidak_hadir', 'izin', 'sakit'])->count();
        if ($attendanceIssue > 0) {
            $alerts[] = $attendanceIssue.' data absensi membutuhkan perhatian.';
        }

        $assessmentPending = $assessments->whereNotIn('status', ['final', 'selesai', 'disetujui'])->count();
        if ($assessmentPending > 0) {
            $alerts[] = $assessmentPending.' penilaian belum berstatus final.';
        }

        return array_slice($alerts, 0, 3);
    }

    private function resolvePenempatan(Internship $internship): string
    {
        return $internship->divisi
            ?: $internship->unit_kerja
            ?: $internship->posisi
            ?: $internship->penempatan
            ?: '-';
    }

    private function monitoringAttendanceLabel(Collection $attendances): string
    {
        $hadirCount = $attendances->where('status', 'hadir')->count();
        $terlambatCount = $attendances->where('status', 'terlambat')->count();
        $izinCount = $attendances->where('status', 'izin')->count();
        $sakitCount = $attendances->where('status', 'sakit')->count();
        $absenCount = $attendances->whereIn('status', ['alpa', 'tidak_hadir'])->count();

        return match (true) {
            $absenCount > 0 => 'Tidak Hadir',
            $terlambatCount > 0 => 'Terlambat',
            $izinCount > 0 => 'Izin',
            $sakitCount > 0 => 'Sakit',
            $hadirCount > 0 => 'Hadir',
            default => 'Dipantau',
        };
    }

    private function emptyMonitoringOverviewContext(): array
    {
        return [
            'monitoringRows' => new Collection(),
            'monitoringPlacementOptions' => new Collection(),
            'monitoringSummary' => [
                'total' => 0,
                'aktif' => 0,
                'perlu_tindak_lanjut' => 0,
                'selesai' => 0,
                'rata_rata' => 0,
            ],
        ];
    }

    private function emptyStatusMagangContext(): array
    {
        return [
            'statusMagangData' => new Collection(),
            'statusMagangPlacementOptions' => new Collection(),
            'statusMagangCampusOptions' => new Collection(),
            'statusMagangSummary' => [
                'total' => 0,
                'sedang' => 0,
                'akan_selesai' => 0,
                'selesai' => 0,
                'rata_rata' => 0,
            ],
        ];
    }

    private function activityCategory($latestActivity): string
    {
        if ($latestActivity instanceof Logbook) {
            return 'harian';
        }

        if ($latestActivity instanceof Report) {
            $jenis = strtolower((string) ($latestActivity->jenis ?? ''));

            return $jenis === 'akhir' ? 'evaluasi' : 'laporan';
        }

        if ($latestActivity instanceof Attendance) {
            return 'mingguan';
        }

        return 'harian';
    }

    private function whenTableExists(string $table, callable $callback)
    {
        if (! Schema::hasTable($table)) {
            return new Collection();
        }

        return $callback();
    }

    private function emptyContext(): array
    {
        return [
            'user' => null,
            'pembimbing' => null,
            'internships' => new Collection(),
            'students' => new Collection(),
            'reports' => new Collection(),
            'logbooks' => new Collection(),
            'attendances' => new Collection(),
            'assessments' => new Collection(),
            'dashboardStats' => [],
            'dashboardAlerts' => [],
        ];
    }

    private function emptyMonitoringContext(): array
    {
        return [
            'activitiesData' => new Collection(),
            'penempatanOptions' => new Collection(),
        ];
    }

    private function emptyCooperationContext(): array
    {
        return [
            'cooperationRows' => new Collection(),
            'cooperationStats' => [
                'total' => 0,
                'aktif' => 0,
                'menunggu_validasi' => 0,
                'selesai' => 0,
                'peserta_aktif' => 0,
            ],
        ];
    }
}
