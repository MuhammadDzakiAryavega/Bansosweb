<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin (petugas)
        User::updateOrCreate(
            ['email' => 'admin@pkh.test'],
            [
                'name'     => 'Administrator',
                'nik'      => '1701010101010001',
                'password' => 'password',
                'role'     => 'admin',
            ]
        );

        // Pengguna (masyarakat)
        User::updateOrCreate(
            ['email' => 'user@pkh.test'],
            [
                'name'     => 'Pengguna',
                'nik'      => '1701010101010002',
                'password' => 'password',
                'role'     => 'user',
            ]
        );
    }
}