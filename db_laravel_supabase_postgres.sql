-- Converted from MySQL/MariaDB phpMyAdmin dump to PostgreSQL/Supabase-compatible SQL
-- Source: db_laravel.sql
-- Run this on a fresh Supabase project or review DROP TABLE lines before running.
BEGIN;
DROP TABLE IF EXISTS "verification_histories" CASCADE;
DROP TABLE IF EXISTS "users" CASCADE;
DROP TABLE IF EXISTS "security_activities" CASCADE;
DROP TABLE IF EXISTS "reports" CASCADE;
DROP TABLE IF EXISTS "pesertas" CASCADE;
DROP TABLE IF EXISTS "personal_access_tokens" CASCADE;
DROP TABLE IF EXISTS "perguruan_tinggi" CASCADE;
DROP TABLE IF EXISTS "pembimbings" CASCADE;
DROP TABLE IF EXISTS "password_reset_tokens" CASCADE;
DROP TABLE IF EXISTS "notification_preferences" CASCADE;
DROP TABLE IF EXISTS "notifications" CASCADE;
DROP TABLE IF EXISTS "migrations" CASCADE;
DROP TABLE IF EXISTS "messages" CASCADE;
DROP TABLE IF EXISTS "mentors" CASCADE;
DROP TABLE IF EXISTS "logbooks" CASCADE;
DROP TABLE IF EXISTS "internships" CASCADE;
DROP TABLE IF EXISTS "failed_jobs" CASCADE;
DROP TABLE IF EXISTS "documents" CASCADE;
DROP TABLE IF EXISTS "conversations" CASCADE;
DROP TABLE IF EXISTS "certificates" CASCADE;
DROP TABLE IF EXISTS "attendance_recap_reviews" CASCADE;
DROP TABLE IF EXISTS "attendances" CASCADE;
DROP TABLE IF EXISTS "assignments" CASCADE;
DROP TABLE IF EXISTS "assessments" CASCADE;
DROP TABLE IF EXISTS "announcement_user" CASCADE;
DROP TABLE IF EXISTS "announcements" CASCADE;
DROP TABLE IF EXISTS "activities" CASCADE;
DROP TABLE IF EXISTS "access_roles" CASCADE;

