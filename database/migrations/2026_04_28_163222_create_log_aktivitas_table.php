<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_aktivitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Menghubungkan ke tabel users
            $table->string('aktivitas'); // Contoh: Login, Logout, Update Data
            $table->text('deskripsi');   // Penjelasan detail aktivitasnya
            $table->timestamps();        // Mencatat waktu created_at & updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_aktivitas');
    }
};
