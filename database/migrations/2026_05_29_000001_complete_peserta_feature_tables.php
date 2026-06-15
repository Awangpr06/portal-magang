<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->extendReports();
        $this->extendDocuments();

        if (! Schema::hasTable('attendances')) {
            Schema::create('attendances', function (Blueprint $table) {
                $table->id();
                $table->foreignId('peserta_id')->constrained('pesertas')->cascadeOnDelete();
                $table->date('tanggal');
                $table->time('jam_masuk')->nullable();
                $table->time('jam_pulang')->nullable();
                $table->string('status')->default('hadir');
                $table->integer('durasi_menit')->nullable();
                $table->text('keterangan')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('assignments')) {
            Schema::create('assignments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('peserta_id')->constrained('pesertas')->cascadeOnDelete();
                $table->foreignId('mentor_id')->nullable()->constrained('mentors')->nullOnDelete();
                $table->string('judul');
                $table->text('deskripsi')->nullable();
                $table->string('prioritas')->default('normal');
                $table->date('deadline')->nullable();
                $table->string('status')->default('belum_dikerjakan');
                $table->unsignedTinyInteger('progress')->default(0);
                $table->string('file_hasil')->nullable();
                $table->text('catatan')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('assessments')) {
            Schema::create('assessments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('peserta_id')->constrained('pesertas')->cascadeOnDelete();
                $table->foreignId('mentor_id')->nullable()->constrained('mentors')->nullOnDelete();
                $table->foreignId('pembimbing_id')->nullable()->constrained('pembimbings')->nullOnDelete();
                $table->string('jenis');
                $table->string('periode')->nullable();
                $table->string('komponen');
                $table->unsignedTinyInteger('bobot')->default(0);
                $table->decimal('nilai', 5, 2)->default(0);
                $table->decimal('nilai_akhir', 5, 2)->default(0);
                $table->string('status')->default('draft');
                $table->text('catatan')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('certificates')) {
            Schema::create('certificates', function (Blueprint $table) {
                $table->id();
                $table->foreignId('peserta_id')->constrained('pesertas')->cascadeOnDelete();
                $table->string('nomor')->unique();
                $table->string('jenis')->default('Magang');
                $table->string('periode')->nullable();
                $table->string('predikat')->nullable();
                $table->string('status')->default('menunggu');
                $table->date('tanggal_terbit')->nullable();
                $table->string('file')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('conversations')) {
            Schema::create('conversations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('peserta_id')->constrained('pesertas')->cascadeOnDelete();
                $table->foreignId('mentor_id')->nullable()->constrained('mentors')->nullOnDelete();
                $table->foreignId('pembimbing_id')->nullable()->constrained('pembimbings')->nullOnDelete();
                $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('topik');
                $table->string('status')->default('aktif');
                $table->timestamp('last_message_at')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('messages')) {
            Schema::create('messages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('conversation_id')->constrained('conversations')->cascadeOnDelete();
                $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
                $table->text('pesan');
                $table->string('lampiran')->nullable();
                $table->timestamp('dibaca_pada')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('announcements')) {
            Schema::create('announcements', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('judul');
                $table->string('kategori')->default('Umum');
                $table->text('isi');
                $table->string('status')->default('aktif');
                $table->date('tanggal')->nullable();
                $table->string('lampiran')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('announcement_user')) {
            Schema::create('announcement_user', function (Blueprint $table) {
                $table->id();
                $table->foreignId('announcement_id')->constrained('announcements')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->timestamp('dibaca_pada')->nullable();
                $table->timestamp('disimpan_pada')->nullable();
                $table->timestamps();
                $table->unique(['announcement_id', 'user_id']);
            });
        }

        if (! Schema::hasTable('security_activities')) {
            Schema::create('security_activities', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('aktivitas');
                $table->string('perangkat')->nullable();
                $table->string('browser')->nullable();
                $table->string('ip_address')->nullable();
                $table->string('status')->default('berhasil');
                $table->text('catatan')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('notification_preferences')) {
            Schema::create('notification_preferences', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->boolean('pesan')->default(true);
                $table->boolean('laporan')->default(true);
                $table->boolean('penugasan')->default(true);
                $table->boolean('pengumuman')->default(true);
                $table->boolean('email')->default(false);
                $table->timestamps();
                $table->unique('user_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_preferences');
        Schema::dropIfExists('security_activities');
        Schema::dropIfExists('announcement_user');
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('conversations');
        Schema::dropIfExists('certificates');
        Schema::dropIfExists('assessments');
        Schema::dropIfExists('assignments');
        Schema::dropIfExists('attendances');

        $this->rollbackDocuments();
        $this->rollbackReports();
    }

    private function extendReports(): void
    {
        if (! Schema::hasColumn('reports', 'jenis')) {
            Schema::table('reports', function (Blueprint $table) {
                $table->string('jenis')->default('berkala')->after('judul');
            });
        }

        if (! Schema::hasColumn('reports', 'periode')) {
            Schema::table('reports', function (Blueprint $table) {
                $table->string('periode')->nullable()->after('jenis');
            });
        }

        if (! Schema::hasColumn('reports', 'durasi_jam')) {
            Schema::table('reports', function (Blueprint $table) {
                $table->unsignedTinyInteger('durasi_jam')->nullable()->after('periode');
            });
        }

        if (! Schema::hasColumn('reports', 'catatan')) {
            Schema::table('reports', function (Blueprint $table) {
                $table->text('catatan')->nullable()->after('status');
            });
        }

        if (! Schema::hasColumn('reports', 'catatan_mentor')) {
            Schema::table('reports', function (Blueprint $table) {
                $table->text('catatan_mentor')->nullable()->after('catatan');
            });
        }

        if (! Schema::hasColumn('reports', 'catatan_pembimbing')) {
            Schema::table('reports', function (Blueprint $table) {
                $table->text('catatan_pembimbing')->nullable()->after('catatan_mentor');
            });
        }

        if (! Schema::hasColumn('reports', 'reviewer_id')) {
            Schema::table('reports', function (Blueprint $table) {
                $table->foreignId('reviewer_id')->nullable()->after('catatan')->constrained('users')->nullOnDelete();
            });
        }
    }

    private function extendDocuments(): void
    {
        if (! Schema::hasColumn('documents', 'jenis_file')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->string('jenis_file')->nullable()->after('kategori');
            });
        }

        if (! Schema::hasColumn('documents', 'ukuran_file')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->unsignedBigInteger('ukuran_file')->nullable()->after('file');
            });
        }

        if (! Schema::hasColumn('documents', 'catatan')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->text('catatan')->nullable()->after('status');
            });
        }
    }

    private function rollbackReports(): void
    {
        if (! Schema::hasTable('reports')) {
            return;
        }

        if (Schema::hasColumn('reports', 'reviewer_id')) {
            Schema::table('reports', function (Blueprint $table) {
                $table->dropConstrainedForeignId('reviewer_id');
            });
        }

        $columns = array_values(array_filter([
            Schema::hasColumn('reports', 'jenis') ? 'jenis' : null,
            Schema::hasColumn('reports', 'periode') ? 'periode' : null,
            Schema::hasColumn('reports', 'durasi_jam') ? 'durasi_jam' : null,
            Schema::hasColumn('reports', 'catatan') ? 'catatan' : null,
            Schema::hasColumn('reports', 'catatan_mentor') ? 'catatan_mentor' : null,
            Schema::hasColumn('reports', 'catatan_pembimbing') ? 'catatan_pembimbing' : null,
        ]));

        if ($columns !== []) {
            Schema::table('reports', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }

    private function rollbackDocuments(): void
    {
        if (! Schema::hasTable('documents')) {
            return;
        }

        $columns = array_values(array_filter([
            Schema::hasColumn('documents', 'jenis_file') ? 'jenis_file' : null,
            Schema::hasColumn('documents', 'ukuran_file') ? 'ukuran_file' : null,
            Schema::hasColumn('documents', 'catatan') ? 'catatan' : null,
        ]));

        if ($columns !== []) {
            Schema::table('documents', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }
};
