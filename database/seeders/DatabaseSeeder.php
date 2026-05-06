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
        $this->command->info('║           POSCARE DATABASE SEEDER - PRODUCTION            ║');
        $this->command->info('╚═══════════════════════════════════════════════════════════╝');
        $this->command->info('');

        $startTime = microtime(true);

        // 1. Akun default (admin & kader) — harus pertama
        $this->command->info('👤 Seeding Users (Admin & Kader default)...');
        $this->call(UserSeeder::class);

        // 2. Data Lansia
        $this->command->info('📋 Seeding Lansia...');
        $this->call(LansiaSeeder::class);

        $endTime       = microtime(true);
        $executionTime = round($endTime - $startTime, 2);

        $this->command->info('');
        $this->command->info('╔═══════════════════════════════════════════════════════════╗');
        $this->command->info('║                  SEEDING COMPLETED! ✅                    ║');
        $this->command->info('╚═══════════════════════════════════════════════════════════╝');
        $this->command->info('');
        $this->command->info("⏱️  Execution Time: {$executionTime} seconds");
        $this->command->info('');
        $this->command->info('📊 DATABASE SUMMARY:');
        $this->command->info('   • Kader  : kader@poscare.id   / PosCare@2025');
        $this->command->info('   • Lansia : 30 data lansia');
        $this->command->info('');
        $this->command->warn('⚠️  PENTING: Segera ganti password setelah pertama kali login!');
        $this->command->info('');
    }
}
