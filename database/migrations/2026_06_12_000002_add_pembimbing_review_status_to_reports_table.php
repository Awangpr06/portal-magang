<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('reports') || Schema::hasColumn('reports', 'pembimbing_review_status')) {
            return;
        }

        Schema::table('reports', function (Blueprint $table) {
            $table->string('pembimbing_review_status', 30)->nullable()->default('menunggu review')->after('catatan_pembimbing');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('reports') || ! Schema::hasColumn('reports', 'pembimbing_review_status')) {
            return;
        }

        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn('pembimbing_review_status');
        });
    }
};
