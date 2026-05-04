<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jadwal', function (Blueprint $table) {
            // Tambah kolom layanan jika belum ada
            if (!Schema::hasColumn('jadwal', 'layanan')) {
                $table->enum('layanan', ['balita', 'lansia'])->default('balita')->after('status');
            }
            // Tambah alias judul_kegiatan (kolom virtual alias tidak bisa, jadi tambah kolom baru)
            if (!Schema::hasColumn('jadwal', 'judul_kegiatan')) {
                $table->string('judul_kegiatan', 255)->nullable()->after('nama_kegiatan');
            }
            // Tambah tanggal_kegiatan alias
            if (!Schema::hasColumn('jadwal', 'tanggal_kegiatan')) {
                $table->date('tanggal_kegiatan')->nullable()->after('tanggal');
            }
            // Tambah dibuat_oleh
            if (!Schema::hasColumn('jadwal', 'dibuat_oleh')) {
                $table->unsignedBigInteger('dibuat_oleh')->nullable()->after('created_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('jadwal', function (Blueprint $table) {
            $cols = ['layanan', 'judul_kegiatan', 'tanggal_kegiatan', 'dibuat_oleh'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('jadwal', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
