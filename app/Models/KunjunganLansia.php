<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model Kunjungan Lansia - History Pemeriksaan Kesehatan
 * 
 * Menyimpan history semua kunjungan dan pemeriksaan kesehatan lansia.
 * Setiap kunjungan = 1 baris baru (tidak update, tapi insert).
 */
class KunjunganLansia extends Model
{
    protected $table = 'kunjungan_lansia';

    protected $fillable = [
        'lansia_id',
        'tanggal_kunjungan',
        
        // Pengukuran Fisik
        'berat_badan',
        'tinggi_badan',
        'tekanan_darah',
        'status_tensi',
        
        // Pemeriksaan Darah
        'gula_darah',
        'status_gula',
        'kolesterol',
        'status_kolesterol',
        'asam_urat',
        'status_asam_urat',
        
        // Keluhan & Pengobatan
        'ada_keluhan',
        'keluhan',
        'obat_diberikan',
        'vitamin_diberikan',
        
        // Catatan
        'catatan_bidan',
        'dicatat_oleh',
    ];

    protected $casts = [
        'tanggal_kunjungan' => 'date',
        'berat_badan' => 'decimal:2',
        'tinggi_badan' => 'decimal:2',
        'gula_darah' => 'decimal:2',
        'kolesterol' => 'decimal:2',
        'asam_urat' => 'decimal:2',
        'ada_keluhan' => 'boolean',
        'obat_diberikan' => 'array',
        'vitamin_diberikan' => 'array',
    ];

    // ============================================================
    // RELASI
    // ============================================================

    /**
     * Relasi ke Lansia
     */
    public function lansia(): BelongsTo
    {
        return $this->belongsTo(Lansia::class, 'lansia_id');
    }

