<?php
/**
 * ============================================================================
 * KLASIFIKASI STATUS GIZI SESUAI STANDAR WHO-KEMENKES 2025
 * ============================================================================
 * 
 * Implementasi PRESISI MATEMATIS sesuai:
 * - WHO Child Growth Standards 2006 (valid hingga 2025)
 * - Peraturan Menteri Kesehatan RI tentang Standar Antropometri Anak
 * 
 * UPDATED: 2025-11-11 - Gunakan metode LMS (Box-Cox transformation)
 * 
 * TABEL KLASIFIKASI LENGKAP:
 * 
 * BB/U (Berat Badan menurut Umur):
 * ┌──────────────┬───────────────────────────┐
 * │ Rentang Z    │ Kategori                  │
 * ├──────────────┼───────────────────────────┤
 * │ Z < -3       │ GIZI BURUK                │
 * │ -3 ≤ Z < -2  │ GIZI KURANG               │
 * │ -2 ≤ Z ≤ +1  │ GIZI BAIK                 │
 * │ +1 < Z ≤ +2  │ BERESIKO GIZI LEBIH       │
 * │ +2 < Z ≤ +3  │ GIZI LEBIH                │
 * │ Z > +3       │ OBESITAS                  │
 * └──────────────┴───────────────────────────┘
 * 
 * TB/U (Tinggi Badan menurut Umur):
 * ┌──────────────┬───────────────────────────┐
 * │ Rentang Z    │ Kategori                  │
 * ├──────────────┼───────────────────────────┤
 * │ Z < -3       │ SANGAT PENDEK (STUNTED)   │
 * │ -3 ≤ Z < -2  │ PENDEK                    │
 * │ -2 ≤ Z ≤ +2  │ NORMAL                    │
 * └──────────────┴───────────────────────────┘
 * (Catatan: WHO tidak menetapkan kategori "Tinggi Berlebih" untuk TB/U)
 * 
 * BB/TB (Berat Badan menurut Tinggi Badan):
 * ┌──────────────┬───────────────────────────┐
 * │ Rentang Z    │ Kategori                  │
 * ├──────────────┼───────────────────────────┤
 * │ Z < -3       │ GIZI BURUK (SEVERELY WASTED)│
 * │ -3 ≤ Z < -2  │ GIZI KURANG (WASTED)      │
 * │ -2 ≤ Z < +1  │ GIZI BAIK                 │
 * │ +1 ≤ Z < +2  │ BERESIKO GIZI LEBIH       │
 * │ +2 ≤ Z < +3  │ GIZI LEBIH                │
 * │ Z ≥ +3       │ OBESITAS                  │
 * └──────────────┴───────────────────────────┘
 * ============================================================================
 */

/**
 * Klasifikasi Status Gizi BB/U (Berat Badan menurut Umur)
 * 
 * @param float $z_score Nilai Z-score hasil perhitungan
 * @return array ['status', 'class', 'keterangan']
 */
function klasifikasiGizi_BBU($z_score) {
    if ($z_score < -3) {
        return [
            'status' => 'Gizi Buruk',
            'class' => 'status-danger',
            'keterangan' => sprintf(
                '⚠️ GIZI BURUK (Z-score: %.2f < -3 SD). SANGAT BERISIKO! Anak memerlukan RUJUKAN SEGERA ke fasilitas kesehatan untuk intervensi gizi darurat, pemeriksaan medis komprehensif, dan program rehabilitasi gizi terstruktur.',
                $z_score
            ),
            'z_score' => round($z_score, 2)
        ];
    } elseif ($z_score >= -3 && $z_score < -2) {
        return [
            'status' => 'Gizi Kurang',
            'class' => 'status-warning',
            'keterangan' => sprintf(
                '⚠️ GIZI KURANG (Z-score: %.2f antara -3 SD sampai -2 SD). Perlu peningkatan asupan gizi seimbang dengan menu 4 sehat 5 sempurna, pemantauan rutin setiap bulan, dan konsultasi dengan ahli gizi untuk program perbaikan status gizi.',
                $z_score
            ),
            'z_score' => round($z_score, 2)
        ];
    } elseif ($z_score >= -2 && $z_score <= 1) {
        return [
            'status' => 'Gizi Baik',
            'class' => 'status-success',
            'keterangan' => sprintf(
                '✅ GIZI BAIK (Z-score: %.2f antara -2 SD sampai +1 SD). Status gizi optimal! Pertahankan pola makan seimbang, aktivitas fisik teratur sesuai usia, dan pemantauan rutin setiap 3 bulan untuk memastikan pertumbuhan tetap optimal.',
                $z_score
            ),
            'z_score' => round($z_score, 2)
        ];
    } elseif ($z_score > 1 && $z_score <= 2) {
        return [
            'status' => 'Beresiko Gizi Lebih',
            'class' => 'status-warning',
            'keterangan' => sprintf(
                '⚠️ BERESIKO GIZI LEBIH (Z-score: %.2f antara +1 SD sampai +2 SD). Perhatian! Mulai atur pola makan dengan mengurangi makanan tinggi gula dan lemak, tingkatkan aktivitas fisik minimal 60 menit per hari, dan pantau berat badan setiap bulan untuk mencegah obesitas.',
                $z_score
            ),
            'z_score' => round($z_score, 2)
        ];
    } elseif ($z_score > 2 && $z_score <= 3) {
        return [
            'status' => 'Gizi Lebih',
            'class' => 'status-warning',
            'keterangan' => sprintf(
                '⚠️ GIZI LEBIH (Z-score: %.2f antara +2 SD sampai +3 SD). Berisiko obesitas! Perlu konsultasi dengan ahli gizi untuk program diet sehat terstruktur, tingkatkan aktivitas fisik 60-90 menit/hari, kurangi screen time, dan batasi konsumsi fast food.',
                $z_score
            ),
            'z_score' => round($z_score, 2)
        ];
    } else { // Z > 3
        return [
            'status' => 'Obesitas',
            'class' => 'status-danger',
            'keterangan' => sprintf(
                '🚨 OBESITAS (Z-score: %.2f > +3 SD). BERISIKO SANGAT TINGGI komplikasi kesehatan (diabetes mellitus tipe 2, hipertensi, penyakit jantung, gangguan pernapasan saat tidur). WAJIB intervensi medis segera: diet ketat terpantau dokter, terapi perilaku, aktivitas fisik terprogram, dan pemeriksaan metabolik lengkap.',
                $z_score
            ),
            'z_score' => round($z_score, 2)
        ];
    }
}

