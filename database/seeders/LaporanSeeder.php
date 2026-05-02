<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $laporanData = [
            // ═══════════════════════════════════════════════════════════
            // LAPORAN BALITA
            // ═══════════════════════════════════════════════════════════
            
            [
                'judul' => 'Laporan Penimbangan Balita Januari 2026',
                'jenis_laporan' => 'Penimbangan Balita',
                'format_file' => 'PDF',
                'periode_awal' => '2026-01-01',
                'periode_akhir' => '2026-01-31',
                'file_path' => 'laporan/balita/penimbangan_januari_2026.pdf',
                'created_by' => 1,
            ],
            [
                'judul' => 'Laporan Penimbangan Balita Februari 2026',
                'jenis_laporan' => 'Penimbangan Balita',
                'format_file' => 'PDF',
                'periode_awal' => '2026-02-01',
                'periode_akhir' => '2026-02-28',
                'file_path' => 'laporan/balita/penimbangan_februari_2026.pdf',
                'created_by' => 1,
            ],
            [
                'judul' => 'Laporan Penimbangan Balita Maret 2026',
                'jenis_laporan' => 'Penimbangan Balita',
                'format_file' => 'PDF',
                'periode_awal' => '2026-03-01',
                'periode_akhir' => '2026-03-31',
                'file_path' => 'laporan/balita/penimbangan_maret_2026.pdf',
                'created_by' => 1,
            ],
            [
                'judul' => 'Laporan Penimbangan Balita April 2026',
                'jenis_laporan' => 'Penimbangan Balita',
                'format_file' => 'PDF',
                'periode_awal' => '2026-04-01',
                'periode_akhir' => '2026-04-30',
                'file_path' => 'laporan/balita/penimbangan_april_2026.pdf',
                'created_by' => 1,
            ],
            
            // Laporan Imunisasi
            [
                'judul' => 'Laporan Imunisasi Triwulan I 2026',
                'jenis_laporan' => 'Imunisasi',
                'format_file' => 'Excel',
                'periode_awal' => '2026-01-01',
                'periode_akhir' => '2026-03-31',
                'file_path' => 'laporan/balita/imunisasi_tw1_2026.xlsx',
                'created_by' => 2,
            ],
            [
                'judul' => 'Laporan Imunisasi Triwulan II 2026',
                'jenis_laporan' => 'Imunisasi',
                'format_file' => 'Excel',
                'periode_awal' => '2026-04-01',
                'periode_akhir' => '2026-06-30',
                'file_path' => 'laporan/balita/imunisasi_tw2_2026.xlsx',
                'created_by' => 2,
            ],
            
            // Laporan Status Gizi
            [
                'judul' => 'Laporan Status Gizi Balita Semester I 2026',
                'jenis_laporan' => 'Status Gizi',
                'format_file' => 'PDF',
                'periode_awal' => '2026-01-01',
                'periode_akhir' => '2026-06-30',
                'file_path' => 'laporan/balita/status_gizi_sem1_2026.pdf',
                'created_by' => 1,
            ],
            
            // ═══════════════════════════════════════════════════════════
            // LAPORAN LANSIA
            // ═══════════════════════════════════════════════════════════
            
            [
                'judul' => 'Laporan Pemeriksaan Lansia Januari 2026',
                'jenis_laporan' => 'Pemeriksaan Lansia',
                'format_file' => 'PDF',
                'periode_awal' => '2026-01-01',
                'periode_akhir' => '2026-01-31',
                'file_path' => 'laporan/lansia/pemeriksaan_januari_2026.pdf',
                'created_by' => 2,
            ],
            [
                'judul' => 'Laporan Pemeriksaan Lansia Februari 2026',
                'jenis_laporan' => 'Pemeriksaan Lansia',
                'format_file' => 'PDF',
                'periode_awal' => '2026-02-01',
                'periode_akhir' => '2026-02-28',
                'file_path' => 'laporan/lansia/pemeriksaan_februari_2026.pdf',
                'created_by' => 2,
            ],
            [
                'judul' => 'Laporan Pemeriksaan Lansia Maret 2026',
                'jenis_laporan' => 'Pemeriksaan Lansia',
                'format_file' => 'PDF',
                'periode_awal' => '2026-03-01',
                'periode_akhir' => '2026-03-31',
                'file_path' => 'laporan/lansia/pemeriksaan_maret_2026.pdf',
                'created_by' => 2,
            ],
            [
                'judul' => 'Laporan Pemeriksaan Lansia April 2026',
                'jenis_laporan' => 'Pemeriksaan Lansia',
                'format_file' => 'PDF',
                'periode_awal' => '2026-04-01',
                'periode_akhir' => '2026-04-30',
                'file_path' => 'laporan/lansia/pemeriksaan_april_2026.pdf',
                'created_by' => 2,
            ],
            
            // Laporan Kesehatan Lansia
            [
                'judul' => 'Laporan Kesehatan Lansia Triwulan I 2026',
                'jenis_laporan' => 'Kesehatan Lansia',
                'format_file' => 'Excel',
                'periode_awal' => '2026-01-01',
                'periode_akhir' => '2026-03-31',
                'file_path' => 'laporan/lansia/kesehatan_tw1_2026.xlsx',
                'created_by' => 3,
            ],
            [
                'judul' => 'Laporan Kesehatan Lansia Triwulan II 2026',
                'jenis_laporan' => 'Kesehatan Lansia',
                'format_file' => 'Excel',
                'periode_awal' => '2026-04-01',
                'periode_akhir' => '2026-06-30',
                'file_path' => 'laporan/lansia/kesehatan_tw2_2026.xlsx',
                'created_by' => 3,
            ],
            
            // ═══════════════════════════════════════════════════════════
            // LAPORAN UMUM
            // ═══════════════════════════════════════════════════════════
            
            [
                'judul' => 'Laporan Kegiatan Posyandu Januari 2026',
                'jenis_laporan' => 'Kegiatan Posyandu',
                'format_file' => 'PDF',
                'periode_awal' => '2026-01-01',
                'periode_akhir' => '2026-01-31',
                'file_path' => 'laporan/umum/kegiatan_januari_2026.pdf',
                'created_by' => 1,
            ],
            [
                'judul' => 'Laporan Kegiatan Posyandu Februari 2026',
                'jenis_laporan' => 'Kegiatan Posyandu',
                'format_file' => 'PDF',
                'periode_awal' => '2026-02-01',
                'periode_akhir' => '2026-02-28',
                'file_path' => 'laporan/umum/kegiatan_februari_2026.pdf',
                'created_by' => 1,
            ],
            [
                'judul' => 'Laporan Kegiatan Posyandu Maret 2026',
                'jenis_laporan' => 'Kegiatan Posyandu',
                'format_file' => 'PDF',
                'periode_awal' => '2026-03-01',
                'periode_akhir' => '2026-03-31',
                'file_path' => 'laporan/umum/kegiatan_maret_2026.pdf',
                'created_by' => 1,
            ],
            [
                'judul' => 'Laporan Kegiatan Posyandu April 2026',
                'jenis_laporan' => 'Kegiatan Posyandu',
                'format_file' => 'PDF',
                'periode_awal' => '2026-04-01',
                'periode_akhir' => '2026-04-30',
                'file_path' => 'laporan/umum/kegiatan_april_2026.pdf',
                'created_by' => 1,
            ],
            
            // Laporan Tahunan
            [
                'judul' => 'Laporan Tahunan Posyandu 2025',
                'jenis_laporan' => 'Tahunan',
                'format_file' => 'PDF',
                'periode_awal' => '2025-01-01',
                'periode_akhir' => '2025-12-31',
                'file_path' => 'laporan/umum/tahunan_2025.pdf',
                'created_by' => 1,
            ],
        ];

        DB::table('laporan')->insert($laporanData);

        $this->command->info('✅ Laporan seeded successfully!');
        $this->command->info('   - Total: ' . count($laporanData) . ' laporan');
        $this->command->info('   - Balita: 7 laporan');
        $this->command->info('   - Lansia: 6 laporan');
        $this->command->info('   - Umum: 5 laporan');
    }
}
