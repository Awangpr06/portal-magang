<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('perguruan_tinggi', function (Blueprint $table) {
            if (! Schema::hasColumn('perguruan_tinggi', 'status_kerja_sama')) {
                $table->string('status_kerja_sama')->default('aktif')->after('jenis');
            }
        });

        DB::table('perguruan_tinggi')
            ->whereNull('status_kerja_sama')
            ->update(['status_kerja_sama' => 'aktif']);
    }

    public function down(): void
    {
        Schema::table('perguruan_tinggi', function (Blueprint $table) {
            if (Schema::hasColumn('perguruan_tinggi', 'status_kerja_sama')) {
                $table->dropColumn('status_kerja_sama');
            }
        });
    }
};
