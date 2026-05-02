<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KunjunganLansiaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lansiaIds = DB::table('lansia')->pluck('id')->toArray();

        if (empty($lansiaIds)) {
            $this->command->error('❌ Tidak ada data lansia! Jalankan LansiaSeeder terlebih dahulu.');
            return;
        }

        $obatOptions = [
            ['Paracetamol 500mg', 'Amoxicillin 500mg', 'Vitamin B Complex'],
            ['Amlodipine 5mg', 'Simvastatin 20mg'],
            ['Metformin 500mg', 'Glimepiride 2mg'],
            ['Allopurinol 100mg', 'Colchicine 0.5mg'],
            ['Captopril 25mg', 'HCT 25mg'],
        ];

        $vitaminOptions = [
            ['Vitamin C 1000mg', 'Vitamin E 400IU'],
            ['Multivitamin Senior', 'Kalsium 500mg'],
            ['Vitamin D3 1000IU', 'Omega 3'],
            ['Vitamin B12', 'Asam Folat'],
        ];

        $keluhanOptions = [
            'Pusing dan sakit kepala',
            'Nyeri sendi lutut',
            'Susah tidur',
            'Nyeri punggung',
            'Kaki bengkak',
            'Sesak napas ringan',
            'Nyeri dada',
            'Lemas dan mudah lelah',
            'Kesemutan di kaki',
            'Penglihatan kabur',
        ];

        $catatanOptions = [
            'Pasien dalam kondisi stabil, lanjutkan pengobatan',
            'Perlu kontrol rutin setiap bulan',
            'Disarankan diet rendah garam',
            'Anjuran olahraga ringan seperti jalan pagi',
            'Perlu pemeriksaan lanjutan di puskesmas',
            'Kondisi membaik, obat dilanjutkan',
            'Disarankan istirahat cukup',
            'Perlu kontrol gula darah berkala',
            'Anjuran diet rendah purin',
            'Kondisi terkontrol dengan baik',
        ];

        $totalKunjungan = 0;

        foreach ($lansiaIds as $lansiaId) {
            // Setiap lansia punya 3-8 kunjungan dalam 6 bulan terakhir
            $jumlahKunjungan = rand(3, 8);

            for ($i = 0; $i < $jumlahKunjungan; $i++) {
                $tanggalKunjungan = Carbon::now()->subDays(rand(1, 180));
                
                // Generate data kesehatan realistis
                $beratBadan = rand(45, 75) + (rand(0, 9) / 10);
                
                // Tekanan darah (mayoritas normal-prehipertensi)
                $sistolik = rand(110, 160);
                $diastolik = rand(70, 100);
                $tekananDarah = "{$sistolik}/{$diastolik}";
                
                // Gula darah sewaktu (mg/dL)
                $gulaDarah = rand(80, 220) + (rand(0, 9) / 10);
                
                // Kolesterol (mg/dL)
                $kolesterol = rand(150, 280) + (rand(0, 9) / 10);
                
                // Asam urat (mg/dL)
                $asamUrat = rand(4, 9) + (rand(0, 9) / 10);
                
                // Keluhan (30% ada keluhan)
                $adaKeluhan = rand(1, 100) <= 30;
                $keluhan = $adaKeluhan ? $keluhanOptions[array_rand($keluhanOptions)] : null;
                
                // Obat dan vitamin (jika ada keluhan atau kondisi tidak normal)
                $perluObat = $adaKeluhan || $sistolik > 140 || $gulaDarah > 140 || $kolesterol > 200 || $asamUrat > 7;
                $obatDiberikan = $perluObat ? json_encode($obatOptions[array_rand($obatOptions)]) : null;
                $vitaminDiberikan = rand(1, 100) <= 60 ? json_encode($vitaminOptions[array_rand($vitaminOptions)]) : null;

                DB::table('kunjungan_lansia')->insert([
                    'lansia_id' => $lansiaId,
                    'tanggal_kunjungan' => $tanggalKunjungan,
                    'berat_badan' => $beratBadan,
                    'tekanan_darah' => $tekananDarah,
                    'status_tensi' => $this->hitungStatusTensi($tekananDarah),
                    'gula_darah' => $gulaDarah,
                    'status_gula' => $this->hitungStatusGula($gulaDarah),
                    'kolesterol' => $kolesterol,
                    'status_kolesterol' => $this->hitungStatusKolesterol($kolesterol),
                    'asam_urat' => $asamUrat,
                    'status_asam_urat' => $this->hitungStatusAsamUrat($asamUrat),
                    'ada_keluhan' => $adaKeluhan,
                    'keluhan' => $keluhan,
                    'obat_diberikan' => $obatDiberikan,
                    'vitamin_diberikan' => $vitaminDiberikan,
                    'catatan_bidan' => $catatanOptions[array_rand($catatanOptions)],
                    'dicatat_oleh' => rand(1, 3), // Kader ID 1-3
                ]);

                $totalKunjungan++;
            }
        }

        $this->command->info('✅ Kunjungan Lansia seeded successfully!');
        $this->command->info("   - Total: {$totalKunjungan} kunjungan");
        $this->command->info("   - Tersebar di " . count($lansiaIds) . " lansia");
    }

    /**
     * Hitung status tensi berdasarkan nilai sistolik/diastolik
     */
    private function hitungStatusTensi(string $tekananDarah): string
    {
        $parts = explode('/', $tekananDarah);
        $sistolik = (int) trim($parts[0]);
        $diastolik = (int) trim($parts[1]);

        if ($sistolik >= 160 || $diastolik >= 100) return 'hipertensi2';
        if ($sistolik >= 140 || $diastolik >= 90) return 'hipertensi1';
        if ($sistolik >= 120 || $diastolik >= 80) return 'prehipertensi';
        return 'normal';
    }

    /**
     * Hitung status gula darah (sewaktu)
     */
    private function hitungStatusGula(float $gula): string
    {
        if ($gula < 70) return 'rendah';
        if ($gula < 140) return 'normal';
        if ($gula < 200) return 'tinggi';
        return 'sangat_tinggi';
    }

    /**
     * Hitung status kolesterol
     */
    private function hitungStatusKolesterol(float $kol): string
    {
        if ($kol < 200) return 'normal';
        if ($kol < 240) return 'batas';
        return 'tinggi';
    }

    /**
     * Hitung status asam urat
     */
    private function hitungStatusAsamUrat(float $au): string
    {
        return $au <= 7.0 ? 'normal' : 'tinggi';
    }
}
