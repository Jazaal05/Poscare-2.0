<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterVaksin extends Model
{
    protected $table = 'master_vaksin';

    public $timestamps = false;

    protected $fillable = [
        'nama_vaksin',
        'usia_standar_bulan',
        'usia_minimal_bulan',
        'usia_maksimal_bulan',
        'keterangan',
    ];

    // =====================
    // RELASI
    // =====================

    // Satu vaksin bisa dipakai di banyak imunisasi
    public function imunisasi()
    {
        return $this->hasMany(Imunisasi::class);
    }
}
