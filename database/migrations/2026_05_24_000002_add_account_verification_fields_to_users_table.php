<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'account_status')) {
                $table->string('account_status')->default('menunggu')->after('role');
            }

            if (! Schema::hasColumn('users', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('account_status');
            }

            if (! Schema::hasColumn('users', 'verified_at')) {
                $table->timestamp('verified_at')->nullable()->after('rejection_reason');
            }

            if (! Schema::hasColumn('users', 'verified_by')) {
                $table->foreignId('verified_by')
                    ->nullable()
                    ->after('verified_at')
                    ->constrained('users')
                    ->nullOnDelete();
            }
        });

        DB::table('users')
            ->where('role', 'super_admin')
            ->update([
                'account_status' => 'disetujui',
                'verified_at' => now(),
            ]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'verified_by')) {
                $table->dropConstrainedForeignId('verified_by');
            }

            foreach (['verified_at', 'rejection_reason', 'account_status'] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