CREATE TABLE "access_roles" (
  "id" BIGSERIAL NOT NULL,
  "role_key" VARCHAR(255) NOT NULL,
  "name" VARCHAR(255) NOT NULL,
  "type" VARCHAR(20) NOT NULL,
  "status" VARCHAR(20) NOT NULL DEFAULT 'aktif',
  "permissions" jsonb DEFAULT NULL,
  "sort_order" INTEGER NOT NULL DEFAULT 0,
  "created_at" TIMESTAMP NULL DEFAULT NULL,
  "updated_at" TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE "activities" (
  "id" BIGSERIAL NOT NULL,
  "user_id" BIGINT NOT NULL,
  "aktivitas" VARCHAR(255) NOT NULL,
  "created_at" TIMESTAMP NULL DEFAULT NULL,
  "updated_at" TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE "announcements" (
  "id" BIGSERIAL NOT NULL,
  "user_id" BIGINT DEFAULT NULL,
  "judul" VARCHAR(255) NOT NULL,
  "kategori" VARCHAR(255) NOT NULL DEFAULT 'Umum',
  "isi" TEXT NOT NULL,
  "status" VARCHAR(255) NOT NULL DEFAULT 'aktif',
  "tanggal" DATE DEFAULT NULL,
  "lampiran" VARCHAR(255) DEFAULT NULL,
  "created_at" TIMESTAMP NULL DEFAULT NULL,
  "updated_at" TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE "announcement_user" (
  "id" BIGSERIAL NOT NULL,
  "announcement_id" BIGINT NOT NULL,
  "user_id" BIGINT NOT NULL,
  "dibaca_pada" TIMESTAMP NULL DEFAULT NULL,
  "disimpan_pada" TIMESTAMP NULL DEFAULT NULL,
  "created_at" TIMESTAMP NULL DEFAULT NULL,
  "updated_at" TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE "assessments" (
  "id" BIGSERIAL NOT NULL,
  "peserta_id" BIGINT NOT NULL,
  "mentor_id" BIGINT DEFAULT NULL,
  "pembimbing_id" BIGINT DEFAULT NULL,
  "jenis" VARCHAR(255) NOT NULL,
  "periode" VARCHAR(255) DEFAULT NULL,
  "komponen" VARCHAR(255) NOT NULL,
  "bobot" SMALLINT NOT NULL DEFAULT 0,
  "nilai" DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  "nilai_akhir" DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  "status" VARCHAR(255) NOT NULL DEFAULT 'draft',
  "catatan" TEXT DEFAULT NULL,
  "created_at" TIMESTAMP NULL DEFAULT NULL,
  "updated_at" TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE "assignments" (
  "id" BIGSERIAL NOT NULL,
  "peserta_id" BIGINT NOT NULL,
  "mentor_id" BIGINT DEFAULT NULL,
  "judul" VARCHAR(255) NOT NULL,
  "deskripsi" TEXT DEFAULT NULL,
  "prioritas" VARCHAR(255) NOT NULL DEFAULT 'normal',
  "deadline" DATE DEFAULT NULL,
  "status" VARCHAR(255) NOT NULL DEFAULT 'belum_dikerjakan',
  "progress" SMALLINT NOT NULL DEFAULT 0,
  "file_hasil" VARCHAR(255) DEFAULT NULL,
  "file_pengumpulan" VARCHAR(255) DEFAULT NULL,
  "submitted_at" TIMESTAMP NULL DEFAULT NULL,
  "catatan" TEXT DEFAULT NULL,
  "created_at" TIMESTAMP NULL DEFAULT NULL,
  "updated_at" TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE "attendances" (
  "id" BIGSERIAL NOT NULL,
  "peserta_id" BIGINT NOT NULL,
  "tanggal" DATE NOT NULL,
  "tanggal_selesai" DATE DEFAULT NULL,
  "jam_masuk" TIME DEFAULT NULL,
  "jam_pulang" TIME DEFAULT NULL,
  "status" VARCHAR(255) NOT NULL DEFAULT 'hadir',
  "durasi_menit" INTEGER DEFAULT NULL,
  "keterangan" TEXT DEFAULT NULL,
  "lampiran" VARCHAR(255) DEFAULT NULL,
  "created_at" TIMESTAMP NULL DEFAULT NULL,
  "updated_at" TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE "attendance_recap_reviews" (
  "id" BIGSERIAL NOT NULL,
  "peserta_id" BIGINT NOT NULL,
  "reviewed_by" BIGINT DEFAULT NULL,
  "status" VARCHAR(255) NOT NULL DEFAULT 'draft',
  "catatan" TEXT DEFAULT NULL,
  "reviewed_at" TIMESTAMP NULL DEFAULT NULL,
  "created_at" TIMESTAMP NULL DEFAULT NULL,
  "updated_at" TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE "certificates" (
  "id" BIGSERIAL NOT NULL,
  "peserta_id" BIGINT NOT NULL,
  "nomor" VARCHAR(255) NOT NULL,
  "jenis" VARCHAR(255) NOT NULL DEFAULT 'Magang',
  "periode" VARCHAR(255) DEFAULT NULL,
  "predikat" VARCHAR(255) DEFAULT NULL,
  "status" VARCHAR(255) NOT NULL DEFAULT 'menunggu',
  "tanggal_terbit" DATE DEFAULT NULL,
  "file" VARCHAR(255) DEFAULT NULL,
  "created_at" TIMESTAMP NULL DEFAULT NULL,
  "updated_at" TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE "conversations" (
  "id" BIGSERIAL NOT NULL,
  "peserta_id" BIGINT NOT NULL,
  "mentor_id" BIGINT DEFAULT NULL,
  "pembimbing_id" BIGINT DEFAULT NULL,
  "admin_id" BIGINT DEFAULT NULL,
  "topik" VARCHAR(255) NOT NULL,
  "status" VARCHAR(255) NOT NULL DEFAULT 'aktif',
  "last_message_at" TIMESTAMP NULL DEFAULT NULL,
  "created_at" TIMESTAMP NULL DEFAULT NULL,
  "updated_at" TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE "documents" (
  "id" BIGSERIAL NOT NULL,
  "user_id" BIGINT NOT NULL,
  "nama_dokumen" VARCHAR(255) NOT NULL,
  "kategori" VARCHAR(255) NOT NULL,
  "jenis_dokumen" VARCHAR(255) DEFAULT NULL,
  "jenis_file" VARCHAR(255) DEFAULT NULL,
  "file" VARCHAR(255) NOT NULL,
  "ukuran_file" BIGINT DEFAULT NULL,
  "status" VARCHAR(255) NOT NULL DEFAULT 'menunggu',
  "catatan" TEXT DEFAULT NULL,
  "created_at" TIMESTAMP NULL DEFAULT NULL,
  "updated_at" TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE "failed_jobs" (
  "id" BIGSERIAL NOT NULL,
  "uuid" VARCHAR(255) NOT NULL,
  "connection" TEXT NOT NULL,
  "queue" TEXT NOT NULL,
  "payload" TEXT NOT NULL,
  "exception" TEXT NOT NULL,
  "failed_at" TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE "internships" (
  "id" BIGSERIAL NOT NULL,
  "peserta_id" BIGINT NOT NULL,
  "mentor_id" BIGINT NOT NULL,
  "pembimbing_id" BIGINT NOT NULL,
  "instansi" VARCHAR(255) DEFAULT NULL,
  "unit_kerja" VARCHAR(255) DEFAULT NULL,
  "posisi" VARCHAR(255) DEFAULT NULL,
  "lokasi" VARCHAR(255) DEFAULT NULL,
  "tanggal_mulai" DATE NOT NULL,
  "tanggal_selesai" DATE NOT NULL,
  "divisi" VARCHAR(255) NOT NULL,
  "status" VARCHAR(255) NOT NULL DEFAULT 'pending',
  "deskripsi" TEXT DEFAULT NULL,
  "created_at" TIMESTAMP NULL DEFAULT NULL,
  "updated_at" TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE "logbooks" (
  "id" BIGSERIAL NOT NULL,
  "peserta_id" BIGINT NOT NULL,
  "tanggal" DATE NOT NULL,
  "kegiatan" VARCHAR(255) NOT NULL,
  "deskripsi" TEXT NOT NULL,
  "status" VARCHAR(255) NOT NULL DEFAULT 'pending',
  "created_at" TIMESTAMP NULL DEFAULT NULL,
  "updated_at" TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE "mentors" (
  "id" BIGSERIAL NOT NULL,
  "user_id" BIGINT NOT NULL,
  "nip" VARCHAR(255) DEFAULT NULL,
  "jenis_kelamin" VARCHAR(255) DEFAULT NULL,
  "jabatan" VARCHAR(255) NOT NULL,
  "divisi" VARCHAR(255) NOT NULL,
  "no_hp" VARCHAR(255) NOT NULL,
  "alamat" TEXT DEFAULT NULL,
  "instansi" VARCHAR(255) DEFAULT NULL,
  "created_at" TIMESTAMP NULL DEFAULT NULL,
  "updated_at" TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE "messages" (
  "id" BIGSERIAL NOT NULL,
  "conversation_id" BIGINT NOT NULL,
  "sender_id" BIGINT NOT NULL,
  "pesan" TEXT NOT NULL,
  "lampiran" VARCHAR(255) DEFAULT NULL,
  "dibaca_pada" TIMESTAMP NULL DEFAULT NULL,
  "created_at" TIMESTAMP NULL DEFAULT NULL,
  "updated_at" TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE "migrations" (
  "id" SERIAL NOT NULL,
  "migration" VARCHAR(255) NOT NULL,
  "batch" INTEGER NOT NULL
);

CREATE TABLE "notifications" (
  "id" BIGSERIAL NOT NULL,
  "user_id" BIGINT NOT NULL,
  "judul" VARCHAR(255) NOT NULL,
  "pesan" TEXT NOT NULL,
  "dibaca" SMALLINT NOT NULL DEFAULT 0,
  "created_at" TIMESTAMP NULL DEFAULT NULL,
  "updated_at" TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE "notification_preferences" (
  "id" BIGSERIAL NOT NULL,
  "user_id" BIGINT NOT NULL,
  "pesan" SMALLINT NOT NULL DEFAULT 1,
  "laporan" SMALLINT NOT NULL DEFAULT 1,
  "penugasan" SMALLINT NOT NULL DEFAULT 1,
  "absensi" SMALLINT NOT NULL DEFAULT 1,
  "pengumuman" SMALLINT NOT NULL DEFAULT 1,
  "email" SMALLINT NOT NULL DEFAULT 0,
  "created_at" TIMESTAMP NULL DEFAULT NULL,
  "updated_at" TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE "password_reset_tokens" (
  "email" VARCHAR(255) NOT NULL,
  "token" VARCHAR(255) NOT NULL,
  "created_at" TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE "pembimbings" (
  "id" BIGSERIAL NOT NULL,
  "user_id" BIGINT NOT NULL,
  "nidn_nip" VARCHAR(255) DEFAULT NULL,
  "tempat_lahir" VARCHAR(255) DEFAULT NULL,
  "tanggal_lahir" DATE DEFAULT NULL,
  "jenis_kelamin" VARCHAR(255) DEFAULT NULL,
  "instansi" VARCHAR(255) NOT NULL,
  "jabatan" VARCHAR(255) NOT NULL,
  "no_hp" VARCHAR(255) NOT NULL,
  "alamat" TEXT DEFAULT NULL,
  "perguruan_tinggi" VARCHAR(255) DEFAULT NULL,
  "program_studi" VARCHAR(255) DEFAULT NULL,
  "created_at" TIMESTAMP NULL DEFAULT NULL,
  "updated_at" TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE "perguruan_tinggi" (
  "id" BIGSERIAL NOT NULL,
  "nama_pt" VARCHAR(255) NOT NULL,
  "jenis" VARCHAR(255) DEFAULT NULL,
  "status_kerja_sama" VARCHAR(255) NOT NULL DEFAULT 'aktif',
  "pic" VARCHAR(255) DEFAULT NULL,
  "pic_nip" VARCHAR(255) DEFAULT NULL,
  "alamat" VARCHAR(255) NOT NULL,
  "telepon" VARCHAR(255) NOT NULL,
  "email" VARCHAR(255) NOT NULL,
  "fakultas" VARCHAR(255) DEFAULT NULL,
  "program_studi" VARCHAR(255) DEFAULT NULL,
  "logo" VARCHAR(255) DEFAULT NULL,
  "created_at" TIMESTAMP NULL DEFAULT NULL,
  "updated_at" TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE "personal_access_tokens" (
  "id" BIGSERIAL NOT NULL,
  "tokenable_type" VARCHAR(255) NOT NULL,
  "tokenable_id" BIGINT NOT NULL,
  "name" VARCHAR(255) NOT NULL,
  "token" VARCHAR(64) NOT NULL,
  "abilities" TEXT DEFAULT NULL,
  "last_used_at" TIMESTAMP NULL DEFAULT NULL,
  "expires_at" TIMESTAMP NULL DEFAULT NULL,
  "created_at" TIMESTAMP NULL DEFAULT NULL,
  "updated_at" TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE "pesertas" (
  "id" BIGSERIAL NOT NULL,
  "user_id" BIGINT NOT NULL,
  "perguruan_tinggi_id" BIGINT NOT NULL,
  "nim" VARCHAR(255) NOT NULL,
  "tempat_lahir" VARCHAR(255) DEFAULT NULL,
  "tanggal_lahir" DATE DEFAULT NULL,
  "jenis_kelamin" VARCHAR(255) DEFAULT NULL,
  "jurusan" VARCHAR(255) NOT NULL,
  "fakultas" VARCHAR(255) DEFAULT NULL,
  "program_magang" VARCHAR(255) DEFAULT NULL,
  "pembimbing_akademik" VARCHAR(255) DEFAULT NULL,
  "tanggal_mulai_magang" DATE DEFAULT NULL,
  "tanggal_selesai_magang" DATE DEFAULT NULL,
  "semester" VARCHAR(255) NOT NULL,
  "no_hp" VARCHAR(255) NOT NULL,
  "alamat" TEXT NOT NULL,
  "status" VARCHAR(255) NOT NULL DEFAULT 'pending',
  "created_at" TIMESTAMP NULL DEFAULT NULL,
  "updated_at" TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE "reports" (
  "id" BIGSERIAL NOT NULL,
  "peserta_id" BIGINT NOT NULL,
  "judul" VARCHAR(255) NOT NULL,
  "jenis" VARCHAR(255) NOT NULL DEFAULT 'berkala',
  "periode" VARCHAR(255) DEFAULT NULL,
  "durasi_jam" SMALLINT DEFAULT NULL,
  "file" VARCHAR(255) NOT NULL,
  "status" VARCHAR(255) NOT NULL DEFAULT 'pending',
  "catatan" TEXT DEFAULT NULL,
  "catatan_mentor" TEXT DEFAULT NULL,
  "catatan_pembimbing" TEXT DEFAULT NULL,
  "pembimbing_review_status" VARCHAR(30) DEFAULT 'menunggu review',
  "reviewer_id" BIGINT DEFAULT NULL,
  "admin_approved_at" TIMESTAMP NULL DEFAULT NULL,
  "created_at" TIMESTAMP NULL DEFAULT NULL,
  "updated_at" TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE "security_activities" (
  "id" BIGSERIAL NOT NULL,
  "user_id" BIGINT NOT NULL,
  "aktivitas" VARCHAR(255) NOT NULL,
  "perangkat" VARCHAR(255) DEFAULT NULL,
  "browser" VARCHAR(255) DEFAULT NULL,
  "ip_address" VARCHAR(255) DEFAULT NULL,
  "status" VARCHAR(255) NOT NULL DEFAULT 'berhasil',
  "catatan" TEXT DEFAULT NULL,
  "created_at" TIMESTAMP NULL DEFAULT NULL,
  "updated_at" TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE "users" (
  "id" BIGSERIAL NOT NULL,
  "name" VARCHAR(255) NOT NULL,
  "username" VARCHAR(255) DEFAULT NULL,
  "email" VARCHAR(255) NOT NULL,
  "password" VARCHAR(255) NOT NULL,
  "role" VARCHAR(255) NOT NULL,
  "account_status" VARCHAR(255) NOT NULL DEFAULT 'menunggu',
  "rejection_reason" TEXT DEFAULT NULL,
  "verified_at" TIMESTAMP NULL DEFAULT NULL,
  "verified_by" BIGINT DEFAULT NULL,
  "foto" VARCHAR(255) DEFAULT NULL,
  "phone" VARCHAR(255) DEFAULT NULL,
  "address" TEXT DEFAULT NULL,
  "is_online" SMALLINT NOT NULL DEFAULT 0,
  "two_factor_enabled" SMALLINT NOT NULL DEFAULT 0,
  "password_changed_at" TIMESTAMP NULL DEFAULT NULL,
  "remember_token" VARCHAR(100) DEFAULT NULL,
  "created_at" TIMESTAMP NULL DEFAULT NULL,
  "updated_at" TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE "verification_histories" (
  "id" BIGSERIAL NOT NULL,
  "user_id" BIGINT NOT NULL,
  "admin_id" BIGINT DEFAULT NULL,
  "jenis" VARCHAR(255) NOT NULL DEFAULT 'akun',
  "status" VARCHAR(255) NOT NULL DEFAULT 'menunggu',
  "keterangan" TEXT DEFAULT NULL,
  "verified_at" TIMESTAMP NULL DEFAULT NULL,
  "created_at" TIMESTAMP NULL DEFAULT NULL,
  "updated_at" TIMESTAMP NULL DEFAULT NULL
);

-- Primary keys and unique constraints
ALTER TABLE "access_roles" ADD PRIMARY KEY ("id");
ALTER TABLE "access_roles" ADD CONSTRAINT "access_roles_role_key_unique" UNIQUE ("role_key");
ALTER TABLE "activities" ADD PRIMARY KEY ("id");
ALTER TABLE "announcements" ADD PRIMARY KEY ("id");
ALTER TABLE "announcement_user" ADD PRIMARY KEY ("id");
ALTER TABLE "announcement_user" ADD CONSTRAINT "announcement_user_announcement_id_user_id_unique" UNIQUE ("announcement_id","user_id");
ALTER TABLE "assessments" ADD PRIMARY KEY ("id");
ALTER TABLE "assignments" ADD PRIMARY KEY ("id");
ALTER TABLE "attendances" ADD PRIMARY KEY ("id");
ALTER TABLE "attendance_recap_reviews" ADD PRIMARY KEY ("id");
ALTER TABLE "attendance_recap_reviews" ADD CONSTRAINT "attendance_recap_reviews_peserta_id_unique" UNIQUE ("peserta_id");
ALTER TABLE "certificates" ADD PRIMARY KEY ("id");
ALTER TABLE "certificates" ADD CONSTRAINT "certificates_nomor_unique" UNIQUE ("nomor");
ALTER TABLE "conversations" ADD PRIMARY KEY ("id");
ALTER TABLE "documents" ADD PRIMARY KEY ("id");
ALTER TABLE "documents" ADD CONSTRAINT "documents_user_id_jenis_dokumen_unique" UNIQUE ("user_id","jenis_dokumen");
ALTER TABLE "failed_jobs" ADD PRIMARY KEY ("id");
ALTER TABLE "failed_jobs" ADD CONSTRAINT "failed_jobs_uuid_unique" UNIQUE ("uuid");
ALTER TABLE "internships" ADD PRIMARY KEY ("id");
ALTER TABLE "logbooks" ADD PRIMARY KEY ("id");
ALTER TABLE "mentors" ADD PRIMARY KEY ("id");
ALTER TABLE "messages" ADD PRIMARY KEY ("id");
ALTER TABLE "migrations" ADD PRIMARY KEY ("id");
ALTER TABLE "notifications" ADD PRIMARY KEY ("id");
ALTER TABLE "notification_preferences" ADD PRIMARY KEY ("id");
ALTER TABLE "notification_preferences" ADD CONSTRAINT "notification_preferences_user_id_unique" UNIQUE ("user_id");
ALTER TABLE "password_reset_tokens" ADD PRIMARY KEY ("email");
ALTER TABLE "pembimbings" ADD PRIMARY KEY ("id");
ALTER TABLE "perguruan_tinggi" ADD PRIMARY KEY ("id");
ALTER TABLE "personal_access_tokens" ADD PRIMARY KEY ("id");
ALTER TABLE "personal_access_tokens" ADD CONSTRAINT "personal_access_tokens_token_unique" UNIQUE ("token");
ALTER TABLE "pesertas" ADD PRIMARY KEY ("id");
ALTER TABLE "reports" ADD PRIMARY KEY ("id");
ALTER TABLE "security_activities" ADD PRIMARY KEY ("id");
ALTER TABLE "users" ADD PRIMARY KEY ("id");
ALTER TABLE "users" ADD CONSTRAINT "users_email_unique" UNIQUE ("email");
ALTER TABLE "users" ADD CONSTRAINT "users_username_unique" UNIQUE ("username");
ALTER TABLE "verification_histories" ADD PRIMARY KEY ("id");

-- Data
INSERT INTO "access_roles" ("id", "role_key", "name", "type", "status", "permissions", "sort_order", "created_at", "updated_at") VALUES
(1, 'super_admin', 'Super Admin', 'Internal', 'aktif', '["Dashboard","Verifikasi > Verifikasi Akun","Verifikasi > Riwayat Verifikasi","Manajemen Pengguna > Peserta Magang","Manajemen Pengguna > Mentor","Manajemen Pengguna > Pembimbing Akademik","Manajemen Perguruan Tinggi > Data Perguruan Tinggi","Manajemen Perguruan Tinggi > Data Kerja Sama","Manajemen Magang > Dokumen Peserta","Manajemen Magang > Absensi","Manajemen Magang > Kegiatan Magang","Manajemen Magang > Laporan Berkala","Manajemen Magang > Laporan Akhir","Manajemen Magang > Periode Magang","Manajemen Magang > Penempatan","Manajemen Magang > Penilaian","Monitoring > Rekap Absensi","Monitoring > Rekap Kegiatan","Monitoring > Statistik Pengguna","Monitoring > Statistik Perguruan Tinggi","Komunikasi > Pesan","Komunikasi > Pengumuman","Komunikasi > Notifikasi","Pengaturan Akun > Profil Akun","Pengaturan Akun > Ubah Password","Pengaturan Akun > Hak Akses"]', 1, '2026-06-13 17:57:48', '2026-06-13 17:57:48'),
(2, 'mentor', 'Mentor', 'Internal', 'aktif', '["Dashboard","Daftar Peserta Magang","Monitoring > Absensi","Monitoring > Penugasan","Monitoring > Logbook Harian","Monitoring > Kerjasama","Monitoring > Status Magang","Review > Laporan","Review > Daftar Laporan","Review > Riwayat Review","Penilaian > Input Nilai","Penilaian > Rekap Nilai","Komunikasi > Pesan","Komunikasi > Pengumuman","Komunikasi > Notifikasi","Pengaturan Akun > Profil","Pengaturan Akun > Ubah Password"]', 2, '2026-06-13 17:57:48', '2026-06-13 17:57:48'),
(3, 'peserta', 'Peserta Magang', 'Eksternal', 'aktif', '["Dashboard","Data Magang > Profil Peserta","Data Magang > Penempatan","Data Magang > Status Verifikasi","Aktivitas Magang > Absensi","Aktivitas Magang > Logbook","Aktivitas Magang > Penugasan","Aktivitas Magang > Riwayat Kegiatan","Dokumen > Dokumen Kerjasama","Dokumen > Dokumen Pendukung","Dokumen > Status Dokumen","Laporan > Input Laporan","Laporan > Riwayat Laporan","Penilaian > Rekap Nilai","Penilaian > Sertifikat","Komunikasi > Pesan","Komunikasi > Pengumuman","Komunikasi > Notifikasi","Pengaturan Akun > Profil Akun","Pengaturan Akun > Ubah Password"]', 3, '2026-06-13 17:57:48', '2026-06-13 17:57:48'),
(4, 'pembimbing', 'Pembimbing Akademik', 'Eksternal', 'aktif', '["Dashboard","Mahasiswa Bimbingan","Monitoring > Kegiatan Mahasiswa","Monitoring > Absensi","Monitoring > Progres Magang","Monitoring > Logbook Harian","Review > Review Laporan","Review > Daftar Laporan","Review > Riwayat Review","Penilaian > Input Nilai","Penilaian > Rekap Nilai","Komunikasi > Pesan","Komunikasi > Pengumuman","Komunikasi > Notifikasi","Pengaturan Akun > Profil","Pengaturan Akun > Ubah Password"]', 4, '2026-06-13 17:57:48', '2026-06-13 17:57:48');;
INSERT INTO "activities" ("id", "user_id", "aktivitas", "created_at", "updated_at") VALUES
(1, 27, 'Akun peserta Aulia Berliana dibuat dari seeder demo.', '2026-06-04 09:21:50', '2026-06-04 09:21:50'),
(2, 28, 'Akun peserta Dewi Lestari dibuat dari seeder demo.', '2026-06-04 09:21:50', '2026-06-04 09:21:50'),
(3, 29, 'Akun peserta Gita Permata dibuat dari seeder demo.', '2026-06-04 09:21:50', '2026-06-04 09:21:50'),
(4, 30, 'Akun peserta Intan Safitri dibuat dari seeder demo.', '2026-06-04 09:21:50', '2026-06-04 09:21:50'),
(5, 31, 'Akun peserta Lukman Hakim dibuat dari seeder demo.', '2026-06-04 09:21:50', '2026-06-04 09:21:50'),
(6, 33, 'Akun peserta Salsa Maulina dibuat dari seeder demo.', '2026-06-04 11:37:31', '2026-06-04 11:37:31'),
(7, 34, 'Akun peserta Raka Pratama dibuat dari seeder demo.', '2026-06-04 11:37:32', '2026-06-04 11:37:32'),
(8, 35, 'Akun peserta Nabila Putri dibuat dari seeder demo.', '2026-06-04 11:40:28', '2026-06-04 11:40:28'),
(9, 24, 'Mengirim pesan ke peserta Dewi Lestari pada menu mahasiswa bimbingan.', '2026-06-04 12:22:50', '2026-06-04 12:22:50'),
(10, 27, 'Mengunggah laporan baru: Logbook Harian.', '2026-06-09 09:36:40', '2026-06-09 09:36:40'),
(11, 27, 'Memperbarui laporan: Logbook Harian.', '2026-06-09 10:11:47', '2026-06-09 10:11:47'),
(12, 27, 'Memperbarui laporan: Logbook Harian.', '2026-06-09 10:15:03', '2026-06-09 10:15:03'),
(13, 27, 'Memperbarui laporan: Logbook Harian.', '2026-06-09 10:16:46', '2026-06-09 10:16:46'),
(14, 27, 'Mengunggah laporan baru: L.', '2026-06-09 10:17:38', '2026-06-09 10:17:38'),
(15, 27, 'Memperbarui laporan: L.', '2026-06-09 10:20:09', '2026-06-09 10:20:09'),
(19, 37, 'Mengunggah dokumen pendukung peserta melalui menu dokumen.', '2026-06-10 16:27:10', '2026-06-10 16:27:10'),
(20, 37, 'Mengunggah dokumen pendukung peserta melalui menu dokumen.', '2026-06-10 16:30:19', '2026-06-10 16:30:19'),
(21, 37, 'Mengunggah dokumen pendukung peserta melalui menu dokumen.', '2026-06-10 16:35:32', '2026-06-10 16:35:32'),
(22, 37, 'Mengunggah dokumen kerja sama peserta melalui menu dokumen kerja sama.', '2026-06-10 16:36:28', '2026-06-10 16:36:28'),
(23, 37, 'Mengunggah dokumen kerja sama peserta melalui menu dokumen kerja sama.', '2026-06-10 16:56:02', '2026-06-10 16:56:02'),
(24, 37, 'Menghapus dokumen pendukung peserta melalui menu dokumen.', '2026-06-10 17:02:35', '2026-06-10 17:02:35'),
(25, 18, 'Memvalidasi dokumen kerja sama milik Ni Komang Lia Apriliana menjadi disetujui.', '2026-06-10 18:09:44', '2026-06-10 18:09:44'),
(26, 37, 'Mengunggah laporan baru: Laporan Mingguan 9.', '2026-06-11 17:36:03', '2026-06-11 17:36:03'),
(27, 37, 'Mengunggah laporan baru: Laporan Akhir.', '2026-06-12 06:46:36', '2026-06-12 06:46:36'),
(28, 18, 'Mengunggah sertifikat magang untuk peserta Ni Komang Lia Apriliana.', '2026-06-12 07:44:19', '2026-06-12 07:44:19');;
INSERT INTO "announcements" ("id", "user_id", "judul", "kategori", "isi", "status", "tanggal", "lampiran", "created_at", "updated_at") VALUES
(1, 18, 'Maintenance sistem', 'peserta', 'Saat ini sedang ada perbaikan sistem, sehingga sistem tidak berjalan dengan baik', 'dipublikasikan', '2026-06-13', NULL, '2026-06-13 13:06:50', '2026-06-13 13:06:50'),
(2, 18, 'Maintenance sistem', 'mentor', 'Saat ini sedang ada perbaikan sistem, sehingga sistem tidak berjalan dengan baik', 'dipublikasikan', '2026-06-13', NULL, '2026-06-13 13:14:35', '2026-06-13 13:14:35'),
(3, 18, 'Maintenance sistem', 'pembimbing', 'Saat ini sedang ada perbaikan sistem, sehingga sistem tidak berjalan dengan baik, mohon untuk bersabar', 'dipublikasikan', '2026-06-13', NULL, '2026-06-13 13:14:53', '2026-06-13 16:49:55'),
(4, 18, 'Pengumpulan Laporan Akhir', 'peserta', 'Deadline pengumpulan laporan akhir adalah tanggal 15 Juli 2026', 'draft', '2026-06-14', NULL, '2026-06-13 17:17:33', '2026-06-13 17:17:33');;
INSERT INTO "announcement_user" ("id", "announcement_id", "user_id", "dibaca_pada", "disimpan_pada", "created_at", "updated_at") VALUES
(1, 3, 23, NULL, NULL, '2026-06-13 16:49:55', '2026-06-13 16:49:55'),
(2, 3, 24, NULL, NULL, '2026-06-13 16:49:55', '2026-06-13 16:49:55'),
(3, 3, 25, NULL, NULL, '2026-06-13 16:49:55', '2026-06-13 16:49:55'),
(4, 3, 26, NULL, NULL, '2026-06-13 16:49:55', '2026-06-13 16:49:55'),
(5, 2, 19, NULL, NULL, '2026-06-13 21:01:04', '2026-06-13 21:01:04'),
(6, 2, 20, NULL, NULL, '2026-06-13 21:01:04', '2026-06-13 21:01:04'),
(7, 2, 21, NULL, NULL, '2026-06-13 21:01:04', '2026-06-13 21:01:04'),
(8, 2, 22, NULL, NULL, '2026-06-13 21:01:04', '2026-06-13 21:01:04'),
(9, 2, 38, NULL, NULL, '2026-06-13 21:01:04', '2026-06-13 21:01:04'),
(10, 1, 27, NULL, NULL, '2026-06-14 14:41:16', '2026-06-14 14:41:16'),
(11, 1, 28, NULL, NULL, '2026-06-14 14:41:16', '2026-06-14 14:41:16'),
(12, 1, 29, NULL, NULL, '2026-06-14 14:41:16', '2026-06-14 14:41:16'),
(13, 1, 30, NULL, NULL, '2026-06-14 14:41:16', '2026-06-14 14:41:16'),
(14, 1, 31, NULL, NULL, '2026-06-14 14:41:16', '2026-06-14 14:41:16'),
(15, 1, 32, NULL, NULL, '2026-06-14 14:41:16', '2026-06-14 14:41:16'),
(16, 1, 33, NULL, NULL, '2026-06-14 14:41:16', '2026-06-14 14:41:16'),
(17, 1, 34, NULL, NULL, '2026-06-14 14:41:16', '2026-06-14 14:41:16'),
(18, 1, 35, NULL, NULL, '2026-06-14 14:41:16', '2026-06-14 14:41:16'),
(19, 1, 37, NULL, NULL, '2026-06-14 14:41:16', '2026-06-14 14:41:16');;
INSERT INTO "assessments" ("id", "peserta_id", "mentor_id", "pembimbing_id", "jenis", "periode", "komponen", "bobot", "nilai", "nilai_akhir", "status", "catatan", "created_at", "updated_at") VALUES
(1, 11, 1, NULL, 'mentor', 'Magang Mandiri', '{"presence":0,"activity":100,"report":100,"attitude":90,"competency":90,"grade":"B"}', 100, 76.00, 76.00, 'final', 'Menyusun rancangan wireframe dashboard peserta, memperbarui komponen tabel laporan, dan mendokumentasikan hasil diskusi dengan mentor.', '2026-06-14 14:18:04', '2026-06-14 14:18:08'),
(2, 11, NULL, 2, 'pembimbing', 'Magang Mandiri', '{"presence":88,"activity":100,"report":100,"attitude":88,"competency":93,"grade":"A"}', 100, 94.00, 94.00, 'final', 'Menyusun rancangan wireframe dashboard peserta, memperbarui komponen tabel laporan, dan mendokumentasikan hasil diskusi dengan mentor.', '2026-06-14 21:22:37', '2026-06-14 21:44:50');;
INSERT INTO "assignments" ("id", "peserta_id", "mentor_id", "judul", "deskripsi", "prioritas", "deadline", "status", "progress", "file_hasil", "file_pengumpulan", "submitted_at", "catatan", "created_at", "updated_at") VALUES
(1, 11, 1, 'Tugas minggu pertama', 'inputkan data yang ada di dalam dokumen ini ke excel', 'Administrasi', '2026-06-12', 'selesai', 100, 'assignments/HVj2hkqoTeSgyBQW83O1yPdc23j23V60YdIpJnqH.pdf', 'assignments/submissions/QEDyP4NyCqzo6MiNnzw3Kd8ZClQhIWXZqND1a7dW.pdf', '2026-06-10 21:21:14', 'inputkan data yang ada di dalam dokumen ini ke excel', '2026-06-10 20:35:01', '2026-06-10 21:50:51'),
(2, 11, 1, 'Tugas minggu pertama', 'Hapus kolom nim yang ada pada file ini', 'Administrasi', '2026-06-12', 'selesai', 100, 'assignments/YhO5t2Z9pxM8ErbFT5CpTHctTfGJI4Zea2EenFsO.pdf', 'assignments/submissions/EXmEwwLvwngKkv5Xfwp5rOuiMAK8dMrV6ZrCBU3r.pdf', '2026-06-10 22:18:44', 'Hapus kolom nim yang ada pada file ini', '2026-06-10 22:17:42', '2026-06-10 22:18:44');;
INSERT INTO "attendances" ("id", "peserta_id", "tanggal", "tanggal_selesai", "jam_masuk", "jam_pulang", "status", "durasi_menit", "keterangan", "lampiran", "created_at", "updated_at") VALUES
(1, 1, '2026-06-04', NULL, '08:00:00', '16:00:00', 'hadir', 480, 'Data absensi awal.', NULL, '2026-06-04 09:21:50', '2026-06-04 09:21:50'),
(2, 2, '2026-06-03', NULL, '08:01:00', '16:01:00', 'terlambat', 480, 'Data absensi awal.', NULL, '2026-06-04 09:21:50', '2026-06-04 09:21:50'),
(3, 3, '2026-06-02', NULL, '08:02:00', '16:02:00', 'izin', 480, 'Data absensi awal.', NULL, '2026-06-04 09:21:50', '2026-06-04 09:21:50'),
(4, 4, '2026-06-01', NULL, '08:03:00', '16:03:00', 'tidak_hadir', 480, 'Data absensi awal.', NULL, '2026-06-04 09:21:50', '2026-06-04 09:21:50'),
(5, 5, '2026-05-31', NULL, '08:04:00', '16:04:00', 'hadir', 480, 'Data absensi awal.', NULL, '2026-06-04 09:21:50', '2026-06-04 09:21:50'),
(6, 6, '2026-06-04', NULL, '23:46:31', '23:46:36', 'terlambat', 0, 'Terlambat 931 menit', NULL, '2026-06-04 09:46:31', '2026-06-04 09:46:36'),
(7, 7, '2026-05-30', NULL, '08:05:00', '16:05:00', 'terlambat', 480, 'Data absensi awal.', NULL, '2026-06-04 11:37:31', '2026-06-04 11:37:31'),
(8, 8, '2026-05-29', NULL, '08:00:00', '16:00:00', 'izin', 480, 'Data absensi awal.', NULL, '2026-06-04 11:37:32', '2026-06-04 11:37:32'),
(9, 9, '2026-05-28', NULL, '08:01:00', '16:01:00', 'tidak_hadir', 480, 'Data absensi awal.', NULL, '2026-06-04 11:40:28', '2026-06-04 11:40:28'),
(10, 2, '2026-06-05', '2026-06-05', NULL, NULL, 'sakit', NULL, 'demam', NULL, '2026-06-05 09:37:37', '2026-06-05 09:40:00'),
(11, 1, '2026-06-08', NULL, '08:00:00', '16:00:00', 'hadir', 480, 'Data absensi awal.', NULL, '2026-06-08 09:09:20', '2026-06-08 09:09:20'),
(12, 2, '2026-06-07', NULL, '08:01:00', '16:01:00', 'terlambat', 480, 'Data absensi awal.', NULL, '2026-06-08 09:09:20', '2026-06-08 09:09:20'),
(13, 3, '2026-06-06', NULL, '08:02:00', '16:02:00', 'izin', 480, 'Data absensi awal.', NULL, '2026-06-08 09:09:20', '2026-06-08 09:09:20'),
(14, 4, '2026-06-05', NULL, '08:03:00', '16:03:00', 'tidak_hadir', 480, 'Data absensi awal.', NULL, '2026-06-08 09:09:21', '2026-06-08 09:09:21'),
(15, 5, '2026-06-04', NULL, '08:04:00', '16:04:00', 'hadir', 480, 'Data absensi awal.', NULL, '2026-06-08 09:09:21', '2026-06-08 09:09:21'),
(16, 7, '2026-06-03', NULL, '08:05:00', '16:05:00', 'terlambat', 480, 'Data absensi awal.', NULL, '2026-06-08 09:09:21', '2026-06-08 09:09:21'),
(17, 8, '2026-06-02', NULL, '08:00:00', '16:00:00', 'izin', 480, 'Data absensi awal.', NULL, '2026-06-08 09:09:21', '2026-06-08 09:09:21'),
(18, 9, '2026-06-01', NULL, '08:01:00', '16:01:00', 'tidak_hadir', 480, 'Data absensi awal.', NULL, '2026-06-08 09:09:21', '2026-06-08 09:09:21'),
(19, 11, '2026-06-11', '2026-06-11', NULL, NULL, 'sakit', NULL, 'demam', NULL, '2026-06-10 18:38:30', '2026-06-10 19:11:12');;
INSERT INTO "attendance_recap_reviews" ("id", "peserta_id", "reviewed_by", "status", "catatan", "reviewed_at", "created_at", "updated_at") VALUES
(1, 11, 18, 'divalidasi', 'Rekap absensi telah divalidasi admin.', '2026-06-14 20:40:07', '2026-06-14 20:40:07', '2026-06-14 20:40:07');;
INSERT INTO "certificates" ("id", "peserta_id", "nomor", "jenis", "periode", "predikat", "status", "tanggal_terbit", "file", "created_at", "updated_at") VALUES
(1, 11, 'LLDIKTI-V/MG/2026/00011', 'Magang', 'Magang Mandiri', 'TBA', 'terbit', '2026-06-12', 'sertifikat/11/ni-komang-lia-apriliana-sertifikat-20260612144419.pdf', '2026-06-12 07:44:19', '2026-06-12 07:44:19');;
INSERT INTO "conversations" ("id", "peserta_id", "mentor_id", "pembimbing_id", "admin_id", "topik", "status", "last_message_at", "created_at", "updated_at") VALUES
(1, 2, NULL, 2, NULL, 'Bimbingan - Dewi Lestari', 'aktif', '2026-06-04 12:22:50', '2026-06-04 12:22:50', '2026-06-04 12:22:50'),
(4, 11, NULL, NULL, 18, 'Pesan Baru', 'aktif', '2026-06-11 14:01:27', '2026-06-11 13:22:34', '2026-06-11 14:01:27'),
(7, 11, NULL, 2, NULL, 'Laporan Akhir', 'aktif', '2026-06-13 21:55:09', '2026-06-13 21:55:09', '2026-06-13 21:55:09'),
(8, 11, 1, NULL, NULL, 'Pesan Mentor', 'aktif', '2026-06-14 18:08:35', '2026-06-14 18:08:35', '2026-06-14 18:08:35');;
INSERT INTO "documents" ("id", "user_id", "nama_dokumen", "kategori", "jenis_dokumen", "jenis_file", "file", "ukuran_file", "status", "catatan", "created_at", "updated_at") VALUES
(1, 27, 'Surat Pengantar Magang', 'Administrasi', NULL, 'PDF', 'surat-pengantar-220001.pdf', 512000, 'menunggu', NULL, '2026-06-04 09:21:50', '2026-06-04 09:21:50'),
(2, 28, 'Surat Pengantar Magang', 'Administrasi', NULL, 'PDF', 'surat-pengantar-220002.pdf', 537000, 'disetujui', NULL, '2026-06-04 09:21:50', '2026-06-04 09:21:50'),
(3, 29, 'Surat Pengantar Magang', 'Administrasi', NULL, 'PDF', 'surat-pengantar-220003.pdf', 562000, 'ditolak', 'Dokumen perlu diperbaiki.', '2026-06-04 09:21:50', '2026-06-04 09:21:50'),
(4, 30, 'Surat Pengantar Magang', 'Administrasi', NULL, 'PDF', 'surat-pengantar-220004.pdf', 587000, 'revisi', NULL, '2026-06-04 09:21:50', '2026-06-04 09:21:50'),
(5, 31, 'Surat Pengantar Magang', 'Administrasi', NULL, 'PDF', 'surat-pengantar-220005.pdf', 612000, 'menunggu', NULL, '2026-06-04 09:21:50', '2026-06-04 09:21:50'),
(6, 33, 'Surat Pengantar Magang', 'Administrasi', NULL, 'PDF', 'surat-pengantar-220006.pdf', 637000, 'disetujui', NULL, '2026-06-04 11:37:31', '2026-06-04 11:37:31'),
(7, 34, 'Surat Pengantar Magang', 'Administrasi', NULL, 'PDF', 'surat-pengantar-220007.pdf', 662000, 'ditolak', 'Dokumen perlu diperbaiki.', '2026-06-04 11:37:31', '2026-06-04 11:37:31'),
(8, 35, 'Surat Pengantar Magang', 'Administrasi', NULL, 'PDF', 'surat-pengantar-220008.pdf', 687000, 'revisi', NULL, '2026-06-04 11:40:28', '2026-06-04 11:40:28'),
(9, 27, 'Proposal Magang 220001.pdf', 'Administrasi', 'proposal', 'PDF', 'dokumen-peserta/220001/proposal.pdf', 420000, 'disetujui', NULL, '2026-06-08 09:09:20', '2026-06-08 09:09:20'),
(10, 27, 'KTM 220001.pdf', 'Administrasi', 'ktm', 'PDF', 'dokumen-peserta/220001/ktm.pdf', 432000, 'disetujui', NULL, '2026-06-08 09:09:20', '2026-06-08 09:09:20'),
(11, 27, 'Transkip Nilai 220001.pdf', 'Administrasi', 'transkip', 'PDF', 'dokumen-peserta/220001/transkip.pdf', 435000, 'revisi', 'Perbarui transkip agar lebih jelas.', '2026-06-08 09:09:20', '2026-06-08 09:09:20'),
(12, 27, 'CV Peserta 220001.pdf', 'Administrasi', 'cv', 'PDF', 'dokumen-peserta/220001/cv.pdf', 438000, 'disetujui', NULL, '2026-06-08 09:09:20', '2026-06-08 09:09:20'),
(13, 27, 'Surat Pengantar 220001.pdf', 'Administrasi', 'surat_pengantar', 'PDF', 'dokumen-peserta/220001/surat_pengantar.pdf', 441000, 'menunggu', 'Menunggu validasi tanda tangan.', '2026-06-08 09:09:20', '2026-06-08 09:09:20'),
(14, 28, 'Proposal Magang 220002.pdf', 'Administrasi', 'proposal', 'PDF', 'dokumen-peserta/220002/proposal.pdf', 455000, 'disetujui', NULL, '2026-06-08 09:09:20', '2026-06-08 09:09:20'),
(15, 28, 'KTM 220002.pdf', 'Administrasi', 'ktm', 'PDF', 'dokumen-peserta/220002/ktm.pdf', 467000, 'disetujui', NULL, '2026-06-08 09:09:20', '2026-06-08 09:09:20'),
(16, 28, 'Transkip Nilai 220002.pdf', 'Administrasi', 'transkip', 'PDF', 'dokumen-peserta/220002/transkip.pdf', 470000, 'disetujui', NULL, '2026-06-08 09:09:20', '2026-06-08 09:09:20'),
(17, 28, 'CV Peserta 220002.pdf', 'Administrasi', 'cv', 'PDF', 'dokumen-peserta/220002/cv.pdf', 473000, 'disetujui', NULL, '2026-06-08 09:09:20', '2026-06-08 09:09:20'),
(18, 28, 'Surat Pengantar 220002.pdf', 'Administrasi', 'surat_pengantar', 'PDF', 'dokumen-peserta/220002/surat_pengantar.pdf', 476000, 'disetujui', NULL, '2026-06-08 09:09:20', '2026-06-08 09:09:20'),
(19, 28, 'Sertifikat Pendukung 220002.pdf', 'Administrasi', 'sertifikat_pendukung', 'PDF', 'dokumen-peserta/220002/sertifikat_pendukung.pdf', 479000, 'disetujui', NULL, '2026-06-08 09:09:20', '2026-06-08 09:09:20'),
(20, 29, 'Proposal Magang 220003.pdf', 'Administrasi', 'proposal', 'PDF', 'dokumen-peserta/220003/proposal.pdf', 490000, 'menunggu', 'Menunggu upload versi final.', '2026-06-08 09:09:20', '2026-06-08 09:09:20'),
(21, 29, 'CV Peserta 220003.pdf', 'Administrasi', 'cv', 'PDF', 'dokumen-peserta/220003/cv.pdf', 508000, 'disetujui', NULL, '2026-06-08 09:09:20', '2026-06-08 09:09:20'),
(22, 29, 'Surat Pengantar 220003.pdf', 'Administrasi', 'surat_pengantar', 'PDF', 'dokumen-peserta/220003/surat_pengantar.pdf', 511000, 'ditolak', 'Berkas belum terbaca dengan jelas.', '2026-06-08 09:09:20', '2026-06-08 09:09:20'),
(23, 30, 'Proposal Magang 220004.pdf', 'Administrasi', 'proposal', 'PDF', 'dokumen-peserta/220004/proposal.pdf', 525000, 'disetujui', NULL, '2026-06-08 09:09:21', '2026-06-08 09:09:21'),
(24, 30, 'KTM 220004.pdf', 'Administrasi', 'ktm', 'PDF', 'dokumen-peserta/220004/ktm.pdf', 537000, 'disetujui', NULL, '2026-06-08 09:09:21', '2026-06-08 09:09:21'),
(25, 30, 'Transkip Nilai 220004.pdf', 'Administrasi', 'transkip', 'PDF', 'dokumen-peserta/220004/transkip.pdf', 540000, 'disetujui', NULL, '2026-06-08 09:09:21', '2026-06-08 09:09:21'),
(26, 30, 'CV Peserta 220004.pdf', 'Administrasi', 'cv', 'PDF', 'dokumen-peserta/220004/cv.pdf', 543000, 'disetujui', NULL, '2026-06-08 09:09:21', '2026-06-08 09:09:21'),
(27, 30, 'Surat Pengantar 220004.pdf', 'Administrasi', 'surat_pengantar', 'PDF', 'dokumen-peserta/220004/surat_pengantar.pdf', 546000, 'menunggu', 'Menunggu tanda tangan kampus.', '2026-06-08 09:09:21', '2026-06-08 09:09:21'),
(28, 30, 'Sertifikat Pendukung 220004.pdf', 'Administrasi', 'sertifikat_pendukung', 'PDF', 'dokumen-peserta/220004/sertifikat_pendukung.pdf', 549000, 'revisi', 'Tambahkan sertifikat pendukung terbaru.', '2026-06-08 09:09:21', '2026-06-08 09:09:21'),
(29, 31, 'Proposal Magang 220005.pdf', 'Administrasi', 'proposal', 'PDF', 'dokumen-peserta/220005/proposal.pdf', 560000, 'disetujui', NULL, '2026-06-08 09:09:21', '2026-06-08 09:09:21'),
(30, 31, 'KTM 220005.pdf', 'Administrasi', 'ktm', 'PDF', 'dokumen-peserta/220005/ktm.pdf', 572000, 'menunggu', 'KTM baru belum diunggah.', '2026-06-08 09:09:21', '2026-06-08 09:09:21'),
(31, 33, 'Proposal Magang 220006.pdf', 'Administrasi', 'proposal', 'PDF', 'dokumen-peserta/220006/proposal.pdf', 595000, 'disetujui', NULL, '2026-06-08 09:09:21', '2026-06-08 09:09:21'),
(32, 33, 'CV Peserta 220006.pdf', 'Administrasi', 'cv', 'PDF', 'dokumen-peserta/220006/cv.pdf', 613000, 'disetujui', NULL, '2026-06-08 09:09:21', '2026-06-08 09:09:21'),
(33, 33, 'Surat Pengantar 220006.pdf', 'Administrasi', 'surat_pengantar', 'PDF', 'dokumen-peserta/220006/surat_pengantar.pdf', 616000, 'disetujui', NULL, '2026-06-08 09:09:21', '2026-06-08 09:09:21'),
(34, 33, 'Sertifikat Pendukung 220006.pdf', 'Administrasi', 'sertifikat_pendukung', 'PDF', 'dokumen-peserta/220006/sertifikat_pendukung.pdf', 619000, 'menunggu', 'Sertifikat masih diproses.', '2026-06-08 09:09:21', '2026-06-08 09:09:21'),
(35, 34, 'Proposal Magang 220007.pdf', 'Administrasi', 'proposal', 'PDF', 'dokumen-peserta/220007/proposal.pdf', 630000, 'disetujui', NULL, '2026-06-08 09:09:21', '2026-06-08 09:09:21'),
(36, 34, 'KTM 220007.pdf', 'Administrasi', 'ktm', 'PDF', 'dokumen-peserta/220007/ktm.pdf', 642000, 'disetujui', NULL, '2026-06-08 09:09:21', '2026-06-08 09:09:21'),
(37, 34, 'Transkip Nilai 220007.pdf', 'Administrasi', 'transkip', 'PDF', 'dokumen-peserta/220007/transkip.pdf', 645000, 'disetujui', NULL, '2026-06-08 09:09:21', '2026-06-08 09:09:21'),
(38, 34, 'CV Peserta 220007.pdf', 'Administrasi', 'cv', 'PDF', 'dokumen-peserta/220007/cv.pdf', 648000, 'disetujui', NULL, '2026-06-08 09:09:21', '2026-06-08 09:09:21'),
(39, 34, 'Surat Pengantar 220007.pdf', 'Administrasi', 'surat_pengantar', 'PDF', 'dokumen-peserta/220007/surat_pengantar.pdf', 651000, 'disetujui', NULL, '2026-06-08 09:09:21', '2026-06-08 09:09:21'),
(40, 34, 'Sertifikat Pendukung 220007.pdf', 'Administrasi', 'sertifikat_pendukung', 'PDF', 'dokumen-peserta/220007/sertifikat_pendukung.pdf', 654000, 'disetujui', NULL, '2026-06-08 09:09:21', '2026-06-08 09:09:21'),
(41, 35, 'Proposal Magang 220008.pdf', 'Administrasi', 'proposal', 'PDF', 'dokumen-peserta/220008/proposal.pdf', 665000, 'disetujui', NULL, '2026-06-08 09:09:21', '2026-06-08 09:09:21'),
(42, 35, 'KTM 220008.pdf', 'Administrasi', 'ktm', 'PDF', 'dokumen-peserta/220008/ktm.pdf', 677000, 'disetujui', NULL, '2026-06-08 09:09:21', '2026-06-08 09:09:21'),
(43, 35, 'Transkip Nilai 220008.pdf', 'Administrasi', 'transkip', 'PDF', 'dokumen-peserta/220008/transkip.pdf', 680000, 'disetujui', NULL, '2026-06-08 09:09:21', '2026-06-08 09:09:21'),
(44, 35, 'CV Peserta 220008.pdf', 'Administrasi', 'cv', 'PDF', 'dokumen-peserta/220008/cv.pdf', 683000, 'disetujui', NULL, '2026-06-08 09:09:21', '2026-06-08 09:09:21'),
(51, 37, 'Proposal 22811334013.pdf', 'Dokumen Pendukung', 'proposal', 'PDF', 'dokumen-peserta/37/proposal-37.pdf', 2378200, 'menunggu', NULL, '2026-06-10 16:27:10', '2026-06-10 16:27:10'),
(52, 37, 'KTM 22811334013.pdf', 'Dokumen Pendukung', 'ktm', 'PDF', 'dokumen-peserta/37/ktm-37.pdf', 622445, 'menunggu', NULL, '2026-06-10 16:27:10', '2026-06-10 16:27:10'),
(53, 37, 'Transkip 22811334013.pdf', 'Dokumen Pendukung', 'transkip', 'PDF', 'dokumen-peserta/37/transkip-37.pdf', 150440, 'menunggu', NULL, '2026-06-10 16:27:10', '2026-06-10 16:27:10'),
(54, 37, 'CV 22811334013.pdf', 'Dokumen Pendukung', 'cv', 'PDF', 'dokumen-peserta/37/cv-37.pdf', 242446, 'menunggu', NULL, '2026-06-10 16:27:10', '2026-06-10 16:27:10'),
(55, 37, 'Surat Pengantar 22811334013.pdf', 'Dokumen Pendukung', 'surat_pengantar', 'PDF', 'dokumen-peserta/37/surat_pengantar-37.pdf', 580469, 'menunggu', NULL, '2026-06-10 16:27:10', '2026-06-10 16:27:10'),
(57, 37, 'MoU 22811334013.docx', 'Dokumen Kerja Sama', 'mou', 'DOCX', 'dokumen-kerjasama/37/mou-37.docx', 80406, 'disetujui', 'Disetujui oleh admin.', '2026-06-10 16:36:28', '2026-06-10 18:09:44');;
INSERT INTO "internships" ("id", "peserta_id", "mentor_id", "pembimbing_id", "instansi", "unit_kerja", "posisi", "lokasi", "tanggal_mulai", "tanggal_selesai", "divisi", "status", "deskripsi", "created_at", "updated_at") VALUES
(1, 1, 1, 1, 'Dinas Kominfo DIY', 'Unit Magang', 'Pengembang Aplikasi', 'Yogyakarta', '2026-05-19', '2026-09-08', 'Teknologi Informasi', 'berjalan', 'Penempatan magang peserta pada instansi mitra.', '2026-06-04 09:21:50', '2026-06-08 09:09:20'),
(2, 2, 2, 2, 'PT Inovasi Digital', 'Unit Magang', 'Administrasi Program', 'Yogyakarta', '2026-05-19', '2026-09-08', 'Program', 'berjalan', 'Penempatan magang peserta pada instansi mitra.', '2026-06-04 09:21:50', '2026-06-08 09:09:20'),
(3, 3, 3, 3, 'Bappeda DIY', 'Unit Magang', 'Analis Data', 'Yogyakarta', '2026-05-19', '2026-09-08', 'Data', 'berjalan', 'Penempatan magang peserta pada instansi mitra.', '2026-06-04 09:21:50', '2026-06-08 09:09:20'),
(4, 4, 4, 4, 'Bank BPD DIY', 'Unit Magang', 'Validasi Transaksi', 'Yogyakarta', '2026-05-19', '2026-09-08', 'Operasional', 'berjalan', 'Penempatan magang peserta pada instansi mitra.', '2026-06-04 09:21:50', '2026-06-08 09:09:21'),
(5, 5, 1, 1, 'Dinas Kominfo DIY', 'Unit Magang', 'Pengembang Aplikasi', 'Yogyakarta', '2026-05-19', '2026-09-08', 'Teknologi Informasi', 'berjalan', 'Penempatan magang peserta pada instansi mitra.', '2026-06-04 09:21:50', '2026-06-08 09:09:21'),
(6, 7, 1, 2, 'Dinas Kominfo DIY', 'Unit Magang', 'Administrasi Program', 'Yogyakarta', '2026-04-24', '2026-06-13', 'Program', 'berjalan', 'Penempatan magang peserta pada instansi mitra.', '2026-06-04 11:37:31', '2026-06-08 09:09:21'),
(7, 8, 1, 3, 'Dinas Kominfo DIY', 'Unit Magang', 'Analis Data', 'Yogyakarta', '2026-02-08', '2026-06-06', 'Data', 'selesai', 'Penempatan magang peserta pada instansi mitra.', '2026-06-04 11:37:31', '2026-06-08 09:09:21'),
(8, 9, 1, 4, 'Universitas Negeri Yogyakarta', 'LLDIKTI Wilayah V', 'Media dan Informasi', 'Yogyakarta', '2026-06-03', '2026-07-08', 'Media dan Informasi', 'berjalan', 'Penempatan peserta dibuat melalui menu admin manajemen magang.', '2026-06-04 11:40:28', '2026-06-08 11:35:17'),
(11, 11, 1, 2, 'LLDIKTI Wilayah V Yogyakarta', 'LLDIKTI Wilayah V Yogyakarta', 'Administrasi', 'LLDIKTI Wilayah V Yogyakarta', '2025-06-01', '2025-09-30', 'Administrasi', 'berjalan', 'Penempatan peserta dibuat melalui menu admin manajemen magang.', '2026-06-10 17:07:05', '2026-06-10 17:07:05');;
INSERT INTO "logbooks" ("id", "peserta_id", "tanggal", "kegiatan", "deskripsi", "status", "created_at", "updated_at") VALUES
(1, 1, '2026-06-04', 'Pengembangan Dashboard Monitoring', 'Kegiatan harian peserta magang sesuai penempatan.', 'pending', '2026-06-04 09:21:50', '2026-06-04 09:21:50'),
(2, 2, '2026-06-03', 'Rekapitulasi Program Kerja', 'Kegiatan harian peserta magang sesuai penempatan.', 'approved', '2026-06-04 09:21:50', '2026-06-04 09:21:50'),
(3, 3, '2026-06-02', 'Klasifikasi Dokumen', 'Kegiatan harian peserta magang sesuai penempatan.', 'approved', '2026-06-04 09:21:50', '2026-06-04 09:21:50'),
(4, 4, '2026-06-01', 'Pengujian Modul Aplikasi', 'Kegiatan harian peserta magang sesuai penempatan.', 'rejected', '2026-06-04 09:21:50', '2026-06-04 09:21:50'),
(5, 5, '2026-05-31', 'Pengembangan Dashboard Monitoring', 'Kegiatan harian peserta magang sesuai penempatan.', 'pending', '2026-06-04 09:21:50', '2026-06-04 09:21:50'),
(6, 7, '2026-05-30', 'Rekapitulasi Program Kerja', 'Kegiatan harian peserta magang sesuai penempatan.', 'approved', '2026-06-04 11:37:31', '2026-06-04 11:37:31'),
(7, 8, '2026-05-29', 'Klasifikasi Dokumen', 'Kegiatan harian peserta magang sesuai penempatan.', 'approved', '2026-06-04 11:37:32', '2026-06-04 11:37:32'),
(8, 9, '2026-05-28', 'Pengujian Modul Aplikasi', 'Kegiatan harian peserta magang sesuai penempatan.', 'rejected', '2026-06-04 11:40:28', '2026-06-04 11:40:28'),
(9, 1, '2026-06-08', 'Pengembangan Dashboard Monitoring', 'Kegiatan harian peserta magang sesuai penempatan.', 'pending', '2026-06-08 09:09:20', '2026-06-08 09:09:20'),
(10, 2, '2026-06-07', 'Rekapitulasi Program Kerja', 'Kegiatan harian peserta magang sesuai penempatan.', 'approved', '2026-06-08 09:09:20', '2026-06-08 09:09:20'),
(11, 3, '2026-06-06', 'Klasifikasi Dokumen', 'Kegiatan harian peserta magang sesuai penempatan.', 'approved', '2026-06-08 09:09:20', '2026-06-08 09:09:20'),
(12, 4, '2026-06-05', 'Pengujian Modul Aplikasi', 'Kegiatan harian peserta magang sesuai penempatan.', 'rejected', '2026-06-08 09:09:21', '2026-06-08 09:09:21'),
(13, 5, '2026-06-04', 'Pengembangan Dashboard Monitoring', 'Kegiatan harian peserta magang sesuai penempatan.', 'pending', '2026-06-08 09:09:21', '2026-06-08 09:09:21'),
(14, 7, '2026-06-03', 'Rekapitulasi Program Kerja', 'Kegiatan harian peserta magang sesuai penempatan.', 'approved', '2026-06-08 09:09:21', '2026-06-08 09:09:21'),
(15, 8, '2026-06-02', 'Klasifikasi Dokumen', 'Kegiatan harian peserta magang sesuai penempatan.', 'approved', '2026-06-08 09:09:21', '2026-06-08 09:09:21'),
(16, 9, '2026-06-01', 'Pengujian Modul Aplikasi', 'Kegiatan harian peserta magang sesuai penempatan.', 'rejected', '2026-06-08 09:09:21', '2026-06-08 09:09:21');;
INSERT INTO "mentors" ("id", "user_id", "nip", "jenis_kelamin", "jabatan", "divisi", "no_hp", "alamat", "instansi", "created_at", "updated_at") VALUES
(1, 19, '197901001', 'Laki-laki', 'Supervisor TI', 'Teknologi Informasi', '08123456001', 'DI Yogyakarta', 'Dinas Kominfo DIY', '2026-06-04 09:21:49', '2026-06-04 09:21:49'),
(2, 20, '197902001', 'Perempuan', 'Lead Developer', 'Developer\r\n', '08123456002', 'DI Yogyakarta', 'PT Inovasi Digital', '2026-06-04 09:21:49', '2026-06-04 09:21:49'),
(3, 21, '197903001', 'Laki-laki', 'Analis Program', 'Teknologi Informasi', '08123456003', 'DI Yogyakarta', 'Bappeda DIY', '2026-06-04 09:21:49', '2026-06-04 09:21:49'),
(4, 22, '197904001', 'Perempuan', 'HR Officer', 'Officer', '08123456004', 'DI Yogyakarta', 'Bank BPD DIY', '2026-06-04 09:21:49', '2026-06-04 09:21:49'),
(5, 38, '199004102019032009', 'Perempuan', 'Kepala bidang', 'Administrasi', '085841802803', 'Jl. Kenanga Raya NO.12 Perumhan Griya sri, Blok c1', 'LLDIKTI Wilayah V Yogyakarta', '2026-06-13 14:02:46', '2026-06-13 14:02:46');;
INSERT INTO "messages" ("id", "conversation_id", "sender_id", "pesan", "lampiran", "dibaca_pada", "created_at", "updated_at") VALUES
(1, 1, 24, 'lanjutkan kegiatan magang dengan baik', NULL, NULL, '2026-06-04 12:22:50', '2026-06-04 12:22:50'),
(2, 4, 18, 'upload sertifikn pendukung', NULL, '2026-06-14 09:24:32', '2026-06-11 13:22:34', '2026-06-14 09:24:32'),
(3, 4, 18, 'upload sertifikn pendukung', NULL, '2026-06-14 09:24:32', '2026-06-11 13:22:39', '2026-06-14 09:24:32'),
(4, 4, 37, 'baik pak akan saya kirimkan', NULL, '2026-06-14 14:40:46', '2026-06-11 14:01:27', '2026-06-14 14:40:46'),
(11, 7, 24, 'Selesaikan dengan benar laporan akhirnya', NULL, '2026-06-14 09:24:31', '2026-06-13 21:55:09', '2026-06-14 09:24:31');;
INSERT INTO "migrations" ("id", "migration", "batch") VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2026_05_19_184017_create_perguruan_tinggi_table', 1),
(6, '2026_05_19_184110_create_pesertas_table', 1),
(7, '2026_05_19_184149_create_mentors_table', 1),
(8, '2026_05_19_184223_create_pembimbings_table', 1),
(9, '2026_05_19_184250_create_interships_table', 1),
(10, '2026_05_19_184323_create_documents_table', 1),
(11, '2026_05_19_184352_create_verifications_table', 1),
(12, '2026_05_19_184422_create_logbooks_table', 1),
(13, '2026_05_19_184456_create_reports_table', 1),
(14, '2026_05_19_184527_create_activities_table', 1),
(15, '2026_05_19_184614_create_notifications_table', 1),
(16, '2026_05_19_203812_add_role_to_users_table', 1),
(17, '2026_05_24_000001_add_registration_fields_for_pembimbing', 1),
(18, '2026_05_24_000002_add_account_verification_fields_to_users_table', 1),
(19, '2026_05_24_000002_add_registration_fields_for_peserta', 1),
(20, '2026_05_24_000003_add_registration_fields_for_mentor', 1),
(21, '2026_05_29_000001_complete_peserta_feature_tables', 1),
(22, '2026_05_29_000002_extend_internships_for_placement_details', 1),
(23, '2026_06_04_000001_add_pic_to_perguruan_tinggi_table', 1),
(24, '2026_06_04_000002_add_academic_and_pic_details_to_perguruan_tinggi_table', 1),
(25, '2026_06_04_000003_add_jenis_to_perguruan_tinggi_table', 1),
(26, '2026_06_04_000004_add_absensi_to_notification_preferences_table', 1),
(27, '2026_06_04_000005_add_request_fields_to_attendances_table', 1),
(28, '2026_06_08_000001_drop_verifications_table', 2),
(29, '2026_06_08_000002_create_verification_histories_table', 2),
(30, '2026_06_08_000003_add_jenis_dokumen_to_documents_table', 2),
(31, '2026_06_10_000001_add_report_review_fields', 3),
(32, '2026_06_11_000001_add_submission_fields_to_assignments_table', 4),
(33, '2026_06_12_000001_add_admin_approved_at_to_reports_table', 5),
(34, '2026_06_12_000002_add_pembimbing_review_status_to_reports_table', 5),
(35, '2026_06_13_000001_add_phone_and_address_to_users_table', 5),
(36, '2026_06_13_150000_create_attendance_recap_reviews_table', 6),
(37, '2026_06_13_000003_add_status_kerja_sama_to_perguruan_tinggi_table', 7),
(38, '2026_06_14_000001_create_access_roles_table', 7),
(39, '2026_06_14_000002_add_security_flags_to_users_table', 8);;
INSERT INTO "notifications" ("id", "user_id", "judul", "pesan", "dibaca", "created_at", "updated_at") VALUES
(1, 19, 'Laporan peserta perlu review', 'Logbook Harian dari Aulia Berliana baru saja dikirim.', 1, '2026-06-09 10:11:47', '2026-06-14 14:15:47'),
(2, 23, 'Laporan peserta perlu review', 'Logbook Harian dari Aulia Berliana baru saja dikirim.', 0, '2026-06-09 10:11:47', '2026-06-09 10:11:47'),
(3, 19, 'Laporan peserta perlu review', 'Logbook Harian dari Aulia Berliana baru saja dikirim.', 1, '2026-06-09 10:15:03', '2026-06-14 14:15:47'),
(4, 23, 'Laporan peserta perlu review', 'Logbook Harian dari Aulia Berliana baru saja dikirim.', 0, '2026-06-09 10:15:03', '2026-06-09 10:15:03'),
(5, 19, 'Laporan peserta perlu review', 'Logbook Harian dari Aulia Berliana baru saja dikirim.', 1, '2026-06-09 10:16:46', '2026-06-14 14:15:47'),
(6, 23, 'Laporan peserta perlu review', 'Logbook Harian dari Aulia Berliana baru saja dikirim.', 0, '2026-06-09 10:16:46', '2026-06-09 10:16:46'),
(7, 19, 'Laporan peserta perlu review', 'L dari Aulia Berliana baru saja dikirim.', 1, '2026-06-09 10:17:38', '2026-06-14 14:15:47'),
(8, 23, 'Laporan peserta perlu review', 'L dari Aulia Berliana baru saja dikirim.', 0, '2026-06-09 10:17:38', '2026-06-09 10:17:38'),
(9, 19, 'Laporan peserta perlu review', 'L dari Aulia Berliana baru saja dikirim.', 1, '2026-06-09 10:20:09', '2026-06-14 14:15:47'),
(10, 23, 'Laporan peserta perlu review', 'L dari Aulia Berliana baru saja dikirim.', 0, '2026-06-09 10:20:09', '2026-06-09 10:20:09'),
(11, 37, 'Dokumen kerja sama diperbarui', 'Dokumen kerja sama "MoU 22811334013.docx" telah disetujui oleh admin.', 1, '2026-06-10 18:09:44', '2026-06-14 09:52:04'),
(12, 37, 'Penugasan baru diterima', 'Anda menerima penugasan baru: Tugas minggu pertama', 1, '2026-06-10 20:35:01', '2026-06-14 09:52:04'),
(13, 19, 'Hasil penugasan peserta dikirim', 'Ni Komang Lia Apriliana sudah mengunggah hasil penugasan "Tugas minggu pertama".', 1, '2026-06-10 21:21:14', '2026-06-14 14:15:47'),
(14, 37, 'Penugasan berhasil dikirim', 'File hasil penugasan "Tugas minggu pertama" sudah tersimpan di database.', 1, '2026-06-10 21:21:14', '2026-06-14 09:52:04'),
(15, 37, 'Penugasan diperbarui', 'Penugasan Tugas minggu pertama sudah diperbarui oleh mentor.', 1, '2026-06-10 21:50:51', '2026-06-14 09:52:04'),
(16, 37, 'Penugasan baru diterima', 'Anda menerima penugasan baru: Tugas minggu pertama', 1, '2026-06-10 22:17:42', '2026-06-14 09:52:04'),
(17, 19, 'Hasil penugasan peserta dikirim', 'Ni Komang Lia Apriliana sudah mengunggah hasil penugasan "Tugas minggu pertama".', 1, '2026-06-10 22:18:44', '2026-06-14 14:15:47'),
(18, 37, 'Penugasan berhasil dikirim', 'File hasil penugasan "Tugas minggu pertama" sudah tersimpan di database.', 1, '2026-06-10 22:18:44', '2026-06-14 09:52:04'),
(19, 37, 'Pesan baru dari Super Admin', 'upload sertifikn pendukung', 1, '2026-06-11 13:22:34', '2026-06-14 09:52:04'),
(20, 37, 'Pesan baru dari Super Admin', 'upload sertifikn pendukung', 1, '2026-06-11 13:22:40', '2026-06-14 09:52:04'),
(21, 18, 'Pesan baru dari Ni Komang Lia Apriliana', 'baik pak akan saya kirimkan', 1, '2026-06-11 14:01:27', '2026-06-14 14:40:51'),
(22, 20, 'Pesan baru dari Super Admin', 'lanjutkan memonitoring', 0, '2026-06-11 14:15:31', '2026-06-11 14:15:31'),
(23, 19, 'Pesan baru dari Super Admin', 'p', 1, '2026-06-11 14:15:55', '2026-06-14 14:15:47'),
(24, 19, 'Pesan baru dari Budi Santoso', 'q', 1, '2026-06-11 14:21:59', '2026-06-14 14:15:47'),
(25, 19, 'Laporan peserta perlu review', 'Laporan Mingguan 9 dari Ni Komang Lia Apriliana baru saja dikirim.', 1, '2026-06-11 17:36:03', '2026-06-14 14:15:47'),
(26, 24, 'Laporan peserta perlu review', 'Laporan Mingguan 9 dari Ni Komang Lia Apriliana baru saja dikirim.', 1, '2026-06-11 17:36:03', '2026-06-14 21:32:47'),
(27, 37, 'Review mentor diperbarui', 'Laporan Laporan Mingguan 9 sudah mendapat catatan dari mentor.', 1, '2026-06-11 17:38:12', '2026-06-14 09:52:04'),
(28, 19, 'Pesan baru dari Super Admin', 'lanjutkan monitoring dengan peserta', 1, '2026-06-12 05:16:16', '2026-06-14 14:15:47'),
(29, 19, 'Laporan peserta perlu review', 'Laporan Akhir dari Ni Komang Lia Apriliana baru saja dikirim.', 1, '2026-06-12 06:46:36', '2026-06-14 14:15:47'),
(30, 24, 'Laporan peserta perlu review', 'Laporan Akhir dari Ni Komang Lia Apriliana baru saja dikirim.', 1, '2026-06-12 06:46:36', '2026-06-14 21:32:47'),
(31, 37, 'Review mentor diperbarui', 'Laporan Laporan Akhir sudah mendapat catatan dari mentor.', 1, '2026-06-12 06:47:40', '2026-06-14 09:52:04'),
(32, 37, 'Review pembimbing akademik diperbarui', 'Laporan Laporan Akhir sudah mendapat catatan dari pembimbing akademik.', 1, '2026-06-12 07:19:35', '2026-06-14 09:52:04'),
(33, 37, 'Sertifikat magang diterbitkan', 'Sertifikat magang Anda sudah diunggah oleh admin dan siap diunduh.', 1, '2026-06-12 07:44:19', '2026-06-14 09:52:04'),
(34, 37, 'Review mentor diperbarui', 'Laporan Laporan Mingguan 9 sudah mendapat catatan dari mentor.', 1, '2026-06-12 08:51:04', '2026-06-14 09:52:04'),
(35, 37, 'Review pembimbing akademik diperbarui', 'Laporan Laporan Mingguan 9 sudah mendapat catatan dari pembimbing akademik.', 1, '2026-06-12 09:38:40', '2026-06-14 09:52:04'),
(36, 19, 'Pesan baru dari Budi Santoso', 'baik', 1, '2026-06-13 19:00:34', '2026-06-14 14:15:47'),
(37, 19, 'Pesan baru dari Budi Santoso', 'apakah ada kendala dengan sistem?', 1, '2026-06-13 19:06:47', '2026-06-14 14:15:47'),
(38, 24, 'Pesan baru dari Dr. Hendra Wijaya', 'Selesaikan dengan benar laporan akhirnya', 1, '2026-06-13 21:55:09', '2026-06-14 21:32:47'),
(39, 27, 'Review laporan mentor diperbarui', 'Laporan L telah mendapat catatan dari mentor.', 0, '2026-06-14 17:04:05', '2026-06-14 17:04:05'),
(40, 37, 'Review laporan mentor diperbarui', 'Laporan Laporan Akhir telah mendapat catatan dari mentor.', 1, '2026-06-14 18:18:08', '2026-06-14 22:24:41'),
(41, 37, 'Review laporan mentor diperbarui', 'Laporan Laporan Akhir telah mendapat catatan dari mentor.', 1, '2026-06-14 18:18:20', '2026-06-14 22:24:41'),
(42, 37, 'Review pembimbing akademik diperbarui', 'Laporan Laporan Akhir sudah mendapat catatan dari pembimbing akademik.', 1, '2026-06-14 22:22:26', '2026-06-14 22:24:41'),
(43, 37, 'Review pembimbing akademik diperbarui', 'Laporan Laporan Mingguan 9 sudah mendapat catatan dari pembimbing akademik.', 1, '2026-06-14 22:22:34', '2026-06-14 22:24:41');;
INSERT INTO "pembimbings" ("id", "user_id", "nidn_nip", "tempat_lahir", "tanggal_lahir", "jenis_kelamin", "instansi", "jabatan", "no_hp", "alamat", "perguruan_tinggi", "program_studi", "created_at", "updated_at") VALUES
(1, 23, '0500000001', 'Yogyakarta', '1984-06-08', 'Perempuan', 'Universitas Gadjah Mada', 'Dosen Pembimbing', '08223456001', 'DI Yogyakarta', 'Universitas Gadjah Mada', 'Informatika', '2026-06-04 09:21:49', '2026-06-08 09:09:20'),
(2, 24, '0500000002', 'Yogyakarta', '1983-06-08', 'Laki-laki', 'Universitas Atma Jaya Yogyakarta', 'Dosen Pembimbing', '08223456002', 'DI Yogyakarta', 'Universitas Atma Jaya Yogyakarta', 'Manajemen', '2026-06-04 09:21:49', '2026-06-08 09:09:20'),
(3, 25, '0500000003', 'Yogyakarta', '1982-06-08', 'Perempuan', 'Universitas Sanata Dharma', 'Dosen Pembimbing', '08223456003', 'DI Yogyakarta', 'Universitas Sanata Dharma', 'Administrasi Publik', '2026-06-04 09:21:49', '2026-06-08 09:09:20'),
(4, 26, '0500000004', 'Yogyakarta', '1981-06-08', 'Laki-laki', 'Universitas Negeri Yogyakarta', 'Dosen Pembimbing', '08223456004', 'DI Yogyakarta', 'Universitas Negeri Yogyakarta', 'Akuntansi', '2026-06-04 09:21:50', '2026-06-08 09:09:20');;
INSERT INTO "perguruan_tinggi" ("id", "nama_pt", "jenis", "status_kerja_sama", "pic", "pic_nip", "alamat", "telepon", "email", "fakultas", "program_studi", "logo", "created_at", "updated_at") VALUES
(1, 'Universitas Negeri Yogyakarta', 'Negeri', 'aktif', 'Dr. Rina Wulandari', '198001012005012001', 'DI Yogyakarta', '0274-000000', 'admin@uny.ac.id', 'Fakultas Teknik', 'Informatika', NULL, '2026-06-04 09:21:49', '2026-06-04 09:21:49'),
(2, 'Universitas Gadjah Mada', 'Negeri', 'aktif', 'Dr. Citra Maharani', '198202022006042002', 'DI Yogyakarta', '0274-000000', 'admin@ugm.ac.id', 'Fakultas Ekonomika dan Bisnis', 'Manajemen', NULL, '2026-06-04 09:21:49', '2026-06-04 09:21:49'),
(3, 'Universitas Ahmad Dahlan', 'Swasta', 'aktif', 'Dian Purnama, M.T.', '198303032007032003', 'DI Yogyakarta', '0274-000000', 'admin@uad.ac.id', 'Fakultas Teknologi Industri', 'Informatika', NULL, '2026-06-04 09:21:49', '2026-06-14 21:19:26'),
(4, 'Universitas Sanata Dharma', 'Swasta', 'aktif', 'Kartika Sari, M.Kom.', '198404042008042004', 'DI Yogyakarta', '0274-000000', 'admin@usd.ac.id', 'Fakultas Sains dan Teknologi', 'Sistem Informasi', NULL, '2026-06-04 09:21:49', '2026-06-04 09:21:49'),
(5, 'Universitas Atma Jaya Yogyakarta', 'Swasta', 'aktif', 'Dr. Hendra Wijaya', '198505052009051005', 'DI Yogyakarta', '0274-000000', 'admin@uajy.ac.id', 'Fakultas Bisnis dan Ekonomika', 'Akuntansi', NULL, '2026-06-04 09:21:49', '2026-06-04 09:21:49'),
(6, 'Universitas Teknologi Yogyakarta', NULL, 'aktif', NULL, NULL, 'Jl. Kenanga Raya NO.12 Perumhan Griya sri, Blok c1', '082176543210', 'novita@gmail.com', NULL, NULL, NULL, '2026-06-04 09:43:26', '2026-06-04 09:43:26');;
INSERT INTO "pesertas" ("id", "user_id", "perguruan_tinggi_id", "nim", "tempat_lahir", "tanggal_lahir", "jenis_kelamin", "jurusan", "fakultas", "program_magang", "pembimbing_akademik", "tanggal_mulai_magang", "tanggal_selesai_magang", "semester", "no_hp", "alamat", "status", "created_at", "updated_at") VALUES
(1, 27, 1, '220001', 'Yogyakarta', '2005-06-08', 'Perempuan', 'Informatika', 'Fakultas Teknik', 'Batch 1 2026', 'Dr. Citra Maharani', '2026-05-19', '2026-09-08', '6', '08345678001', 'DI Yogyakarta', 'aktif', '2026-06-04 09:21:50', '2026-06-08 09:09:20'),
(2, 28, 3, '220002', 'Yogyakarta', '2005-05-08', 'Laki-laki', 'Manajemen', 'Fakultas Ekonomi', 'Batch 2 2026', 'Dr. Hendra Wijaya', '2026-05-19', '2026-09-08', '7', '08345678002', 'DI Yogyakarta', 'aktif', '2026-06-04 09:21:50', '2026-06-08 09:09:20'),
(3, 29, 4, '220003', 'Yogyakarta', '2005-04-08', 'Perempuan', 'Administrasi Publik', 'Fakultas Teknik', 'Batch 1 2026', 'Kartika Sari, M.Kom.', '2026-05-19', '2026-09-08', '6', '08345678003', 'DI Yogyakarta', 'aktif', '2026-06-04 09:21:50', '2026-06-08 09:09:20'),
(4, 30, 2, '220004', 'Yogyakarta', '2005-03-08', 'Laki-laki', 'Informatika', 'Fakultas Ekonomi', 'Batch 2 2026', 'Prof. Raden Prakoso', '2026-05-19', '2026-09-08', '7', '08345678004', 'DI Yogyakarta', 'aktif', '2026-06-04 09:21:50', '2026-06-08 09:09:21'),
(5, 31, 5, '220005', 'Yogyakarta', '2005-02-08', 'Perempuan', 'Akuntansi', 'Fakultas Teknik', 'Batch 1 2026', 'Dr. Citra Maharani', '2026-05-19', '2026-09-08', '6', '08345678005', 'DI Yogyakarta', 'aktif', '2026-06-04 09:21:50', '2026-06-08 09:09:21'),
(6, 32, 6, '22103050045', 'Bantul', '2004-08-22', 'Perempuan', 'Sistem Informasi', 'Fakultas Sains dan Teknologi', 'Magang MBKM', 'Dr. Budi Santoso, M.Kom.', '2026-05-27', '2026-07-10', '-', '082176543210', 'Jl. Kenanga Raya NO.12 Perumhan Griya sri, Blok c1', 'pending', '2026-06-04 09:43:26', '2026-06-04 09:43:26'),
(7, 33, 2, '220006', 'Yogyakarta', '2005-01-08', 'Laki-laki', 'Informatika', 'Fakultas Ekonomi', 'Batch 2 2026', 'Dr. Hendra Wijaya', '2026-04-24', '2026-06-13', '7', '08345678006', 'DI Yogyakarta', 'aktif', '2026-06-04 11:37:31', '2026-06-08 09:09:21'),
(8, 34, 3, '220007', 'Yogyakarta', '2004-12-08', 'Perempuan', 'Manajemen', 'Fakultas Teknik', 'Batch 1 2026', 'Kartika Sari, M.Kom.', '2026-02-08', '2026-06-06', '6', '08345678007', 'DI Yogyakarta', 'selesai', '2026-06-04 11:37:31', '2026-06-08 09:09:21'),
(9, 35, 1, '220008', 'Yogyakarta', '2004-11-08', 'Laki-laki', 'Informatika', 'Fakultas Ekonomi', 'Batch 2 2026', 'Prof. Raden Prakoso', '2026-06-03', '2026-07-08', '7', '08345678008', 'DI Yogyakarta', 'aktif', '2026-06-04 11:40:28', '2026-06-08 09:09:21'),
(11, 37, 1, '22811334013', 'Lampung', '2004-04-09', 'Perempuan', 'Administrasi Perkantoram', 'Vokasi', 'Magang Mandiri', 'Dr. Hendra Wijaya', '2025-06-01', '2025-09-30', '-', '089518433234', 'Bratasena  Mandiri, Lampung', 'pending', '2026-06-10 16:22:52', '2026-06-10 16:22:52');;
INSERT INTO "reports" ("id", "peserta_id", "judul", "jenis", "periode", "durasi_jam", "file", "status", "catatan", "catatan_mentor", "catatan_pembimbing", "pembimbing_review_status", "reviewer_id", "admin_approved_at", "created_at", "updated_at") VALUES
(1, 1, 'Laporan Magang 220001', 'berkala', 'Batch 1 2026', NULL, 'laporan-220001.pdf', 'pending', NULL, NULL, NULL, 'menunggu review', 18, NULL, '2026-06-04 09:21:50', '2026-06-04 09:21:50'),
(2, 2, 'Laporan Magang 220002', 'berkala', 'Batch 2 2026', NULL, 'laporan-220002.pdf', 'approved', NULL, NULL, NULL, 'menunggu review', 18, NULL, '2026-06-04 09:21:50', '2026-06-04 09:21:50'),
(3, 3, 'Laporan Magang 220003', 'berkala', 'Batch 1 2026', NULL, 'laporan-220003.pdf', 'revisi', NULL, NULL, NULL, 'menunggu review', 18, NULL, '2026-06-04 09:21:50', '2026-06-04 09:21:50'),
(4, 4, 'Laporan Magang 220004', 'berkala', 'Batch 2 2026', NULL, 'laporan-220004.pdf', 'rejected', NULL, NULL, NULL, 'menunggu review', 18, NULL, '2026-06-04 09:21:50', '2026-06-04 09:21:50'),
(5, 5, 'Laporan Magang 220005', 'berkala', 'Batch 1 2026', NULL, 'laporan-220005.pdf', 'pending', NULL, NULL, NULL, 'menunggu review', 18, NULL, '2026-06-04 09:21:50', '2026-06-04 09:21:50'),
(6, 7, 'Laporan Magang 220006', 'berkala', 'Batch 2 2026', NULL, 'laporan-220006.pdf', 'approved', NULL, NULL, NULL, 'menunggu review', 18, NULL, '2026-06-04 11:37:31', '2026-06-04 11:37:31'),
(7, 8, 'Laporan Magang 220007', 'berkala', 'Batch 1 2026', NULL, 'laporan-220007.pdf', 'revisi', NULL, NULL, NULL, 'menunggu review', 18, NULL, '2026-06-04 11:37:31', '2026-06-04 11:37:31'),
(8, 9, 'Laporan Magang 220008', 'berkala', 'Batch 2 2026', NULL, 'laporan-220008.pdf', 'rejected', NULL, NULL, NULL, 'menunggu review', 18, NULL, '2026-06-04 11:40:28', '2026-06-04 11:40:28'),
(9, 1, 'Logbook Harian', 'berkala', 'Bacth 1 2026', NULL, 'laporan-peserta/27/logbook-harian-27.pdf', 'pending', 'Menyusun rancangan wireframe dashboard peserta, memperbarui komponen tabel laporan, dan mendokumentasikan hasil diskusi dengan mentor.', NULL, NULL, 'menunggu review', 19, NULL, '2026-06-09 09:36:40', '2026-06-09 10:15:03'),
(10, 1, 'L', 'berkala', 'Bacth 1 2026', 4, 'laporan-peserta/27/l-27.pdf', 'approved', 'Menyusun rancangan wireframe dashboard peserta, memperbarui komponen tabel laporan, dan mendokumentasikan hasil diskusi dengan mentor.', 'Menyusun rancangan wireframe dashboard peserta, memperbarui komponen tabel laporan, dan mendokumentasikan hasil diskusi dengan mentor.', NULL, 'menunggu review', 19, NULL, '2026-06-09 10:17:38', '2026-06-14 17:04:05'),
(11, 11, 'Laporan Mingguan 9', 'berkala', '27 Mei-2 Juni 2026', 4, 'laporan-peserta/37/laporan-mingguan-9-37.docx', 'approved', 'Menyusun rancangan wireframe dashboard peserta, memperbarui komponen tabel laporan, dan mendokumentasikan hasil diskusi dengan mentor.', 'Sudah sangat bagus lanjutkan untuk laporan lainnya', 'Menyusun rancangan wireframe dashboard peserta, memperbarui komponen tabel laporan, dan mendokumentasikan hasil diskusi dengan mentor.', 'disetujui', 19, NULL, '2026-06-11 17:36:03', '2026-06-14 22:22:34'),
(12, 11, 'Laporan Akhir', 'akhir', 'Magang Mandiri', NULL, 'laporan-peserta/37/laporan-akhir-37.docx', 'approved', 'Menyusun rancangan wireframe dashboard peserta, memperbarui komponen tabel laporan, dan mendokumentasikan hasil diskusi dengan mentor.', 'Menyusun rancangan wireframe dashboard peserta', 'sudah bagus semuanya', 'disetujui', 19, '2026-06-14 20:28:58', '2026-06-12 06:46:36', '2026-06-14 22:22:26');;
INSERT INTO "security_activities" ("id", "user_id", "aktivitas", "perangkat", "browser", "ip_address", "status", "catatan", "created_at", "updated_at") VALUES
(1, 19, 'Memperbarui profil mentor.', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '127.0.0.1', 'berhasil', 'Identitas: Muhammad Budi Santoso | Username: budi | Email: budi@kominfo.go.id | Nip: 197901001 | Jabatan: Supervisor TI | Divisi: Teknologi Informasi | No hp: 08123456001 | Alamat: DI Yogyakarta | Instansi: Dinas Kominfo DIY', '2026-06-14 19:14:56', '2026-06-14 19:14:56'),
(2, 18, 'Memperbarui profil akun admin.', 'Desktop', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '127.0.0.1', 'berhasil', 'Nama, username, email, telepon, dan alamat diperbarui.', '2026-06-14 21:02:16', '2026-06-14 21:02:16');;
INSERT INTO "users" ("id", "name", "username", "email", "password", "role", "account_status", "rejection_reason", "verified_at", "verified_by", "foto", "phone", "address", "is_online", "two_factor_enabled", "password_changed_at", "remember_token", "created_at", "updated_at") VALUES
(18, 'Super Admin', 'superadminn', 'admin@gmail.com', '$2y$10$i2W5DFDuGa8jm7qyZU5WZOxqo5aNZg9l85wVKgsQAjfckEFVkbtMO', 'super_admin', 'disetujui', NULL, '2026-06-08 09:09:19', NULL, 'foto-admin/7Xw5S98TJRkOZjCcrUWFQUgMfR0qG2asrMOKirdu.png', NULL, NULL, 0, 0, NULL, NULL, '2026-06-04 09:21:49', '2026-06-14 21:02:16'),
(19, 'Muhammad Budi Santoso', 'budi', 'budi@kominfo.go.id', '$2y$10$AQGJbafJ6TDOUgSlWgObuulW9m.8PXQXR8ccPPCRBpx5YhFtALp5q', 'mentor', 'disetujui', NULL, '2026-06-08 09:09:20', 18, 'foto-mentor/Jx4z4IStKUo4cWekehPJsf0vdf9iEN8jFEd6h7qB.png', NULL, NULL, 0, 0, NULL, NULL, '2026-06-04 09:21:49', '2026-06-14 18:52:03'),
(20, 'Fajar Nugroho', 'fajar', 'fajar@inovasi.id', '$2y$10$df/uU1mr7e8gxrktgRw1dOni8OUycHpPvfDUz7zoEdWVtNHoPuu7i', 'mentor', 'nonaktif', NULL, '2026-06-08 09:09:20', 18, NULL, NULL, NULL, 0, 0, NULL, NULL, '2026-06-04 09:21:49', '2026-06-08 09:09:20'),
(21, 'Joko Firmansyah', 'joko', 'joko@bappeda.go.id', '$2y$10$k2SqJipEUa5geSzZTx51ful73o95oRxvtEg49AUF6Yrt6WCWZZTMe', 'mentor', 'disetujui', NULL, '2026-06-08 09:09:20', 18, NULL, NULL, NULL, 0, 0, NULL, NULL, '2026-06-04 09:21:49', '2026-06-08 09:09:20'),
(22, 'Mira Handayani', 'mira', 'mira@bpddiy.co.id', '$2y$10$W/fJ1e/4sISLDF.gKbhp0.oyi/bYltlfVjscuXOqj78ADK2nVzTKC', 'mentor', 'menunggu', 'institusi khusus untuk mahasiswa dari perguruan tinggi', NULL, 18, NULL, NULL, NULL, 0, 0, NULL, NULL, '2026-06-04 09:21:49', '2026-06-08 09:09:20'),
(23, 'Dr. Citra Maharani', 'citra', 'citra@ugm.ac.id', '$2y$10$rNlU4nROWq6OFM.apeplFOM4j3wtMW22uzZnZX3kozOolCINdmss6', 'pembimbing', 'menunggu', NULL, NULL, 18, NULL, NULL, NULL, 0, 0, NULL, NULL, '2026-06-04 09:21:49', '2026-06-08 09:09:20'),
(24, 'Dr. Hendra Wijaya', 'hendra', 'hendra@uajy.ac.id', '$2y$10$N9XcmwXWBqVAhWvYukH0getCWZzyRGjv91kYzADOtSzSnS0o4siBm', 'pembimbing', 'disetujui', NULL, '2026-06-08 09:09:20', 18, 'foto-pembimbing/cAZbBHF2tcjkLK5BMv3lAno2iTvL1LZ6LCcRknTc.png', NULL, NULL, 0, 0, NULL, NULL, '2026-06-04 09:21:49', '2026-06-14 21:53:43'),
(25, 'Kartika Sari, M.Kom.', 'kartika', 'kartika@usd.ac.id', '$2y$10$GRPxir.jnCHfm2vnwWmva.NJ/x8i6FfiQGDZMpVakvgT2CabdQNc6', 'pembimbing', 'nonaktif', NULL, '2026-06-08 09:09:20', 18, NULL, NULL, NULL, 0, 0, NULL, NULL, '2026-06-04 09:21:49', '2026-06-08 09:09:20'),
(26, 'Prof. Raden Prakoso', 'raden', 'raden@uny.ac.id', '$2y$10$61fc2XC/aP9dPzPf25hcwerWbMKjDvu1mXE8TddrOWG87MVH1Lc1O', 'pembimbing', 'disetujui', NULL, '2026-06-08 09:09:20', 18, NULL, NULL, NULL, 0, 0, NULL, NULL, '2026-06-04 09:21:50', '2026-06-08 09:09:20'),
(27, 'Aulia Berliana', 'aulia', 'aulia@email.com', '$2y$10$HBnSlyjIrdPS6eWpV0GhHeEXhSwf09hRQbzFG6tM5MThZIqvhGcJG', 'peserta', 'disetujui', NULL, '2026-06-08 09:09:20', 18, NULL, NULL, NULL, 0, 0, NULL, NULL, '2026-06-04 09:21:50', '2026-06-08 09:09:20'),
(28, 'Dewi Lestari', 'dewi', 'dewi@email.com', '$2y$10$0zVCePqTENxwHysgj3hOiOR09DXgpfvcLLNPph9ljMGycZujYo4oy', 'peserta', 'disetujui', NULL, '2026-06-08 09:44:50', 18, NULL, NULL, NULL, 0, 0, NULL, NULL, '2026-06-04 09:21:50', '2026-06-08 09:44:50'),
(29, 'Gita Permata', 'gita', 'gita@email.com', '$2y$10$Kp.Di16mbERRH29mB64o4e7opsC5pXjuQ6/r7gg//8s5kp5P5zNd6', 'peserta', 'ditolak', NULL, '2026-06-08 09:09:20', 18, NULL, NULL, NULL, 0, 0, NULL, NULL, '2026-06-04 09:21:50', '2026-06-08 09:09:20'),
(30, 'Intan Safitri', 'intan', 'intan@email.com', '$2y$10$XiVq8rAr/HsR00genBdX4.vm1NrNLibs0/rWqhq8Kl2RTabtJJAiq', 'peserta', 'menunggu', NULL, NULL, 18, NULL, NULL, NULL, 0, 0, NULL, NULL, '2026-06-04 09:21:50', '2026-06-08 09:09:21'),
(31, 'Lukman Hakim', 'lukman', 'lukman@email.com', '$2y$10$4arqtzVw21uHmQglA.m0V.HvH1UJ.tAPYCVZEWieC1fVhG6NCfPgu', 'peserta', 'disetujui', NULL, '2026-06-08 09:09:21', 18, NULL, NULL, NULL, 0, 0, NULL, NULL, '2026-06-04 09:21:50', '2026-06-08 09:09:21'),
(32, 'Novita Sari', 'peserta_novitasari', 'novita@gmail.com', '$2y$10$f9N.uoG2wrKs0hQV8dRMY.STsca7U5utnVhf0e4BIHzShVCKGolTW', 'peserta', 'disetujui', NULL, '2026-06-04 09:44:32', 18, NULL, NULL, NULL, 0, 0, NULL, NULL, '2026-06-04 09:43:26', '2026-06-04 09:44:32'),
(33, 'Salsa Maulina', 'salsa', 'salsa@email.com', '$2y$10$n1dkB0DQnn4ubQRDuoEy3.25XTrcW1xjq1higyQ6k9jtZuhDpTuPW', 'peserta', 'disetujui', NULL, '2026-06-08 09:09:21', 18, NULL, NULL, NULL, 0, 0, NULL, NULL, '2026-06-04 11:37:31', '2026-06-08 09:09:21'),
(34, 'Raka Pratama', 'raka', 'raka@email.com', '$2y$10$0CUamRbTbYB7vP22.wraP.SywL3UScVK0UDoAdAAw76ldUXfVQHqe', 'peserta', 'disetujui', NULL, '2026-06-14 20:56:58', 18, NULL, NULL, NULL, 0, 0, NULL, NULL, '2026-06-04 11:37:31', '2026-06-14 20:56:58'),
(35, 'Nabila Putri', 'nabila', 'nabila@email.com', '$2y$10$KckPcDUWOEQksAldBC6reuae4pIlsIpcgFK4IstjIPCPAV8bZs3Pu', 'peserta', 'disetujui', NULL, '2026-06-13 16:43:32', 18, NULL, NULL, NULL, 0, 0, NULL, NULL, '2026-06-04 11:40:28', '2026-06-13 16:43:32'),
(37, 'Ni Komang Lia Apriliana', 'peserta_KomangLia', 'komanglia190@gmail.com', '$2y$10$sdqBwBmLCZUj2lfGaQG/oOOnnwSd2Cg0fQBEyESLf2zFqJpI.vWK6', 'peserta', 'disetujui', NULL, '2026-06-10 16:23:20', 18, 'foto-peserta/2PqWKqlhPyLm605CEmgIukEqsWlb5Si9Vk3K6fBj.jpg', NULL, NULL, 0, 0, NULL, NULL, '2026-06-10 16:22:52', '2026-06-10 16:23:20'),
(38, 'Mutiara Cantika', 'pa_mutiaraa', 'mutiara@gmail.com', '$2y$10$c.PU4k387wRDAUkK.QTO9OJHe1ucMRKsDlwNMibqFFZcVj3TdlkmC', 'mentor', 'disetujui', NULL, '2026-06-14 15:15:43', 18, 'foto-mentor/bWQpRqTQpmHcOsLQPBACyyYwVuu6Y0zS6yGoqD38.png', NULL, NULL, 0, 0, NULL, NULL, '2026-06-13 14:02:46', '2026-06-14 15:15:43');;
INSERT INTO "verification_histories" ("id", "user_id", "admin_id", "jenis", "status", "keterangan", "verified_at", "created_at", "updated_at") VALUES
(2, 37, 18, 'akun', 'disetujui', 'Akun Ni Komang Lia Apriliana disetujui melalui menu verifikasi akun.', '2026-06-10 16:23:20', '2026-06-10 16:23:20', '2026-06-10 16:23:20'),
(3, 38, 18, 'akun', 'disetujui', 'Akun mentor baru Mutiara Cantika berhasil dibuat oleh admin.', '2026-06-13 14:02:46', '2026-06-13 14:02:46', '2026-06-13 14:02:46');;

-- Indexes
CREATE INDEX "activities_user_id_foreign" ON "activities" ("user_id");
CREATE INDEX "announcements_user_id_foreign" ON "announcements" ("user_id");
CREATE INDEX "announcement_user_user_id_foreign" ON "announcement_user" ("user_id");
CREATE INDEX "assessments_peserta_id_foreign" ON "assessments" ("peserta_id");
CREATE INDEX "assessments_mentor_id_foreign" ON "assessments" ("mentor_id");
CREATE INDEX "assessments_pembimbing_id_foreign" ON "assessments" ("pembimbing_id");
CREATE INDEX "assignments_peserta_id_foreign" ON "assignments" ("peserta_id");
CREATE INDEX "assignments_mentor_id_foreign" ON "assignments" ("mentor_id");
CREATE INDEX "attendances_peserta_id_foreign" ON "attendances" ("peserta_id");
CREATE INDEX "attendance_recap_reviews_reviewed_by_foreign" ON "attendance_recap_reviews" ("reviewed_by");
CREATE INDEX "certificates_peserta_id_foreign" ON "certificates" ("peserta_id");
CREATE INDEX "conversations_peserta_id_foreign" ON "conversations" ("peserta_id");
CREATE INDEX "conversations_mentor_id_foreign" ON "conversations" ("mentor_id");
CREATE INDEX "conversations_pembimbing_id_foreign" ON "conversations" ("pembimbing_id");
CREATE INDEX "conversations_admin_id_foreign" ON "conversations" ("admin_id");
CREATE INDEX "internships_peserta_id_foreign" ON "internships" ("peserta_id");
CREATE INDEX "internships_mentor_id_foreign" ON "internships" ("mentor_id");
CREATE INDEX "internships_pembimbing_id_foreign" ON "internships" ("pembimbing_id");
CREATE INDEX "logbooks_peserta_id_foreign" ON "logbooks" ("peserta_id");
CREATE INDEX "mentors_user_id_foreign" ON "mentors" ("user_id");
CREATE INDEX "messages_conversation_id_foreign" ON "messages" ("conversation_id");
CREATE INDEX "messages_sender_id_foreign" ON "messages" ("sender_id");
CREATE INDEX "notifications_user_id_foreign" ON "notifications" ("user_id");
CREATE INDEX "pembimbings_user_id_foreign" ON "pembimbings" ("user_id");
CREATE INDEX "personal_access_tokens_tokenable_type_tokenable_id_index" ON "personal_access_tokens" ("tokenable_type","tokenable_id");
CREATE INDEX "pesertas_user_id_foreign" ON "pesertas" ("user_id");
CREATE INDEX "pesertas_perguruan_tinggi_id_foreign" ON "pesertas" ("perguruan_tinggi_id");
CREATE INDEX "reports_peserta_id_foreign" ON "reports" ("peserta_id");
CREATE INDEX "reports_reviewer_id_foreign" ON "reports" ("reviewer_id");
CREATE INDEX "security_activities_user_id_foreign" ON "security_activities" ("user_id");
CREATE INDEX "users_verified_by_foreign" ON "users" ("verified_by");
CREATE INDEX "verification_histories_user_id_foreign" ON "verification_histories" ("user_id");
CREATE INDEX "verification_histories_admin_id_foreign" ON "verification_histories" ("admin_id");

-- Foreign keys
ALTER TABLE "activities" ADD CONSTRAINT "activities_user_id_foreign" FOREIGN KEY ("user_id") REFERENCES "users" ("id") ON DELETE CASCADE;
ALTER TABLE "announcements" ADD CONSTRAINT "announcements_user_id_foreign" FOREIGN KEY ("user_id") REFERENCES "users" ("id") ON DELETE SET NULL;
ALTER TABLE "announcement_user" ADD CONSTRAINT "announcement_user_announcement_id_foreign" FOREIGN KEY ("announcement_id") REFERENCES "announcements" ("id") ON DELETE CASCADE;
ALTER TABLE "announcement_user" ADD CONSTRAINT "announcement_user_user_id_foreign" FOREIGN KEY ("user_id") REFERENCES "users" ("id") ON DELETE CASCADE;
ALTER TABLE "assessments" ADD CONSTRAINT "assessments_mentor_id_foreign" FOREIGN KEY ("mentor_id") REFERENCES "mentors" ("id") ON DELETE SET NULL;
ALTER TABLE "assessments" ADD CONSTRAINT "assessments_pembimbing_id_foreign" FOREIGN KEY ("pembimbing_id") REFERENCES "pembimbings" ("id") ON DELETE SET NULL;
ALTER TABLE "assessments" ADD CONSTRAINT "assessments_peserta_id_foreign" FOREIGN KEY ("peserta_id") REFERENCES "pesertas" ("id") ON DELETE CASCADE;
ALTER TABLE "assignments" ADD CONSTRAINT "assignments_mentor_id_foreign" FOREIGN KEY ("mentor_id") REFERENCES "mentors" ("id") ON DELETE SET NULL;
ALTER TABLE "assignments" ADD CONSTRAINT "assignments_peserta_id_foreign" FOREIGN KEY ("peserta_id") REFERENCES "pesertas" ("id") ON DELETE CASCADE;
ALTER TABLE "attendances" ADD CONSTRAINT "attendances_peserta_id_foreign" FOREIGN KEY ("peserta_id") REFERENCES "pesertas" ("id") ON DELETE CASCADE;
ALTER TABLE "attendance_recap_reviews" ADD CONSTRAINT "attendance_recap_reviews_peserta_id_foreign" FOREIGN KEY ("peserta_id") REFERENCES "pesertas" ("id") ON DELETE CASCADE;
ALTER TABLE "attendance_recap_reviews" ADD CONSTRAINT "attendance_recap_reviews_reviewed_by_foreign" FOREIGN KEY ("reviewed_by") REFERENCES "users" ("id") ON DELETE SET NULL;
ALTER TABLE "certificates" ADD CONSTRAINT "certificates_peserta_id_foreign" FOREIGN KEY ("peserta_id") REFERENCES "pesertas" ("id") ON DELETE CASCADE;
ALTER TABLE "conversations" ADD CONSTRAINT "conversations_admin_id_foreign" FOREIGN KEY ("admin_id") REFERENCES "users" ("id") ON DELETE SET NULL;
ALTER TABLE "conversations" ADD CONSTRAINT "conversations_mentor_id_foreign" FOREIGN KEY ("mentor_id") REFERENCES "mentors" ("id") ON DELETE SET NULL;
ALTER TABLE "conversations" ADD CONSTRAINT "conversations_pembimbing_id_foreign" FOREIGN KEY ("pembimbing_id") REFERENCES "pembimbings" ("id") ON DELETE SET NULL;
ALTER TABLE "conversations" ADD CONSTRAINT "conversations_peserta_id_foreign" FOREIGN KEY ("peserta_id") REFERENCES "pesertas" ("id") ON DELETE CASCADE;
ALTER TABLE "documents" ADD CONSTRAINT "documents_user_id_foreign" FOREIGN KEY ("user_id") REFERENCES "users" ("id") ON DELETE CASCADE;
ALTER TABLE "internships" ADD CONSTRAINT "internships_mentor_id_foreign" FOREIGN KEY ("mentor_id") REFERENCES "mentors" ("id") ON DELETE CASCADE;
ALTER TABLE "internships" ADD CONSTRAINT "internships_pembimbing_id_foreign" FOREIGN KEY ("pembimbing_id") REFERENCES "pembimbings" ("id") ON DELETE CASCADE;
ALTER TABLE "internships" ADD CONSTRAINT "internships_peserta_id_foreign" FOREIGN KEY ("peserta_id") REFERENCES "pesertas" ("id") ON DELETE CASCADE;
ALTER TABLE "logbooks" ADD CONSTRAINT "logbooks_peserta_id_foreign" FOREIGN KEY ("peserta_id") REFERENCES "pesertas" ("id") ON DELETE CASCADE;
ALTER TABLE "mentors" ADD CONSTRAINT "mentors_user_id_foreign" FOREIGN KEY ("user_id") REFERENCES "users" ("id") ON DELETE CASCADE;
ALTER TABLE "messages" ADD CONSTRAINT "messages_conversation_id_foreign" FOREIGN KEY ("conversation_id") REFERENCES "conversations" ("id") ON DELETE CASCADE;
ALTER TABLE "messages" ADD CONSTRAINT "messages_sender_id_foreign" FOREIGN KEY ("sender_id") REFERENCES "users" ("id") ON DELETE CASCADE;
ALTER TABLE "notifications" ADD CONSTRAINT "notifications_user_id_foreign" FOREIGN KEY ("user_id") REFERENCES "users" ("id") ON DELETE CASCADE;
ALTER TABLE "notification_preferences" ADD CONSTRAINT "notification_preferences_user_id_foreign" FOREIGN KEY ("user_id") REFERENCES "users" ("id") ON DELETE CASCADE;
ALTER TABLE "pembimbings" ADD CONSTRAINT "pembimbings_user_id_foreign" FOREIGN KEY ("user_id") REFERENCES "users" ("id") ON DELETE CASCADE;
ALTER TABLE "pesertas" ADD CONSTRAINT "pesertas_perguruan_tinggi_id_foreign" FOREIGN KEY ("perguruan_tinggi_id") REFERENCES "perguruan_tinggi" ("id") ON DELETE CASCADE;
ALTER TABLE "pesertas" ADD CONSTRAINT "pesertas_user_id_foreign" FOREIGN KEY ("user_id") REFERENCES "users" ("id") ON DELETE CASCADE;
ALTER TABLE "reports" ADD CONSTRAINT "reports_peserta_id_foreign" FOREIGN KEY ("peserta_id") REFERENCES "pesertas" ("id") ON DELETE CASCADE;
ALTER TABLE "reports" ADD CONSTRAINT "reports_reviewer_id_foreign" FOREIGN KEY ("reviewer_id") REFERENCES "users" ("id") ON DELETE SET NULL;
ALTER TABLE "security_activities" ADD CONSTRAINT "security_activities_user_id_foreign" FOREIGN KEY ("user_id") REFERENCES "users" ("id") ON DELETE CASCADE;
ALTER TABLE "users" ADD CONSTRAINT "users_verified_by_foreign" FOREIGN KEY ("verified_by") REFERENCES "users" ("id") ON DELETE SET NULL;
ALTER TABLE "verification_histories" ADD CONSTRAINT "verification_histories_admin_id_foreign" FOREIGN KEY ("admin_id") REFERENCES "users" ("id") ON DELETE SET NULL;
ALTER TABLE "verification_histories" ADD CONSTRAINT "verification_histories_user_id_foreign" FOREIGN KEY ("user_id") REFERENCES "users" ("id") ON DELETE CASCADE;

-- Reset sequences after importing explicit id values
SELECT setval(pg_get_serial_sequence('"access_roles"', 'id'), COALESCE((SELECT MAX("id") FROM "access_roles"), 1), true);
SELECT setval(pg_get_serial_sequence('"activities"', 'id'), COALESCE((SELECT MAX("id") FROM "activities"), 1), true);
SELECT setval(pg_get_serial_sequence('"announcements"', 'id'), COALESCE((SELECT MAX("id") FROM "announcements"), 1), true);
SELECT setval(pg_get_serial_sequence('"announcement_user"', 'id'), COALESCE((SELECT MAX("id") FROM "announcement_user"), 1), true);
SELECT setval(pg_get_serial_sequence('"assessments"', 'id'), COALESCE((SELECT MAX("id") FROM "assessments"), 1), true);
SELECT setval(pg_get_serial_sequence('"assignments"', 'id'), COALESCE((SELECT MAX("id") FROM "assignments"), 1), true);
SELECT setval(pg_get_serial_sequence('"attendances"', 'id'), COALESCE((SELECT MAX("id") FROM "attendances"), 1), true);
SELECT setval(pg_get_serial_sequence('"attendance_recap_reviews"', 'id'), COALESCE((SELECT MAX("id") FROM "attendance_recap_reviews"), 1), true);
SELECT setval(pg_get_serial_sequence('"certificates"', 'id'), COALESCE((SELECT MAX("id") FROM "certificates"), 1), true);
SELECT setval(pg_get_serial_sequence('"conversations"', 'id'), COALESCE((SELECT MAX("id") FROM "conversations"), 1), true);
SELECT setval(pg_get_serial_sequence('"documents"', 'id'), COALESCE((SELECT MAX("id") FROM "documents"), 1), true);
SELECT setval(pg_get_serial_sequence('"failed_jobs"', 'id'), COALESCE((SELECT MAX("id") FROM "failed_jobs"), 1), true);
SELECT setval(pg_get_serial_sequence('"internships"', 'id'), COALESCE((SELECT MAX("id") FROM "internships"), 1), true);
SELECT setval(pg_get_serial_sequence('"logbooks"', 'id'), COALESCE((SELECT MAX("id") FROM "logbooks"), 1), true);
SELECT setval(pg_get_serial_sequence('"mentors"', 'id'), COALESCE((SELECT MAX("id") FROM "mentors"), 1), true);
SELECT setval(pg_get_serial_sequence('"messages"', 'id'), COALESCE((SELECT MAX("id") FROM "messages"), 1), true);
SELECT setval(pg_get_serial_sequence('"migrations"', 'id'), COALESCE((SELECT MAX("id") FROM "migrations"), 1), true);
SELECT setval(pg_get_serial_sequence('"notifications"', 'id'), COALESCE((SELECT MAX("id") FROM "notifications"), 1), true);
SELECT setval(pg_get_serial_sequence('"notification_preferences"', 'id'), COALESCE((SELECT MAX("id") FROM "notification_preferences"), 1), true);
SELECT setval(pg_get_serial_sequence('"pembimbings"', 'id'), COALESCE((SELECT MAX("id") FROM "pembimbings"), 1), true);
SELECT setval(pg_get_serial_sequence('"perguruan_tinggi"', 'id'), COALESCE((SELECT MAX("id") FROM "perguruan_tinggi"), 1), true);
SELECT setval(pg_get_serial_sequence('"personal_access_tokens"', 'id'), COALESCE((SELECT MAX("id") FROM "personal_access_tokens"), 1), true);
SELECT setval(pg_get_serial_sequence('"pesertas"', 'id'), COALESCE((SELECT MAX("id") FROM "pesertas"), 1), true);
SELECT setval(pg_get_serial_sequence('"reports"', 'id'), COALESCE((SELECT MAX("id") FROM "reports"), 1), true);
SELECT setval(pg_get_serial_sequence('"security_activities"', 'id'), COALESCE((SELECT MAX("id") FROM "security_activities"), 1), true);
SELECT setval(pg_get_serial_sequence('"users"', 'id'), COALESCE((SELECT MAX("id") FROM "users"), 1), true);
SELECT setval(pg_get_serial_sequence('"verification_histories"', 'id'), COALESCE((SELECT MAX("id") FROM "verification_histories"), 1), true);
COMMIT;