<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('reports')) {
            return;
        }

        Schema::table('reports', function (Blueprint $table) {
            if (! Schema::hasColumn('reports', 'durasi_jam')) {
                $table->unsignedTinyInteger('durasi_jam')->nullable()->after('periode');
            }

            if (! Schema::hasColumn('reports', 'catatan_mentor')) {
                $table->text('catatan_mentor')->nullable()->after('catatan');
            }

            if (! Schema::hasColumn('reports', 'catatan_pembimbing')) {
                $table->text('catatan_pembimbing')->nullable()->after('catatan_mentor');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('reports')) {
            return;
        }

        Schema::table('reports', function (Blueprint $table) {
            $columns = array_values(array_filter([
                Schema::hasColumn('reports', 'durasi_jam') ? 'durasi_jam' : null,
                Schema::hasColumn('reports', 'catatan_mentor') ? 'catatan_mentor' : null,
                Schema::hasColumn('reports', 'catatan_pembimbing') ? 'catatan_pembimbing' : null,
            ]));

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
