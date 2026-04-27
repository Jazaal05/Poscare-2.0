<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Lansia extends Model
{
    protected $table = 'lansia';
    public $timestamps = false;

    protected $fillable = [
        'nik', 'nama_lengkap', 'jenis_kelamin', 'tanggal_lahir',
        'tempat_lahir', 'alamat', 'rt_rw', 'no_hp',
        'nama_wali', 'hubungan_wali', 'is_deleted', 'created_by',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'is_deleted'    => 'boolean',
    ];

    public function scopeAktif($query)
    {
        return $query->where('is_deleted', false);
    }

    public function kunjungan()
    {
        return $this->hasMany(KunjunganLansia::class);
    }

    public function kunjunganTerakhir()
    {
        return $this->hasOne(KunjunganLansia::class)->latestOfMany('tanggal_kunjungan');
    }

    public function pemeriksaan()
    {
        return $this->hasMany(PemeriksaanLansia::class);
    }

    public function pemeriksaanTerakhir()
    {
        return $this->hasOne(PemeriksaanLansia::class)->latestOfMany('tanggal_periksa');
    }

    public function getUmurAttribute(): int
    {
        return Carbon::parse($this->tanggal_lahir)->age;
    }
}
