<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('documents', 'jenis_dokumen')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->string('jenis_dokumen')->nullable()->after('kategori');
                $table->unique(['user_id', 'jenis_dokumen']);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('documents', 'jenis_dokumen')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->dropUnique(['user_id', 'jenis_dokumen']);
                $table->dropColumn('jenis_dokumen');
            });
        }
    }
};
