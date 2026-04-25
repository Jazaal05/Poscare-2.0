<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penimbangan extends Model
{
    protected $table = 'penimbangan';

    // Tabel ini punya created_at dan updated_at
    public $timestamps = true;

    protected $fillable = [
        'anak_id',
        'tanggal_ukur',
        'umur_bulan',
        'bb_kg',
        'tb_cm',
        'lk_cm',
        'cara_ukur',
        'status_gizi',
        'zscore_bbu',
        'zscore_tbu',
        'zscore_bbtb',
        'status_nt',
        'kbm_gram',
        'kenaikan_bb_gram',
        'catatan',
        'user_id',
    ];

    protected $casts = [
        'tanggal_ukur'     => 'date',
        'bb_kg'            => 'float',
        'tb_cm'            => 'float',
        'lk_cm'            => 'float',
        'zscore_bbu'       => 'float',
        'zscore_tbu'       => 'float',
        'zscore_bbtb'      => 'float',
    ];

    // =====================
    // RELASI
    // =====================

    // Penimbangan milik satu anak
    public function anak()
    {
        return $this->belongsTo(Anak::class);
    }

    // Penimbangan dicatat oleh satu user (kader/petugas)
    public function pencatat()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