/**
 * Klasifikasi Status Gizi TB/U (Tinggi Badan menurut Umur)
 * 
 * @param float $z_score Nilai Z-score hasil perhitungan
 * @return array ['status', 'class', 'keterangan']
 */
function klasifikasiGizi_TBU($z_score) {
    if ($z_score < -3) {
        return [
            'status' => 'Sangat Pendek (Stunted)',
            'class' => 'status-danger',
            'keterangan' => sprintf(
                '⚠️ SANGAT PENDEK (Z-score: %.2f < -3 SD). Indikasi STUNTING KRONIS! Perlu intervensi gizi intensif sejak dini dengan suplementasi mikronutrien (zinc, vitamin A, vitamin D), peningkatan asupan protein hewani, pemantauan tumbuh kembang ketat setiap bulan, dan rujukan ke layanan kesehatan untuk evaluasi hormonal.',
                $z_score
            ),
            'z_score' => round($z_score, 2)
        ];
    } elseif ($z_score >= -3 && $z_score < -2) {
        return [
            'status' => 'Pendek',
            'class' => 'status-warning',
            'keterangan' => sprintf(
                '⚠️ PENDEK (Z-score: %.2f antara -3 SD sampai -2 SD). Risiko stunting! Tingkatkan asupan protein hewani (telur, ikan, daging, susu), sayuran hijau, buah-buahan, serta pantau tumbuh kembang rutin setiap 3 bulan. Konsultasi dokter anak untuk evaluasi potensi gangguan hormon pertumbuhan.',
                $z_score
            ),
            'z_score' => round($z_score, 2)
        ];
    } else { // Z >= -2 (termasuk kategori Normal sampai Sangat Tinggi)
        return [
            'status' => 'Normal',
            'class' => 'status-success',
            'keterangan' => sprintf(
                '✅ NORMAL (Z-score: %.2f ≥ -2 SD). Pertumbuhan tinggi badan sesuai usia! Pertahankan gizi seimbang dengan protein hewani cukup, aktivitas fisik teratur (olahraga, bermain), tidur cukup 10-12 jam per hari untuk mengoptimalkan produksi hormon pertumbuhan.',
                $z_score
            ),
            'z_score' => round($z_score, 2)
        ];
    }
    // Catatan: WHO tidak menetapkan kategori "Tinggi" atau "Sangat Tinggi" sebagai masalah gizi untuk TB/U
    // Tinggi badan berlebih umumnya bukan indikator masalah gizi, bisa faktor genetik
}

/**
 * Klasifikasi Status Gizi BB/TB (Berat Badan menurut Tinggi Badan)
 * 
 * @param float $z_score Nilai Z-score hasil perhitungan
 * @return array ['status', 'class', 'keterangan']
 */
