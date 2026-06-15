<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('access_roles', function (Blueprint $table) {
            $table->id();
            $table->string('role_key')->unique();
            $table->string('name');
            $table->string('type', 20);
            $table->string('status', 20)->default('aktif');
            $table->json('permissions')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        $now = now();

        DB::table('access_roles')->insert([
            [
                'role_key' => 'super_admin',
                'name' => 'Super Admin',
                'type' => 'Internal',
                'status' => 'aktif',
                'permissions' => json_encode([
                    'Dashboard',
                    'Verifikasi > Verifikasi Akun',
                    'Verifikasi > Riwayat Verifikasi',
                    'Manajemen Pengguna > Peserta Magang',
                    'Manajemen Pengguna > Mentor',
                    'Manajemen Pengguna > Pembimbing Akademik',
                    'Manajemen Perguruan Tinggi > Data Perguruan Tinggi',
                    'Manajemen Perguruan Tinggi > Data Kerja Sama',
                    'Manajemen Magang > Dokumen Peserta',
                    'Manajemen Magang > Absensi',
                    'Manajemen Magang > Kegiatan Magang',
                    'Manajemen Magang > Laporan Berkala',
                    'Manajemen Magang > Laporan Akhir',
                    'Manajemen Magang > Periode Magang',
                    'Manajemen Magang > Penempatan',
                    'Manajemen Magang > Penilaian',
                    'Monitoring > Rekap Absensi',
                    'Monitoring > Rekap Kegiatan',
                    'Monitoring > Statistik Pengguna',
                    'Monitoring > Statistik Perguruan Tinggi',
                    'Komunikasi > Pesan',
                    'Komunikasi > Pengumuman',
                    'Komunikasi > Notifikasi',
                    'Pengaturan Akun > Profil Akun',
                    'Pengaturan Akun > Ubah Password',
                    'Pengaturan Akun > Hak Akses',
                ]),
                'sort_order' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'role_key' => 'mentor',
                'name' => 'Mentor',
                'type' => 'Internal',
                'status' => 'aktif',
                'permissions' => json_encode([
                    'Dashboard',
                    'Daftar Peserta Magang',
                    'Monitoring > Absensi',
                    'Monitoring > Penugasan',
                    'Monitoring > Logbook Harian',
                    'Monitoring > Kerjasama',
                    'Monitoring > Status Magang',
                    'Review > Laporan',
                    'Review > Daftar Laporan',
                    'Review > Riwayat Review',
                    'Penilaian > Input Nilai',
                    'Penilaian > Rekap Nilai',
                    'Komunikasi > Pesan',
                    'Komunikasi > Pengumuman',
                    'Komunikasi > Notifikasi',
                    'Pengaturan Akun > Profil',
                    'Pengaturan Akun > Ubah Password',
                ]),
                'sort_order' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'role_key' => 'peserta',
                'name' => 'Peserta Magang',
                'type' => 'Eksternal',
                'status' => 'aktif',
                'permissions' => json_encode([
                    'Dashboard',
                    'Data Magang > Profil Peserta',
                    'Data Magang > Penempatan',
                    'Data Magang > Status Verifikasi',
                    'Aktivitas Magang > Absensi',
                    'Aktivitas Magang > Logbook',
                    'Aktivitas Magang > Penugasan',
                    'Aktivitas Magang > Riwayat Kegiatan',
                    'Dokumen > Dokumen Kerjasama',
                    'Dokumen > Dokumen Pendukung',
                    'Dokumen > Status Dokumen',
                    'Laporan > Input Laporan',
                    'Laporan > Riwayat Laporan',
                    'Penilaian > Rekap Nilai',
                    'Penilaian > Sertifikat',
                    'Komunikasi > Pesan',
                    'Komunikasi > Pengumuman',
                    'Komunikasi > Notifikasi',
                    'Pengaturan Akun > Profil Akun',
                    'Pengaturan Akun > Ubah Password',
                ]),
                'sort_order' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'role_key' => 'pembimbing',
                'name' => 'Pembimbing Akademik',
                'type' => 'Eksternal',
                'status' => 'aktif',
                'permissions' => json_encode([
                    'Dashboard',
                    'Mahasiswa Bimbingan',
                    'Monitoring > Kegiatan Mahasiswa',
                    'Monitoring > Absensi',
                    'Monitoring > Progres Magang',
                    'Monitoring > Logbook Harian',
                    'Review > Review Laporan',
                    'Review > Daftar Laporan',
                    'Review > Riwayat Review',
                    'Penilaian > Input Nilai',
                    'Penilaian > Rekap Nilai',
                    'Komunikasi > Pesan',
                    'Komunikasi > Pengumuman',
                    'Komunikasi > Notifikasi',
                    'Pengaturan Akun > Profil',
                    'Pengaturan Akun > Ubah Password',
                ]),
                'sort_order' => 4,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('access_roles');
    }
};
