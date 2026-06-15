<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Attendance;
use App\Models\Document;
use App\Models\Internship;
use App\Models\Logbook;
use App\Models\Mentor;
use App\Models\Pembimbing;
use App\Models\PerguruanTinggi;
use App\Models\Peserta;
use App\Models\Report;
use App\Models\VerificationHistory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class AdminDemoSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Super Admin',
                'username' => 'superadmin',
                'role' => 'super_admin',
                'password' => Hash::make('12345678'),
                'account_status' => 'disetujui',
                'verified_at' => now(),
            ]
        );

        $campuses = collect([
            ['nama_pt' => 'Universitas Negeri Yogyakarta', 'email' => 'admin@uny.ac.id', 'pic' => 'Dr. Rina Wulandari', 'pic_nip' => '198001012005012001', 'fakultas' => 'Fakultas Teknik', 'program_studi' => 'Informatika'],
            ['nama_pt' => 'Universitas Gadjah Mada', 'email' => 'admin@ugm.ac.id', 'pic' => 'Dr. Citra Maharani', 'pic_nip' => '198202022006042002', 'fakultas' => 'Fakultas Ekonomika dan Bisnis', 'program_studi' => 'Manajemen'],
            ['nama_pt' => 'Universitas Ahmad Dahlan', 'email' => 'admin@uad.ac.id', 'pic' => 'Dian Purnama, M.T.', 'pic_nip' => '198303032007032003', 'fakultas' => 'Fakultas Teknologi Industri', 'program_studi' => 'Informatika'],
            ['nama_pt' => 'Universitas Sanata Dharma', 'email' => 'admin@usd.ac.id', 'pic' => 'Kartika Sari, M.Kom.', 'pic_nip' => '198404042008042004', 'fakultas' => 'Fakultas Sains dan Teknologi', 'program_studi' => 'Sistem Informasi'],
            ['nama_pt' => 'Universitas Atma Jaya Yogyakarta', 'email' => 'admin@uajy.ac.id', 'pic' => 'Dr. Hendra Wijaya', 'pic_nip' => '198505052009051005', 'fakultas' => 'Fakultas Bisnis dan Ekonomika', 'program_studi' => 'Akuntansi'],
        ])->mapWithKeys(function (array $campus) {
            $model = PerguruanTinggi::updateOrCreate(
                ['nama_pt' => $campus['nama_pt']],
                [
                    'alamat' => 'DI Yogyakarta',
                    'jenis' => str_contains($campus['nama_pt'], 'Negeri') || str_contains($campus['nama_pt'], 'Gadjah Mada') ? 'Negeri' : 'Swasta',
                    'pic' => $campus['pic'],
                    'pic_nip' => $campus['pic_nip'],
                    'telepon' => '0274-000000',
                    'email' => $campus['email'],
                    'fakultas' => $campus['fakultas'],
                    'program_studi' => $campus['program_studi'],
                ]
            );

            return [$campus['nama_pt'] => $model];
        });

        $mentorUsers = collect([
            ['name' => 'Budi Santoso', 'email' => 'budi@kominfo.go.id', 'status' => 'disetujui', 'jabatan' => 'Supervisor TI', 'divisi' => 'Dinas Kominfo DIY'],
            ['name' => 'Fajar Nugroho', 'email' => 'fajar@inovasi.id', 'status' => 'nonaktif', 'jabatan' => 'Lead Developer', 'divisi' => 'PT Inovasi Digital'],
            ['name' => 'Joko Firmansyah', 'email' => 'joko@bappeda.go.id', 'status' => 'disetujui', 'jabatan' => 'Analis Program', 'divisi' => 'Bappeda DIY'],
            ['name' => 'Mira Handayani', 'email' => 'mira@bpddiy.co.id', 'status' => 'menunggu', 'jabatan' => 'HR Officer', 'divisi' => 'Bank BPD DIY'],
        ])->map(function (array $row, int $index) {
            $user = $this->user($row['name'], $row['email'], 'mentor', $row['status']);

            return Mentor::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nip' => '19790'.($index + 1).'001',
                    'jenis_kelamin' => $index % 2 ? 'Perempuan' : 'Laki-laki',
                    'jabatan' => $row['jabatan'],
                    'divisi' => $row['divisi'],
                    'no_hp' => '0812345600'.($index + 1),
                    'alamat' => 'DI Yogyakarta',
                    'perguruan_tinggi' => $row['divisi'],
                ]
            );
        });

        $mentorByEmail = $mentorUsers->keyBy(fn (Mentor $mentor) => $mentor->user->email);
        $mentorList = $mentorUsers->values();

        $advisorUsers = collect([
            ['name' => 'Dr. Citra Maharani', 'email' => 'citra@ugm.ac.id', 'status' => 'menunggu', 'kampus' => 'Universitas Gadjah Mada'],
            ['name' => 'Dr. Hendra Wijaya', 'email' => 'hendra@uajy.ac.id', 'status' => 'disetujui', 'kampus' => 'Universitas Atma Jaya Yogyakarta'],
            ['name' => 'Kartika Sari, M.Kom.', 'email' => 'kartika@usd.ac.id', 'status' => 'nonaktif', 'kampus' => 'Universitas Sanata Dharma'],
            ['name' => 'Prof. Raden Prakoso', 'email' => 'raden@uny.ac.id', 'status' => 'disetujui', 'kampus' => 'Universitas Negeri Yogyakarta'],
        ])->map(function (array $row, int $index) {
            $user = $this->user($row['name'], $row['email'], 'pembimbing', $row['status']);

            return Pembimbing::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nidn_nip' => '05'.str_pad((string) ($index + 1), 8, '0', STR_PAD_LEFT),
                    'tempat_lahir' => 'Yogyakarta',
                    'tanggal_lahir' => now()->subYears(42 + $index)->toDateString(),
                    'jenis_kelamin' => $index % 2 ? 'Laki-laki' : 'Perempuan',
                    'instansi' => $row['kampus'],
                    'jabatan' => 'Dosen Pembimbing',
                    'no_hp' => '0822345600'.($index + 1),
                    'alamat' => 'DI Yogyakarta',
                    'perguruan_tinggi' => $row['kampus'],
                    'program_studi' => ['Informatika', 'Manajemen', 'Administrasi Publik', 'Akuntansi'][$index],
                ]
            );
        });

        collect([
            [
                'name' => 'Aulia Berliana',
                'email' => 'aulia@email.com',
                'account_status' => 'disetujui',
                'mentor_email' => 'budi@kominfo.go.id',
                'campus' => 'Universitas Negeri Yogyakarta',
                'nim' => '220001',
                'major' => 'Informatika',
                'participant_status' => 'aktif',
                'internship_status' => 'berjalan',
                'tanggal_mulai_magang' => now()->subDays(20)->toDateString(),
                'tanggal_selesai_magang' => now()->addMonths(3)->toDateString(),
            ],
            [
                'name' => 'Dewi Lestari',
                'email' => 'dewi@email.com',
                'account_status' => 'menunggu',
                'mentor_email' => 'fajar@inovasi.id',
                'campus' => 'Universitas Ahmad Dahlan',
                'nim' => '220002',
                'major' => 'Manajemen',
                'participant_status' => 'aktif',
                'internship_status' => 'berjalan',
                'tanggal_mulai_magang' => now()->subDays(20)->toDateString(),
                'tanggal_selesai_magang' => now()->addMonths(3)->toDateString(),
            ],
            [
                'name' => 'Gita Permata',
                'email' => 'gita@email.com',
                'account_status' => 'ditolak',
                'mentor_email' => 'joko@bappeda.go.id',
                'campus' => 'Universitas Sanata Dharma',
                'nim' => '220003',
                'major' => 'Administrasi Publik',
                'participant_status' => 'aktif',
                'internship_status' => 'berjalan',
                'tanggal_mulai_magang' => now()->subDays(20)->toDateString(),
                'tanggal_selesai_magang' => now()->addMonths(3)->toDateString(),
            ],
            [
                'name' => 'Intan Safitri',
                'email' => 'intan@email.com',
                'account_status' => 'menunggu',
                'mentor_email' => 'mira@bpddiy.co.id',
                'campus' => 'Universitas Gadjah Mada',
                'nim' => '220004',
                'major' => 'Informatika',
                'participant_status' => 'aktif',
                'internship_status' => 'berjalan',
                'tanggal_mulai_magang' => now()->subDays(20)->toDateString(),
                'tanggal_selesai_magang' => now()->addMonths(3)->toDateString(),
            ],
            [
                'name' => 'Lukman Hakim',
                'email' => 'lukman@email.com',
                'account_status' => 'disetujui',
                'mentor_email' => 'budi@kominfo.go.id',
                'campus' => 'Universitas Atma Jaya Yogyakarta',
                'nim' => '220005',
                'major' => 'Akuntansi',
                'participant_status' => 'aktif',
                'internship_status' => 'berjalan',
                'tanggal_mulai_magang' => now()->subDays(20)->toDateString(),
                'tanggal_selesai_magang' => now()->addMonths(3)->toDateString(),
            ],
            [
                'name' => 'Salsa Maulina',
                'email' => 'salsa@email.com',
                'account_status' => 'disetujui',
                'mentor_email' => 'budi@kominfo.go.id',
                'campus' => 'Universitas Gadjah Mada',
                'nim' => '220006',
                'major' => 'Informatika',
                'participant_status' => 'aktif',
                'internship_status' => 'berjalan',
                'tanggal_mulai_magang' => now()->subDays(45)->toDateString(),
                'tanggal_selesai_magang' => now()->addDays(5)->toDateString(),
            ],
            [
                'name' => 'Raka Pratama',
                'email' => 'raka@email.com',
                'account_status' => 'disetujui',
                'mentor_email' => 'budi@kominfo.go.id',
                'campus' => 'Universitas Ahmad Dahlan',
                'nim' => '220007',
                'major' => 'Manajemen',
                'participant_status' => 'selesai',
                'internship_status' => 'selesai',
                'tanggal_mulai_magang' => now()->subMonths(4)->toDateString(),
                'tanggal_selesai_magang' => now()->subDays(2)->toDateString(),
            ],
            [
                'name' => 'Nabila Putri',
                'email' => 'nabila@email.com',
                'account_status' => 'disetujui',
                'mentor_email' => 'budi@kominfo.go.id',
                'campus' => 'Universitas Negeri Yogyakarta',
                'nim' => '220008',
                'major' => 'Informatika',
                'participant_status' => 'aktif',
                'internship_status' => 'berjalan',
                'tanggal_mulai_magang' => now()->subDays(5)->toDateString(),
                'tanggal_selesai_magang' => now()->addDays(30)->toDateString(),
            ],
        ])->each(function (array $row, int $index) use ($admin, $campuses, $mentorByEmail, $mentorList, $advisorUsers) {
            $user = $this->user($row['name'], $row['email'], 'peserta', $row['account_status']);
            $campus = $campuses[$row['campus']];
            $mentor = $mentorByEmail->get($row['mentor_email']) ?? $mentorList[$index % $mentorList->count()];

            $peserta = Peserta::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'perguruan_tinggi_id' => $campus->id,
                    'nim' => $row['nim'],
                    'tempat_lahir' => 'Yogyakarta',
                    'tanggal_lahir' => now()->subYears(21)->subMonths($index)->toDateString(),
                    'jenis_kelamin' => $index % 2 ? 'Laki-laki' : 'Perempuan',
                    'jurusan' => $row['major'],
                    'fakultas' => 'Fakultas '.($index % 2 ? 'Ekonomi' : 'Teknik'),
                    'program_magang' => $index % 2 ? 'Batch 2 2026' : 'Batch 1 2026',
                    'pembimbing_akademik' => $advisorUsers[$index % $advisorUsers->count()]->user?->name,
                    'tanggal_mulai_magang' => $row['tanggal_mulai_magang'],
                    'tanggal_selesai_magang' => $row['tanggal_selesai_magang'],
                    'semester' => (string) (6 + ($index % 2)),
                    'no_hp' => '0834567800'.($index + 1),
                    'alamat' => 'DI Yogyakarta',
                    'status' => $row['participant_status'],
                ]
            );

            Internship::updateOrCreate(
                ['peserta_id' => $peserta->id],
                [
                    'mentor_id' => $mentor->id,
                    'pembimbing_id' => $advisorUsers[$index % $advisorUsers->count()]->id,
                    'instansi' => $mentor->divisi,
                    'unit_kerja' => 'Unit Magang',
                    'posisi' => ['Pengembang Aplikasi', 'Administrasi Program', 'Analis Data', 'Validasi Transaksi'][$index % 4],
                    'lokasi' => 'Yogyakarta',
                    'tanggal_mulai' => $row['tanggal_mulai_magang'],
                    'tanggal_selesai' => $row['tanggal_selesai_magang'],
                    'divisi' => ['Teknologi Informasi', 'Program', 'Data', 'Operasional'][$index % 4],
                    'status' => $row['internship_status'],
                    'deskripsi' => 'Penempatan magang peserta pada instansi mitra.',
                ]
            );

            $this->seedParticipantRecords($user, $peserta, $admin, $index);
        });
    }

    private function user(string $name, string $email, string $role, string $status): User
    {
        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'username' => str($email)->before('@')->slug('_'),
                'role' => $role,
                'password' => Hash::make('12345678'),
                'account_status' => $status,
                'verified_at' => in_array($status, ['disetujui', 'ditolak', 'nonaktif'], true) ? now() : null,
                'verified_by' => User::where('email', 'admin@gmail.com')->value('id'),
            ]
        );

        if (in_array($role, ['peserta', 'mentor', 'pembimbing'], true)) {
            VerificationHistory::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'jenis' => 'akun',
                ],
                [
                    'admin_id' => User::where('email', 'admin@gmail.com')->value('id'),
                    'status' => $status === 'disetujui' ? 'disetujui' : ($status === 'ditolak' ? 'ditolak' : 'menunggu'),
                    'keterangan' => 'Verifikasi akun '.$this->verificationRoleLabel($role).' '.$name.'.',
                    'verified_at' => in_array($status, ['disetujui', 'ditolak', 'nonaktif'], true) ? now()->subDays(6) : now()->subDays(2),
                ]
            );
        }

        return $user;
    }

    private function seedParticipantRecords(User $user, Peserta $peserta, User $admin, int $index): void
    {
        foreach ($this->participantDocuments($user, $peserta, $index) as $document) {
            Document::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'jenis_dokumen' => $document['jenis_dokumen'],
                ],
                [
                    'nama_dokumen' => $document['nama_dokumen'],
                    'kategori' => 'Administrasi',
                    'jenis_file' => $document['jenis_file'],
                    'file' => $document['file'],
                    'ukuran_file' => $document['ukuran_file'],
                    'status' => $document['status'],
                    'catatan' => $document['catatan'],
                ]
            );
        }

        $this->seedDocumentVerificationHistories($user, $peserta, $admin, $index);

        Report::updateOrCreate(
            ['peserta_id' => $peserta->id, 'judul' => 'Laporan Magang '.$peserta->nim],
            [
                'jenis' => 'berkala',
                'periode' => $peserta->program_magang,
                'file' => 'laporan-'.$peserta->nim.'.pdf',
                'status' => ['pending', 'approved', 'revisi', 'rejected'][$index % 4],
                'catatan' => null,
                'reviewer_id' => $admin->id,
            ]
        );

        Logbook::updateOrCreate(
            ['peserta_id' => $peserta->id, 'tanggal' => now()->subDays($index)->toDateString()],
            [
                'kegiatan' => ['Pengembangan Dashboard Monitoring', 'Rekapitulasi Program Kerja', 'Klasifikasi Dokumen', 'Pengujian Modul Aplikasi'][$index % 4],
                'deskripsi' => 'Kegiatan harian peserta magang sesuai penempatan.',
                'status' => ['pending', 'approved', 'approved', 'rejected'][$index % 4],
            ]
        );

        if (Schema::hasTable('attendances')) {
            Attendance::updateOrCreate(
                ['peserta_id' => $peserta->id, 'tanggal' => now()->subDays($index)->toDateString()],
                [
                    'jam_masuk' => '08:0'.($index % 6),
                    'jam_pulang' => '16:0'.($index % 6),
                    'status' => ['hadir', 'terlambat', 'izin', 'tidak_hadir'][$index % 4],
                    'durasi_menit' => 480,
                    'keterangan' => 'Data absensi awal.',
                ]
            );
        }

        Activity::updateOrCreate(
            ['user_id' => $user->id, 'aktivitas' => 'Akun peserta '.$user->name.' dibuat dari seeder demo.'],
            []
        );
    }

    private function seedDocumentVerificationHistories(User $user, Peserta $peserta, User $admin, int $index): void
    {
        $documents = collect($this->participantDocuments($user, $peserta, $index));

        $documents->values()->each(function (array $document, int $docIndex) use ($user, $admin, $index) {
            VerificationHistory::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'jenis' => 'dokumen',
                    'keterangan' => $document['nama_dokumen'],
                ],
                [
                    'admin_id' => $admin->id,
                    'status' => $document['status'] === 'disetujui' ? 'disetujui' : ($document['status'] === 'ditolak' ? 'ditolak' : 'menunggu'),
                    'verified_at' => now()->subDays($index + $docIndex + 1),
                ]
            );
        });
    }

    private function participantDocuments(User $user, Peserta $peserta, int $index): array
    {
        $baseSize = 420000 + ($index * 35000);
        $slug = str($peserta->nim)->slug('-');

        $make = function (string $key, string $label, string $extension = 'pdf', ?string $status = null, ?string $note = null, ?int $size = null) use ($slug) {
            return [
                'jenis_dokumen' => $key,
                'nama_dokumen' => $label.' '.$slug.'.'.$extension,
                'jenis_file' => strtoupper($extension),
                'file' => 'dokumen-peserta/'.$slug.'/'.$key.'.'.$extension,
                'ukuran_file' => $size ?? 512000,
                'status' => $status ?? 'disetujui',
                'catatan' => $note,
            ];
        };

        $documents = [
            'proposal' => $make('proposal', 'Proposal Magang', 'pdf', 'disetujui', null, $baseSize),
            'ktm' => $make('ktm', 'KTM', 'pdf', 'disetujui', null, $baseSize + 12000),
            'transkip' => $make('transkip', 'Transkip Nilai', 'pdf', 'disetujui', null, $baseSize + 15000),
            'cv' => $make('cv', 'CV Peserta', 'pdf', 'disetujui', null, $baseSize + 18000),
            'surat_pengantar' => $make('surat_pengantar', 'Surat Pengantar', 'pdf', 'disetujui', null, $baseSize + 21000),
            'sertifikat_pendukung' => $make('sertifikat_pendukung', 'Sertifikat Pendukung', 'pdf', 'disetujui', null, $baseSize + 24000),
        ];

        $overrides = match ($user->email) {
            'aulia@email.com' => [
                'transkip' => ['status' => 'revisi', 'catatan' => 'Perbarui transkip agar lebih jelas.'],
                'surat_pengantar' => ['status' => 'menunggu', 'catatan' => 'Menunggu validasi tanda tangan.'],
            ],
            'gita@email.com' => [
                'surat_pengantar' => ['status' => 'ditolak', 'catatan' => 'Berkas belum terbaca dengan jelas.'],
            ],
            'intan@email.com' => [
                'surat_pengantar' => ['status' => 'menunggu', 'catatan' => 'Menunggu tanda tangan kampus.'],
                'sertifikat_pendukung' => ['status' => 'revisi', 'catatan' => 'Tambahkan sertifikat pendukung terbaru.'],
            ],
            'lukman@email.com' => [
                'ktm' => ['status' => 'menunggu', 'catatan' => 'KTM baru belum diunggah.'],
            ],
            'salsa@email.com' => [
                'sertifikat_pendukung' => ['status' => 'menunggu', 'catatan' => 'Sertifikat masih diproses.'],
            ],
            default => [
                'proposal' => ['status' => ['menunggu', 'disetujui', 'revisi'][$index % 3], 'catatan' => $index % 3 === 2 ? 'Perbaiki isi proposal.' : null],
                'ktm' => ['status' => ['disetujui', 'menunggu', 'disetujui'][$index % 3], 'catatan' => null],
            ],
        };

        foreach ($overrides as $key => $override) {
            $documents[$key]['status'] = $override['status'] ?? $documents[$key]['status'];
            $documents[$key]['catatan'] = $override['catatan'] ?? $documents[$key]['catatan'];
        }

        return array_values($documents);
    }

    private function verificationRoleLabel(string $role): string
    {
        return match ($role) {
            'peserta' => 'peserta',
            'mentor' => 'mentor',
            'pembimbing' => 'pembimbing akademik',
            default => $role,
        };
    }
}
