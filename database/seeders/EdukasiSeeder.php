<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EdukasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $edukasiData = [
            // ═══════════════════════════════════════════════════════════
            // KONTEN BALITA
            // ═══════════════════════════════════════════════════════════
            
            // YouTube - Balita
            [
                'platform' => 'youtube',
                'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'title' => 'Cara Mengatasi Anak Susah Makan',
                'category' => 'Nutrisi',
                'thumbnail' => 'https://img.youtube.com/vi/dQw4w9WgXcQ/maxresdefault.jpg',
                'duration' => '10:25',
                'penulis_id' => 1,
                'layanan' => 'balita',
            ],
            [
                'platform' => 'youtube',
                'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'title' => 'Stimulasi Tumbuh Kembang Anak 0-2 Tahun',
                'category' => 'Tumbuh Kembang',
                'thumbnail' => 'https://img.youtube.com/vi/dQw4w9WgXcQ/maxresdefault.jpg',
                'duration' => '15:30',
                'penulis_id' => 1,
                'layanan' => 'balita',
            ],
            [
                'platform' => 'youtube',
                'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'title' => 'Pentingnya Imunisasi Lengkap untuk Balita',
                'category' => 'Imunisasi',
                'thumbnail' => 'https://img.youtube.com/vi/dQw4w9WgXcQ/maxresdefault.jpg',
                'duration' => '8:45',
                'penulis_id' => 2,
                'layanan' => 'balita',
            ],
            [
                'platform' => 'youtube',
                'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'title' => 'Mencegah Stunting Sejak Dini',
                'category' => 'Nutrisi',
                'thumbnail' => 'https://img.youtube.com/vi/dQw4w9WgXcQ/maxresdefault.jpg',
                'duration' => '12:15',
                'penulis_id' => 1,
                'layanan' => 'balita',
            ],
            [
                'platform' => 'youtube',
                'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'title' => 'MPASI Sehat dan Bergizi untuk Bayi 6 Bulan',
                'category' => 'Nutrisi',
                'thumbnail' => 'https://img.youtube.com/vi/dQw4w9WgXcQ/maxresdefault.jpg',
                'duration' => '18:20',
                'penulis_id' => 2,
                'layanan' => 'balita',
            ],

            // Artikel - Balita
            [
                'platform' => 'artikel',
                'url' => 'https://www.idai.or.id/artikel/klinik/asi/manfaat-asi',
                'title' => 'Manfaat ASI Eksklusif 6 Bulan',
                'category' => 'Nutrisi',
                'thumbnail' => null,
                'duration' => null,
                'penulis_id' => 1,
                'layanan' => 'balita',
            ],
            [
                'platform' => 'artikel',
                'url' => 'https://www.idai.or.id/artikel/klinik/imunisasi',
                'title' => 'Jadwal Imunisasi Anak Terbaru',
                'category' => 'Imunisasi',
                'thumbnail' => null,
                'duration' => null,
                'penulis_id' => 2,
                'layanan' => 'balita',
            ],
            [
                'platform' => 'artikel',
                'url' => 'https://www.idai.or.id/artikel/seputar-kesehatan-anak/tumbuh-kembang',
                'title' => 'Tahapan Tumbuh Kembang Anak 0-5 Tahun',
                'category' => 'Tumbuh Kembang',
                'thumbnail' => null,
                'duration' => null,
                'penulis_id' => 1,
                'layanan' => 'balita',
            ],
            [
                'platform' => 'artikel',
                'url' => 'https://www.idai.or.id/artikel/klinik/pengasuhan-anak/bermain',
                'title' => 'Pentingnya Bermain untuk Perkembangan Anak',
                'category' => 'Tumbuh Kembang',
                'thumbnail' => null,
                'duration' => null,
                'penulis_id' => 2,
                'layanan' => 'balita',
            ],
            [
                'platform' => 'artikel',
                'url' => 'https://www.idai.or.id/artikel/klinik/asi/menyusui',
                'title' => 'Tips Menyusui yang Benar',
                'category' => 'Nutrisi',
                'thumbnail' => null,
                'duration' => null,
                'penulis_id' => 1,
                'layanan' => 'balita',
            ],

            // ═══════════════════════════════════════════════════════════
            // KONTEN LANSIA
            // ═══════════════════════════════════════════════════════════
            
            // YouTube - Lansia
            [
                'platform' => 'youtube',
                'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'title' => 'Senam Lansia untuk Kesehatan Jantung',
                'category' => 'Olahraga',
                'thumbnail' => 'https://img.youtube.com/vi/dQw4w9WgXcQ/maxresdefault.jpg',
                'duration' => '20:00',
                'penulis_id' => 2,
                'layanan' => 'lansia',
            ],
            [
                'platform' => 'youtube',
                'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'title' => 'Mengelola Diabetes di Usia Lanjut',
                'category' => 'Penyakit Kronis',
                'thumbnail' => 'https://img.youtube.com/vi/dQw4w9WgXcQ/maxresdefault.jpg',
                'duration' => '14:30',
                'penulis_id' => 1,
                'layanan' => 'lansia',
            ],
            [
                'platform' => 'youtube',
                'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'title' => 'Diet Sehat untuk Penderita Hipertensi',
                'category' => 'Nutrisi',
                'thumbnail' => 'https://img.youtube.com/vi/dQw4w9WgXcQ/maxresdefault.jpg',
                'duration' => '11:45',
                'penulis_id' => 2,
                'layanan' => 'lansia',
            ],
            [
                'platform' => 'youtube',
                'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'title' => 'Mencegah Osteoporosis pada Lansia',
                'category' => 'Kesehatan Tulang',
                'thumbnail' => 'https://img.youtube.com/vi/dQw4w9WgXcQ/maxresdefault.jpg',
                'duration' => '9:20',
                'penulis_id' => 1,
                'layanan' => 'lansia',
            ],
            [
                'platform' => 'youtube',
                'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'title' => 'Menjaga Kesehatan Mental di Usia Senja',
                'category' => 'Kesehatan Mental',
                'thumbnail' => 'https://img.youtube.com/vi/dQw4w9WgXcQ/maxresdefault.jpg',
                'duration' => '13:10',
                'penulis_id' => 2,
                'layanan' => 'lansia',
            ],

            // Artikel - Lansia
            [
                'platform' => 'artikel',
                'url' => 'https://www.alodokter.com/hipertensi',
                'title' => 'Mengenal Hipertensi dan Cara Mengatasinya',
                'category' => 'Penyakit Kronis',
                'thumbnail' => null,
                'duration' => null,
                'penulis_id' => 1,
                'layanan' => 'lansia',
            ],
            [
                'platform' => 'artikel',
                'url' => 'https://www.alodokter.com/kolesterol-tinggi',
                'title' => 'Kolesterol Tinggi: Penyebab dan Pencegahan',
                'category' => 'Penyakit Kronis',
                'thumbnail' => null,
                'duration' => null,
                'penulis_id' => 2,
                'layanan' => 'lansia',
            ],
            [
                'platform' => 'artikel',
                'url' => 'https://www.alodokter.com/asam-urat',
                'title' => 'Asam Urat: Gejala dan Pengobatan',
                'category' => 'Penyakit Kronis',
                'thumbnail' => null,
                'duration' => null,
                'penulis_id' => 1,
                'layanan' => 'lansia',
            ],
            [
                'platform' => 'artikel',
                'url' => 'https://www.alodokter.com/nutrisi-lansia',
                'title' => 'Panduan Nutrisi untuk Lansia',
                'category' => 'Nutrisi',
                'thumbnail' => null,
                'duration' => null,
                'penulis_id' => 2,
                'layanan' => 'lansia',
            ],
            [
                'platform' => 'artikel',
                'url' => 'https://www.alodokter.com/olahraga-lansia',
                'title' => 'Olahraga yang Aman untuk Lansia',
                'category' => 'Olahraga',
                'thumbnail' => null,
                'duration' => null,
                'penulis_id' => 1,
                'layanan' => 'lansia',
            ],
        ];

        DB::table('edukasi_content')->insert($edukasiData);

        $this->command->info('✅ Edukasi Content seeded successfully!');
        $this->command->info('   - Total: ' . count($edukasiData) . ' konten');
        $this->command->info('   - Balita: 10 konten (5 YouTube + 5 Artikel)');
        $this->command->info('   - Lansia: 10 konten (5 YouTube + 5 Artikel)');
    }
}
