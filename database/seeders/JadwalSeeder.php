<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class JadwalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jenisKegiatan = [
            'Posyandu Balita',
            'Posyandu Lansia',
            'Imunisasi',
            'Pemeriksaan Kesehatan',
            'Penyuluhan Kesehatan',
            'Senam Lansia',
        ];

        $lokasiOptions = [
            'Balai Desa Nganjuk',
            'Posyandu RW 01',
            'Posyandu RW 02',
            'Posyandu RW 03',
            'Puskesmas Nganjuk',
            'Balai RT 05',
            'Gedung Serbaguna Desa',
        ];

        $keteranganTemplates = [
            'Posyandu Balita' => 'Penimbangan dan pemeriksaan tumbuh kembang balita. Harap membawa KMS.',
            'Posyandu Lansia' => 'Pemeriksaan kesehatan lansia meliputi tensi, gula darah, dan kolesterol.',
            'Imunisasi' => 'Pelaksanaan imunisasi dasar dan lanjutan untuk balita.',
            'Pemeriksaan Kesehatan' => 'Pemeriksaan kesehatan umum dan konsultasi gratis.',
            'Penyuluhan Kesehatan' => 'Edukasi kesehatan untuk masyarakat.',
            'Senam Lansia' => 'Senam bersama untuk lansia. Harap membawa matras.',
        ];

        $layananOptions = ['balita', 'lansia', 'umum'];

        $jadwalData = [];

        // Generate jadwal untuk 3 bulan ke depan
        $startDate = Carbon::now();
        $endDate = Carbon::now()->addMonths(3);

        // Posyandu Balita - setiap minggu ke-2 dan ke-4
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            // Minggu ke-2 (hari ke 8-14)
            $tanggal = $currentDate->copy()->day(rand(8, 14));
            if ($tanggal <= $endDate) {
                $jadwalData[] = [
                    'nama_kegiatan' => 'Posyandu Balita RW 01',
                    'jenis_kegiatan' => 'Posyandu Balita',
                    'tanggal' => $tanggal->format('Y-m-d'),
                    'waktu_mulai' => '08:00',
                    'lokasi' => 'Posyandu RW 01',
                    'keterangan' => $keteranganTemplates['Posyandu Balita'],
                    'status' => $tanggal < Carbon::now() ? 'Selesai' : 'Terjadwal',
                    'layanan' => 'balita',
                    'created_by' => 1,
                    'is_posted' => true,
                ];
            }

            // Minggu ke-4 (hari ke 22-28)
            $tanggal = $currentDate->copy()->day(rand(22, 28));
            if ($tanggal <= $endDate) {
                $jadwalData[] = [
                    'nama_kegiatan' => 'Posyandu Balita RW 02',
                    'jenis_kegiatan' => 'Posyandu Balita',
                    'tanggal' => $tanggal->format('Y-m-d'),
                    'waktu_mulai' => '08:00',
                    'lokasi' => 'Posyandu RW 02',
                    'keterangan' => $keteranganTemplates['Posyandu Balita'],
                    'status' => $tanggal < Carbon::now() ? 'Selesai' : 'Terjadwal',
                    'layanan' => 'balita',
                    'created_by' => 1,
                    'is_posted' => true,
                ];
            }

            $currentDate->addMonth();
        }

        // Posyandu Lansia - setiap minggu ke-3
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $tanggal = $currentDate->copy()->day(rand(15, 21));
            if ($tanggal <= $endDate) {
                $jadwalData[] = [
                    'nama_kegiatan' => 'Posyandu Lansia',
                    'jenis_kegiatan' => 'Posyandu Lansia',
                    'tanggal' => $tanggal->format('Y-m-d'),
                    'waktu_mulai' => '09:00',
                    'lokasi' => 'Balai Desa Nganjuk',
                    'keterangan' => $keteranganTemplates['Posyandu Lansia'],
                    'status' => $tanggal < Carbon::now() ? 'Selesai' : 'Terjadwal',
                    'layanan' => 'lansia',
                    'created_by' => 2,
                    'is_posted' => true,
                ];
            }
            $currentDate->addMonth();
        }

        // Imunisasi - setiap bulan minggu pertama
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $tanggal = $currentDate->copy()->day(rand(1, 7));
            if ($tanggal <= $endDate) {
                $jadwalData[] = [
                    'nama_kegiatan' => 'Imunisasi Rutin',
                    'jenis_kegiatan' => 'Imunisasi',
                    'tanggal' => $tanggal->format('Y-m-d'),
                    'waktu_mulai' => '08:30',
                    'lokasi' => 'Puskesmas Nganjuk',
                    'keterangan' => $keteranganTemplates['Imunisasi'],
                    'status' => $tanggal < Carbon::now() ? 'Selesai' : 'Terjadwal',
                    'layanan' => 'balita',
                    'created_by' => 1,
                    'is_posted' => true,
                ];
            }
            $currentDate->addMonth();
        }

        // Senam Lansia - setiap 2 minggu sekali
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $jadwalData[] = [
                'nama_kegiatan' => 'Senam Lansia Sehat',
                'jenis_kegiatan' => 'Senam Lansia',
                'tanggal' => $currentDate->format('Y-m-d'),
                'waktu_mulai' => '07:00',
                'lokasi' => 'Gedung Serbaguna Desa',
                'keterangan' => $keteranganTemplates['Senam Lansia'],
                'status' => $currentDate < Carbon::now() ? 'Selesai' : 'Terjadwal',
                'layanan' => 'lansia',
                'created_by' => 2,
                'is_posted' => true,
            ];
            $currentDate->addWeeks(2);
        }

        // Penyuluhan - acara khusus
        $penyuluhanTopik = [
            'Gizi Seimbang untuk Balita',
            'Pencegahan Stunting',
            'Hidup Sehat di Usia Lanjut',
            'Manajemen Diabetes',
            'Hipertensi dan Pencegahannya',
        ];

        for ($i = 0; $i < 5; $i++) {
            $tanggal = Carbon::now()->addDays(rand(10, 90));
            $jadwalData[] = [
                'nama_kegiatan' => $penyuluhanTopik[$i],
                'jenis_kegiatan' => 'Penyuluhan Kesehatan',
                'tanggal' => $tanggal->format('Y-m-d'),
                'waktu_mulai' => '13:00',
                'lokasi' => $lokasiOptions[array_rand($lokasiOptions)],
                'keterangan' => $keteranganTemplates['Penyuluhan Kesehatan'] . ' Topik: ' . $penyuluhanTopik[$i],
                'status' => 'Terjadwal',
                'layanan' => $i < 2 ? 'balita' : ($i < 4 ? 'lansia' : 'umum'),
                'created_by' => rand(1, 3),
                'is_posted' => true,
            ];
        }

        // Insert semua jadwal
        DB::table('jadwal')->insert($jadwalData);

        $this->command->info('✅ Jadwal seeded successfully!');
        $this->command->info('   - Total: ' . count($jadwalData) . ' jadwal');
        $this->command->info('   - Periode: 3 bulan ke depan');
        $this->command->info('   - Jenis: Posyandu, Imunisasi, Senam, Penyuluhan');
    }
}
