<?php

namespace App\Services\Peserta;

use App\Models\Announcement;
use App\Models\Peserta;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Collection as SupportCollection;

class PesertaDataService
{
    public function forUser(?User $user): array
    {
        if (! $user) {
            return $this->emptyContext();
        }

        $resolvedUser = $this->resolveUserContext($user);

        try {
            $resolvedUser->loadMissing([
                'peserta.perguruanTinggi',
                'peserta.internship.mentor.user',
                'peserta.internship.pembimbing.user',
                'peserta.internships.mentor.user',
                'peserta.internships.pembimbing.user',
                'documents',
                'activities',
                'notifications',
            ]);
        } catch (\Throwable $exception) {
            report($exception);

            return $this->emptyContext();
        }

        try {
        $peserta = $resolvedUser->peserta;
        $internship = $peserta?->internship;
        $internships = $peserta?->internships?->sortByDesc(function ($item) {
            return optional($item->tanggal_mulai ?? $item->created_at)->getTimestamp() ?? 0;
        })->values() ?? new Collection();
        $mentor = $internship?->mentor;
        $pembimbing = $internship?->pembimbing;

        $reports = $peserta
            ? $peserta->reports()->with('reviewer')->latest()->get()
            : new Collection();

        $logbooks = $peserta
            ? $peserta->logbooks()->latest('tanggal')->get()
            : new Collection();

        $attendances = $this->whenTableExists('attendances', fn () => $peserta
            ? $peserta->attendances()->latest('tanggal')->get()
            : new Collection());

        $assignments = $this->whenTableExists('assignments', fn () => $peserta
            ? $peserta->assignments()->with('mentor.user')->latest('deadline')->latest()->get()
            : new Collection());

        $assessments = $this->whenTableExists('assessments', fn () => $peserta
            ? $peserta->assessments()->with(['mentor.user', 'pembimbing.user'])->latest()->get()
            : new Collection());

        $certificates = $this->whenTableExists('certificates', fn () => $peserta
            ? $peserta->certificates()->latest('tanggal_terbit')->latest()->get()
            : new Collection());

        $conversations = $this->whenTableExists('conversations', fn () => $peserta
            ? $peserta->conversations()
                ->with(['mentor.user', 'pembimbing.user', 'admin', 'messages.sender'])
                ->latest('last_message_at')
                ->latest()
                ->get()
            : new Collection());

        $announcements = $this->whenTableExists('announcements', fn () => Announcement::query()
            ->with('author')
            ->latest('tanggal')
            ->latest()
            ->get());

        $securityActivities = $this->whenTableExists('security_activities', fn () => $resolvedUser
            ->securityActivities()
            ->latest()
            ->get());

        $notificationPreference = $this->whenTableExists('notification_preferences', fn () => $resolvedUser
            ->notificationPreference()
            ->first());

        $documents = $resolvedUser->documents->sortByDesc('created_at')->values();
        $activities = $resolvedUser->activities->sortByDesc('created_at')->values();
        $notifications = $resolvedUser->notifications->sortByDesc('created_at')->values();
        $placementPosition = $internship?->posisi ?? $internship?->divisi ?? $internship?->unit_kerja ?? '-';
        $placementInstitution = $internship?->instansi ?? $peserta?->perguruanTinggi?->nama_pt ?? '-';
        $activityTimeline = $this->activityTimeline($reports, $logbooks, $assignments, $activities);

        return [
            'user' => $resolvedUser,
            'peserta' => $peserta,
            'perguruanTinggi' => $peserta?->perguruanTinggi,
            'internship' => $internship,
            'internships' => $internships,
            'placementPosition' => $placementPosition,
            'placementUnit' => $internship?->divisi ?? $internship?->unit_kerja ?? $placementPosition,
            'placementInstitution' => $placementInstitution,
            'mentor' => $mentor,
            'mentorUser' => $mentor?->user,
            'pembimbing' => $pembimbing,
            'pembimbingUser' => $pembimbing?->user,
            'documents' => $documents,
            'reports' => $reports,
            'logbooks' => $logbooks,
            'attendances' => $attendances,
            'assignments' => $assignments,
            'assessments' => $assessments,
            'certificates' => $certificates,
            'conversations' => $conversations,
            'announcements' => $announcements,
            'securityActivities' => $securityActivities,
            'notificationPreference' => $notificationPreference,
            'activities' => $activities,
            'notifications' => $notifications,
            'activityTimeline' => $activityTimeline,
            'stats' => $this->stats(
                $resolvedUser,
                $peserta,
                $internship,
                $documents,
                $reports,
                $logbooks,
                $attendances,
                $assignments,
                $assessments,
                $certificates,
                $conversations,
                $announcements,
                $securityActivities,
                $activities,
                $notifications,
            ),
        ];
        } catch (\Throwable $exception) {
            report($exception);

            return $this->emptyContext();
        }
    }

