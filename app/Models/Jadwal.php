<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    protected $table = 'jadwal';

    public $timestamps = false;

    protected $fillable = [
        'judul_kegiatan',
        'nama_kegiatan',
        'jenis_kegiatan',
        'tanggal',
        'tanggal_kegiatan',
        'waktu_mulai',
        'waktu_selesai',
        'lokasi',
        'keterangan',
        'status',
        'layanan',
        'dibuat_oleh',
        'created_by',
        'is_posted',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'tanggal_kegiatan' => 'date',
    ];

    // =====================
    // RELASI
    // =====================

    // Jadwal dibuat oleh satu user
    public function pembuat()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // =====================
    // SCOPE
    // =====================

    public function scopeTerjadwal($query)
    {
        return $query->where('status', 'Terjadwal');
    }

    public function scopeSelesai($query)
    {
        return $query->where('status', 'Selesai');
    }
}
