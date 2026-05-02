<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LansiaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $namaLansia = [
            'L' => [
                'Pak Slamet', 'Pak Karno', 'Pak Sutrisno', 'Pak Hadi', 'Pak Bambang',
                'Pak Suparman', 'Pak Joko', 'Pak Agus', 'Pak Budi', 'Pak Eko',
                'Pak Rudi', 'Pak Dedi', 'Pak Hendra', 'Pak Teguh', 'Pak Wahyu',
            ],
            'P' => [
                'Bu Siti', 'Bu Aminah', 'Bu Fatimah', 'Bu Khadijah', 'Bu Aisyah',
                'Bu Maryam', 'Bu Zainab', 'Bu Hafsah', 'Bu Ruqayyah', 'Bu Ummu',
                'Bu Sari', 'Bu Dewi', 'Bu Ratna', 'Bu Endang', 'Bu Yuni',
            ],
        ];

        $alamat = [
            'Jl. Merdeka No. 10, Nganjuk',
            'Jl. Sudirman No. 25, Nganjuk',
            'Jl. Ahmad Yani No. 15, Nganjuk',
            'Jl. Gatot Subroto No. 30, Nganjuk',
            'Jl. Diponegoro No. 5, Nganjuk',
            'Jl. Pahlawan No. 20, Nganjuk',
            'Jl. Veteran No. 12, Nganjuk',
            'Jl. Kartini No. 8, Nganjuk',
            'Jl. Sukarno Hatta No. 18, Nganjuk',
            'Jl. Pemuda No. 22, Nganjuk',
        ];

        $namaWali = [
            'Ahmad Rizki', 'Budi Santoso', 'Citra Dewi', 'Dina Mariana',
            'Eko Prasetyo', 'Fitri Handayani', 'Gilang Permana', 'Hani Pertiwi',
        ];

        $hubunganWali = ['Anak', 'Cucu', 'Menantu', 'Keponakan'];

        for ($i = 1; $i <= 20; $i++) {
            $jenisKelamin = rand(0, 1) ? 'L' : 'P';
            $umurTahun = rand(60, 85);
            $tanggalLahir = Carbon::now()->subYears($umurTahun);

            DB::table('lansia')->insert([
                'nik' => '3518' . str_pad($i, 12, '0', STR_PAD_LEFT),
                'nama_lengkap' => $namaLansia[$jenisKelamin][array_rand($namaLansia[$jenisKelamin])] . ' ' . $i,
                'jenis_kelamin' => $jenisKelamin,
                'tanggal_lahir' => $tanggalLahir,
                'tempat_lahir' => 'Nganjuk',
                'alamat' => $alamat[array_rand($alamat)],
                'rt_rw' => sprintf('%03d/%03d', rand(1, 10), rand(1, 10)),
                'no_hp' => '08' . rand(1000000000, 9999999999),
                'nama_wali' => $namaWali[array_rand($namaWali)],
                'hubungan_wali' => $hubunganWali[array_rand($hubunganWali)],
                'is_deleted' => false,
                'created_by' => 1, // Admin
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✅ Lansia seeded successfully!');
        $this->command->info('   - Total: 20 lansia');
        $this->command->info('   - Umur: 60-85 tahun');
    }
}