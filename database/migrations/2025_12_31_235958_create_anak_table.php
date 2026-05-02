<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anak', function (Blueprint $table) {
            $table->id();
            $table->string('no_registrasi', 20)->nullable();
            $table->string('nik_anak', 16)->nullable();
            $table->string('nama_anak', 100);
            $table->date('tanggal_lahir');
            $table->string('tempat_lahir', 100)->nullable();
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->integer('anak_ke')->default(1);
            $table->text('alamat_domisili')->nullable();
            $table->string('rt_rw', 10)->nullable()->comment('RT/RW format: 001/005');
            $table->string('nama_kk', 100)->nullable();
            $table->string('nama_ayah', 100)->nullable();
            $table->string('nama_ibu', 100);
            $table->string('nik_ayah', 16)->nullable();
            $table->string('nik_ibu', 16)->nullable();
            $table->date('tanggal_lahir_ibu')->nullable();
            $table->string('hp_kontak_ortu', 15)->nullable();
            
            // Snapshot data antropometri terbaru
            $table->decimal('berat_badan', 5, 2)->nullable()->comment('Berat badan terbaru (kg) - snapshot dari riwayat_pengukuran');
            $table->decimal('tinggi_badan', 5, 2)->nullable()->comment('Tinggi badan terbaru (cm) - snapshot dari riwayat_pengukuran');
            $table->decimal('lingkar_kepala', 5, 2)->nullable()->comment('Lingkar kepala terbaru (cm) - snapshot dari riwayat_pengukuran');
            $table->enum('cara_ukur', ['berdiri', 'berbaring'])->nullable()->comment('Cara pengukuran tinggi badan');
            
            // Relasi ke user (orangtua)
            $table->unsignedBigInteger('user_id')->nullable();
            
            // Status gizi
            $table->string('status_gizi', 50)->default('Belum diukur')->comment('Status gizi terbaru (8 kategori WHO)');
            $table->text('status_gizi_detail')->nullable()->comment('JSON detail z-score & kategori per indeks');
            $table->date('tanggal_penimbangan_terakhir')->nullable()->comment('Tanggal pengukuran terakhir');
            
            // Soft delete
            $table->boolean('is_deleted')->default(false)->comment('Soft delete flag: 0=aktif, 1=dihapus');
            
            // Timestamps
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            
            // Indexes
            $table->index('user_id');
            $table->index('nik_anak');
            $table->index('is_deleted');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anak');
    }
};
