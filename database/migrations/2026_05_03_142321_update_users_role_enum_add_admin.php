<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Ubah enum role untuk menambahkan 'admin'
        // admin dan kader adalah sama (admin = kader, kader = admin)
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'kader', 'orangtua') NOT NULL DEFAULT 'orangtua'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Kembalikan ke enum lama (hanya kader dan orangtua)
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('kader', 'orangtua') NOT NULL DEFAULT 'orangtua'");
    }
};
