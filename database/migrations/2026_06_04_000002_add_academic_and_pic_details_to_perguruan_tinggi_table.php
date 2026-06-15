<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('perguruan_tinggi', function (Blueprint $table) {
            $table->string('pic_nip')->nullable()->after('pic');
            $table->string('fakultas')->nullable()->after('email');
            $table->string('program_studi')->nullable()->after('fakultas');
        });
    }

    public function down(): void
    {
        Schema::table('perguruan_tinggi', function (Blueprint $table) {
            $table->dropColumn(['pic_nip', 'fakultas', 'program_studi']);
        });
    }
};
