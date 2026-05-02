<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * 
     * ═══════════════════════════════════════════════════════════════════
     * POSCARE DEVELOPMENT SEEDER
     * ═══════════════════════════════════════════════════════════════════
     * 
     * Seeder ini akan mengisi database dengan data dummy untuk development.
     * 
     * URUTAN SEEDING:
     * 1. UserSeeder          - 3 kader + 10 orangtua
     * 2. MasterVaksinSeeder  - 20 jenis vaksin
     * 3. AnakSeeder          - 10-20 anak (1-2 per orangtua)
     * 4. LansiaSeeder        - 20 lansia
     * 5. RiwayatPengukuranSeeder - Riwayat pengukuran anak
     * 6. ImunisasiSeeder     - Riwayat imunisasi anak
     * 7. KunjunganLansiaSeeder - Riwayat kunjungan lansia
     * 8. JadwalSeeder        - Jadwal posyandu 3 bulan ke depan
     * 9. EdukasiSeeder       - Konten edukasi (YouTube + Artikel)
     * 10. LaporanSeeder      - Laporan bulanan dan triwulan
     * 
     * CARA MENGGUNAKAN:
     * php artisan migrate:fresh --seed
     * 
     * CATATAN:
     * - Semua password default: "password"
     * - Data bersifat realistis dengan nama Indonesia
     * - Status gizi dan kesehatan dihitung otomatis
     * 
     * ═══════════════════════════════════════════════════════════════════
     */
    public function run(): void
    {
        $this->command->info('');
        $this->command->info('╔═══════════════════════════════════════════════════════════╗');
        $this->command->info('║         POSCARE DATABASE SEEDER - DEVELOPMENT             ║');
        $this->command->info('╚═══════════════════════════════════════════════════════════╝');
        $this->command->info('');

        $startTime = microtime(true);

        // ═══════════════════════════════════════════════════════════
        // STEP 1: USERS & MASTER DATA
        // ═══════════════════════════════════════════════════════════
        
        $this->command->info('📋 [1/10] Seeding Users...');
        $this->call(UserSeeder::class);
        
        $this->command->info('📋 [2/10] Seeding Master Vaksin...');
        $this->call(MasterVaksinSeeder::class);

        // ═══════════════════════════════════════════════════════════
        // STEP 2: ANAK & LANSIA
        // ═══════════════════════════════════════════════════════════
        
        $this->command->info('📋 [3/10] Seeding Anak...');
        $this->call(AnakSeeder::class);
        
        $this->command->info('📋 [4/10] Seeding Lansia...');
        $this->call(LansiaSeeder::class);

        // ═══════════════════════════════════════════════════════════
        // STEP 3: RIWAYAT KESEHATAN
        // ═══════════════════════════════════════════════════════════
        
        $this->command->info('📋 [5/10] Seeding Riwayat Pengukuran...');
        $this->call(RiwayatPengukuranSeeder::class);
        
        $this->command->info('📋 [6/10] Seeding Imunisasi...');
        $this->call(ImunisasiSeeder::class);
        
        $this->command->info('📋 [7/10] Seeding Kunjungan Lansia...');
        $this->call(KunjunganLansiaSeeder::class);

        // ═══════════════════════════════════════════════════════════
        // STEP 4: JADWAL, EDUKASI, LAPORAN
        // ═══════════════════════════════════════════════════════════
        
        $this->command->info('📋 [8/10] Seeding Jadwal...');
        $this->call(JadwalSeeder::class);
        
        $this->command->info('📋 [9/10] Seeding Edukasi Content...');
        $this->call(EdukasiSeeder::class);
        
        $this->command->info('📋 [10/10] Seeding Laporan...');
        $this->call(LaporanSeeder::class);

        // ═══════════════════════════════════════════════════════════
        // SUMMARY
        // ═══════════════════════════════════════════════════════════
        
        $endTime = microtime(true);
        $executionTime = round($endTime - $startTime, 2);

        $this->command->info('');
        $this->command->info('╔═══════════════════════════════════════════════════════════╗');
        $this->command->info('║                  SEEDING COMPLETED! ✅                    ║');
        $this->command->info('╚═══════════════════════════════════════════════════════════╝');
        $this->command->info('');
        $this->command->info("⏱️  Execution Time: {$executionTime} seconds");
        $this->command->info('');
        $this->command->info('📊 DATABASE SUMMARY:');
        $this->command->info('   • Users: 13 (3 kader + 10 orangtua)');
        $this->command->info('   • Anak: ~15-20 balita');
        $this->command->info('   • Lansia: 20 lansia');
        $this->command->info('   • Vaksin: 20 jenis vaksin');
        $this->command->info('   • Riwayat Pengukuran: ~100+ records');
        $this->command->info('   • Imunisasi: ~50+ records');
        $this->command->info('   • Kunjungan Lansia: ~100+ records');
        $this->command->info('   • Jadwal: ~30+ jadwal');
        $this->command->info('   • Edukasi: 20 konten');
        $this->command->info('   • Laporan: 18 laporan');
        $this->command->info('');
        $this->command->info('🔐 LOGIN CREDENTIALS:');
        $this->command->info('   Admin/Kader:');
        $this->command->info('   • Username: admin | Password: password');
        $this->command->info('   • Username: kader1 | Password: password');
        $this->command->info('   • Username: kader2 | Password: password');
        $this->command->info('');
        $this->command->info('   Orangtua (untuk testing API):');
        $this->command->info('   • Username: budi.santoso | Password: password');
        $this->command->info('   • Username: ahmad.wijaya | Password: password');
        $this->command->info('');
        $this->command->info('💡 NEXT STEPS:');
        $this->command->info('   1. Start server: php artisan serve');
        $this->command->info('   2. Login dengan kredensial di atas');
        $this->command->info('   3. Explore fitur-fitur yang tersedia');
        $this->command->info('');
        $this->command->info('📖 DOCUMENTATION:');
        $this->command->info('   • API_DOCUMENTATION.md - API reference');
        $this->command->info('   • DEVELOPER_GUIDE.md - Developer guide');
        $this->command->info('   • QUICK_REFERENCE.md - Quick reference');
        $this->command->info('');
    }
}
