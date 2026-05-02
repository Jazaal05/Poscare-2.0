<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_vaksin', function (Blueprint $table) {
            $table->id();
            $table->string('nama_vaksin', 100);
            $table->text('deskripsi')->nullable();
            $table->string('umur_pemberian', 100);
            $table->boolean('is_wajib')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_vaksin');
    }
};
