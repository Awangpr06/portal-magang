<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('internships', 'instansi')) {
            Schema::table('internships', function (Blueprint $table) {
                $table->string('instansi')->nullable()->after('pembimbing_id');
            });
        }

        if (! Schema::hasColumn('internships', 'unit_kerja')) {
            Schema::table('internships', function (Blueprint $table) {
                $table->string('unit_kerja')->nullable()->after('instansi');
            });
        }

        if (! Schema::hasColumn('internships', 'posisi')) {
            Schema::table('internships', function (Blueprint $table) {
                $table->string('posisi')->nullable()->after('unit_kerja');
            });
        }

        if (! Schema::hasColumn('internships', 'lokasi')) {
            Schema::table('internships', function (Blueprint $table) {
                $table->string('lokasi')->nullable()->after('posisi');
            });
        }

        if (! Schema::hasColumn('internships', 'deskripsi')) {
            Schema::table('internships', function (Blueprint $table) {
                $table->text('deskripsi')->nullable()->after('status');
            });
        }
    }

    public function down(): void
    {
        $columns = array_values(array_filter([
            Schema::hasColumn('internships', 'instansi') ? 'instansi' : null,
            Schema::hasColumn('internships', 'unit_kerja') ? 'unit_kerja' : null,
            Schema::hasColumn('internships', 'posisi') ? 'posisi' : null,
            Schema::hasColumn('internships', 'lokasi') ? 'lokasi' : null,
            Schema::hasColumn('internships', 'deskripsi') ? 'deskripsi' : null,
        ]));

        if ($columns !== []) {
            Schema::table('internships', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }
};
