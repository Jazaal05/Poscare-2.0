<?php

namespace Database\Seeders;

use App\Models\Anak;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ImunisasiSeeder extends Seeder
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

        // Cek apakah ada master vaksin
        $vaksinCount = DB::table('master_vaksin')->count();
        if ($vaksinCount == 0) {
            $this->command->error('❌ Tidak ada master vaksin! Jalankan MasterVaksinSeeder terlebih dahulu.');
            return;
        }

        $totalImunisasi = 0;

        foreach ($anak as $child) {
            $umurBulan = Carbon::parse($child->tanggal_lahir)->diffInMonths(Carbon::now());
            
            // Jadwal imunisasi berdasarkan umur
            $jadwalImunisasi = [
                0 => [1], // HB-0
                1 => [2, 3], // BCG, Polio 1
                2 => [4, 5], // DPT-HB-Hib 1, Polio 2
                3 => [6, 7], // DPT-HB-Hib 2, Polio 3
                4 => [8, 9, 10], // DPT-HB-Hib 3, Polio 4, IPV
                9 => [11], // Campak/MR 1
                18 => [12, 13], // Campak/MR 2, DPT Booster
            ];

            foreach ($jadwalImunisasi as $bulan => $vaksinIds) {
                if ($umurBulan >= $bulan) {
                    foreach ($vaksinIds as $vaksinId) {
                        // Cek apakah vaksin sudah ada
                        $exists = DB::table('imunisasi')
                            ->where('anak_id', $child->id)
                            ->where('master_vaksin_id', $vaksinId)
                            ->exists();

                        if (!$exists) {
                            $tanggalImunisasi = Carbon::parse($child->tanggal_lahir)
                                ->addMonths($bulan)
                                ->addDays(rand(0, 7)); // Random 0-7 hari setelah jadwal

                            DB::table('imunisasi')->insert([
                                'anak_id' => $child->id,
                                'master_vaksin_id' => $vaksinId,
                                'tanggal' => $tanggalImunisasi,
                                'umur_bulan' => $bulan,
                                'created_at' => $tanggalImunisasi,
                                'updated_at' => $tanggalImunisasi,
                            ]);

                            $totalImunisasi++;
                        }
                    }
                }
            }
        }

        $this->command->info('✅ Imunisasi seeded successfully!');
        $this->command->info("   - Total: {$totalImunisasi} imunisasi");
        $this->command->info("   - Untuk {$anak->count()} anak");
    }
}