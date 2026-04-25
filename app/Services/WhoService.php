<?php

namespace App\Services;

/**
 * WhoService - Wrapper untuk kalkulasi z-score WHO
 * Menggunakan fungsi PHP native dari project lama yang sudah terbukti akurat
 */
class WhoService
{
    private bool $loaded = false;

    private function load(): void
    {
        if ($this->loaded) return;

        $base = __DIR__;
        require_once $base . '/who_lms_data.php';
        require_once $base . '/fungsi_gizi.php';
        require_once $base . '/klasifikasi_gizi_who_kemenkes.php';
        require_once $base . '/data_who.php';
        require_once $base . '/fungsi_klasifikasi_who.php';

        $this->loaded = true;
    }

    /**
     * Hitung status gizi lengkap (z-score + kategori WHO 8 item)
     * Sama persis dengan hitungStatusGiziLengkap() di project lama
     */
    public function hitungStatusGiziLengkap(
        float $umurBulan,
        float $bbKg,
        float $tbCm,
        string $jenisKelamin,
        string $caraUkur = 'berdiri',
        ?float $lkCm = null
    ): array {
        $this->load();

        return hitungStatusGiziLengkap(
            $umurBulan,
            $bbKg,
            $tbCm,
            $jenisKelamin,
            $caraUkur,
            $lkCm
        );
    }
}
