<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kunjungan_lansia', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('lansia_id');
            $table->foreign('lansia_id')->references('id')->on('lansia')->cascadeOnDelete();
            $table->date('tanggal_kunjungan');

            // ── Pengukuran Fisik ─────────────────────────────────────
            $table->decimal('berat_badan', 5, 2)->nullable();          // kg
            $table->string('tekanan_darah', 20)->nullable();           // "120/80"
            $table->enum('status_tensi', [
                'normal', 'prehipertensi', 'hipertensi1', 'hipertensi2'
            ])->nullable();

            // ── Cek Darah ────────────────────────────────────────────
            $table->decimal('gula_darah', 6, 2)->nullable();           // mg/dL
            $table->enum('status_gula', [
                'normal', 'rendah', 'tinggi', 'sangat_tinggi'
            ])->nullable();

            $table->decimal('kolesterol', 6, 2)->nullable();           // mg/dL
            $table->enum('status_kolesterol', [
                'normal', 'batas', 'tinggi'
            ])->nullable();

            $table->decimal('asam_urat', 5, 2)->nullable();            // mg/dL
            $table->enum('status_asam_urat', [
                'normal', 'tinggi'
            ])->nullable();

            // ── Pengobatan ───────────────────────────────────────────
            $table->boolean('ada_keluhan')->default(false);
            $table->text('keluhan')->nullable();
            $table->json('obat_diberikan')->nullable();
            $table->json('vitamin_diberikan')->nullable();

            // ── Tambahan ─────────────────────────────────────────────
            $table->text('catatan_bidan')->nullable();
            $table->unsignedInteger('dicatat_oleh')->nullable();
        });

        // Hapus tabel lama yang digantikan
        Schema::dropIfExists('pengobatan_lansia');
        Schema::dropIfExists('pemeriksaan_lansia');
    }

    public function down(): void
    {
        Schema::dropIfExists('kunjungan_lansia');
    }
};
