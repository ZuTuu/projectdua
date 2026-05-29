<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('rekam_medis', function (Blueprint $table) {
            // Nambahin kolom catatan setelah kolom tindakan
            $table->text('catatan')->nullable()->after('tindakan');
        });
    }

    public function down()
    {
        Schema::table('rekam_medis', function (Blueprint $table) {
            $table->dropColumn('catatan');
        });
    }
};
