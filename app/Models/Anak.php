<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Anak extends Model
{
    protected $table = 'anak';

    // Aktifkan timestamps untuk audit trail
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'no_registrasi',
        'nik_anak',
        'nama_anak',
        'tanggal_lahir',
        'tempat_lahir',
        'jenis_kelamin',
        'anak_ke',
        'alamat_domisili',
        'rt_rw',
        'nama_kk',
        'nama_ayah',
        'nama_ibu',
        'nik_ayah',
        'nik_ibu',
        'tanggal_lahir_ibu',
        'hp_kontak_ortu',
        'berat_badan',
        'tinggi_badan',
        'lingkar_kepala',
        'cara_ukur',
        'status_gizi',
        'status_gizi_detail',
        'tanggal_penimbangan_terakhir',
        'is_deleted',
    ];

    protected $casts = [
        'tanggal_lahir'                => 'date',
        'tanggal_penimbangan_terakhir' => 'date',
        'berat_badan'                  => 'float',
        'tinggi_badan'                 => 'float',
        'lingkar_kepala'               => 'float',
        'is_deleted'                   => 'boolean',
        'status_gizi_detail'           => 'array', // otomatis decode JSON
    ];

    // =====================
    // RELASI
    // =====================

    // Anak dimiliki oleh satu User (orang tua)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Anak punya banyak riwayat pengukuran
    public function riwayatPengukuran()
    {
        return $this->hasMany(RiwayatPengukuran::class);
    }

    // Anak punya banyak data imunisasi
    public function imunisasi()
    {
        return $this->hasMany(Imunisasi::class);
    }

    // =====================
    // SCOPE (filter siap pakai)
    // =====================

    // Hanya ambil data yang belum dihapus
    public function scopeAktif($query)
    {
        return $query->where('is_deleted', 0);
    }

    // Filter berdasarkan jenis kelamin
    public function scopeLakiLaki($query)
    {
        return $query->where('jenis_kelamin', 'L');
    }

    public function scopePerempuan($query)
    {
        return $query->where('jenis_kelamin', 'P');
    }

    // Hitung umur dalam bulan (sama seperti di children_list.php)
    public function getUmurBulanAttribute()
    {
        if (!$this->tanggal_lahir) return 0;
        return \Carbon\Carbon::parse($this->tanggal_lahir)->diffInMonths(now());
    }

    // Format tampilan umur (sama seperti di children_list.php)
    public function getUmurDisplayAttribute()
    {
        $bulan = $this->umur_bulan;
        $tahun = floor($bulan / 12);
        $sisaBulan = $bulan % 12;

        if ($tahun > 0 && $sisaBulan > 0) return "{$tahun} tahun {$sisaBulan} bulan";
        if ($tahun > 0) return "{$tahun} tahun";
        return "{$bulan} bulan";
    }
}
