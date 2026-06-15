<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            if (! Schema::hasColumn('assignments', 'file_pengumpulan')) {
                $table->string('file_pengumpulan')->nullable()->after('file_hasil');
            }

            if (! Schema::hasColumn('assignments', 'submitted_at')) {
                $table->timestamp('submitted_at')->nullable()->after('file_pengumpulan');
            }
        });
    }

    public function down(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            if (Schema::hasColumn('assignments', 'submitted_at')) {
                $table->dropColumn('submitted_at');
            }

            if (Schema::hasColumn('assignments', 'file_pengumpulan')) {
                $table->dropColumn('file_pengumpulan');
            }
        });
    }
};
