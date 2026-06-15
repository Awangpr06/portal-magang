<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {

            $table->id();

            $table->foreignId('peserta_id')
                ->constrained('pesertas')
                ->onDelete('cascade');

            $table->string('judul');

            $table->string('file');

            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
                'revisi'
            ])->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};