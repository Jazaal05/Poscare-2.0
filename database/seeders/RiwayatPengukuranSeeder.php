<?php

namespace Database\Seeders;

use App\Models\Anak;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RiwayatPengukuranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $anak = Anak::where('is_deleted', false)->get();

        if ($anak->isEmpty()) {
            $this->command->error('❌ Tidak ada data anak! Jalankan AnakSeeder terlebih dahulu.');
            return;
        }

        $totalRiwayat = 0;

        foreach ($anak as $child) {
            $umurBulan = Carbon::parse($child->tanggal_lahir)->diffInMonths(Carbon::now());
            
            // Buat riwayat pengukuran setiap 1-2 bulan
            $jumlahPengukuran = min(floor($umurBulan / 2), 10); // Max 10 riwayat

            for ($i = 0; $i < $jumlahPengukuran; $i++) {
                $bulanPengukuran = $i * 2; // Setiap 2 bulan
                $tanggalUkur = Carbon::parse($child->tanggal_lahir)->addMonths($bulanPengukuran);

                // Generate data antropometri progresif
                $dataAntro = $this->generateProgresifAntropometri($bulanPengukuran, $child->jenis_kelamin);

                DB::table('riwayat_pengukuran')->insert([
                    'anak_id' => $child->id,
                    'tanggal_ukur' => $tanggalUkur,
                    'umur_bulan' => $bulanPengukuran,
                    'bb_kg' => $dataAntro['bb'],
                    'tb_pb_cm' => $dataAntro['tb'],
                    'lk_cm' => $dataAntro['lk'],
                    'cara_ukur' => $bulanPengukuran < 24 ? 'berbaring' : 'berdiri',
                    'imt' => round($dataAntro['bb'] / (($dataAntro['tb'] / 100) ** 2), 2),
                    'z_tbu' => round(rand(-20, 30) / 10, 2),
                    'z_bbu' => round(rand(-20, 30) / 10, 2),
                    'z_bbtb' => round(rand(-20, 30) / 10, 2),
                    'kat_tbu' => $this->getKategori(),
                    'kat_bbu' => $this->getKategori(),
                    'kat_bbtb' => $this->getKategori(),
                    'overall_8' => $this->getStatusGizi(),
                    'created_at' => $tanggalUkur,
                    'updated_at' => $tanggalUkur,
                ]);

                $totalRiwayat++;
            }
        }

        $this->command->info('✅ Riwayat Pengukuran seeded successfully!');
        $this->command->info("   - Total: {$totalRiwayat} riwayat");
        $this->command->info("   - Untuk {$anak->count()} anak");
    }

    private function generateProgresifAntropometri($umurBulan, $jenisKelamin): array
    {
        if ($umurBulan <= 12) {
            $bb = 3.5 + ($umurBulan * 0.5) + rand(-5, 5) / 10;
            $tb = 50 + ($umurBulan * 2) + rand(-2, 2);
            $lk = 35 + ($umurBulan * 0.5) + rand(-1, 1);
        } elseif ($umurBulan <= 24) {
            $bb = 9 + (($umurBulan - 12) * 0.3) + rand(-5, 5) / 10;
            $tb = 74 + (($umurBulan - 12) * 1) + rand(-2, 2);
            $lk = 45 + (($umurBulan - 12) * 0.2) + rand(-1, 1);
        } elseif ($umurBulan <= 36) {
            $bb = 12 + (($umurBulan - 24) * 0.2) + rand(-5, 5) / 10;
            $tb = 86 + (($umurBulan - 24) * 0.8) + rand(-2, 2);
            $lk = 47 + (($umurBulan - 24) * 0.1) + rand(-1, 1);
        } else {
            $bb = 14 + (($umurBulan - 36) * 0.15) + rand(-10, 10) / 10;
            $tb = 95 + (($umurBulan - 36) * 0.5) + rand(-3, 3);
            $lk = 48 + (($umurBulan - 36) * 0.05) + rand(-1, 1);
        }

        return [
            'bb' => round($bb, 2),
            'tb' => round($tb, 2),
            'lk' => round($lk, 2),
        ];
    }

    private function getKategori(): string
    {
        $kategori = ['Normal', 'Beresiko', 'Kurang', 'Lebih'];
        return $kategori[array_rand($kategori)];
    }

    private function getStatusGizi(): string
    {
        $status = [
            'Gizi Baik' => 70,
            'Beresiko Gizi Lebih' => 15,
            'Gizi Lebih' => 8,
            'Risiko Stunting' => 5,
            'Stunting' => 2,
        ];

        $rand = rand(1, 100);
        $cumulative = 0;

        foreach ($status as $s => $probability) {
            $cumulative += $probability;
            if ($rand <= $cumulative) {
                return $s;
            }
        }

        return 'Gizi Baik';
    }
}