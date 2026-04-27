<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KunjunganLansia extends Model
{
    protected $table = 'kunjungan_lansia';
    public $timestamps = false;

    protected $fillable = [
        'lansia_id', 'tanggal_kunjungan',
        // Pengukuran fisik
        'berat_badan', 'tekanan_darah', 'status_tensi',
        // Cek darah
        'gula_darah', 'status_gula',
        'kolesterol', 'status_kolesterol',
        'asam_urat', 'status_asam_urat',
        // Pengobatan
        'ada_keluhan', 'keluhan', 'obat_diberikan', 'vitamin_diberikan',
        // Tambahan
        'catatan_bidan', 'dicatat_oleh',
    ];

    protected $casts = [
        'tanggal_kunjungan' => 'date',
        'berat_badan'       => 'float',
        'gula_darah'        => 'float',
        'kolesterol'        => 'float',
        'asam_urat'         => 'float',
        'ada_keluhan'       => 'boolean',
        'obat_diberikan'    => 'array',
        'vitamin_diberikan' => 'array',
    ];

    public function lansia()
    {
        return $this->belongsTo(Lansia::class);
    }

    // ── Kalkulasi status otomatis ──────────────────────────────

    /**
     * Hitung status tensi berdasarkan nilai sistolik/diastolik
     * Normal: < 120/80 | Prehipertensi: 120-139/80-89
     * Hipertensi 1: 140-159/90-99 | Hipertensi 2: >= 160/100
     */
    public static function hitungStatusTensi(?string $tekananDarah): ?string
    {
        if (!$tekananDarah) return null;
        $parts = explode('/', $tekananDarah);
        if (count($parts) < 2) return null;
        $sistolik  = (int) trim($parts[0]);
        $diastolik = (int) trim($parts[1]);

        if ($sistolik >= 160 || $diastolik >= 100) return 'hipertensi2';
        if ($sistolik >= 140 || $diastolik >= 90)  return 'hipertensi1';
        if ($sistolik >= 120 || $diastolik >= 80)  return 'prehipertensi';
        return 'normal';
    }

    /**
     * Hitung status gula darah (sewaktu)
     * Normal: < 140 | Tinggi: 140-199 | Sangat Tinggi: >= 200 | Rendah: < 70
     */
    public static function hitungStatusGula(?float $gula): ?string
    {
        if ($gula === null) return null;
        if ($gula < 70)   return 'rendah';
        if ($gula < 140)  return 'normal';
        if ($gula < 200)  return 'tinggi';
        return 'sangat_tinggi';
    }

    /**
     * Hitung status kolesterol
     * Normal: < 200 | Batas: 200-239 | Tinggi: >= 240
     */
    public static function hitungStatusKolesterol(?float $kol): ?string
    {
        if ($kol === null) return null;
        if ($kol < 200) return 'normal';
        if ($kol < 240) return 'batas';
        return 'tinggi';
    }

    /**
     * Hitung status asam urat
     * Normal L: <= 7.0 | Normal P: <= 6.0 | Tinggi: di atas itu
     */
    public static function hitungStatusAsamUrat(?float $au, string $jk = 'L'): ?string
    {
        if ($au === null) return null;
        $batas = $jk === 'P' ? 6.0 : 7.0;
        return $au <= $batas ? 'normal' : 'tinggi';
    }

    // ── Label & warna untuk tampilan ──────────────────────────

    public static function labelStatus(string $field, ?string $status): array
    {
        $map = [
            'status_tensi' => [
                'normal'        => ['label' => 'Normal',        'color' => '#10B981', 'bg' => '#D1FAE5'],
                'prehipertensi' => ['label' => 'Prehipertensi', 'color' => '#F59E0B', 'bg' => '#FEF3C7'],
                'hipertensi1'   => ['label' => 'Hipertensi I',  'color' => '#EF4444', 'bg' => '#FEE2E2'],
                'hipertensi2'   => ['label' => 'Hipertensi II', 'color' => '#991B1B', 'bg' => '#FEE2E2'],
            ],
            'status_gula' => [
                'rendah'       => ['label' => 'Rendah',       'color' => '#3B82F6', 'bg' => '#DBEAFE'],
                'normal'       => ['label' => 'Normal',       'color' => '#10B981', 'bg' => '#D1FAE5'],
                'tinggi'       => ['label' => 'Tinggi',       'color' => '#F59E0B', 'bg' => '#FEF3C7'],
                'sangat_tinggi'=> ['label' => 'Sangat Tinggi','color' => '#EF4444', 'bg' => '#FEE2E2'],
            ],
            'status_kolesterol' => [
                'normal' => ['label' => 'Normal', 'color' => '#10B981', 'bg' => '#D1FAE5'],
                'batas'  => ['label' => 'Batas',  'color' => '#F59E0B', 'bg' => '#FEF3C7'],
                'tinggi' => ['label' => 'Tinggi', 'color' => '#EF4444', 'bg' => '#FEE2E2'],
            ],
            'status_asam_urat' => [
                'normal' => ['label' => 'Normal', 'color' => '#10B981', 'bg' => '#D1FAE5'],
                'tinggi' => ['label' => 'Tinggi', 'color' => '#EF4444', 'bg' => '#FEE2E2'],
            ],
        ];

        return $map[$field][$status] ?? ['label' => '-', 'color' => '#9CA3AF', 'bg' => '#F3F4F6'];
    }
}
