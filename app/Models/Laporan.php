<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    protected $table = 'laporan';

    public $timestamps = false;

    protected $fillable = [
        'judul',
        'jenis_laporan',
        'format_file',
        'periode_awal',
        'periode_akhir',
        'file_path',
        'created_by',
    ];

    protected $casts = [
        'periode_awal'  => 'date',
        'periode_akhir' => 'date',
    ];

    // =====================
    // RELASI
    // =====================

    // Laporan dibuat oleh satu user
    public function pembuat()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
