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
                'Pak Slamet Riyadi', 'Pak Karno Sutrisno', 'Pak Hadi Wijaya', 'Pak Bambang Santoso',
                'Pak Suparman Agus', 'Pak Joko Widodo', 'Pak Agus Salim', 'Pak Budi Hartono',
                'Pak Eko Prasetyo', 'Pak Rudi Setiawan', 'Pak Dedi Kurniawan', 'Pak Hendra Gunawan',
                'Pak Teguh Wahyudi', 'Pak Wahyu Hidayat', 'Pak Yanto Suryanto',
            ],
            'P' => [
                'Bu Siti Aminah', 'Bu Fatimah Zahra', 'Bu Khadijah Maryam', 'Bu Aisyah Rahmawati',
                'Bu Maryam Sari', 'Bu Zainab Dewi', 'Bu Hafsah Ratna', 'Bu Ruqayyah Endang',
                'Bu Ummu Yuni', 'Bu Sari Lestari', 'Bu Dewi Kartika', 'Bu Ratna Sari',
                'Bu Endang Susilowati', 'Bu Yuni Astuti', 'Bu Sri Wahyuni',
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
            'Jl. Sukomoro No. 14, Nganjuk',
            'Jl. Payaman No. 7, Nganjuk',
        ];

        $namaWali = [
            'Ahmad Rizki', 'Budi Santoso', 'Citra Dewi', 'Dina Mariana',
            'Eko Prasetyo', 'Fitri Handayani', 'Gilang Permana', 'Hani Pertiwi',
            'Indra Kusuma', 'Joko Susilo', 'Kartika Sari', 'Lina Marlina',
        ];

        $statusKesehatan = [
            'Sehat' => 60,
            'Hipertensi' => 20,
            'Diabetes' => 10,
            'Kolesterol Tinggi' => 5,
            'Asam Urat Tinggi' => 5,
        ];

        for ($i = 1; $i <= 30; $i++) {
            $jenisKelamin = rand(0, 1) ? 'L' : 'P';
            $umurTahun = rand(60, 85);
            $tanggalLahir = Carbon::now()->subYears($umurTahun)->subMonths(rand(0, 11))->subDays(rand(0, 28));

            // Generate data kesehatan
            $beratBadan = rand(45, 80) + (rand(0, 9) / 10);
            $tinggiBadan = rand(145, 175);
            $tekananDarah = rand(110, 160) . '/' . rand(70, 100);
            $gulaDarah = rand(80, 200) + (rand(0, 9) / 10);
            $kolesterol = rand(150, 280) + (rand(0, 9) / 10);
            $asamUrat = rand(3, 9) + (rand(0, 9) / 10);

            // Tentukan status kesehatan berdasarkan probabilitas
            $rand = rand(1, 100);
            $cumulative = 0;
            $status = 'Sehat';
            foreach ($statusKesehatan as $s => $probability) {
                $cumulative += $probability;
                if ($rand <= $cumulative) {
                    $status = $s;
                    break;
                }
            }

            // Detail status kesehatan (JSON)
            $statusDetail = json_encode([
                'tekanan_darah' => [
                    'nilai' => $tekananDarah,
                    'kategori' => $this->kategoriTekananDarah($tekananDarah),
                ],
                'gula_darah' => [
                    'nilai' => $gulaDarah,
                    'kategori' => $this->kategoriGulaDarah($gulaDarah),
                ],
                'kolesterol' => [
                    'nilai' => $kolesterol,
                    'kategori' => $this->kategoriKolesterol($kolesterol),
                ],
                'asam_urat' => [
                    'nilai' => $asamUrat,
                    'kategori' => $this->kategoriAsamUrat($asamUrat, $jenisKelamin),
                ],
                'bmi' => [
                    'nilai' => round($beratBadan / (($tinggiBadan / 100) ** 2), 2),
                    'kategori' => $this->kategoriBMI($beratBadan, $tinggiBadan),
                ],
            ]);

            DB::table('lansia')->insert([
                'nik_lansia'                    => '3518' . str_pad($i, 12, '0', STR_PAD_LEFT),
                'nama_lengkap'                  => $namaLansia[$jenisKelamin][array_rand($namaLansia[$jenisKelamin])],
                'tgl_lahir'                     => $tanggalLahir->format('Y-m-d'),
                'tempat_lahir'                  => 'Nganjuk',
                'jenis_kelamin'                 => $jenisKelamin,
                'alamat_domisili'               => $alamat[array_rand($alamat)],
                'rt_rw'                         => sprintf('%03d/%03d', rand(1, 15), rand(1, 10)),
                'nama_kk'                       => $namaLansia[$jenisKelamin][array_rand($namaLansia[$jenisKelamin])],
                'nama_wali'                     => $namaWali[array_rand($namaWali)],
                'nik_wali'                      => '3518' . rand(1000000000, 9999999999),
                'hp_kontak_wali'                => '08' . rand(1000000000, 9999999999),
                'berat_badan'                   => $beratBadan,
                'tinggi_badan'                  => $tinggiBadan,
                'tekanan_darah'                 => $tekananDarah,
                'gula_darah'                    => $gulaDarah,
                'kolesterol'                    => $kolesterol,
                'asam_urat'                     => $asamUrat,
                'dicatat_oleh'                  => 1,
                'status_kesehatan'              => $status,
                'tanggal_pemeriksaan_terakhir'  => Carbon::now()->subDays(rand(1, 30))->format('Y-m-d'),
                'is_deleted'                    => 0,
            ]);
        }

        $this->command->info('✅ Lansia seeded successfully!');
        $this->command->info('   - Total: 30 lansia');
        $this->command->info('   - Umur: 60-85 tahun');
        $this->command->info('   - Status: Sehat, Hipertensi, Diabetes, dll');
    }

    private function kategoriTekananDarah($tekananDarah): string
    {
        list($sistolik, $diastolik) = explode('/', $tekananDarah);
        $sistolik = (int)$sistolik;
        $diastolik = (int)$diastolik;

        if ($sistolik < 120 && $diastolik < 80) return 'Normal';
        if ($sistolik < 130 && $diastolik < 80) return 'Elevated';
        if ($sistolik < 140 || $diastolik < 90) return 'Hipertensi Stage 1';
        if ($sistolik < 180 || $diastolik < 120) return 'Hipertensi Stage 2';
        return 'Krisis Hipertensi';
    }

    private function kategoriGulaDarah($gulaDarah): string
    {
        if ($gulaDarah < 100) return 'Normal';
        if ($gulaDarah < 126) return 'Prediabetes';
        return 'Diabetes';
    }

    private function kategoriKolesterol($kolesterol): string
    {
        if ($kolesterol < 200) return 'Normal';
        if ($kolesterol < 240) return 'Borderline High';
        return 'Tinggi';
    }

    private function kategoriAsamUrat($asamUrat, $jenisKelamin): string
    {
        if ($jenisKelamin == 'L') {
            if ($asamUrat < 7) return 'Normal';
            return 'Tinggi';
        } else {
            if ($asamUrat < 6) return 'Normal';
            return 'Tinggi';
        }
    }

    private function kategoriBMI($beratBadan, $tinggiBadan): string
    {
        $bmi = $beratBadan / (($tinggiBadan / 100) ** 2);
        if ($bmi < 18.5) return 'Underweight';
        if ($bmi < 25) return 'Normal';
        if ($bmi < 30) return 'Overweight';
        return 'Obesitas';
    }
}
