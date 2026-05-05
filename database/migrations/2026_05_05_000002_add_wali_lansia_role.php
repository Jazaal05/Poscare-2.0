<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah 'wali_lansia' ke enum role
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','kader','orangtua','wali_lansia') NOT NULL DEFAULT 'orangtua'");
    }

    public function down(): void
    {
        // Kembalikan ke enum sebelumnya (pastikan tidak ada data wali_lansia dulu)
        DB::statement("UPDATE users SET role = 'orangtua' WHERE role = 'wali_lansia'");
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','kader','orangtua') NOT NULL DEFAULT 'orangtua'");
    }
};
