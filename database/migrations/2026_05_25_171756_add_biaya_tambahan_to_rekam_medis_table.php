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
        Schema::table('rekam_medis', function (Blueprint $table) {
            // Menambahkan kolom biaya_tambahan (tipe integer, boleh kosong/nullable)
            // Posisinya ditaruh setelah kolom 'catatan' biar rapi di database
            $table->integer('biaya_tambahan')->nullable()->after('catatan');
        });
    }

    public function down()
    {
        Schema::table('rekam_medis', function (Blueprint $table) {
            // Untuk menghapus kolom kalau migration di-rollback
            $table->dropColumn('biaya_tambahan');
        });
    }
};
