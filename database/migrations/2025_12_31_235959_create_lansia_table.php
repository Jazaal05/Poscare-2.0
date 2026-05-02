<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Tabel utama data lansia ──────────────────────────────────
        Schema::create('lansia', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nik', 16)->unique();
            $table->string('nama_lengkap', 100);
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->date('tanggal_lahir');
            $table->string('tempat_lahir', 100)->nullable();
            $table->string('alamat', 255)->nullable();
            $table->string('rt_rw', 10)->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->string('nama_wali', 100)->nullable();
            $table->string('hubungan_wali', 50)->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->integer('created_by')->nullable(); // ref ke users.id (int)
        });

        // ── Tabel pemeriksaan kesehatan lansia ───────────────────────
        Schema::create('pemeriksaan_lansia', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('lansia_id');
            $table->foreign('lansia_id')->references('id')->on('lansia')->cascadeOnDelete();
            $table->date('tanggal_periksa');
            $table->decimal('berat_badan', 5, 2)->nullable();
            $table->decimal('tinggi_badan', 5, 2)->nullable();
            $table->string('tekanan_darah', 20)->nullable();  // contoh: 120/80
            $table->decimal('gula_darah', 6, 2)->nullable();  // mg/dL
            $table->decimal('asam_urat', 5, 2)->nullable();   // mg/dL
            $table->decimal('kolesterol', 6, 2)->nullable();  // mg/dL
            $table->text('catatan')->nullable();
            $table->integer('dicatat_oleh')->nullable();
        });

        // ── Tabel pengobatan lansia (checklist obat/vitamin) ─────────
        Schema::create('pengobatan_lansia', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('lansia_id');
            $table->foreign('lansia_id')->references('id')->on('lansia')->cascadeOnDelete();
            $table->date('tanggal');
            $table->json('keluhan')->nullable();
            $table->json('obat_diberikan')->nullable();
            $table->json('vitamin_diberikan')->nullable();
            $table->boolean('ada_keluhan')->default(false);
            $table->text('catatan')->nullable();
            $table->integer('dicatat_oleh')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengobatan_lansia');
        Schema::dropIfExists('pemeriksaan_lansia');
        Schema::dropIfExists('lansia');
    }
};
