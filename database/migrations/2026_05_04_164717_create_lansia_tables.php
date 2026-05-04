<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * STRUKTUR DATABASE MODUL LANSIA
     * ===============================
     * 1. lansia - Data master lansia
     * 2. kunjungan_lansia - History kunjungan & pemeriksaan kesehatan
     * 3. jadwal_lansia - Jadwal kegiatan posyandu lansia
     * 4. edukasi_lansia - Konten edukasi untuk lansia
     */
    public function up(): void
    {
        // ============================================================
        // TABEL 1: LANSIA (Data Master)
        // ============================================================
        Schema::create('lansia', function (Blueprint $table) {
            $table->id();
            
            // Data Pribadi
            $table->string('nik_lansia', 16)->unique()->nullable();
            $table->string('nama_lengkap');
            $table->date('tgl_lahir');
            $table->string('tempat_lahir', 100)->nullable();
            $table->enum('jenis_kelamin', ['L', 'P']);
            
            // Alamat
            $table->text('alamat_domisili')->nullable();
            $table->string('rt_rw', 10)->nullable();
            
            // Data Keluarga
            $table->string('nama_kk', 255)->nullable();
            $table->string('nama_wali', 255)->nullable();
            $table->string('nik_wali', 16)->nullable();
            $table->string('hp_kontak_wali', 20)->nullable();
            
            // Data Kesehatan Terkini (dari kunjungan terakhir)
            $table->decimal('berat_badan', 5, 2)->nullable();
            $table->decimal('tinggi_badan', 5, 2)->nullable();
            $table->string('tekanan_darah', 20)->nullable();
            $table->decimal('gula_darah', 5, 2)->nullable();
            $table->decimal('kolesterol', 5, 2)->nullable();
            $table->decimal('asam_urat', 5, 2)->nullable();
            
            // Status Kesehatan
            $table->enum('status_kesehatan', [
                'Sehat',
                'Hipertensi',
                'Diabetes',
                'Kolesterol Tinggi',
                'Asam Urat Tinggi',
                'Penyakit Jantung',
                'Stroke',
                'Lainnya'
            ])->default('Sehat');
            
            $table->date('tanggal_pemeriksaan_terakhir')->nullable();
            
            // Metadata
            $table->unsignedBigInteger('dicatat_oleh')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
            
            // Indexes
            $table->index('nik_lansia');
            $table->index('nama_lengkap');
            $table->index('is_deleted');
            $table->index('status_kesehatan');
        });

        // ============================================================
        // TABEL 2: KUNJUNGAN_LANSIA (History Pemeriksaan)
        // ============================================================
        Schema::create('kunjungan_lansia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lansia_id')->constrained('lansia')->onDelete('cascade');
            $table->date('tanggal_kunjungan');
            
            // Pengukuran Fisik
            $table->decimal('berat_badan', 5, 2)->nullable();
            $table->decimal('tinggi_badan', 5, 2)->nullable();
            $table->string('tekanan_darah', 20)->nullable();
            $table->enum('status_tensi', [
                'normal',
                'prehipertensi',
                'hipertensi1',
                'hipertensi2'
            ])->nullable();
            
            // Pemeriksaan Darah
            $table->decimal('gula_darah', 5, 2)->nullable();
            $table->enum('status_gula', [
                'rendah',
                'normal',
                'tinggi',
                'sangat_tinggi'
            ])->nullable();
            
            $table->decimal('kolesterol', 5, 2)->nullable();
            $table->enum('status_kolesterol', [
                'normal',
                'batas',
                'tinggi'
            ])->nullable();
            
            $table->decimal('asam_urat', 5, 2)->nullable();
            $table->enum('status_asam_urat', [
                'normal',
                'tinggi'
            ])->nullable();
            
            // Keluhan & Pengobatan
            $table->boolean('ada_keluhan')->default(false);
            $table->text('keluhan')->nullable();
            $table->json('obat_diberikan')->nullable();
            $table->json('vitamin_diberikan')->nullable();
            
            // Catatan
            $table->text('catatan_bidan')->nullable();
            $table->unsignedBigInteger('dicatat_oleh')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('lansia_id');
            $table->index('tanggal_kunjungan');
            $table->index(['lansia_id', 'tanggal_kunjungan']);
        });

        // ============================================================
        // TABEL 3: JADWAL_LANSIA (Jadwal Kegiatan Posyandu)
        // ============================================================
        Schema::create('jadwal_lansia', function (Blueprint $table) {
            $table->id();
            $table->string('judul_kegiatan');
            $table->text('deskripsi')->nullable();
            $table->date('tanggal');
            $table->time('waktu_mulai'); // Format 24 jam (HH:mm)
            $table->string('lokasi')->nullable();
            $table->enum('jenis_kegiatan', [
                'Posyandu',
                'Senam Lansia',
                'Penyuluhan',
                'Pemeriksaan Kesehatan',
                'Lainnya'
            ])->default('Posyandu');
            $table->enum('status', [
                'dijadwalkan',
                'terlaksana',
                'dibatalkan'
            ])->default('dijadwalkan');
            
            // Metadata
            $table->unsignedBigInteger('dibuat_oleh')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('tanggal');
            $table->index('status');
            $table->index(['tanggal', 'status']);
        });

        // ============================================================
        // TABEL 4: EDUKASI_LANSIA (Konten Edukasi)
        // ============================================================
        Schema::create('edukasi_lansia', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->enum('platform', [
                'Youtube',
                'Tiktok',
                'Facebook',
                'Instagram',
                'Artikel'
            ]);
            $table->string('tautan', 500);
            $table->string('thumbnail', 500)->nullable();
            $table->enum('kategori', [
                'Kesehatan Lansia',
                'Pola Hidup Sehat',
                'Pencegahan Penyakit',
                'Gizi Lansia',
                'Olahraga Lansia',
                'Tips Lansia',
                'Lainnya'
            ])->default('Kesehatan Lansia');
            
            // Metadata
            $table->unsignedBigInteger('dibuat_oleh')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Indexes
            $table->index('platform');
            $table->index('kategori');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('edukasi_lansia');
        Schema::dropIfExists('jadwal_lansia');
        Schema::dropIfExists('kunjungan_lansia');
        Schema::dropIfExists('lansia');
    }
};
