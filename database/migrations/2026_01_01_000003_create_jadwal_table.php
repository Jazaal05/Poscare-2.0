<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kegiatan', 100);
            $table->string('jenis_kegiatan', 100)->default('Penimbangan');
            $table->date('tanggal');
            $table->time('waktu_mulai')->nullable();
            $table->string('lokasi', 200)->nullable();
            $table->text('keterangan')->nullable();
            $table->enum('status', ['Terjadwal', 'Selesai', 'Dibatalkan'])->default('Terjadwal');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->boolean('is_posted')->default(false);
            
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal');
    }
};
