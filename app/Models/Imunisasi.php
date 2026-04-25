<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Imunisasi extends Model
{
    protected $table = 'imunisasi';

    public $timestamps = false;

    protected $fillable = [
        'anak_id',
        'master_vaksin_id',
        'tanggal',
        'umur_bulan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    // =====================
    // RELASI
    // =====================

    // Imunisasi milik satu anak
    public function anak()
    {
        return $this->belongsTo(Anak::class);
    }

    // Imunisasi menggunakan satu jenis vaksin
    public function masterVaksin()
    {
        return $this->belongsTo(MasterVaksin::class);
    }

    // =====================
    // SCOPE
    // =====================

    // Hanya imunisasi yang sudah dilakukan (tanggal tidak NULL)
    // Sama seperti filter di immunization_list.php & children_detail.php
    public function scopeSudahDilakukan($query)
    {
        return $query->whereNotNull('tanggal');
    }
}
