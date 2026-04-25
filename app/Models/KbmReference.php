<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KbmReference extends Model
{
    protected $table = 'kbm_reference';

    public $timestamps = false;

    protected $fillable = [
        'umur_bulan',
        'kbm_gram',
        'keterangan',
    ];

    // =====================
    // SCOPE
    // =====================

    // Ambil KBM berdasarkan umur bulan tertentu
    public function scopeByUmur($query, $umurBulan)
    {
        return $query->where('umur_bulan', $umurBulan);
    }
}