    /**
     * Relasi ke User (kader yang menginput)
     */
    public function pencatat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }

    // ============================================================
    // SCOPES
    // ============================================================

    /**
     * Scope untuk kunjungan bulan ini
     */
    public function scopeBulanIni($query)
    {
        return $query->whereMonth('tanggal_kunjungan', now()->month)
            ->whereYear('tanggal_kunjungan', now()->year);
    }

    /**
     * Scope untuk kunjungan dalam rentang tanggal
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal_kunjungan', [$startDate, $endDate]);
    }

    /**
     * Scope untuk kunjungan dengan kondisi tidak normal
     */
    public function scopeTidakNormal($query)
    {
        return $query->where(function ($q) {
            $q->whereIn('status_tensi', ['hipertensi1', 'hipertensi2'])
              ->orWhereIn('status_gula', ['tinggi', 'sangat_tinggi'])
              ->orWhere('status_kolesterol', 'tinggi')
              ->orWhere('status_asam_urat', 'tinggi');
        });
    }

    // ============================================================
    // STATIC METHODS - KALKULASI STATUS
    // ============================================================

    /**
     * Hitung status tensi berdasarkan nilai sistolik/diastolik
     * 
     * Normal: < 120/80
     * Prehipertensi: 120-139/80-89
     * Hipertensi 1: 140-159/90-99
     * Hipertensi 2: >= 160/100
     */
    public static function hitungStatusTensi(?string $tekananDarah): ?string
    {
        if (!$tekananDarah) return null;
        
        $parts = explode('/', $tekananDarah);
        if (count($parts) < 2) return null;
        
        $sistolik = (int) trim($parts[0]);
        $diastolik = (int) trim($parts[1]);

        if ($sistolik >= 160 || $diastolik >= 100) return 'hipertensi2';
        if ($sistolik >= 140 || $diastolik >= 90) return 'hipertensi1';
        if ($sistolik >= 120 || $diastolik >= 80) return 'prehipertensi';
        
        return 'normal';
    }

    /**
     * Hitung status gula darah (sewaktu)
     * 
     * Rendah: < 70
     * Normal: 70-139
     * Tinggi: 140-199
     * Sangat Tinggi: >= 200
     */
    public static function hitungStatusGula(?float $gula): ?string
    {
        if ($gula === null) return null;
        
        if ($gula < 70) return 'rendah';
        if ($gula < 140) return 'normal';
        if ($gula < 200) return 'tinggi';
        
        return 'sangat_tinggi';
    }

    /**
     * Hitung status kolesterol
     * 
     * Normal: < 200
     * Batas: 200-239
     * Tinggi: >= 240
     */
    public static function hitungStatusKolesterol(?float $kolesterol): ?string
    {
        if ($kolesterol === null) return null;
        
        if ($kolesterol < 200) return 'normal';
        if ($kolesterol < 240) return 'batas';
        
        return 'tinggi';
    }

    /**
     * Hitung status asam urat
     * 
     * Normal Laki-laki: <= 7.0
     * Normal Perempuan: <= 6.0
     * Tinggi: di atas batas normal
     */
    public static function hitungStatusAsamUrat(?float $asamUrat, string $jenisKelamin = 'L'): ?string
    {
        if ($asamUrat === null) return null;
        
        $batas = $jenisKelamin === 'P' ? 6.0 : 7.0;
        
        return $asamUrat <= $batas ? 'normal' : 'tinggi';
    }

    // ============================================================
    // ACCESSORS
    // ============================================================

    /**
     * Accessor untuk BMI
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
     * Accessor untuk label status tensi
     */
    public function getLabelStatusTensiAttribute(): array
    {
        return $this->getLabelStatus('status_tensi', $this->status_tensi);
    }

    /**
     * Accessor untuk label status gula
     */
    public function getLabelStatusGulaAttribute(): array
    {
        return $this->getLabelStatus('status_gula', $this->status_gula);
    }

    /**
     * Accessor untuk label status kolesterol
     */
    public function getLabelStatusKolesterolAttribute(): array
    {
        return $this->getLabelStatus('status_kolesterol', $this->status_kolesterol);
    }

    /**
     * Accessor untuk label status asam urat
     */
    public function getLabelStatusAsamUratAttribute(): array
    {
        return $this->getLabelStatus('status_asam_urat', $this->status_asam_urat);
    }

    // ============================================================
    // HELPER METHODS
    // ============================================================

    /**
     * Get label dan warna untuk status
     */
    private function getLabelStatus(string $field, ?string $status): array
    {
        if (!$status) {
            return ['label' => '-', 'color' => '#9CA3AF', 'bg' => '#F3F4F6'];
        }

        $map = [
            'status_tensi' => [
                'normal' => ['label' => 'Normal', 'color' => '#10B981', 'bg' => '#D1FAE5'],
                'prehipertensi' => ['label' => 'Prehipertensi', 'color' => '#F59E0B', 'bg' => '#FEF3C7'],
                'hipertensi1' => ['label' => 'Hipertensi I', 'color' => '#EF4444', 'bg' => '#FEE2E2'],
                'hipertensi2' => ['label' => 'Hipertensi II', 'color' => '#991B1B', 'bg' => '#FEE2E2'],
            ],
            'status_gula' => [
                'rendah' => ['label' => 'Rendah', 'color' => '#3B82F6', 'bg' => '#DBEAFE'],
                'normal' => ['label' => 'Normal', 'color' => '#10B981', 'bg' => '#D1FAE5'],
                'tinggi' => ['label' => 'Tinggi', 'color' => '#F59E0B', 'bg' => '#FEF3C7'],
                'sangat_tinggi' => ['label' => 'Sangat Tinggi', 'color' => '#EF4444', 'bg' => '#FEE2E2'],
            ],
            'status_kolesterol' => [
                'normal' => ['label' => 'Normal', 'color' => '#10B981', 'bg' => '#D1FAE5'],
                'batas' => ['label' => 'Batas', 'color' => '#F59E0B', 'bg' => '#FEF3C7'],
                'tinggi' => ['label' => 'Tinggi', 'color' => '#EF4444', 'bg' => '#FEE2E2'],
            ],
            'status_asam_urat' => [
                'normal' => ['label' => 'Normal', 'color' => '#10B981', 'bg' => '#D1FAE5'],
                'tinggi' => ['label' => 'Tinggi', 'color' => '#EF4444', 'bg' => '#FEE2E2'],
            ],
        ];

        return $map[$field][$status] ?? ['label' => '-', 'color' => '#9CA3AF', 'bg' => '#F3F4F6'];
    }

    /**
     * Cek apakah kunjungan ini menunjukkan kondisi tidak normal
     */
    public function isTidakNormal(): bool
    {
        return in_array($this->status_tensi, ['hipertensi1', 'hipertensi2'])
            || in_array($this->status_gula, ['tinggi', 'sangat_tinggi'])
            || $this->status_kolesterol === 'tinggi'
            || $this->status_asam_urat === 'tinggi';
    }

    /**
     * Get daftar kondisi tidak normal
     */
    public function getKondisiTidakNormal(): array
    {
        $kondisi = [];

        if (in_array($this->status_tensi, ['hipertensi1', 'hipertensi2'])) {
            $kondisi[] = 'Hipertensi';
        }

        if (in_array($this->status_gula, ['tinggi', 'sangat_tinggi'])) {
            $kondisi[] = 'Gula Darah Tinggi';
        }

        if ($this->status_kolesterol === 'tinggi') {
            $kondisi[] = 'Kolesterol Tinggi';
        }

        if ($this->status_asam_urat === 'tinggi') {
            $kondisi[] = 'Asam Urat Tinggi';
        }

        return $kondisi;
    }
}