    private function resolveUserContext(User $user): User
    {
        if ($user->relationLoaded('peserta') && $user->peserta) {
            return $user;
        }

        $candidate = Peserta::query()
            ->with([
                'user',
                'perguruanTinggi',
                'internship.mentor.user',
                'internship.pembimbing.user',
            ])
            ->whereHas('user', function ($query) use ($user) {
                $query->where('email', $user->email)
                    ->orWhere('username', $user->username)
                    ->orWhere('name', $user->name);
            })
            ->first();

        if (! $candidate && filled($user->username)) {
            $candidate = Peserta::query()
                ->with([
                    'user',
                    'perguruanTinggi',
                    'internship.mentor.user',
                    'internship.pembimbing.user',
                ])
                ->where('nim', $user->username)
                ->first();
        }

        if (! $candidate && filled($user->name)) {
            $matchedByName = Peserta::query()
                ->with([
                    'user',
                    'perguruanTinggi',
                    'internship.mentor.user',
                    'internship.pembimbing.user',
                ])
                ->whereHas('user', fn ($query) => $query->where('name', $user->name))
                ->get();

            if ($matchedByName->count() === 1) {
                $candidate = $matchedByName->first();
            }
        }

        if ($candidate?->user) {
            return $candidate->user;
        }

        if (filled($user->username) && preg_match('/^\d+$/', (string) $user->username)) {
            $candidate = Peserta::query()
                ->with([
                    'user',
                    'perguruanTinggi',
                    'internship.mentor.user',
                    'internship.pembimbing.user',
                ])
                ->where('nim', $user->username)
                ->first();
        }

        return $candidate?->user ?? $user;
    }

