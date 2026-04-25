<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatPengukuran extends Model
{
    protected $table = 'riwayat_pengukuran';

    public $timestamps = false;

    protected $fillable = [
        'anak_id',
        'tanggal_ukur',
        'umur_hari',
        'umur_bulan',
        'bb_kg',
        'tb_pb_cm',
        'lk_cm',
        'cara_ukur',
        'imt',
        'z_tbu',
        'z_bbu',
        'z_bbtb',
        'z_imtu',
        'kat_tbu',
        'kat_bbu',
        'kat_bbtb',
        'kat_imtu',
        'overall_8',
        'overall_source',
    ];

    protected $casts = [
        'tanggal_ukur' => 'date',
        'umur_bulan'   => 'float',
        'bb_kg'        => 'float',
        'tb_pb_cm'     => 'float',
        'lk_cm'        => 'float',
        'imt'          => 'float',
        'z_tbu'        => 'float',
        'z_bbu'        => 'float',
        'z_bbtb'       => 'float',
        'z_imtu'       => 'float',
    ];

    // =====================
    // RELASI
    // =====================

    // Riwayat pengukuran milik satu anak
    public function anak()
    {
        return $this->belongsTo(Anak::class);
    }

    // =====================
    // SCOPE
    // =====================

    // Ambil data terbaru berdasarkan tanggal (sering dipakai di children_detail.php)
    public function scopeTerbaru($query)
    {
        return $query->orderBy('tanggal_ukur', 'desc')->orderBy('id', 'desc');
    }

    // Filter berdasarkan anak tertentu
    public function scopeUntukAnak($query, $anakId)
    {
        return $query->where('anak_id', $anakId);
    }
}