function klasifikasiGizi_BBTB($z_score) {
    if ($z_score < -3) {
        return [
            'status' => 'Gizi Buruk (Severely Wasted)',
            'class' => 'status-danger',
            'keterangan' => sprintf(
                '🚨 GIZI BURUK - SANGAT KURUS (Z-score: %.2f < -3 SD). DARURAT GIZI! Rujuk SEGERA ke rumah sakit untuk rawat inap, terapi gizi medis intensif menggunakan formula WHO (F-75/F-100), pemeriksaan infeksi, dehidrasi, hipoglikemia, dan komplikasi medis lainnya. Risiko kematian sangat tinggi!',
                $z_score
            ),
            'z_score' => round($z_score, 2)
        ];
    } elseif ($z_score >= -3 && $z_score < -2) {
        return [
            'status' => 'Gizi Kurang (Wasted)',
            'class' => 'status-warning',
            'keterangan' => sprintf(
                '⚠️ GIZI KURANG - KURUS (Z-score: %.2f antara -3 SD sampai -2 SD). Risiko wasting! Tingkatkan asupan kalori dan protein (telur 2-3 butir/hari, ikan, daging, susu full cream 400ml/hari), makanan padat nutrisi 5-6x sehari, pemantauan berat badan setiap minggu, dan konsultasi ahli gizi.',
                $z_score
            ),
            'z_score' => round($z_score, 2)
        ];
    } elseif ($z_score >= -2 && $z_score < 1) {
        return [
            'status' => 'Gizi Baik',
            'class' => 'status-success',
            'keterangan' => sprintf(
                '✅ GIZI BAIK (Z-score: %.2f antara -2 SD sampai +1 SD). Proporsi berat terhadap tinggi badan ideal! Pertahankan pola makan seimbang dengan 3x makan utama + 2x snack sehat, aktivitas fisik teratur sesuai usia, dan pemantauan rutin setiap 3 bulan.',
                $z_score
            ),
            'z_score' => round($z_score, 2)
        ];
    } elseif ($z_score >= 1 && $z_score < 2) {
        return [
            'status' => 'Beresiko Gizi Lebih',
            'class' => 'status-warning',
            'keterangan' => sprintf(
                '⚠️ BERESIKO GIZI LEBIH (Z-score: %.2f antara +1 SD sampai +2 SD). Berat badan mulai berlebih untuk tinggi badan! Atur porsi makan (kurangi 10-15%%), tingkatkan konsumsi sayur dan buah, kurangi gorengan dan minuman manis, aktivitas fisik minimal 60 menit/hari, dan batasi screen time maksimal 2 jam/hari.',
                $z_score
            ),
            'z_score' => round($z_score, 2)
        ];
    } elseif ($z_score >= 2 && $z_score < 3) {
        return [
            'status' => 'Gizi Lebih (Overweight)',
            'class' => 'status-warning',
            'keterangan' => sprintf(
                '⚠️ GIZI LEBIH (Z-score: %.2f antara +2 SD sampai +3 SD). Overweight! Konsultasi ahli gizi untuk program diet terapeutik, tingkatkan olahraga terstruktur (berenang, bersepeda, senam) 60-90 menit/hari, evaluasi pola makan keluarga, kurangi junk food dan fast food, serta pantau setiap 2 minggu.',
                $z_score
            ),
            'z_score' => round($z_score, 2)
        ];
    } else { // Z >= 3
        return [
            'status' => 'Obesitas',
            'class' => 'status-danger',
            'keterangan' => sprintf(
                '🚨 OBESITAS (Z-score: %.2f ≥ +3 SD). KRITIS! Rujuk ke dokter spesialis anak sub-bagian endokrin untuk: pemeriksaan gula darah puasa dan 2 jam PP, HbA1c, profil lipid lengkap, fungsi hati (SGOT/SGPT), terapi obesitas multi-disiplin (ahli gizi, psikolog, fisioterapi), dan program penurunan berat badan terpantau.',
                $z_score
            ),
            'z_score' => round($z_score, 2)
        ];
    }
}

/**
 * Wrapper Function: Klasifikasi otomatis berdasarkan indikator
 * 
 * @param float $z_score Nilai Z-score
 * @param string $indikator 'bbu', 'tbu', atau 'bbtb'
 * @return array ['status', 'class', 'keterangan', 'z_score']
 */
function klasifikasiStatusGizi($z_score, $indikator) {
    switch (strtolower($indikator)) {
        case 'bbu':
        case 'bb_u':
            return klasifikasiGizi_BBU($z_score);
        
        case 'tbu':
        case 'tb_u':
            return klasifikasiGizi_TBU($z_score);
        
        case 'bbtb':
        case 'bb_tb':
            return klasifikasiGizi_BBTB($z_score);
        
        default:
            return [
                'status' => 'Error: Indikator Tidak Valid',
                'class' => 'status-normal',
                'keterangan' => 'Indikator harus: bbu, tbu, atau bbtb',
                'z_score' => 0
            ];
    }
}