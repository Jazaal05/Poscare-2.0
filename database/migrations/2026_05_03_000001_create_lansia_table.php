<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Tabel lansia dengan struktur yang sama dengan tabel anak
     */
    public function up(): void
    {
        Schema::create('lansia', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('nik_lansia', 16)->nullable();
            $table->string('nama_lansia', 100);
            $table->date('tanggal_lahir');
            $table->string('tempat_lahir', 100)->nullable();
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->text('alamat_domisili')->nullable();
            $table->string('rt_rw', 10)->nullable()->comment('RT/RW format: 001/005');
            $table->string('nama_kk', 100)->nullable();
            $table->string('nama_wali', 100)->nullable()->comment('Nama anak/keluarga yang merawat');
            $table->string('nik_wali', 16)->nullable();
            $table->string('hp_kontak_wali', 15)->nullable();
            $table->decimal('berat_badan', 5, 2)->nullable()->comment('Berat badan terbaru (kg)');
            $table->decimal('tinggi_badan', 5, 2)->nullable()->comment('Tinggi badan terbaru (cm)');
            $table->string('tekanan_darah', 20)->nullable()->comment('Tekanan darah terbaru (contoh: 120/80)');
            $table->decimal('gula_darah', 6, 2)->nullable()->comment('Gula darah terbaru (mg/dL)');
            $table->decimal('kolesterol', 6, 2)->nullable()->comment('Kolesterol terbaru (mg/dL)');
            $table->decimal('asam_urat', 5, 2)->nullable()->comment('Asam urat terbaru (mg/dL)');
            $table->integer('user_id')->nullable();
            $table->string('status_kesehatan', 50)->default('Belum diperiksa')->comment('Status kesehatan terbaru');
            $table->text('status_kesehatan_detail')->nullable()->comment('JSON detail pemeriksaan kesehatan');
            $table->date('tanggal_pemeriksaan_terakhir')->nullable()->comment('Tanggal pemeriksaan terakhir');
            $table->tinyInteger('is_deleted')->default(0)->comment('Soft delete flag: 0=aktif, 1=dihapus');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lansia');
    }
};
