<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::create('reservasi', function (Blueprint $table) {
        $table->id();

        // Relasi ke tabel users
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

        // Kolom-kolom yang wajib ada buat nampilin dashboard
        $table->string('nama_layanan');
        $table->date('tanggal');
        $table->time('waktu');
        $table->string('pet_name');
        $table->string('status')->default('Menunggu');

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservasi');
    }
};
