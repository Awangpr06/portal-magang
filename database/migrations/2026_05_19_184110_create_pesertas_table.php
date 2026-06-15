<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesertas', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');

            $table->foreignId('perguruan_tinggi_id')
                ->constrained('perguruan_tinggi')
                ->onDelete('cascade');

            $table->string('nim');

            $table->string('jurusan');

            $table->string('semester');

            $table->string('no_hp');

            $table->text('alamat');

            $table->enum('status', [
                'pending',
                'aktif',
                'selesai'
            ])->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesertas');
    }
};