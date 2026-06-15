<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('perguruan_tinggi', function (Blueprint $table) {
            $table->string('pic')->nullable()->after('nama_pt');
        });
    }

    public function down(): void
    {
        Schema::table('perguruan_tinggi', function (Blueprint $table) {
            $table->dropColumn('pic');
        });
    }
};
