<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detil_transaksi_produk', function (Blueprint $table) {
            $table->id();

            // Relasi ke tabel transaksi induknya
            $table->foreignId('transaksi_id')->constrained('transaksi')->onDelete('cascade');

            // Relasi ke produk apa yang dibeli
            $table->foreignId('produk_id')->constrained('produk')->onDelete('cascade');

            // Jumlah barang dan harga per barang saat itu
            $table->integer('jumlah');
            $table->decimal('harga_satuan', 15, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detil_transaksi_produk');
    }
};
