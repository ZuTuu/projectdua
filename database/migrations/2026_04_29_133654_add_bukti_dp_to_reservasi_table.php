<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservasi', function (Blueprint $table) {
            // Nambahin kolom buat nyimpen gambar bukti DP
            $table->string('bukti_pembayaran_dp')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('reservasi', function (Blueprint $table) {
            $table->dropColumn('bukti_pembayaran_dp');
        });
    }
};
