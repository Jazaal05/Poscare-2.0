<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 200);
            $table->string('jenis_laporan', 100);
            $table->enum('format_file', ['Excel', 'PDF', 'CSV'])->default('Excel');
            $table->date('periode_awal')->nullable();
            $table->date('periode_akhir')->nullable();
            $table->string('file_path', 255)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan');
    }
};
