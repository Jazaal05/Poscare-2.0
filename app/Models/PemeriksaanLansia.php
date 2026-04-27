<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PemeriksaanLansia extends Model
{
    protected $table = 'pemeriksaan_lansia';
    public $timestamps = false;

    protected $fillable = [
        'lansia_id',
        'tanggal_periksa',
        'berat_badan',
        'tinggi_badan',
        'tekanan_darah',
        'gula_darah',
        'asam_urat',
        'kolesterol',
        'catatan',
        'dicatat_oleh',
    ];

    protected $casts = [
        'tanggal_periksa' => 'date',
        'berat_badan'     => 'float',
        'tinggi_badan'    => 'float',
        'gula_darah'      => 'float',
        'asam_urat'       => 'float',
        'kolesterol'      => 'float',
    ];

    public function lansia()
    {
        return $this->belongsTo(Lansia::class);
    }
}
