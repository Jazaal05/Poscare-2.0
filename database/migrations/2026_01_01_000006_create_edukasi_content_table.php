<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('edukasi_content', function (Blueprint $table) {
            $table->id();
            $table->enum('platform', ['youtube', 'tiktok', 'article', 'artikel', 'facebook', 'instagram']);
            $table->text('url');
            $table->string('title', 255);
            $table->string('category', 100);
            $table->text('thumbnail')->nullable();
            $table->string('duration', 50)->nullable();
            $table->unsignedBigInteger('penulis_id')->nullable();
            
            $table->foreign('penulis_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('edukasi_content');
    }
};
