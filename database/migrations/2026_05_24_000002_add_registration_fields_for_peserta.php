<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesertas', function (Blueprint $table) {
            if (! Schema::hasColumn('pesertas', 'tempat_lahir')) {
                $table->string('tempat_lahir')->nullable()->after('nim');
            }

            if (! Schema::hasColumn('pesertas', 'tanggal_lahir')) {
                $table->date('tanggal_lahir')->nullable()->after('tempat_lahir');
            }

            if (! Schema::hasColumn('pesertas', 'jenis_kelamin')) {
                $table->string('jenis_kelamin')->nullable()->after('tanggal_lahir');
            }

            if (! Schema::hasColumn('pesertas', 'fakultas')) {
                $table->string('fakultas')->nullable()->after('jurusan');
            }

            if (! Schema::hasColumn('pesertas', 'program_magang')) {
                $table->string('program_magang')->nullable()->after('fakultas');
            }

            if (! Schema::hasColumn('pesertas', 'pembimbing_akademik')) {
                $table->string('pembimbing_akademik')->nullable()->after('program_magang');
            }

            if (! Schema::hasColumn('pesertas', 'tanggal_mulai_magang')) {
                $table->date('tanggal_mulai_magang')->nullable()->after('pembimbing_akademik');
            }

            if (! Schema::hasColumn('pesertas', 'tanggal_selesai_magang')) {
                $table->date('tanggal_selesai_magang')->nullable()->after('tanggal_mulai_magang');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pesertas', function (Blueprint $table) {
            foreach ([
                'tanggal_selesai_magang',
                'tanggal_mulai_magang',
                'pembimbing_akademik',
                'program_magang',
                'fakultas',
                'jenis_kelamin',
                'tanggal_lahir',
                'tempat_lahir',
            ] as $column) {
                if (Schema::hasColumn('pesertas', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
