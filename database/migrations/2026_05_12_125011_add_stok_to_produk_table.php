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
        Schema::table('produk', function (Blueprint $table) {
            // Nambahin kolom stok posisinya setelah kolom harga
            $table->integer('stok')->default(0)->after('harga'); 
        });
    }

    public function down()
    {
        Schema::table('produk', function (Blueprint $table) {
            // Buat ngehapus kolomnya kalau sewaktu-waktu di-rollback
            $table->dropColumn('stok');
        });
    }
};
