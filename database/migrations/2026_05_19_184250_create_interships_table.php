<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('internships', function (Blueprint $table) {

            $table->id();

            $table->foreignId('peserta_id')
                ->constrained('pesertas')
                ->onDelete('cascade');

            $table->foreignId('mentor_id')
                ->constrained('mentors')
                ->onDelete('cascade');

            $table->foreignId('pembimbing_id')
                ->constrained('pembimbings')
                ->onDelete('cascade');

            $table->date('tanggal_mulai');

            $table->date('tanggal_selesai');

            $table->string('divisi');

            $table->enum('status', [
                'pending',
                'berjalan',
                'selesai'
            ])->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('internships');
    }
};