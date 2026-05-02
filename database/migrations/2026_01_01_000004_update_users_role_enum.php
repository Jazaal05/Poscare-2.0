<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Update kolom role di tabel users
        // Hanya ada 2 role: kader (admin) dan orangtua (user)
        try {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('kader', 'orangtua') NOT NULL DEFAULT 'orangtua'");
            
            // Update role 'admin' menjadi 'kader' jika ada
            DB::statement("UPDATE users SET role = 'kader' WHERE role = 'admin'");
            
        } catch (\Exception $e) {
            \Log::warning("Migration warning: " . $e->getMessage());
        }
    }

    public function down(): void
    {
        // Rollback ke struktur lama jika diperlukan
        try {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'kader', 'orangtua') NOT NULL DEFAULT 'orangtua'");
        } catch (\Exception $e) {
            \Log::warning("Migration rollback warning: " . $e->getMessage());
        }
    }
};