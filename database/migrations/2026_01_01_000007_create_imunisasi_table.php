<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('imunisasi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('anak_id');
            $table->unsignedBigInteger('master_vaksin_id')->nullable();
            $table->date('tanggal');
            $table->integer('umur_bulan')->nullable();
            $table->string('batch_number', 50)->nullable();
            $table->text('keterangan')->nullable();
            
            // Timestamps
            $table->timestamps();
            
            $table->foreign('anak_id')->references('id')->on('anak')->cascadeOnDelete();
            $table->foreign('master_vaksin_id')->references('id')->on('master_vaksin')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('imunisasi');
    }
};
