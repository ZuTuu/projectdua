<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            // Add payment method field
            $table->string('metode_pembayaran')->nullable()->default('cash')->after('status');
            // Add change field for cash payments
            $table->decimal('kembalian', 15, 2)->nullable()->after('metode_pembayaran');
        });
    }

    public function down(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropColumn(['metode_pembayaran', 'kembalian']);
        });
    }
};
