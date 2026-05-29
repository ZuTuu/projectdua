<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Owner (Bos Besar)
        User::create([
            'name' => 'Owner Paw Center',
            'email' => 'owner@pawcenter.com',
            'password' => Hash::make('password123'),
            'role' => 'owner',
        ]);

        // 2. Admin/Kasir
        User::create([
            'name' => 'Admin Kasir',
            'email' => 'admin@pawcenter.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // 3. Staff
        User::create([
            'name' => 'Staff Lapangan',
            'email' => 'staff@pawcenter.com',
            'password' => Hash::make('password123'),
            'role' => 'staff',
        ]);

        // 4. Dokter
        User::create([
            'name' => 'drh. Karisma',
            'email' => 'dokter@pawcenter.com',
            'password' => Hash::make('password123'),
            'role' => 'dokter',
        ]);

        // 5. Pelanggan (Akun Kamu)
        User::create([
            'name' => 'Karisma Pelanggan',
            'email' => 'pelanggan@pawcenter.com',
            'password' => Hash::make('password123'),
            'role' => 'pelanggan',
        ]);
    }
}
