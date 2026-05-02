<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterVaksinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vaksin = [
            [
                'nama_vaksin' => 'Hepatitis B (HB-0)',
                'deskripsi' => 'Vaksin Hepatitis B dosis pertama, diberikan segera setelah lahir',
                'umur_pemberian' => '0 bulan (segera setelah lahir)',
                'is_wajib' => true,
            ],
            [
                'nama_vaksin' => 'BCG',
                'deskripsi' => 'Vaksin untuk mencegah penyakit Tuberkulosis (TBC)',
                'umur_pemberian' => '1 bulan',
                'is_wajib' => true,
            ],
            [
                'nama_vaksin' => 'Polio 1',
                'deskripsi' => 'Vaksin Polio dosis pertama',
                'umur_pemberian' => '1 bulan',
                'is_wajib' => true,
            ],
            [
                'nama_vaksin' => 'DPT-HB-Hib 1',
                'deskripsi' => 'Vaksin kombinasi untuk Difteri, Pertusis, Tetanus, Hepatitis B, dan Haemophilus influenzae type b',
                'umur_pemberian' => '2 bulan',
                'is_wajib' => true,
            ],
            [
                'nama_vaksin' => 'Polio 2',
                'deskripsi' => 'Vaksin Polio dosis kedua',
                'umur_pemberian' => '2 bulan',
                'is_wajib' => true,
            ],
            [
                'nama_vaksin' => 'DPT-HB-Hib 2',
                'deskripsi' => 'Vaksin kombinasi dosis kedua',
                'umur_pemberian' => '3 bulan',
                'is_wajib' => true,
            ],
            [
                'nama_vaksin' => 'Polio 3',
                'deskripsi' => 'Vaksin Polio dosis ketiga',
                'umur_pemberian' => '3 bulan',
                'is_wajib' => true,
            ],
            [
                'nama_vaksin' => 'DPT-HB-Hib 3',
                'deskripsi' => 'Vaksin kombinasi dosis ketiga',
                'umur_pemberian' => '4 bulan',
                'is_wajib' => true,
            ],
            [
                'nama_vaksin' => 'Polio 4',
                'deskripsi' => 'Vaksin Polio dosis keempat',
                'umur_pemberian' => '4 bulan',
                'is_wajib' => true,
            ],
            [
                'nama_vaksin' => 'IPV (Polio Suntik)',
                'deskripsi' => 'Inactivated Poliovirus Vaccine',
                'umur_pemberian' => '4 bulan',
                'is_wajib' => true,
            ],
            [
                'nama_vaksin' => 'Campak/MR 1',
                'deskripsi' => 'Vaksin Campak atau Measles-Rubella dosis pertama',
                'umur_pemberian' => '9 bulan',
                'is_wajib' => true,
            ],
            [
                'nama_vaksin' => 'Campak/MR 2',
                'deskripsi' => 'Vaksin Campak atau Measles-Rubella dosis kedua (booster)',
                'umur_pemberian' => '18 bulan',
                'is_wajib' => true,
            ],
            [
                'nama_vaksin' => 'DPT-HB-Hib Booster',
                'deskripsi' => 'Vaksin kombinasi dosis booster',
                'umur_pemberian' => '18 bulan',
                'is_wajib' => true,
            ],
            // Vaksin Tambahan (Tidak Wajib)
            [
                'nama_vaksin' => 'Rotavirus',
                'deskripsi' => 'Vaksin untuk mencegah diare berat akibat rotavirus',
                'umur_pemberian' => '2, 4, 6 bulan',
                'is_wajib' => false,
            ],
            [
                'nama_vaksin' => 'PCV (Pneumokokus)',
                'deskripsi' => 'Vaksin untuk mencegah infeksi pneumokokus',
                'umur_pemberian' => '2, 4, 6, 12-15 bulan',
                'is_wajib' => false,
            ],
            [
                'nama_vaksin' => 'Influenza',
                'deskripsi' => 'Vaksin flu, diberikan setiap tahun',
                'umur_pemberian' => '6 bulan keatas (setiap tahun)',
                'is_wajib' => false,
            ],
            [
                'nama_vaksin' => 'MMR',
                'deskripsi' => 'Vaksin Measles, Mumps, Rubella',
                'umur_pemberian' => '15 bulan',
                'is_wajib' => false,
            ],
            [
                'nama_vaksin' => 'Varicella',
                'deskripsi' => 'Vaksin cacar air',
                'umur_pemberian' => '12-18 bulan',
                'is_wajib' => false,
            ],
            [
                'nama_vaksin' => 'Hepatitis A',
                'deskripsi' => 'Vaksin Hepatitis A',
                'umur_pemberian' => '2 tahun (2 dosis)',
                'is_wajib' => false,
            ],
            [
                'nama_vaksin' => 'Tifoid',
                'deskripsi' => 'Vaksin tifus',
                'umur_pemberian' => '2 tahun keatas',
                'is_wajib' => false,
            ],
        ];

        foreach ($vaksin as $v) {
            DB::table('master_vaksin')->insert([
                'nama_vaksin' => $v['nama_vaksin'],
                'deskripsi' => $v['deskripsi'],
                'umur_pemberian' => $v['umur_pemberian'],
                'is_wajib' => $v['is_wajib'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✅ Master Vaksin seeded successfully!');
        $this->command->info('   - 13 Vaksin Wajib');
        $this->command->info('   - 7 Vaksin Tambahan');
    }
}