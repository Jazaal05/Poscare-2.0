<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed akun default untuk production.
     * 
     * ⚠️  PENTING: Ganti password akun admin setelah pertama kali login!
     */
    public function run(): void
    {
        // ── Akun Kader (Admin = Kader, keduanya sama) ──────────────────────────
        User::firstOrCreate(
            ['email' => 'kader@poscare.id'],
            [
                'username'     => 'kader_poscare',
                'nama_lengkap' => 'Kader Posyandu',
                'password'     => Hash::make('PosCare@2025'),
                'role'         => 'kader',
                'no_telp'      => '08123456789',
                'nik'          => '3500000000000001',
            ]
        );
    }
}
