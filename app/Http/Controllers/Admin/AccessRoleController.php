<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccessRole;
use App\Models\User;
use App\Services\Admin\AdminDataService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class AccessRoleController extends Controller
{
    private function permissionCatalog(): array
    {
        return [
            'Super Admin' => [
                ['menu' => 'Dashboard', 'items' => []],
                ['menu' => 'Verifikasi', 'items' => ['Verifikasi Akun', 'Riwayat Verifikasi']],
                ['menu' => 'Manajemen Pengguna', 'items' => ['Peserta Magang', 'Mentor', 'Pembimbing Akademik']],
                ['menu' => 'Manajemen Perguruan Tinggi', 'items' => ['Data Perguruan Tinggi', 'Data Kerja Sama']],
                ['menu' => 'Manajemen Magang', 'items' => ['Dokumen Peserta', 'Absensi', 'Kegiatan Magang', 'Laporan Berkala', 'Laporan Akhir', 'Periode Magang', 'Penempatan', 'Penilaian']],
                ['menu' => 'Monitoring', 'items' => ['Rekap Absensi', 'Rekap Kegiatan', 'Statistik Pengguna', 'Statistik Perguruan Tinggi']],
                ['menu' => 'Komunikasi', 'items' => ['Pesan', 'Pengumuman', 'Notifikasi']],
                ['menu' => 'Pengaturan Akun', 'items' => ['Profil Akun', 'Ubah Password', 'Hak Akses']],
            ],
            'Mentor' => [
                ['menu' => 'Dashboard', 'items' => []],
                ['menu' => 'Daftar Peserta Magang', 'items' => []],
                ['menu' => 'Monitoring', 'items' => ['Absensi', 'Penugasan', 'Logbook Harian', 'Kerjasama', 'Status Magang']],
                ['menu' => 'Review', 'items' => ['Laporan', 'Daftar Laporan', 'Riwayat Review']],
                ['menu' => 'Penilaian', 'items' => ['Input Nilai', 'Rekap Nilai']],
                ['menu' => 'Komunikasi', 'items' => ['Pesan', 'Pengumuman', 'Notifikasi']],
                ['menu' => 'Pengaturan Akun', 'items' => ['Profil', 'Ubah Password']],
            ],
            'Peserta Magang' => [
                ['menu' => 'Dashboard', 'items' => []],
                ['menu' => 'Data Magang', 'items' => ['Profil Peserta', 'Penempatan', 'Status Verifikasi']],
                ['menu' => 'Aktivitas Magang', 'items' => ['Absensi', 'Logbook', 'Penugasan', 'Riwayat Kegiatan']],
                ['menu' => 'Dokumen', 'items' => ['Dokumen Kerjasama', 'Dokumen Pendukung', 'Status Dokumen']],
                ['menu' => 'Laporan', 'items' => ['Input Laporan', 'Riwayat Laporan']],
                ['menu' => 'Penilaian', 'items' => ['Rekap Nilai', 'Sertifikat']],
                ['menu' => 'Komunikasi', 'items' => ['Pesan', 'Pengumuman', 'Notifikasi']],
                ['menu' => 'Pengaturan Akun', 'items' => ['Profil Akun', 'Ubah Password']],
            ],
            'Pembimbing Akademik' => [
                ['menu' => 'Dashboard', 'items' => []],
                ['menu' => 'Mahasiswa Bimbingan', 'items' => []],
                ['menu' => 'Monitoring', 'items' => ['Kegiatan Mahasiswa', 'Absensi', 'Progres Magang', 'Logbook Harian']],
                ['menu' => 'Review', 'items' => ['Review Laporan', 'Daftar Laporan', 'Riwayat Review']],
                ['menu' => 'Penilaian', 'items' => ['Input Nilai', 'Rekap Nilai']],
                ['menu' => 'Komunikasi', 'items' => ['Pesan', 'Pengumuman', 'Notifikasi']],
                ['menu' => 'Pengaturan Akun', 'items' => ['Profil', 'Ubah Password']],
            ],
        ];
    }

    private function flattenPermissions(array $tree): array
    {
        return collect($tree)
            ->flatMap(function (array $group) {
                if (! empty($group['items'])) {
                    return collect($group['items'])->map(fn ($item) => $group['menu'].' > '.$item);
                }

                return [$group['menu']];
            })
            ->values()
            ->all();
    }

    private function userCountFor(string $roleKey): int
    {
        return User::query()->where('role', $roleKey)->count();
    }

    private function formatRole(AccessRole $role): array
    {
        $permissionTree = $this->permissionCatalog()[$role->name] ?? [];
        $permissionsList = $this->flattenPermissions($permissionTree);

        return [
            'id' => $role->id,
            'role_key' => $role->role_key,
            'name' => $role->name,
            'type' => $role->type,
            'status' => $role->status,
            'users' => $this->userCountFor($role->role_key),
            'updated' => $role->updated_at ? Carbon::parse($role->updated_at)->format('d M Y') : '-',
            'menus' => count($permissionTree),
            'permissions' => count($permissionsList),
            'permissionTree' => $permissionTree,
            'permissionsList' => $role->permissions ?? $permissionsList,
        ];
    }

    private function payload(): array
    {
        $roles = AccessRole::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (AccessRole $role) => $this->formatRole($role))
            ->values()
            ->all();

        $context = app(AdminDataService::class)->context();

        return [
            'accessRoles' => $roles,
            'accessPermissionCatalog' => $this->permissionCatalog(),
            'adminMentors' => $context['adminMentors'] ?? [],
            'adminAdvisors' => $context['adminAdvisors'] ?? [],
        ];
    }

    public function index()
    {
        return view('admin.pengaturan.hak_akses', $this->payload());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'role_key' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:access_roles,role_key'],
            'name' => ['required', 'string', 'max:120'],
            'type' => ['required', Rule::in(['Internal', 'Eksternal'])],
            'status' => ['required', Rule::in(['aktif', 'nonaktif', 'review'])],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'max:255'],
        ]);

        $role = AccessRole::create([
            'role_key' => $validated['role_key'],
            'name' => $validated['name'],
            'type' => $validated['type'],
            'status' => $validated['status'],
            'permissions' => $validated['permissions'] ?? [],
            'sort_order' => AccessRole::query()->max('sort_order') + 1,
        ]);

        return response()->json([
            'message' => 'Peran berhasil ditambahkan.',
            'role' => $this->formatRole($role->fresh()),
        ]);
    }

    public function update(Request $request, AccessRole $accessRole): JsonResponse
    {
        $validated = $request->validate([
            'type' => ['sometimes', Rule::in(['Internal', 'Eksternal'])],
            'status' => ['sometimes', Rule::in(['aktif', 'nonaktif', 'review'])],
            'permissions' => ['sometimes', 'array'],
            'permissions.*' => ['string', 'max:255'],
        ]);

        $accessRole->fill([
            'type' => $validated['type'] ?? $accessRole->type,
            'status' => $validated['status'] ?? $accessRole->status,
            'permissions' => $validated['permissions'] ?? $accessRole->permissions ?? [],
        ])->save();

        return response()->json([
            'message' => 'Hak akses berhasil diperbarui.',
            'role' => $this->formatRole($accessRole->fresh()),
        ]);
    }

    public function updateStatus(AccessRole $accessRole): JsonResponse
    {
        $accessRole->status = $accessRole->status === 'aktif' ? 'nonaktif' : 'aktif';
        $accessRole->save();

        return response()->json([
            'message' => 'Status peran berhasil diperbarui.',
            'role' => $this->formatRole($accessRole->fresh()),
        ]);
    }

    public function destroy(AccessRole $accessRole): JsonResponse
    {
        $accessRole->delete();

        return response()->json([
            'message' => 'Peran berhasil dihapus.',
        ]);
    }
}