    private function stats(
        User $user,
        $peserta,
        $internship,
        Collection $documents,
        Collection $reports,
        Collection $logbooks,
        Collection $attendances,
        Collection $assignments,
        Collection $assessments,
        Collection $certificates,
        Collection $conversations,
        Collection $announcements,
        Collection $securityActivities,
        Collection $activities,
        Collection $notifications,
    ): array
    {
        $start = $internship?->tanggal_mulai;
        $end = $internship?->tanggal_selesai;
        $daysLeft = $end ? max(now()->startOfDay()->diffInDays($end->copy()->startOfDay(), false), 0) : null;

        $totalDays = $start && $end ? max($start->diffInDays($end), 1) : null;
        $elapsedDays = $start ? max($start->diffInDays(now()), 0) : null;
        $internshipProgress = $totalDays ? min(100, (int) round(($elapsedDays / $totalDays) * 100)) : 0;

        $reportApproved = $reports->where('status', 'approved')->count() + $reports->where('status', 'disetujui')->count();
        $logbookApproved = $logbooks->where('status', 'approved')->count() + $logbooks->where('status', 'disetujui')->count();
        $attendanceValid = $attendances->whereIn('status', ['hadir', 'terlambat']);
        $attendancePercent = $attendances->count() > 0
            ? (int) round(($attendanceValid->count() / $attendances->count()) * 100)
            : 0;
        $assignmentProgress = $assignments->count() > 0 ? (int) round($assignments->avg('progress')) : 0;
        $finalAssessment = $assessments->whereIn('status', ['final', 'selesai', 'disetujui'])->avg('nilai_akhir')
            ?? $assessments->avg('nilai_akhir');
        $unreadMessages = $conversations
            ->flatMap(fn ($conversation) => $conversation->messages)
            ->where('sender_id', '!=', $user->id)
            ->whereNull('dibaca_pada')
            ->count();

        return [
            'document_total' => $documents->count(),
            'document_waiting' => $documents->whereIn('status', ['menunggu', 'pending'])->count(),
            'document_approved' => $documents->whereIn('status', ['disetujui', 'approved'])->count(),
            'document_revision' => $documents->whereIn('status', ['revisi', 'rejected', 'ditolak'])->count(),
            'report_total' => $reports->count(),
            'report_approved' => $reportApproved,
            'report_waiting' => $reports->whereIn('status', ['pending', 'menunggu'])->count(),
            'report_revision' => $reports->whereIn('status', ['revisi', 'rejected', 'ditolak'])->count(),
            'logbook_total' => $logbooks->count(),
            'logbook_approved' => $logbookApproved,
            'logbook_waiting' => $logbooks->whereIn('status', ['pending', 'menunggu'])->count(),
            'attendance_total' => $attendances->count(),
            'attendance_present' => $attendances->where('status', 'hadir')->count(),
            'attendance_late' => $attendances->where('status', 'terlambat')->count(),
            'attendance_permission' => $attendances->where('status', 'izin')->count(),
            'attendance_absent' => $attendances->whereIn('status', ['alpa', 'tidak_hadir'])->count(),
            'attendance_percent' => $attendancePercent,
            'assignment_total' => $assignments->count(),
            'assignment_done' => $assignments->whereIn('status', ['selesai', 'disetujui'])->count(),
            'assignment_late' => $assignments->where('status', 'terlambat')->count(),
            'assignment_progress' => $assignmentProgress,
            'assessment_total' => $assessments->count(),
            'assessment_final' => $finalAssessment ? round($finalAssessment, 2) : null,
            'certificate_total' => $certificates->count(),
            'certificate_issued' => $certificates->whereIn('status', ['terbit', 'tersedia', 'published'])->count(),
            'conversation_total' => $conversations->count(),
            'message_unread' => $unreadMessages,
            'announcement_total' => $announcements->count(),
            'announcement_active' => $announcements->where('status', 'aktif')->count(),
            'security_activity_total' => $securityActivities->count(),
            'activity_total' => $activities->count(),
            'notification_total' => $notifications->count(),
            'notification_unread' => $notifications->where('dibaca', false)->count(),
            'internship_progress' => $internshipProgress,
            'days_left' => $daysLeft,
            'account_status' => $user->account_status ?? $peserta?->status ?? 'aktif',
            'last_login_at' => $user->updated_at instanceof Carbon ? $user->updated_at : null,
        ];
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
            'peserta' => null,
            'perguruanTinggi' => null,
            'internship' => null,
            'internships' => new Collection(),
            'mentor' => null,
            'mentorUser' => null,
            'pembimbing' => null,
            'pembimbingUser' => null,
            'placementUnit' => '-',
            'placementPosition' => '-',
            'placementInstitution' => '-',
            'documents' => new Collection(),
            'reports' => new Collection(),
            'logbooks' => new Collection(),
            'attendances' => new Collection(),
            'assignments' => new Collection(),
            'assessments' => new Collection(),
            'certificates' => new Collection(),
            'conversations' => new Collection(),
            'announcements' => new Collection(),
            'securityActivities' => new Collection(),
            'notificationPreference' => null,
            'activities' => new Collection(),
            'notifications' => new Collection(),
            'activityTimeline' => new Collection(),
            'stats' => [],
        ];
    }

    private function activityTimeline(Collection $reports, Collection $logbooks, Collection $assignments, Collection $activities): SupportCollection
    {
        $timeline = collect();

        $timeline = $timeline->concat($reports->map(function ($report) {
            $status = $this->timelineStatus($report->status, 'report');

            return [
                'tanggal' => optional($report->created_at)->translatedFormat('d M Y') ?? '-',
                'jenis' => 'Laporan',
                'judul' => $report->judul ?: 'Laporan Magang',
                'kategori' => $report->jenis ?: 'Administrasi',
                'status_label' => $status['label'],
                'status_class' => $status['class'],
                'durasi' => $report->periode ?: '-',
                'sumber' => 'Laporan',
                'created_at' => $report->created_at,
            ];
        }));

        $timeline = $timeline->concat($logbooks->map(function ($logbook) {
            $status = $this->timelineStatus($logbook->status, 'logbook');

            return [
                'tanggal' => optional($logbook->tanggal)->translatedFormat('d M Y') ?? '-',
                'jenis' => 'Logbook',
                'judul' => $logbook->kegiatan ?: 'Logbook Harian',
                'kategori' => 'Harian',
                'status_label' => $status['label'],
                'status_class' => $status['class'],
                'durasi' => '-',
                'sumber' => 'Logbook',
                'created_at' => $logbook->created_at ?? $logbook->tanggal,
            ];
        }));

        $timeline = $timeline->concat($assignments->map(function ($assignment) {
            $status = $this->timelineStatus($assignment->status, 'assignment');

            return [
                'tanggal' => optional($assignment->deadline ?? $assignment->created_at)->translatedFormat('d M Y') ?? '-',
                'jenis' => 'Penugasan',
                'judul' => $assignment->judul ?: 'Penugasan',
                'kategori' => $assignment->prioritas ?: 'Tugas',
                'status_label' => $status['label'],
                'status_class' => $status['class'],
                'durasi' => (int) ($assignment->progress ?? 0) . '%',
                'sumber' => 'Penugasan',
                'created_at' => $assignment->created_at ?? $assignment->deadline,
            ];
        }));

        $timeline = $timeline->concat($activities->map(function ($activity) {
            return [
                'tanggal' => optional($activity->created_at)->translatedFormat('d M Y') ?? '-',
                'jenis' => 'Aktivitas',
                'judul' => $activity->aktivitas ?: 'Aktivitas Sistem',
                'kategori' => 'Administrasi',
                'status_label' => 'Selesai',
                'status_class' => 'success',
                'durasi' => '-',
                'sumber' => 'Aktivitas Magang',
                'created_at' => $activity->created_at,
            ];
        }));

        return $timeline
            ->sortByDesc(fn (array $item) => optional($item['created_at'])->getTimestamp() ?? 0)
            ->values();
    }

    private function timelineStatus(?string $status, string $type): array
    {
        $statusKey = strtolower((string) $status);

        return match ($type) {
            'report' => match ($statusKey) {
                'approved', 'disetujui' => ['label' => 'Selesai', 'class' => 'success'],
                'revisi', 'rejected', 'ditolak' => ['label' => 'Terlambat', 'class' => 'danger'],
                'pending', 'menunggu' => ['label' => 'Berjalan', 'class' => 'info text-dark'],
                default => ['label' => $statusKey !== '' ? ucfirst($statusKey) : 'Berjalan', 'class' => 'warning text-dark'],
            },
            'logbook' => match ($statusKey) {
                'approved', 'disetujui' => ['label' => 'Selesai', 'class' => 'success'],
                'revisi', 'rejected', 'ditolak' => ['label' => 'Terlambat', 'class' => 'danger'],
                'pending', 'menunggu' => ['label' => 'Berjalan', 'class' => 'info text-dark'],
                default => ['label' => $statusKey !== '' ? ucfirst($statusKey) : 'Berjalan', 'class' => 'warning text-dark'],
            },
            'assignment' => match ($statusKey) {
                'selesai', 'done', 'completed', 'disetujui' => ['label' => 'Selesai', 'class' => 'success'],
                'terlambat', 'late' => ['label' => 'Terlambat', 'class' => 'danger'],
                'review', 'finalisasi' => ['label' => 'Berjalan', 'class' => 'info text-dark'],
                default => ['label' => 'Berjalan', 'class' => 'warning text-dark'],
            },
            default => ['label' => 'Berjalan', 'class' => 'info text-dark'],
        };
    }
}
