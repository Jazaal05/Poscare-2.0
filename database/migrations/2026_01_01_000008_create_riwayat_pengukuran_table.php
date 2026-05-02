<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_pengukuran', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('anak_id');
            $table->date('tanggal_ukur');
            $table->integer('umur_hari')->default(0)->comment('Umur dalam hari saat diukur');
            $table->decimal('umur_bulan', 5, 2)->default(0)->comment('Umur dalam bulan (WHO: 1 bulan = 30.4375 hari)');
            $table->decimal('bb_kg', 5, 2)->comment('Berat badan dalam kilogram');
            $table->decimal('tb_pb_cm', 5, 2)->comment('Tinggi/Panjang badan dalam cm (sudah dinormalisasi)');
            $table->decimal('lk_cm', 5, 2)->nullable()->comment('Lingkar kepala dalam cm');
            $table->enum('cara_ukur', ['berdiri', 'berbaring'])->default('berdiri');
            $table->decimal('imt', 5, 2)->nullable()->comment('Indeks Massa Tubuh (BMI)');
            
            // Z-Scores
            $table->decimal('z_tbu', 6, 3)->nullable()->comment('Z-Score Tinggi Badan menurut Umur (HAZ)');
            $table->decimal('z_bbu', 6, 3)->nullable()->comment('Z-Score Berat Badan menurut Umur (WAZ)');
            $table->decimal('z_bbtb', 6, 3)->nullable()->comment('Z-Score Berat Badan menurut Tinggi Badan (WHZ)');
            $table->decimal('z_imtu', 6, 3)->nullable()->comment('Z-Score IMT menurut Umur (BAZ)');
            
            // Kategori
            $table->string('kat_tbu', 50)->nullable()->comment('Kategori TB/U (Normal, Pendek, Sangat Pendek)');
            $table->string('kat_bbu', 50)->nullable()->comment('Kategori BB/U (Normal, Kurang, Sangat Kurang)');
            $table->string('kat_bbtb', 50)->nullable()->comment('Kategori BB/TB (Normal, Kurus, Gemuk, Obesitas)');
            $table->string('kat_imtu', 50)->nullable()->comment('Kategori IMT/U (Normal, Kurus, Gemuk, Obesitas)');
            
            // Overall status
            $table->string('overall_8', 50)->comment('Status gizi overall: Stunting, Risiko Stunting, Gizi Kurang, Beresiko Gizi Kurang, Gizi Baik, Beresiko Gizi Lebih, Gizi Lebih, Obesitas');
            $table->string('overall_source', 100)->default('WHO Child Growth Standards 2006');
            
            // Timestamps
            $table->timestamps();
            
            $table->foreign('anak_id')->references('id')->on('anak')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_pengukuran');
    }
};
