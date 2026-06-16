<?php

namespace App\Services\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AdminDataService
{
    public function context(): array
    {
        $users = $this->tableExists('users')
            ? DB::table('users')->orderByDesc('created_at')->get()
            : collect();

        $pesertas = $this->tableExists('pesertas')
            ? DB::table('pesertas')
                ->leftJoin('users', 'pesertas.user_id', '=', 'users.id')
                ->leftJoin('perguruan_tinggi', 'pesertas.perguruan_tinggi_id', '=', 'perguruan_tinggi.id')
                ->select(
                    'pesertas.*',
                    'users.name as user_name',
                    'users.username as username',
                    'users.email as user_email',
                    'users.account_status as user_account_status',
                    'users.created_at as user_created_at',
                    'perguruan_tinggi.nama_pt as nama_pt'
                )
                ->orderByDesc('pesertas.created_at')
                ->get()
            : collect();

        $mentors = $this->tableExists('mentors')
            ? DB::table('mentors')
                ->leftJoin('users', 'mentors.user_id', '=', 'users.id')
                ->select('mentors.*', 'users.name as user_name', 'users.email as user_email', 'users.username as username', 'users.account_status as user_account_status')
                ->orderByDesc('mentors.created_at')
                ->get()
            : collect();

        $pembimbings = $this->tableExists('pembimbings')
            ? DB::table('pembimbings')
                ->leftJoin('users', 'pembimbings.user_id', '=', 'users.id')
                ->select('pembimbings.*', 'users.name as user_name', 'users.email as user_email', 'users.username as username', 'users.account_status as user_account_status')
                ->orderByDesc('pembimbings.created_at')
                ->get()
            : collect();

        $campuses = $this->tableExists('perguruan_tinggi')
            ? DB::table('perguruan_tinggi')->orderByDesc('created_at')->get()
            : collect();

        $documents = $this->tableExists('documents')
            ? DB::table('documents')
                ->leftJoin('users', 'documents.user_id', '=', 'users.id')
                ->select('documents.*', 'users.name as user_name')
                ->orderByDesc('documents.created_at')
                ->get()
            : collect();

        $activities = $this->tableExists('activities')
            ? DB::table('activities')
                ->leftJoin('users', 'activities.user_id', '=', 'users.id')
                ->select('activities.*', 'users.name as user_name', 'users.role as user_role')
                ->orderByDesc('activities.created_at')
                ->limit(10)
                ->get()
            : collect();

        $verificationHistories = $this->tableExists('verification_histories')
            ? DB::table('verification_histories')
                ->leftJoin('users', 'verification_histories.user_id', '=', 'users.id')
                ->leftJoin('users as admins', 'verification_histories.admin_id', '=', 'admins.id')
                ->select(
                    'verification_histories.*',
                    'users.name as user_name',
                    'users.email as user_email',
                    'users.role as user_role',
                    'admins.name as admin_name'
                )
                ->orderByDesc('verification_histories.created_at')
                ->get()
            : collect();

        $verificationAccounts = $users
            ->whereIn('role', ['peserta', 'mentor', 'pembimbing'])
            ->sortByDesc('created_at')
            ->values();

        $adminVerificationHistories = $verificationHistories->map(fn ($item) => [
            'id' => $item->id,
            'nama' => $item->user_name ?? '-',
            'email' => $item->user_email ?? '-',
            'jenis' => $item->jenis ?? 'akun',
            'jenis_label' => ucfirst($item->jenis ?? 'akun'),
            'status' => $this->uiStatus($item->status ?? 'menunggu'),
            'tanggal' => $this->formatDate($item->created_at ?? null),
            'periode' => $this->formatDate($item->created_at ?? null),
            'admin' => $item->admin_name ?? '-',
            'keterangan' => $item->keterangan ?? '-',
            'verified_at' => $this->formatDateTime($item->verified_at ?? null),
            'sort_at' => $item->verified_at ?? $item->created_at ?? null,
            'role' => $item->user_role ?? '-',
            'role_label' => $this->roleLabel($item->user_role ?? '-'),
        ])->values();

        $documentTypes = collect([
            ['key' => 'surat_pengantar', 'label' => 'Surat Pengantar'],
            ['key' => 'cv', 'label' => 'CV'],
            ['key' => 'transkrip_nilai', 'label' => 'Transkrip Nilai'],
            ['key' => 'ktp', 'label' => 'KTP'],
            ['key' => 'foto', 'label' => 'Foto'],
        ]);

        return [
            'adminStats' => [
                'total_users' => $users->count(),
                'active_users' => $users->whereIn('account_status', ['disetujui', 'aktif', 'approved'])->count(),
                'waiting_users' => $users->whereIn('account_status', ['menunggu', 'pending'])->count(),
                'rejected_users' => $users->whereIn('account_status', ['ditolak', 'rejected'])->count(),
                'active_participants' => $pesertas->whereIn('status', ['aktif', 'active'])->count(),
                'total_participants' => $pesertas->count(),
                'total_campuses' => $campuses->count(),
                'total_documents' => $documents->count(),
                'total_reports' => $this->tableExists('reports') ? DB::table('reports')->count() : 0,
            ],

            'adminRecentActivities' => $activities->map(fn ($item) => [
                'id' => $item->id,
                'nama' => $item->user_name ?? '-',
                'role' => $item->user_role ?? '-',
                'aktivitas' => $item->aktivitas ?? '-',
                'waktu' => '-',
                'tanggal' => $this->formatDateTime($item->created_at ?? null),
            ])->values(),

            'adminRecentDocuments' => $documents->take(5)->map(fn ($item) => [
                'id' => $item->id,
                'nama' => $item->nama_dokumen ?? '-',
                'pemilik' => $item->user_name ?? '-',
                'status' => $this->uiStatus($item->status ?? 'menunggu'),
                'tanggal' => $this->formatDate($item->created_at ?? null),
                'waktu' => '-',
            ])->values(),

            'adminVerificationAccounts' => $verificationAccounts->map(fn ($user) => [
                'id' => $user->id,
                'nama' => $user->name ?? '-',
                'email' => $user->email ?? '-',
                'role' => $user->role ?? '-',
                'role_label' => $this->roleLabel($user->role ?? '-'),
                'instansi' => '-',
                'status' => $this->uiStatus($user->account_status ?? 'menunggu'),
                'tanggal' => $this->formatDate($user->created_at ?? null),
                'verified_at' => $this->formatDateTime($user->verified_at ?? null),
                'rejection_reason' => $user->rejection_reason ?? '-',
            ])->values(),

            'adminUsers' => $users->map(fn ($user) => [
                'id' => $user->id,
                'user_id' => $user->id,
                'nama' => $user->name ?? '-',
                'username' => $user->username ?? '-',
                'role' => $user->role ?? '-',
                'role_label' => $this->roleLabel($user->role ?? '-'),
                'email' => $user->email ?? '-',
                'instansi' => '-',
                'status' => $this->uiStatus($user->account_status ?? 'menunggu'),
                'tanggal' => $this->formatDate($user->created_at ?? null),
                'verified_at' => $this->formatDateTime($user->verified_at ?? null),
                'foto' => null,
                'phone' => $user->phone ?? '-',
                'address' => $user->address ?? '-',
                'nim' => '-',
                'nip' => '-',
                'nidn' => '-',
                'jabatan' => '-',
                'divisi' => '-',
                'kampus' => '-',
            ])->values(),

            'adminParticipants' => $pesertas->map(fn ($item) => [
                'id' => $item->id,
                'user_id' => $item->user_id,
                'nama' => $item->user_name ?? '-',
                'username' => $item->username ?? '-',
                'role' => 'peserta',
                'email' => $item->user_email ?? '-',
                'instansi' => $item->nama_pt ?? '-',
                'nim' => $item->nim ?? '-',
                'tempat_lahir' => $item->tempat_lahir ?? '-',
                'tanggal_lahir' => $this->formatDate($item->tanggal_lahir ?? null),
                'tanggal_lahir_raw' => $item->tanggal_lahir ?? '',
                'jenis_kelamin' => $item->jenis_kelamin ?? '-',
                'no_hp' => $item->no_hp ?? '-',
                'alamat' => $item->alamat ?? '-',
                'program_studi' => $item->jurusan ?? '-',
                'fakultas' => $item->fakultas ?? '-',
                'program_magang' => $item->program_magang ?? '-',
                'pembimbing_akademik' => $item->pembimbing_akademik ?? '-',
                'tanggal_mulai_magang' => $this->formatDate($item->tanggal_mulai_magang ?? null),
                'tanggal_mulai_magang_raw' => $item->tanggal_mulai_magang ?? '',
                'tanggal_selesai_magang' => $this->formatDate($item->tanggal_selesai_magang ?? null),
                'tanggal_selesai_magang_raw' => $item->tanggal_selesai_magang ?? '',
                'foto' => null,
                'status' => $this->uiStatus($item->user_account_status ?? $item->status ?? 'menunggu'),
                'tanggal' => $this->formatDate($item->created_at ?? null),
            ])->values(),

            'adminMentors' => $mentors->map(fn ($item) => [
                'id' => $item->id,
                'user_id' => $item->user_id,
                'nama' => $item->user_name ?? '-',
                'username' => $item->username ?? '-',
                'instansi' => $item->instansi ?? '-',
                'unit_kerja' => $item->divisi ?? '-',
                'email' => $item->user_email ?? '-',
                'nip' => $item->nip ?? '-',
                'jenis_kelamin' => $item->jenis_kelamin ?? '-',
                'no_hp' => $item->no_hp ?? '-',
                'alamat' => $item->alamat ?? '-',
                'perguruan_tinggi_raw' => $item->instansi ?? '',
                'jabatan' => $item->jabatan ?? '-',
                'divisi' => $item->divisi ?? '-',
                'foto' => null,
                'status' => $this->uiStatus($item->user_account_status ?? 'menunggu'),
                'tanggal' => $this->formatDate($item->created_at ?? null),
            ])->values(),

            'adminAdvisors' => $pembimbings->map(fn ($item) => [
                'id' => $item->id,
                'user_id' => $item->user_id,
                'nama' => $item->user_name ?? '-',
                'username' => $item->username ?? '-',
                'nidn' => $item->nidn_nip ?? '-',
                'kampus' => $item->perguruan_tinggi ?? $item->instansi ?? '-',
                'email' => $item->user_email ?? '-',
                'tempat_lahir' => $item->tempat_lahir ?? '-',
                'tanggal_lahir' => $this->formatDate($item->tanggal_lahir ?? null),
                'tanggal_lahir_raw' => $item->tanggal_lahir ?? '',
                'jenis_kelamin' => $item->jenis_kelamin ?? '-',
                'no_hp' => $item->no_hp ?? '-',
                'alamat' => $item->alamat ?? '-',
                'program_studi' => $item->program_studi ?? '-',
                'jabatan' => $item->jabatan ?? '-',
                'perguruan_tinggi_raw' => $item->perguruan_tinggi ?? '',
                'foto' => null,
                'status' => $this->uiStatus($item->user_account_status ?? 'menunggu'),
                'tanggal' => $this->formatDate($item->created_at ?? null),
            ])->values(),

            'adminCampuses' => $campuses->map(fn ($item) => [
                'id' => $item->id,
                'nama' => $item->nama_pt ?? '-',
                'pic' => $item->pic ?? '-',
                'pic_nip' => $item->pic_nip ?? '-',
                'fakultas' => $item->fakultas ?? '-',
                'program_studi' => $item->program_studi ?? '-',
                'jenis' => $item->jenis ?? '-',
                'provinsi' => '-',
                'alamat' => $item->alamat ?? '-',
                'email' => $item->email ?? '-',
                'status' => $this->uiStatus($item->status_kerja_sama ?? 'menunggu'),
                'mahasiswa_count' => $pesertas->where('perguruan_tinggi_id', $item->id)->count(),
                'program_studi_count' => $pesertas->where('perguruan_tinggi_id', $item->id)->pluck('jurusan')->filter()->unique()->count(),
                'instansi_mitra_count' => $pesertas->where('perguruan_tinggi_id', $item->id)->count(),
                'terakhir_aktif' => $this->formatDate($item->updated_at ?? null),
                'tanggal' => $this->formatDate($item->created_at ?? null),
            ])->values(),

            'adminPlacements' => collect(),
            'adminAttendances' => collect(),
            'adminAttendanceRecaps' => collect(),
            'adminMagangActivities' => collect(),
            'adminMagangReports' => collect(),
            'adminVerificationHistories' => $adminVerificationHistories,
            'adminVerificationHistoryStats' => [
                'total' => $adminVerificationHistories->count(),
                'disetujui' => $adminVerificationHistories->where('status', 'aktif')->count(),
                'ditolak' => $adminVerificationHistories->where('status', 'ditolak')->count(),
                'hari_ini' => 0,
            ],
            'adminDocumentTypes' => $documentTypes,
            'adminDocumentParticipants' => $pesertas->map(fn ($item) => [
                'id' => $item->id,
                'user_id' => $item->user_id,
                'nama' => $item->user_name ?? '-',
                'email' => $item->user_email ?? '-',
                'nim' => $item->nim ?? '-',
                'prodi' => $item->jurusan ?? '-',
                'instansi' => $item->nama_pt ?? '-',
                'periode' => $item->program_magang ?? '-',
                'status' => $this->uiStatus($item->user_account_status ?? $item->status ?? 'menunggu'),
                'uploaded_count' => 0,
                'required_count' => $documentTypes->count(),
                'completion' => 'Belum Upload',
                'completion_class' => 'danger',
                'documents' => [],
                'last_upload' => '-',
            ])->values(),
            'adminDocumentStats' => [
                'total_participants' => $pesertas->count(),
                'complete' => 0,
                'partial' => 0,
                'empty' => $pesertas->count(),
                'uploaded_documents' => $documents->count(),
            ],
            'adminMonitoringProgram' => collect(),
        ];
    }

    private function tableExists(string $table): bool
    {
        try {
            return Schema::hasTable($table);
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function uiStatus(?string $status): string
    {
        return match ($status) {
            'disetujui', 'aktif', 'approved', 'active' => 'aktif',
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
            default => Str::headline((string) $role),
        };
    }

    private function formatDate($date): string
    {
        if (! $date) {
            return '-';
        }

        try {
            return \Carbon\Carbon::parse($date)->translatedFormat('d F Y');
        } catch (\Throwable $e) {
            return '-';
        }
    }

    private function formatDateTime($date): string
    {
        if (! $date) {
            return '-';
        }

        try {
            return \Carbon\Carbon::parse($date)->translatedFormat('d F Y H:i');
        } catch (\Throwable $e) {
            return '-';
        }
    }
}