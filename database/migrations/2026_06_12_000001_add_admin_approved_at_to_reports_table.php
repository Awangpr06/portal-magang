<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('reports') || Schema::hasColumn('reports', 'admin_approved_at')) {
            return;
        }

        Schema::table('reports', function (Blueprint $table) {
            $table->timestamp('admin_approved_at')->nullable()->after('reviewer_id');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('reports') || ! Schema::hasColumn('reports', 'admin_approved_at')) {
            return;
        }

        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn('admin_approved_at');
        });
    }
};
