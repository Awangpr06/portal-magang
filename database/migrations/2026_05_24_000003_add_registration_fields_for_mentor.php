<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mentors', function (Blueprint $table) {
            if (! Schema::hasColumn('mentors', 'nip')) {
                $table->string('nip')->nullable()->after('user_id');
            }

            if (! Schema::hasColumn('mentors', 'jenis_kelamin')) {
                $table->string('jenis_kelamin')->nullable()->after('nip');
            }

            if (! Schema::hasColumn('mentors', 'alamat')) {
                $table->text('alamat')->nullable()->after('no_hp');
            }

            if (! Schema::hasColumn('mentors', 'perguruan_tinggi')) {
                $table->string('perguruan_tinggi')->nullable()->after('alamat');
            }
        });
    }

    public function down(): void
    {
        Schema::table('mentors', function (Blueprint $table) {
            foreach (['perguruan_tinggi', 'alamat', 'jenis_kelamin', 'nip'] as $column) {
                if (Schema::hasColumn('mentors', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
