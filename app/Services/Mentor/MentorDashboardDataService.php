<?php

namespace App\Services\Mentor;

use App\Models\Assessment;
use App\Models\Assignment;
use App\Models\Attendance;
use App\Models\Document;
use App\Models\Internship;
use App\Models\Logbook;
use App\Models\Report;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class MentorDashboardDataService
{
    public function forUser(?User $user): array
    {
        if (! $user) {
            return $this->emptyContext();
        }

        try {
            $user->loadMissing(['mentor']);
        } catch (\Throwable $exception) {
            report($exception);

            return $this->emptyContext();
        }

        $mentor = $user->mentor;

        if (! $mentor) {
            return $this->emptyContext();
        }

        $mentorIds = collect([$mentor->id, $mentor->user_id])
            ->filter(fn ($value) => filled($value))
            ->unique()
            ->values()
            ->all();

        try {
            $internships = $this->whenTableExists('internships', fn () => Internship::query()
                ->with([
                    'peserta.user.documents',
                    'peserta.perguruanTinggi',
                    'peserta.logbooks' => fn ($query) => $query->orderByDesc('tanggal')->orderByDesc('id'),
                    'peserta.attendances' => fn ($query) => $query->orderByDesc('tanggal')->orderByDesc('id'),
                    'peserta.reports' => fn ($query) => $query->orderByDesc('created_at')->orderByDesc('id'),
                    'peserta.assignments' => fn ($query) => $query->orderByDesc('deadline')->orderByDesc('id'),
                    'peserta.assessments' => fn ($query) => $query->orderByDesc('created_at')->orderByDesc('id'),
                    'mentor.user',
                ])
                ->whereIn('mentor_id', $mentorIds)
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->get());

            $participants = $internships->map(function (Internship $internship) use ($mentor) {
                return $this->buildParticipantRow($internship, $mentor);
            })->values();

            $reports = $this->whenTableExists('reports', fn () => Report::query()
                ->with(['peserta.user', 'peserta.internship'])
                ->whereHas('peserta.internship', fn ($query) => $query->whereIn('mentor_id', $mentorIds))
                ->latest()
                ->get());

            $logbooks = $this->whenTableExists('logbooks', fn () => Logbook::query()
                ->with(['peserta.user', 'peserta.internship'])
                ->whereHas('peserta.internship', fn ($query) => $query->whereIn('mentor_id', $mentorIds))
                ->latest('tanggal')
                ->latest('id')
                ->get());

            $attendances = $this->whenTableExists('attendances', fn () => Attendance::query()
                ->with(['peserta.user', 'peserta.internship'])
                ->whereHas('peserta.internship', fn ($query) => $query->whereIn('mentor_id', $mentorIds))
                ->latest('tanggal')
                ->latest('id')
                ->get());

            $assignments = $this->whenTableExists('assignments', fn () => Assignment::query()
                ->with(['peserta.user', 'peserta.internship'])
                ->whereHas('peserta.internship', fn ($query) => $query->whereIn('mentor_id', $mentorIds))
                ->latest('deadline')
                ->latest('id')
                ->get());

            $assessments = $this->whenTableExists('assessments', fn () => Assessment::query()
                ->with(['peserta.user', 'peserta.internship'])
                ->whereHas('peserta.internship', fn ($query) => $query->whereIn('mentor_id', $mentorIds))
                ->latest('created_at')
                ->latest('id')
                ->get());

            $documents = $this->whenTableExists('documents', fn () => Document::query()
                ->with(['user.peserta.internship.mentor.user'])
                ->whereHas('user.peserta.internship', fn ($query) => $query->whereIn('mentor_id', $mentorIds))
                ->latest()
                ->get());

            $dashboardStats = [
                [
                    'label' => 'Peserta Bimbingan',
                    'value' => (string) $participants->count(),
                    'icon' => 'bi-people-fill',
                    'color' => 'primary',
                ],
                [
                    'label' => 'Peserta Aktif',
                    'value' => (string) $participants->where('status', 'aktif')->count(),
                    'icon' => 'bi-person-check',
                    'color' => 'success',
                ],
                [
                    'label' => 'Logbook Review',
                    'value' => (string) $logbooks->whereIn('status', ['pending', 'menunggu', 'revisi', 'ditolak'])->count(),
                    'icon' => 'bi-journal-text',
                    'color' => 'warning',
                ],
                [
                    'label' => 'Laporan Diperiksa',
                    'value' => (string) $reports->whereIn('status', ['approved', 'disetujui', 'review'])->count(),
                    'icon' => 'bi-file-earmark-text',
                    'color' => 'info',
                ],
                [
                    'label' => 'Penugasan Aktif',
                    'value' => (string) $assignments->whereNotIn('status', ['selesai', 'disetujui'])->count(),
                    'icon' => 'bi-list-task',
                    'color' => 'dark',
                ],
                [
                    'label' => 'Absensi Dipantau',
                    'value' => (string) $attendances->whereIn('status', ['terlambat', 'alpa', 'tidak_hadir', 'izin', 'sakit'])->count(),
                    'icon' => 'bi-calendar-x',
                    'color' => 'danger',
                ],
                [
                    'label' => 'Status Penilaian',
                    'value' => $this->assessmentAverageLabel($assessments),
                    'icon' => 'bi-clipboard2-check',
                    'color' => 'secondary',
                ],
            ];

            $recentActivities = $participants
                ->sortByDesc('sort_at')
                ->take(4)
                ->values();

            $progressRows = $participants
                ->sortByDesc('progress')
                ->take(4)
                ->values();

            $documentRows = $documents
                ->take(4)
                ->map(function (Document $document) {
                    $status = $this->documentStatus((string) ($document->status ?? 'menunggu'));

                    return [
                        'id' => $document->id,
                        'nama' => $document->nama_dokumen ?: ($document->jenis_dokumen ?: 'Dokumen'),
                        'peserta' => $document->user?->name ?? '-',
                        'tanggal' => optional($document->created_at)->translatedFormat('d M Y') ?? '-',
                        'status' => $status['label'],
                        'status_class' => $status['class'],
                        'aksi' => route('mentor.monitoring'),
                    ];
                })
                ->values();

            return [
                'mentorDashboard' => [
                    'user' => $user,
                    'mentor' => $mentor,
                    'dashboardStats' => $dashboardStats,
                    'recentActivities' => $recentActivities,
                    'progressRows' => $progressRows,
                    'documentRows' => $documentRows,
                    'summary' => [
                        'total' => $participants->count(),
                        'aktif' => $participants->where('status', 'aktif')->count(),
                        'review' => $participants->where('status', 'review')->count(),
                        'selesai' => $participants->where('status', 'selesai')->count(),
                        'perlu_perhatian' => $participants->where('status', 'perlu perhatian')->count(),
                        'rata_progress' => $participants->count() ? (int) round($participants->avg('progress')) : 0,
                    ],
                ],
            ];
        } catch (\Throwable $exception) {
            report($exception);

            return $this->emptyContext();
        }
    }

    public function monitoringForUser(?User $user): array
    {
        if (! $user) {
            return $this->emptyMonitoringContext();
        }

        try {
            $user->loadMissing(['mentor']);
        } catch (\Throwable $exception) {
            report($exception);

            return $this->emptyMonitoringContext();
        }

        $mentor = $user->mentor;

        if (! $mentor) {
            return $this->emptyMonitoringContext();
        }

        $mentorIds = collect([$mentor->id, $mentor->user_id])
            ->filter(fn ($value) => filled($value))
            ->unique()
            ->values()
            ->all();

        try {
            $internships = $this->whenTableExists('internships', fn () => Internship::query()
                ->with([
                    'peserta.user',
                    'peserta.perguruanTinggi',
                    'peserta.logbooks' => fn ($query) => $query->orderByDesc('tanggal')->orderByDesc('id'),
                    'peserta.attendances' => fn ($query) => $query->orderByDesc('tanggal')->orderByDesc('id'),
                    'peserta.reports' => fn ($query) => $query->orderByDesc('created_at')->orderByDesc('id'),
                    'peserta.assignments' => fn ($query) => $query->orderByDesc('deadline')->orderByDesc('id'),
                    'mentor.user',
                    'pembimbing.user',
                ])
                ->whereIn('mentor_id', $mentorIds)
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->get());

            $today = Carbon::today();

            $monitoringRows = $internships->map(function (Internship $internship) use ($today) {
                $peserta = $internship->peserta;
                $reports = $peserta?->reports ?? collect();
                $logbooks = $peserta?->logbooks ?? collect();
                $attendances = $peserta?->attendances ?? collect();
                $assignments = $peserta?->assignments ?? collect();

                $reportCount = $reports->count();
                $logbookCount = $logbooks->count();
                $attendanceCount = $attendances->count();
                $assignmentCount = $assignments->count();
                $activityCount = $reportCount + $logbookCount + $attendanceCount + $assignmentCount;

                $hadirCount = $attendances->where('status', 'hadir')->count();
                $terlambatCount = $attendances->where('status', 'terlambat')->count();
                $izinCount = $attendances->where('status', 'izin')->count();
                $sakitCount = $attendances->where('status', 'sakit')->count();
                $absenCount = $attendances->whereIn('status', ['alpa', 'tidak_hadir'])->count();

                $attendanceValid = $hadirCount + $terlambatCount;
                $attendancePercent = $attendanceCount > 0
                    ? (int) round(($attendanceValid / $attendanceCount) * 100)
                    : 0;

                $placement = $internship->divisi ?: ($internship->unit_kerja ?: ($internship->posisi ?: '-'));
                $latestLogbook = $logbooks->first();
                $latestReport = $reports->first();
                $latestAttendance = $attendances->first();
                $latestAssignment = $assignments->first();
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

                $activityTitle = collect([
                    filled($latestLogbook?->kegiatan) ? $latestLogbook->kegiatan : null,
                    filled($latestReport?->judul) ? $latestReport->judul : null,
                    filled($latestAssignment?->judul) ? $latestAssignment->judul : null,
                    $latestAttendance ? 'Absensi '.($latestAttendance->status ?? '-') : null,
                ])->filter()->first() ?? 'Belum ada aktivitas terbaru.';

                $status = 'proses';
                if (($latestAssignment && in_array($latestAssignment->status, ['terlambat'], true)) || $absenCount > 0) {
                    $status = 'terlambat';
                } elseif (($latestReport && in_array($latestReport->status, ['revisi', 'pending', 'review', 'draft'], true)) || $latestLogbook && in_array($latestLogbook->status, ['pending', 'menunggu', 'revisi', 'ditolak'], true)) {
                    $status = 'perlu review';
                } elseif ($attendancePercent >= 75) {
                    $status = 'sesuai target';
                }

                return [
                    'id' => $internship->id,
                    'peserta_id' => $peserta?->id,
                    'nama' => $peserta?->user?->name ?? '-',
                    'kampus' => $peserta?->perguruanTinggi?->nama_pt ?? '-',
                    'hadir' => $attendancePercent,
                    'aktivitas' => $activityCount,
                    'laporan' => $reportCount,
                    'progress' => $this->monitoringProgress($internship, $peserta),
                    'status' => $status,
                    'periode' => $peserta?->program_magang ?? '-',
                    'terakhir' => optional($updatedAt)->translatedFormat('d M Y, H:i') ?? '-',
                    'detail' => $latestLogbook?->deskripsi
                        ?: $latestReport?->catatan
                        ?: $latestAssignment?->catatan
                        ?: ('Penempatan '.$placement.' di LLDIKTI Wilayah V Yogyakarta.'),
                ];
            })->values();

            $attendanceRows = $internships->map(function (Internship $internship) use ($today) {
                $peserta = $internship->peserta;
                $attendances = $peserta?->attendances ?? collect();

                $hadirCount = $attendances->where('status', 'hadir')->count();
                $terlambatCount = $attendances->where('status', 'terlambat')->count();
                $izinCount = $attendances->where('status', 'izin')->count();
                $sakitCount = $attendances->where('status', 'sakit')->count();
                $absenCount = $attendances->whereIn('status', ['alpa', 'tidak_hadir'])->count();

                $totalAttendances = $attendances->count();
                $validAttendances = $hadirCount + $terlambatCount;
                $attendancePercent = $totalAttendances > 0
                    ? (int) round(($validAttendances / $totalAttendances) * 100)
                    : 0;

                $placement = $internship->divisi ?: ($internship->unit_kerja ?: ($internship->posisi ?: '-'));
                $latestAttendance = $attendances->first();

                $status = match (true) {
                    $sakitCount > 0 || $izinCount > 0 => 'dipantau',
                    $absenCount > 0 && $attendancePercent < 80 => 'tidak hadir',
                    $terlambatCount > 0 && $attendancePercent < 90 => 'terlambat',
                    $attendancePercent >= 90 => 'hadir',
                    default => 'dipantau',
                };

                $note = match (true) {
                    $sakitCount > 0 => 'Ada data sakit yang perlu dipantau mentor.',
                    $izinCount > 0 => 'Ada data izin yang perlu dipantau mentor.',
                    $absenCount > 0 => 'Ada ketidakhadiran yang perlu ditindaklanjuti.',
                    $terlambatCount > 0 => 'Ada keterlambatan yang perlu dipantau mentor.',
                    default => 'Absensi peserta berjalan stabil.',
                };

                return [
                    'id' => $internship->id,
                    'nama' => $peserta?->user?->name ?? '-',
                    'nim' => $peserta?->nim ?? '-',
                    'prodi' => $peserta?->jurusan ?? '-',
                    'kampus' => $peserta?->perguruanTinggi?->nama_pt ?? '-',
                    'penempatan' => $placement,
                    'lokasi' => $placement,
                    'periode' => $peserta?->program_magang ?? '-',
                    'hadir' => $hadirCount,
                    'terlambat' => $terlambatCount,
                    'absen' => $absenCount,
                    'izin' => $izinCount,
                    'sakit' => $sakitCount,
                    'persen' => $attendancePercent,
                    'status' => $status,
                    'catatan' => $note,
                    'tanggal_terakhir' => $latestAttendance?->tanggal?->format('d M Y') ?? '-',
                    'mentor' => $internship->mentor?->user?->name ?? '-',
                ];
            })->values();

            $attendanceSummary = [
                'total' => $attendanceRows->count(),
                'present' => $attendanceRows->where('status', 'hadir')->count(),
                'late' => $attendanceRows->where('status', 'terlambat')->count(),
                'permit' => $attendanceRows->sum('izin'),
                'sick' => $attendanceRows->sum('sakit'),
                'absent' => $attendanceRows->where('status', 'tidak hadir')->count(),
                'percentage' => $attendanceRows->count()
                    ? (int) round($attendanceRows->avg('persen'))
                    : 0,
            ];

            $attendancePlacementOptions = $attendanceRows
                ->pluck('penempatan')
                ->filter(fn ($value) => filled($value) && $value !== '-')
                ->unique()
                ->values();

            $statusMagangData = $internships->map(function (Internship $internship) use ($today) {
                $peserta = $internship->peserta;
                $tanggalMulai = $peserta?->tanggal_mulai_magang?->copy() ?? $internship->tanggal_mulai?->copy();
                $tanggalSelesai = $peserta?->tanggal_selesai_magang?->copy() ?? $internship->tanggal_selesai?->copy();
                $penempatan = $internship->divisi ?: ($internship->unit_kerja ?: ($internship->posisi ?: '-'));

                $status = 'sedang magang';
                if ($tanggalSelesai) {
                    if ($today->greaterThanOrEqualTo($tanggalSelesai)) {
                        $status = 'selesai';
                    } elseif ($today->diffInDays($tanggalSelesai) <= 7) {
                        $status = 'akan selesai';
                    }
                }

                $totalDays = ($tanggalMulai && $tanggalSelesai) ? max(1, $tanggalMulai->diffInDays($tanggalSelesai)) : 0;
                $elapsedDays = ($tanggalMulai && $totalDays > 0) ? max(0, min($totalDays, $tanggalMulai->diffInDays($today))) : 0;
                $progress = $totalDays > 0 ? (int) round(($elapsedDays / $totalDays) * 100) : 0;
                $latestAttendance = $peserta?->attendances?->first();

                return [
                    'id' => $internship->id,
                    'nama' => $peserta?->user?->name ?? '-',
                    'kampus' => $peserta?->perguruanTinggi?->nama_pt ?? '-',
                    'instansi' => $internship->instansi ?? '-',
                    'penempatan' => $penempatan,
                    'lokasi' => $penempatan,
                    'periode' => $peserta?->program_magang ?? '-',
                    'pembimbing_akademik' => $internship->pembimbing?->user?->name ?? '-',
                    'status' => $status,
                    'progress' => $progress,
                    'prodi' => $peserta?->jurusan ?? '-',
                    'catatan' => match ($status) {
                        'akan selesai' => 'Sisa periode magang 7 hari atau kurang.',
                        'selesai' => 'Periode magang sudah selesai.',
                        default => 'Periode magang masih berjalan.',
                    },
                    'tanggal_mulai' => $tanggalMulai?->format('d M Y') ?? '-',
                    'tanggal_selesai' => $tanggalSelesai?->format('d M Y') ?? '-',
                    'hari_tersisa' => $tanggalSelesai ? max(0, $today->diffInDays($tanggalSelesai)) : null,
                    'absensi_terakhir' => $latestAttendance?->tanggal?->format('d M Y') ?? '-',
                ];
            })->values();

            $logbookRows = $internships
                ->flatMap(function (Internship $internship) {
                    $peserta = $internship->peserta;
                    $logbooks = $peserta?->logbooks ?? collect();

                    return $logbooks->map(function (Logbook $logbook) use ($internship, $peserta) {
                        $tanggal = $logbook->tanggal instanceof Carbon
                            ? $logbook->tanggal
                            : Carbon::parse($logbook->tanggal ?? $logbook->created_at ?? now());
                        $statusKey = strtolower((string) ($logbook->status ?? 'menunggu'));

                        return [
                            'id' => $logbook->id,
                            'peserta' => $peserta?->user?->name ?? '-',
                            'tanggal' => $tanggal->format('Y-m-d'),
                            'tanggal_label' => $tanggal->translatedFormat('d M Y'),
                            'periode' => $this->monitoringPeriodLabel($tanggal, $peserta?->program_magang),
                            'kegiatan' => $logbook->kegiatan ?: 'Logbook Harian',
                            'lampiran' => filled($logbook->lampiran ?? $logbook->file ?? null) ? 'ada' : 'tidak ada',
                            'status' => $this->mentorLogbookStatusLabel($statusKey),
                            'reviewer' => $internship->mentor?->user?->name ?? '-',
                            'reviewDate' => optional($logbook->updated_at ?? $logbook->created_at)->translatedFormat('d M Y') ?? '-',
                            'catatan' => $logbook->deskripsi ?: 'Belum ada catatan.',
                        ];
                    });
                })
                ->sortByDesc(fn (array $row) => strtotime($row['tanggal'] ?? now()->toDateString()) ?: 0)
                ->values();

            $assignmentRows = $internships
                ->flatMap(function (Internship $internship) {
                    $peserta = $internship->peserta;
                    $assignments = $peserta?->assignments ?? collect();

                    return $assignments->map(function (Assignment $assignment) use ($internship, $peserta) {
                        $deadline = $assignment->deadline instanceof Carbon
                            ? $assignment->deadline
                            : Carbon::parse($assignment->deadline ?? $assignment->created_at ?? now());
                        $createdAt = $assignment->created_at instanceof Carbon
                            ? $assignment->created_at
                            : Carbon::parse($assignment->created_at ?? now());

                        return [
                            'id' => $assignment->id,
                            'peserta_id' => $peserta?->id,
                            'judul' => $assignment->judul ?: 'Penugasan',
                            'peserta' => $peserta?->user?->name ?? '-',
                            'peserta_label' => $peserta?->user?->name ?? '-',
                            'kategori' => $assignment->prioritas ?: 'Administrasi',
                            'kategori_key' => $assignment->prioritas ?: 'Administrasi',
                            'diberikan' => $createdAt->translatedFormat('d M Y'),
                            'diberikan_raw' => $createdAt->format('Y-m-d'),
                            'deadline' => $deadline->translatedFormat('d M Y'),
                            'deadline_raw' => $deadline->format('Y-m-d'),
                            'status' => $this->mentorAssignmentStatusLabel((string) ($assignment->status ?? 'aktif')),
                            'status_key' => (string) ($assignment->status ?? 'belum_dikerjakan'),
                            'progress' => (int) ($assignment->progress ?? 0),
                            'periode' => $peserta?->program_magang ?? '-',
                            'catatan' => $assignment->catatan ?: 'Belum ada catatan.',
                            'lampiran' => filled($assignment->file_hasil ?? null) ? basename((string) $assignment->file_hasil) : '-',
                            'file_url' => filled($assignment->file_hasil ?? null) ? route('mentor.monitoring.penugasan.download', $assignment) : null,
                            'submission' => filled($assignment->file_pengumpulan ?? null) ? basename((string) $assignment->file_pengumpulan) : '-',
                            'submission_url' => filled($assignment->file_pengumpulan ?? null) ? route('mentor.monitoring.penugasan.submission.download', $assignment) : null,
                            'submission_at' => optional($assignment->submitted_at ?? $assignment->updated_at)->translatedFormat('d M Y H:i') ?? '-',
                        ];
                    });
                })
                ->sortByDesc(fn (array $row) => strtotime($row['diberikan_raw'] ?? now()->toDateString()) ?: 0)
                ->values();

            return [
                'monitoringRows' => $monitoringRows,
                'attendanceRows' => $attendanceRows,
                'attendanceSummary' => $attendanceSummary,
                'attendancePlacementOptions' => $attendancePlacementOptions,
                'statusMagangData' => $statusMagangData,
                'logbookRows' => $logbookRows,
                'assignmentRows' => $assignmentRows,
            ];
        } catch (\Throwable $exception) {
            report($exception);

            return $this->emptyMonitoringContext();
        }
    }

    private function buildParticipantRow(Internship $internship, $mentor): array
    {
        $peserta = $internship->peserta;
        $reports = $peserta?->reports ?? collect();
        $logbooks = $peserta?->logbooks ?? collect();
        $attendances = $peserta?->attendances ?? collect();
        $assignments = $peserta?->assignments ?? collect();
        $assessments = $peserta?->assessments ?? collect();

        $start = $internship->tanggal_mulai ?: $peserta?->tanggal_mulai_magang;
        $end = $internship->tanggal_selesai ?: $peserta?->tanggal_selesai_magang;
        if ($start && ! $start instanceof Carbon) {
            $start = Carbon::parse($start);
        }
        if ($end && ! $end instanceof Carbon) {
            $end = Carbon::parse($end);
        }

        $totalDays = ($start && $end) ? max($start->diffInDays($end), 1) : 0;
        $elapsedDays = ($start && $totalDays > 0) ? max(0, min($totalDays, $start->diffInDays(now()))) : 0;
        $progress = $totalDays > 0 ? (int) round(($elapsedDays / $totalDays) * 100) : 0;

        if ($internship->status === 'selesai') {
            $progress = 100;
        }

        $attendanceTotal = $attendances->count();
        $attendanceValid = $attendances->whereIn('status', ['hadir', 'terlambat'])->count();
        $attendancePercent = $attendanceTotal > 0
            ? (int) round(($attendanceValid / $attendanceTotal) * 100)
            : 0;

        $latestReport = $reports->first();
        $latestLogbook = $logbooks->first();
        $latestAttendance = $attendances->first();
        $latestAssignment = $assignments->first();
        $latestAssessment = $assessments->first();

        $latestActivityTitle = collect([
            filled($latestReport?->judul) ? 'Laporan: '.$latestReport->judul.' ('.str_replace('_', ' ', (string) ($latestReport->status ?? '-')).')' : null,
            filled($latestLogbook?->kegiatan) ? 'Logbook: '.$latestLogbook->kegiatan : null,
            filled($latestAssignment?->judul) ? 'Tugas: '.$latestAssignment->judul.' ('.str_replace('_', ' ', (string) ($latestAssignment->status ?? '-')).')' : null,
            $latestAttendance ? 'Absensi: '.str_replace('_', ' ', (string) $latestAttendance->status).' '.optional($latestAttendance->tanggal)->format('d M Y') : null,
        ])->filter()->first() ?? 'Belum ada aktivitas tercatat';

        $updatedAt = collect([
            $latestReport?->updated_at,
            $latestReport?->created_at,
            $latestLogbook?->updated_at,
            $latestLogbook?->created_at,
            $latestAttendance?->updated_at,
            $latestAttendance?->created_at,
            $latestAssignment?->updated_at,
            $latestAssignment?->created_at,
            $internship->updated_at,
        ])->filter()->sortDesc()->first();

        $status = 'aktif';
        if ($internship->status === 'selesai' || ($end && now()->greaterThanOrEqualTo($end))) {
            $status = 'selesai';
        } elseif (($peserta?->status ?? 'aktif') !== 'aktif' || $internship->status === 'pending') {
            $status = 'review';
        } elseif (
            ($attendancePercent > 0 && $attendancePercent < 75)
            || in_array($latestReport?->status, ['review', 'revisi', 'pending', 'draft'], true)
            || in_array($latestLogbook?->status, ['pending', 'menunggu', 'revisi', 'ditolak'], true)
            || ($latestAssessment && ! in_array($latestAssessment->status, ['final', 'selesai', 'disetujui'], true))
        ) {
            $status = 'perlu perhatian';
        }

        return [
            'id' => $internship->id,
            'nama' => $peserta?->user?->name ?? '-',
            'nim' => $peserta?->nim ?? '-',
            'prodi' => $peserta?->jurusan ?? '-',
            'kampus' => $peserta?->perguruanTinggi?->nama_pt ?? '-',
            'periode' => $peserta?->program_magang ?? '-',
            'divisi' => $internship->divisi ?: '-',
            'penempatan' => $internship->divisi ?: ($internship->unit_kerja ?: ($internship->posisi ?: '-')),
            'pa' => $peserta?->pembimbing_akademik ?? '-',
            'status' => $status,
            'progress' => $progress,
            'hadir' => $attendancePercent,
            'aktivitas' => $latestActivityTitle,
            'dokumen' => $peserta?->user?->documents?->count() ?? 0,
            'histori' => $latestReport?->catatan_mentor
                ?: $latestLogbook?->deskripsi
                ?: $latestAssignment?->catatan
                ?: $latestActivityTitle,
            'foto' => $peserta?->user?->foto ? asset('storage/'.$peserta->user->foto) : null,
            'instansi' => $internship->instansi ?: 'LLDIKTI Wilayah V Yogyakarta',
            'lokasi' => $internship->lokasi ?: 'LLDIKTI Wilayah V Yogyakarta',
            'sort_at' => optional($updatedAt)->toDateTimeString(),
            'assessment' => $latestAssessment?->nilai_akhir ? (int) round((float) $latestAssessment->nilai_akhir) : null,
            'assessment_status' => $latestAssessment?->status ?? 'draft',
        ];
    }

    private function assessmentAverageLabel(Collection $assessments): string
    {
        $validAssessments = $assessments->filter(fn (Assessment $assessment) => filled($assessment->nilai_akhir));

        if ($validAssessments->isEmpty()) {
            return '0%';
        }

        return (int) round($validAssessments->avg('nilai_akhir')).'%';
    }

    private function documentStatus(?string $status): array
    {
        return match (Str::lower((string) $status)) {
            'disetujui', 'approved', 'aktif' => ['label' => 'Disetujui', 'class' => 'bg-success'],
            'ditolak', 'rejected', 'nonaktif' => ['label' => 'Ditolak', 'class' => 'bg-danger'],
            'revisi' => ['label' => 'Revisi', 'class' => 'bg-warning text-dark'],
            default => ['label' => 'Menunggu', 'class' => 'bg-info text-dark'],
        };
    }

    private function monitoringProgress(Internship $internship, $peserta): int
    {
        $start = $internship->tanggal_mulai ?: $peserta?->tanggal_mulai_magang;
        $end = $internship->tanggal_selesai ?: $peserta?->tanggal_selesai_magang;

        if ($start && ! $start instanceof Carbon) {
            $start = Carbon::parse($start);
        }

        if ($end && ! $end instanceof Carbon) {
            $end = Carbon::parse($end);
        }

        $totalDays = ($start && $end) ? max($start->diffInDays($end), 1) : 0;
        $elapsedDays = ($start && $totalDays > 0) ? max(0, min($totalDays, $start->diffInDays(now()))) : 0;

        return $totalDays > 0 ? (int) round(($elapsedDays / $totalDays) * 100) : 0;
    }

    private function monitoringPeriodLabel(Carbon $date, ?string $fallbackPeriod = null): string
    {
        $diffDays = $date->diffInDays(now(), false);

        if ($diffDays === 0) {
            return 'Hari Ini';
        }

        if (abs($diffDays) <= 7) {
            return 'Minggu Ini';
        }

        return $fallbackPeriod ?: 'Bulan Ini';
    }

    private function mentorLogbookStatusLabel(string $status): string
    {
        return match (Str::lower($status)) {
            'direview', 'approved', 'disetujui' => 'direview',
            'perlu revisi', 'revisi' => 'perlu revisi',
            'ditolak', 'rejected' => 'ditolak',
            default => 'menunggu review',
        };
    }

    private function mentorAssignmentStatusLabel(string $status): string
    {
        return match (Str::lower($status)) {
            'selesai', 'done', 'completed' => 'selesai',
            'terlambat', 'late' => 'terlambat',
            'aktif', 'review' => 'aktif',
            default => 'belum selesai',
        };
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
            'mentorDashboard' => [
                'user' => null,
                'mentor' => null,
                'dashboardStats' => [],
                'recentActivities' => new Collection(),
                'progressRows' => new Collection(),
                'documentRows' => new Collection(),
                'summary' => [
                    'total' => 0,
                    'aktif' => 0,
                    'review' => 0,
                    'selesai' => 0,
                    'perlu_perhatian' => 0,
                    'rata_progress' => 0,
                ],
            ],
        ];
    }

    private function emptyMonitoringContext(): array
    {
        return [
            'monitoringRows' => new Collection(),
            'attendanceRows' => new Collection(),
            'attendanceSummary' => [
                'total' => 0,
                'present' => 0,
                'late' => 0,
                'permit' => 0,
                'sick' => 0,
                'absent' => 0,
                'percentage' => 0,
            ],
            'attendancePlacementOptions' => new Collection(),
            'statusMagangData' => new Collection(),
            'logbookRows' => new Collection(),
            'assignmentRows' => new Collection(),
        ];
    }
}
