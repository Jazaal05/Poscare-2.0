<?php

namespace Database\Seeders;

use App\Models\Anak;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AnakSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orangtua = User::where('role', 'orangtua')->get();

        if ($orangtua->isEmpty()) {
            $this->command->error('❌ Tidak ada user orangtua! Jalankan UserSeeder terlebih dahulu.');
            return;
        }

        $namaAnak = [
            'L' => [
                'Ahmad Rizki', 'Budi Setiawan', 'Dimas Pratama', 'Eko Saputra', 'Fajar Ramadhan',
                'Gilang Permana', 'Hendra Wijaya', 'Irfan Maulana', 'Joko Susilo', 'Kurniawan',
                'Lukman Hakim', 'Muhammad Iqbal', 'Nanda Pratama', 'Oscar Wijaya', 'Putra Mahendra',
                'Rafi Ahmad', 'Satria Budi', 'Taufik Hidayat', 'Umar Bakri', 'Vino Bastian',
            ],
            'P' => [
                'Ayu Lestari', 'Bella Safira', 'Citra Dewi', 'Dina Mariana', 'Eka Putri',
                'Fitri Handayani', 'Gita Savitri', 'Hani Pertiwi', 'Indah Permata', 'Jasmine',
                'Kartika Sari', 'Lina Marlina', 'Maya Sari', 'Nisa Aulia', 'Olivia',
                'Putri Ayu', 'Qonita', 'Rina Susanti', 'Siti Nurhaliza', 'Tania',
            ],
        ];

        $namaIbu = [
            'Siti Aminah', 'Dewi Lestari', 'Ratna Sari', 'Endang Susilowati', 'Fitri Handayani',
            'Yuni Shara', 'Rina Susanti', 'Wati Kurniawati', 'Sri Mulyani', 'Ani Yudhoyono',
        ];

        $namaAyah = [
            'Budi Santoso', 'Ahmad Dahlan', 'Eko Prasetyo', 'Rudi Hartono', 'Agus Salim',
            'Hendra Gunawan', 'Dedi Kurniawan', 'Bambang Susilo', 'Joko Widodo', 'Teguh Santoso',
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

        $count = 0;

        foreach ($orangtua as $index => $ortu) {
            // Setiap orangtua punya 1-2 anak
            $jumlahAnak = rand(1, 2);

            for ($i = 1; $i <= $jumlahAnak; $i++) {
                $jenisKelamin = rand(0, 1) ? 'L' : 'P';
                $umurBulan = rand(0, 60); // 0-5 tahun
                $tanggalLahir = Carbon::now()->subMonths($umurBulan);

                // Pilih nama random
                $nama = $namaAnak[$jenisKelamin][array_rand($namaAnak[$jenisKelamin])];
                
                // Generate data antropometri berdasarkan umur
                $dataAntro = $this->generateAntropometri($umurBulan, $jenisKelamin);

                Anak::create([
                    'user_id' => $ortu->id,
                    'no_registrasi' => 'REG' . str_pad($count + 1, 5, '0', STR_PAD_LEFT),
                    'nik_anak' => '3518' . str_pad($count + 1, 12, '0', STR_PAD_LEFT),
                    'nama_anak' => $nama . ' ' . explode(' ', $ortu->nama_lengkap)[0],
                    'tanggal_lahir' => $tanggalLahir,
                    'tempat_lahir' => 'Nganjuk',
                    'jenis_kelamin' => $jenisKelamin,
                    'anak_ke' => $i,
                    'alamat_domisili' => $alamat[array_rand($alamat)],
                    'rt_rw' => sprintf('%03d/%03d', rand(1, 10), rand(1, 10)),
                    'nama_kk' => $ortu->nama_lengkap,
                    'nama_ayah' => $namaAyah[array_rand($namaAyah)],
                    'nama_ibu' => $namaIbu[array_rand($namaIbu)],
                    'nik_ayah' => '3518' . str_pad(rand(1, 999999), 12, '0', STR_PAD_LEFT),
                    'nik_ibu' => '3518' . str_pad(rand(1, 999999), 12, '0', STR_PAD_LEFT),
                    'tanggal_lahir_ibu' => Carbon::now()->subYears(rand(25, 40)),
                    'hp_kontak_ortu' => '08' . rand(1000000000, 9999999999),
                    'berat_badan' => $dataAntro['bb'],
                    'tinggi_badan' => $dataAntro['tb'],
                    'lingkar_kepala' => $dataAntro['lk'],
                    'cara_ukur' => $umurBulan < 24 ? 'berbaring' : 'berdiri',
                    'status_gizi' => $dataAntro['status'],
                    'tanggal_penimbangan_terakhir' => Carbon::now()->subDays(rand(1, 30)),
                    'is_deleted' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $count++;
            }
        }

        $this->command->info("✅ Anak seeded successfully!");
        $this->command->info("   - Total: {$count} anak");
        $this->command->info("   - Tersebar di {$orangtua->count()} orangtua");
    }

    /**
     * Generate data antropometri berdasarkan umur
     */
    private function generateAntropometri($umurBulan, $jenisKelamin): array
    {
        // Data antropometri rata-rata berdasarkan umur (simplified)
        if ($umurBulan <= 12) {
            // 0-12 bulan
            $bb = 3.5 + ($umurBulan * 0.5) + rand(-5, 5) / 10;
            $tb = 50 + ($umurBulan * 2) + rand(-2, 2);
            $lk = 35 + ($umurBulan * 0.5) + rand(-1, 1);
        } elseif ($umurBulan <= 24) {
            // 13-24 bulan
            $bb = 9 + (($umurBulan - 12) * 0.3) + rand(-5, 5) / 10;
            $tb = 74 + (($umurBulan - 12) * 1) + rand(-2, 2);
            $lk = 45 + (($umurBulan - 12) * 0.2) + rand(-1, 1);
        } elseif ($umurBulan <= 36) {
            // 25-36 bulan
            $bb = 12 + (($umurBulan - 24) * 0.2) + rand(-5, 5) / 10;
            $tb = 86 + (($umurBulan - 24) * 0.8) + rand(-2, 2);
            $lk = 47 + (($umurBulan - 24) * 0.1) + rand(-1, 1);
        } else {
            // > 36 bulan
            $bb = 14 + (($umurBulan - 36) * 0.15) + rand(-10, 10) / 10;
            $tb = 95 + (($umurBulan - 36) * 0.5) + rand(-3, 3);
            $lk = 48 + (($umurBulan - 36) * 0.05) + rand(-1, 1);
        }

        // Status gizi random (mayoritas normal)
        $statusOptions = [
            'Gizi Baik' => 70,
            'Beresiko Gizi Lebih' => 15,
            'Gizi Lebih' => 8,
            'Risiko Stunting' => 5,
            'Stunting' => 2,
        ];

        $rand = rand(1, 100);
        $cumulative = 0;
        $status = 'Gizi Baik';

        foreach ($statusOptions as $s => $probability) {
            $cumulative += $probability;
            if ($rand <= $cumulative) {
                $status = $s;
                break;
            }
        }

        return [
            'bb' => round($bb, 2),
            'tb' => round($tb, 2),
            'lk' => round($lk, 2),
            'status' => $status,
        ];
    }
}