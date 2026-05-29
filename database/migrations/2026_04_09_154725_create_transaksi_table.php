<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();

            // Relasi ke user yang belanja
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Total harga belanjaan
            $table->decimal('total_harga', 15, 2);

            // Status pesanan
            $table->string('status')->default('Menunggu Pembayaran');

            // Bukti transfer (boleh kosong/null dulu)
            $table->string('bukti_pembayaran')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
