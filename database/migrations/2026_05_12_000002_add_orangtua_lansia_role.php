<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah 'orangtua_lansia' ke enum role
        // Role ini untuk user yang punya data balita DAN lansia sekaligus
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','kader','orangtua','wali_lansia','orangtua_lansia') NOT NULL DEFAULT 'orangtua'");
    }

    public function down(): void
    {
        DB::statement("UPDATE users SET role = 'orangtua' WHERE role = 'orangtua_lansia'");
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','kader','orangtua','wali_lansia') NOT NULL DEFAULT 'orangtua'");
    }
};
