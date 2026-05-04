<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

/**
 * Model Lansia - Data Master Lansia
 * 
 * Menyimpan data pribadi lansia dan status kesehatan terkini.
 * Data kesehatan selalu diupdate dari kunjungan terakhir.
 */
class Lansia extends Model
{
    protected $table = 'lansia';

    protected $fillable = [
        // Data Pribadi
        'nik_lansia',
        'nama_lengkap',
        'tgl_lahir',
        'tempat_lahir',
        'jenis_kelamin',
        
        // Alamat
        'alamat_domisili',
        'rt_rw',
        
        // Data Keluarga
        'nama_kk',
        'nama_wali',
        'nik_wali',
        'hp_kontak_wali',
        
        // Data Kesehatan Terkini
        'berat_badan',
        'tinggi_badan',
        'tekanan_darah',
        'gula_darah',
        'kolesterol',
        'asam_urat',
        'status_kesehatan',
        'tanggal_pemeriksaan_terakhir',
        
        // Metadata
        'dicatat_oleh',
        'is_deleted',
    ];

    protected $casts = [
        'tgl_lahir' => 'date',
        'tanggal_pemeriksaan_terakhir' => 'date',
        'berat_badan' => 'decimal:2',
        'tinggi_badan' => 'decimal:2',
        'gula_darah' => 'decimal:2',
        'kolesterol' => 'decimal:2',
        'asam_urat' => 'decimal:2',
        'is_deleted' => 'boolean',
    ];

    // ============================================================
    // RELASI
    // ============================================================

    /**
     * Relasi ke User (kader yang menginput)
     */
    public function pencatat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }

    /**
     * Relasi ke Kunjungan Lansia (history)
     */
    public function kunjungan(): HasMany
    {
        return $this->hasMany(KunjunganLansia::class, 'lansia_id');
    }

    /**
     * Kunjungan terakhir
     */
    public function kunjunganTerakhir()
    {
        return $this->hasOne(KunjunganLansia::class, 'lansia_id')
            ->latestOfMany('tanggal_kunjungan');
    }

    // ============================================================
    // SCOPES
    // ============================================================

    /**
     * Scope untuk data aktif (tidak dihapus)
     */
    public function scopeAktif($query)
    {
        return $query->where('is_deleted', false);
    }

    /**
     * Scope untuk filter berdasarkan status kesehatan
     */
    public function scopeByStatusKesehatan($query, string $status)
    {
        return $query->where('status_kesehatan', $status);
    }

    /**
     * Scope untuk filter berdasarkan rentang usia
     */
    public function scopeByRentangUsia($query, int $min, int $max)
    {
        $tahunMin = now()->year - $max;
        $tahunMax = now()->year - $min;
        
        return $query->whereYear('tgl_lahir', '>=', $tahunMin)
            ->whereYear('tgl_lahir', '<=', $tahunMax);
    }

    // ============================================================
    // ACCESSORS
    // ============================================================

    /**
     * Accessor untuk umur (dalam tahun)
     */
    public function getUmurAttribute(): int
    {
        if (!$this->tgl_lahir) return 0;
        return Carbon::parse($this->tgl_lahir)->age;
    }

    /**
     * Accessor untuk umur display (format: "65 tahun")
     */
    public function getUmurDisplayAttribute(): string
    {
        return $this->umur . ' tahun';
    }

    /**
     * Accessor untuk rentang usia (untuk grafik)
     */
    public function getRentangUsiaAttribute(): string
    {
        $umur = $this->umur;
        
        return match(true) {
            $umur >= 60 && $umur <= 65 => '60-65 tahun',
            $umur >= 66 && $umur <= 70 => '66-70 tahun',
            $umur >= 71 && $umur <= 75 => '71-75 tahun',
            $umur >= 76 && $umur <= 80 => '76-80 tahun',
            $umur > 80 => '80+ tahun',
            default => 'Di bawah 60 tahun',
        };
    }

    /**
     * Accessor untuk BMI (Body Mass Index)
     */
    public function getBmiAttribute(): ?float
    {
        if (!$this->berat_badan || !$this->tinggi_badan) {
            return null;
        }
        
        $tinggiMeter = $this->tinggi_badan / 100;
        return round($this->berat_badan / ($tinggiMeter ** 2), 2);
    }

    /**
     * Accessor untuk kategori BMI
     */
    public function getKategoriBmiAttribute(): ?string
    {
        $bmi = $this->bmi;
        
        if (!$bmi) return null;
        
        return match(true) {
            $bmi < 18.5 => 'Kurus',
            $bmi < 25 => 'Normal',
            $bmi < 30 => 'Gemuk',
            default => 'Obesitas',
        };
    }

    /**
     * Accessor untuk jenis kelamin display
     */
    public function getJenisKelaminDisplayAttribute(): string
    {
        return $this->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
    }

    // ============================================================
    // METHODS
    // ============================================================

    /**
     * Update data kesehatan dari kunjungan terakhir
     */
    public function updateDataKesehatan(KunjunganLansia $kunjungan): void
    {
        $this->update([
            'berat_badan' => $kunjungan->berat_badan,
            'tinggi_badan' => $kunjungan->tinggi_badan,
            'tekanan_darah' => $kunjungan->tekanan_darah,
            'gula_darah' => $kunjungan->gula_darah,
            'kolesterol' => $kunjungan->kolesterol,
            'asam_urat' => $kunjungan->asam_urat,
            'status_kesehatan' => $this->tentukanStatusKesehatan($kunjungan),
            'tanggal_pemeriksaan_terakhir' => $kunjungan->tanggal_kunjungan,
        ]);
    }

    /**
     * Tentukan status kesehatan berdasarkan hasil pemeriksaan
     */
    private function tentukanStatusKesehatan(KunjunganLansia $kunjungan): string
    {
        // Prioritas: Stroke > Penyakit Jantung > Hipertensi > Diabetes > Kolesterol > Asam Urat > Sehat
        
        if (in_array($kunjungan->status_tensi, ['hipertensi2'])) {
            return 'Hipertensi';
        }
        
        if (in_array($kunjungan->status_gula, ['sangat_tinggi'])) {
            return 'Diabetes';
        }
        
        if (in_array($kunjungan->status_tensi, ['hipertensi1'])) {
            return 'Hipertensi';
        }
        
        if (in_array($kunjungan->status_gula, ['tinggi'])) {
            return 'Diabetes';
        }
        
        if ($kunjungan->status_kolesterol === 'tinggi') {
            return 'Kolesterol Tinggi';
        }
        
        if ($kunjungan->status_asam_urat === 'tinggi') {
            return 'Asam Urat Tinggi';
        }
        
        return 'Sehat';
    }

    /**
     * Cek apakah lansia memiliki kondisi berisiko
     */
    public function isBerisiko(): bool
    {
        return !in_array($this->status_kesehatan, ['Sehat']);
    }

    /**
     * Get jumlah kunjungan
     */
    public function getJumlahKunjunganAttribute(): int
    {
        return $this->kunjungan()->count();
    }

    /**
     * Get kunjungan dalam 3 bulan terakhir
     */
    public function kunjunganTerakhir3Bulan()
    {
        return $this->kunjungan()
            ->where('tanggal_kunjungan', '>=', now()->subMonths(3))
            ->orderBy('tanggal_kunjungan', 'desc')
            ->get();
    }
}
