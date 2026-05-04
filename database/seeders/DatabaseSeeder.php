<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('');
        $this->command->info('╔═══════════════════════════════════════════════════════════╗');
        $this->command->info('║         POSCARE DATABASE SEEDER - LANSIA ONLY             ║');
        $this->command->info('╚═══════════════════════════════════════════════════════════╝');
        $this->command->info('');

        $startTime = microtime(true);

        $this->command->info('📋 Seeding Lansia...');
        $this->call(LansiaSeeder::class);

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
        $this->command->info('   • Lansia: 30 lansia (baru ditambahkan)');
        $this->command->info('   • Anak: Menggunakan data dari SQL import');
        $this->command->info('   • Users: Menggunakan data dari SQL import');
        $this->command->info('   • Tabel lainnya: Dari SQL import');
        $this->command->info('');
    }
}
