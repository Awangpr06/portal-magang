<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'username')) {
                $table->string('username')->nullable()->unique()->after('name');
            }
        });

        Schema::table('pembimbings', function (Blueprint $table) {
            if (! Schema::hasColumn('pembimbings', 'nidn_nip')) {
                $table->string('nidn_nip')->nullable()->after('user_id');
            }

            if (! Schema::hasColumn('pembimbings', 'tempat_lahir')) {
                $table->string('tempat_lahir')->nullable()->after('nidn_nip');
            }

            if (! Schema::hasColumn('pembimbings', 'tanggal_lahir')) {
                $table->date('tanggal_lahir')->nullable()->after('tempat_lahir');
            }

            if (! Schema::hasColumn('pembimbings', 'jenis_kelamin')) {
                $table->string('jenis_kelamin')->nullable()->after('tanggal_lahir');
            }

            if (! Schema::hasColumn('pembimbings', 'alamat')) {
                $table->text('alamat')->nullable()->after('no_hp');
            }

            if (! Schema::hasColumn('pembimbings', 'perguruan_tinggi')) {
                $table->string('perguruan_tinggi')->nullable()->after('alamat');
            }

            if (! Schema::hasColumn('pembimbings', 'program_studi')) {
                $table->string('program_studi')->nullable()->after('perguruan_tinggi');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pembimbings', function (Blueprint $table) {
            foreach (['program_studi', 'perguruan_tinggi', 'alamat', 'jenis_kelamin', 'tanggal_lahir', 'tempat_lahir', 'nidn_nip'] as $column) {
                if (Schema::hasColumn('pembimbings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'username')) {
                $table->dropColumn('username');
            }
        });
    }
};
