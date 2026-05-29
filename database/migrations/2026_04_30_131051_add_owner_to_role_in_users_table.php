<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Masukkan SEMUA role yang sudah ada sebelumnya ditambah 'owner'
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'staff', 'pelanggan', 'dokter', 'kasir', 'owner') NOT NULL DEFAULT 'pelanggan'");
    }

    public function down(): void
    {
        // Kembalikan tanpa 'owner'
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'staff', 'pelanggan', 'dokter', 'kasir') NOT NULL DEFAULT 'pelanggan'");
    }
};
