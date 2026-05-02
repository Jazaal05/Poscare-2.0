<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ═══════════════════════════════════════════════════════════
        // KADER (ADMIN)
        // ═══════════════════════════════════════════════════════════
        
        User::create([
            'username' => 'admin',
            'email' => 'admin@poscare.com',
            'password' => Hash::make('password'),
            'nama_lengkap' => 'Administrator PosCare',
            'role' => 'kader',
            'no_telp' => '081234567890',
            'nik' => '3518010101900001',
        ]);

        User::create([
            'username' => 'kader1',
            'email' => 'kader1@poscare.com',
            'password' => Hash::make('password'),
            'nama_lengkap' => 'Siti Nurhaliza',
            'role' => 'kader',
            'no_telp' => '081234567891',
            'nik' => '3518010101900002',
        ]);

        User::create([
            'username' => 'kader2',
            'email' => 'kader2@poscare.com',
            'password' => Hash::make('password'),
            'nama_lengkap' => 'Dewi Lestari',
            'role' => 'kader',
            'no_telp' => '081234567892',
            'nik' => '3518010101900003',
        ]);

        // ═══════════════════════════════════════════════════════════
        // ORANGTUA (USER)
        // ═══════════════════════════════════════════════════════════
        
        $orangtua = [
            [
                'username' => 'budi.santoso',
                'email' => 'budi.santoso@gmail.com',
                'nama_lengkap' => 'Budi Santoso',
                'no_telp' => '081234567893',
                'nik' => '3518010101850001',
            ],
            [
                'username' => 'ahmad.wijaya',
                'email' => 'ahmad.wijaya@gmail.com',
                'nama_lengkap' => 'Ahmad Wijaya',
                'no_telp' => '081234567894',
                'nik' => '3518010101850002',
            ],
            [
                'username' => 'eko.prasetyo',
                'email' => 'eko.prasetyo@gmail.com',
                'nama_lengkap' => 'Eko Prasetyo',
                'no_telp' => '081234567895',
                'nik' => '3518010101850003',
            ],
            [
                'username' => 'rudi.hartono',
                'email' => 'rudi.hartono@gmail.com',
                'nama_lengkap' => 'Rudi Hartono',
                'no_telp' => '081234567896',
                'nik' => '3518010101850004',
            ],
            [
                'username' => 'agus.setiawan',
                'email' => 'agus.setiawan@gmail.com',
                'nama_lengkap' => 'Agus Setiawan',
                'no_telp' => '081234567897',
                'nik' => '3518010101850005',
            ],
            [
                'username' => 'dedi.kurniawan',
                'email' => 'dedi.kurniawan@gmail.com',
                'nama_lengkap' => 'Dedi Kurniawan',
                'no_telp' => '081234567898',
                'nik' => '3518010101850006',
            ],
            [
                'username' => 'hendra.gunawan',
                'email' => 'hendra.gunawan@gmail.com',
                'nama_lengkap' => 'Hendra Gunawan',
                'no_telp' => '081234567899',
                'nik' => '3518010101850007',
            ],
            [
                'username' => 'bambang.susilo',
                'email' => 'bambang.susilo@gmail.com',
                'nama_lengkap' => 'Bambang Susilo',
                'no_telp' => '081234567900',
                'nik' => '3518010101850008',
            ],
            [
                'username' => 'joko.widodo',
                'email' => 'joko.widodo@gmail.com',
                'nama_lengkap' => 'Joko Widodo',
                'no_telp' => '081234567901',
                'nik' => '3518010101850009',
            ],
            [
                'username' => 'teguh.santoso',
                'email' => 'teguh.santoso@gmail.com',
                'nama_lengkap' => 'Teguh Santoso',
                'no_telp' => '081234567902',
                'nik' => '3518010101850010',
            ],
        ];

        foreach ($orangtua as $data) {
            User::create([
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => Hash::make('password'),
                'nama_lengkap' => $data['nama_lengkap'],
                'role' => 'orangtua',
                'no_telp' => $data['no_telp'],
                'nik' => $data['nik'],
            ]);
        }

        $this->command->info('✅ Users seeded successfully!');
        $this->command->info('   - 3 Kader (admin)');
        $this->command->info('   - 10 Orangtua (user)');
        $this->command->info('   - Default password: password');
    }
}