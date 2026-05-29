<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            // Tambahin kolom metode pengiriman & tanggal ambil setelah bukti_pembayaran
            $table->string('metode_pengiriman', 50)->nullable()->after('bukti_pembayaran');
            $table->date('tanggal_ambil')->nullable()->after('metode_pengiriman');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            // Drop kolomnya kalau sewaktu-waktu di-rollback
            $table->dropColumn(['metode_pengiriman', 'tanggal_ambil']);
        });
    }
};