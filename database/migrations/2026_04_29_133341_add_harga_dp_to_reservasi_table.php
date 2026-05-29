<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservasi', function (Blueprint $table) {
            // Nambahin kolom harga dan DP tanpa ngehapus data lama
            $table->decimal('harga_total', 12, 2)->nullable()->after('pet_name');
            $table->decimal('dp', 12, 2)->nullable()->after('harga_total');
            $table->decimal('sisa_bayar', 12, 2)->nullable()->after('dp');
        });
    }

    public function down(): void
    {
        Schema::table('reservasi', function (Blueprint $table) {
            $table->dropColumn(['harga_total', 'dp', 'sisa_bayar']);
        });
    }
};
