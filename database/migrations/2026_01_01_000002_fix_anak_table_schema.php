<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tambahkan kolom yang hilang ke tabel anak
        try {
            // Cek apakah kolom no_registrasi sudah ada
            $hasNoRegistrasi = DB::select("SHOW COLUMNS FROM anak LIKE 'no_registrasi'");
            if (empty($hasNoRegistrasi)) {
                DB::statement("ALTER TABLE anak ADD COLUMN no_registrasi VARCHAR(20) NULL AFTER id");
            }

            // Cek apakah kolom tanggal_lahir_ibu sudah ada
            $hasTanggalLahirIbu = DB::select("SHOW COLUMNS FROM anak LIKE 'tanggal_lahir_ibu'");
            if (empty($hasTanggalLahirIbu)) {
                DB::statement("ALTER TABLE anak ADD COLUMN tanggal_lahir_ibu DATE NULL AFTER nik_ibu");
            }

            // Tambahkan timestamps jika belum ada
            $hasCreatedAt = DB::select("SHOW COLUMNS FROM anak LIKE 'created_at'");
            if (empty($hasCreatedAt)) {
                DB::statement("ALTER TABLE anak ADD COLUMN created_at TIMESTAMP NULL");
                DB::statement("ALTER TABLE anak ADD COLUMN updated_at TIMESTAMP NULL");
            }

            // Tambahkan foreign key constraint untuk user_id jika belum ada
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'anak' 
                AND CONSTRAINT_NAME LIKE '%user_id%'
            ");
            
            if (empty($foreignKeys)) {
                DB::statement("ALTER TABLE anak ADD CONSTRAINT anak_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL");
            }

            // Tambahkan indexes untuk performance
            $indexes = DB::select("SHOW INDEX FROM anak WHERE Key_name = 'anak_user_id_index'");
            if (empty($indexes)) {
                DB::statement("ALTER TABLE anak ADD INDEX anak_user_id_index (user_id)");
            }

            $indexes = DB::select("SHOW INDEX FROM anak WHERE Key_name = 'anak_nik_anak_index'");
            if (empty($indexes)) {
                DB::statement("ALTER TABLE anak ADD INDEX anak_nik_anak_index (nik_anak)");
            }

            $indexes = DB::select("SHOW INDEX FROM anak WHERE Key_name = 'anak_is_deleted_index'");
            if (empty($indexes)) {
                DB::statement("ALTER TABLE anak ADD INDEX anak_is_deleted_index (is_deleted)");
            }

        } catch (Exception $e) {
            // Log error but don't fail migration
            \Log::warning("Migration warning: " . $e->getMessage());
        }

        // Tambahkan timestamps ke tabel lain
        $tables = ['users', 'lansia', 'kunjungan_lansia', 'imunisasi', 'edukasi_content'];
        
        foreach ($tables as $table) {
            try {
                $hasCreatedAt = DB::select("SHOW COLUMNS FROM {$table} LIKE 'created_at'");
                if (empty($hasCreatedAt)) {
                    DB::statement("ALTER TABLE {$table} ADD COLUMN created_at TIMESTAMP NULL");
                    DB::statement("ALTER TABLE {$table} ADD COLUMN updated_at TIMESTAMP NULL");
                }
            } catch (Exception $e) {
                \Log::warning("Migration warning for table {$table}: " . $e->getMessage());
            }
        }

        // Tambahkan indexes untuk performance
        try {
            $indexes = DB::select("SHOW INDEX FROM users WHERE Key_name = 'users_username_index'");
            if (empty($indexes)) {
                DB::statement("ALTER TABLE users ADD INDEX users_username_index (username)");
            }

            $indexes = DB::select("SHOW INDEX FROM users WHERE Key_name = 'users_role_index'");
            if (empty($indexes)) {
                DB::statement("ALTER TABLE users ADD INDEX users_role_index (role)");
            }

            $indexes = DB::select("SHOW INDEX FROM lansia WHERE Key_name = 'lansia_nik_index'");
            if (empty($indexes)) {
                DB::statement("ALTER TABLE lansia ADD INDEX lansia_nik_index (nik)");
            }

            $indexes = DB::select("SHOW INDEX FROM lansia WHERE Key_name = 'lansia_is_deleted_index'");
            if (empty($indexes)) {
                DB::statement("ALTER TABLE lansia ADD INDEX lansia_is_deleted_index (is_deleted)");
            }
        } catch (Exception $e) {
            \Log::warning("Migration warning for indexes: " . $e->getMessage());
        }
    }

    public function down(): void
    {
        // Rollback changes
        try {
            DB::statement("ALTER TABLE anak DROP FOREIGN KEY IF EXISTS anak_user_id_foreign");
            DB::statement("ALTER TABLE anak DROP INDEX IF EXISTS anak_user_id_index");
            DB::statement("ALTER TABLE anak DROP INDEX IF EXISTS anak_nik_anak_index");
            DB::statement("ALTER TABLE anak DROP INDEX IF EXISTS anak_is_deleted_index");
            DB::statement("ALTER TABLE anak DROP COLUMN IF EXISTS no_registrasi");
            DB::statement("ALTER TABLE anak DROP COLUMN IF EXISTS tanggal_lahir_ibu");
            DB::statement("ALTER TABLE anak DROP COLUMN IF EXISTS created_at");
            DB::statement("ALTER TABLE anak DROP COLUMN IF EXISTS updated_at");
        } catch (Exception $e) {
            \Log::warning("Migration rollback warning: " . $e->getMessage());
        }
    }
};