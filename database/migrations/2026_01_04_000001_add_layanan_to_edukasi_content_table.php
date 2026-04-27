<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('edukasi_content', function (Blueprint $table) {
            $table->enum('layanan', ['balita', 'lansia'])->default('balita')->after('category');
        });
    }

    public function down(): void
    {
        Schema::table('edukasi_content', function (Blueprint $table) {
            $table->dropColumn('layanan');
        });
    }
};
