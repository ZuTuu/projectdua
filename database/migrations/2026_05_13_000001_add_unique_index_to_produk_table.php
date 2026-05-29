<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Hindari produk dobel dari sisi DB
        // Pilihan: unique nama_produk + kategori (lebih aman dari perubahan nama saja)
        Schema::table('produk', function (Blueprint $table) {
            $table->unique(['nama_produk', 'kategori'], 'produk_nama_kategori_unique');
        });
    }

    public function down(): void
    {
        Schema::table('produk', function (Blueprint $table) {
            $table->dropUnique('produk_nama_kategori_unique');
        });
    }


};




