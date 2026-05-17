<?php
/**
 * ============================================================================
 * FUNGSI KLASIFIKASI STATUS GIZI WHO - 8 KATEGORI PROGRAMATIK
 * ============================================================================
 * 
 * Implementasi WHO Child Growth Standards (0-59 bulan) dengan 8 kategori:
 * 1. Stunting
 * 2. Risiko Stunting  
 * 3. Gizi Kurang
 * 4. Beresiko Gizi Kurang
 * 5. Gizi Baik
 * 6. Beresiko Gizi Lebih
 * 7. Gizi Lebih
 * 8. Obesitas
 * 
 * RULES:
 * - LMS WHO untuk z-score
 * - <24 bulan: BB/PB (panjang badan/berbaring)
 * - ≥24 bulan: BB/TB (tinggi badan/berdiri)
 * - Koreksi 0.7 cm untuk PB↔TB
 * - Prioritas: TB/U → BB/U → Adiposity (BB/TB/IMT-U)
 */

require_once __DIR__ . '/who_lms_data.php';
require_once __DIR__ . '/fungsi_gizi.php';

/**
 * ============================================================================
 * WRAPPER FUNCTIONS FOR Z-SCORE CALCULATION (API COMPATIBILITY)
 * ============================================================================
 * Map fungsi yang sudah ada ke format API yang diminta
 */

/**
 * Hitung z-score untuk indeks berbasis umur (HAZ, WAZ, BAZ, HCZ)
 * @param string $index 'HAZ' (TB/U), 'WAZ' (BB/U), 'BAZ' (IMT/U), 'HCZ' (LK/U)
 * @param string $sex 'L' atau 'P'
 * @param int $age_days Umur dalam hari
 * @param float $X Nilai yang diukur (tinggi cm, berat kg, BMI, atau lingkar kepala cm)
 * @return float|null Z-score or null if not available
 */
function zAge($index, $sex, $age_days, $X) {
    $age_month = $age_days / 30.4375; // Konversi hari ke bulan
    
    switch ($index) {
        case 'HAZ': // TB/U (Height-for-Age)
            return hitungZScoreTBU_LMS($age_month, $X, $sex, 'berdiri');
            
        case 'WAZ': // BB/U (Weight-for-Age)
            return hitungZScoreBBU_LMS($age_month, $X, $sex);
            
        case 'BAZ': // IMT/U (BMI-for-Age) - gunakan proxy BB/TB
            // Untuk BMI, kita perlu tinggi badan, tapi tidak tersedia di parameter
            // Sebagai workaround, return null atau gunakan pendekatan lain
            return null; // Will be calculated separately in main function
            
        case 'HCZ': // LK/U (Head Circumference-for-Age)
            // Belum ada data LMS untuk lingkar kepala, return null
            return null;
            
        default:
            return 0.0;
    }
}

/**
 * Hitung z-score untuk indeks berbasis panjang/tinggi (WHZ - BB/TB atau BB/PB)
 * @param string $index 'WHZ' (Weight-for-Height/Length)
 * @param string $sex 'L' atau 'P'
 * @param float $len_cm Panjang/tinggi badan dalam cm
 * @param float $weight_kg Berat badan dalam kg
 * @return float Z-score
 */
function zLen($index, $sex, $len_cm, $weight_kg) {
    if ($index === 'WHZ') {
        // Gunakan fungsi BB/TB yang sudah ada
        $age_month = 24; // Default, akan disesuaikan dengan umur sebenarnya
        return hitungZScoreBBTB_LMS($age_month, $weight_kg, $len_cm, $sex, 'berdiri');
    }
    return 0.0;
}

/**
 * Helper: hitung max z-score (untuk adiposity)
 */
function maxZ($a, $b) {
    if ($a === null && $b === null) return null;
    if ($a === null) return $b;
    if ($b === null) return $a;
    return max($a, $b);
}

/**
 * Helper: format usia dalam string readable
 */
function humanAge($dob, $dref) {
    if (!$dob) return '-';
    try {
        $d1 = new DateTime($dob);
        $d2 = new DateTime($dref);
        $months = ($d2->format('Y') - $d1->format('Y')) * 12 + ($d2->format('n') - $d1->format('n'));
        
        if ($months < 12) {
            return $months . ' bulan';
        }
        
        $years = intval($months / 12);
        $remaining_months = $months % 12;
        
        if ($remaining_months > 0) {
            return $years . ' tahun ' . $remaining_months . ' bulan';
        }
        return $years . ' tahun';
    } catch (Exception $e) {
        return '-';
    }
}

/**
 * ============================================================================
 * 1. KOREKSI TINGGI BADAN (PB ↔ TB)
 * ============================================================================
 */
function koreksiTinggi($height_cm, $measure_method) {
    // PB (berbaring) → TB (berdiri): kurangi 0.7 cm
    // TB (berdiri) → PB (berbaring): tambah 0.7 cm
    
    if ($measure_method === 'berbaring') {
        // Input adalah PB, untuk TB kurangi 0.7
        return [
            'PB' => $height_cm,
            'TB' => $height_cm - 0.7
        ];
    } else {
        // Input adalah TB, untuk PB tambah 0.7
        return [
            'PB' => $height_cm + 0.7,
            'TB' => $height_cm
        ];
    }
}

/**
 * ============================================================================
 * 2. PILIH REFERENSI BB/TB BERDASARKAN UMUR
 * ============================================================================
 */
function pilihReferensiBBTB($age_month) {
    // <24 bulan: gunakan BB/PB (panjang badan/berbaring)
    // ≥24 bulan: gunakan BB/TB (tinggi badan/berdiri)
    
    if ($age_month < 24) {
        return 'BB_PB';
    } else {
        return 'BB_TB';
    }
}

/**
 * ============================================================================
 * 3. KLASIFIKASI TB/U (Stunting Axis)
 * ============================================================================
 */
function classify_tbu_axis($z_tbu) {
    if ($z_tbu < -3.0) {
        return [
            'label' => 'Stunting',
            'detail' => 'Sangat Pendek (Severely Stunted)',
            'flag_stunting' => true,
            'severity' => 'severe',
            'color' => '#DC2626'
        ];
    } elseif ($z_tbu < -2.0) {
        return [
            'label' => 'Stunting',
            'detail' => 'Pendek (Stunted)',
            'flag_stunting' => true,
            'severity' => 'moderate',
            'color' => '#EF4444'
        ];
    } elseif ($z_tbu <= -1.0) {  // Changed to <= for inclusive boundary
        return [
            'label' => 'Risiko Stunting',
            'detail' => 'Band programatik (−2 s/d −1 SD)',
            'flag_stunting' => false,
            'severity' => 'risk',
            'color' => '#F59E0B'
        ];
    } else {
        return [
            'label' => 'Normal',
            'detail' => 'TB/U normal (≥ −1 SD)',
            'flag_stunting' => false,
            'severity' => null,
            'color' => '#10B981'
        ];
    }
}

/**
 * ============================================================================
 * 4. KLASIFIKASI BB/U (Underweight Axis)
 * ============================================================================
 */
function classify_bbu_axis($z_bbu) {
    if ($z_bbu < -3.0) {
        return [
            'label' => 'Gizi Kurang',
            'detail' => 'Severely Underweight (WHO: "gizi buruk")',
            'flag_under' => true,
            'severity' => 'severe',
            'color' => '#DC2626'
        ];
    } elseif ($z_bbu < -2.0) {
        return [
            'label' => 'Gizi Kurang',
            'detail' => 'Underweight (−3 s/d < −2 SD)',
            'flag_under' => true,
            'severity' => 'moderate',
            'color' => '#EF4444'
        ];
    } elseif ($z_bbu <= -1.0) {  // Changed to <= for inclusive boundary
        return [
            'label' => 'Beresiko Gizi Kurang',
            'detail' => 'Band programatik (−2 s/d −1 SD)',
            'flag_under' => false,
            'severity' => 'risk',
            'color' => '#F59E0B'
        ];
    } else {
        return [
            'label' => 'Normal',
            'detail' => 'BB/U normal (≥ −1 SD)',
            'flag_under' => false,
            'severity' => null,
            'color' => '#10B981'
        ];
    }
}

/**
 * ============================================================================
 * 5. KLASIFIKASI ADIPOSITY (BB/TB atau IMT/U)
 * ============================================================================
 */
function classify_adiposity_axis($z_prop) {
    if ($z_prop > 3.0) {
        return [
            'label' => 'Obesitas',
            'detail' => '> +3 SD (WHO: obese)',
            'flags' => ['obesity' => true],
            'color' => '#7C3AED'
        ];
    } elseif ($z_prop > 2.0) {
        return [
            'label' => 'Gizi Lebih',
            'detail' => '> +2 s/d ≤ +3 SD (overweight)',
            'flags' => ['overweight' => true],
            'color' => '#8B5CF6'
        ];
    } elseif ($z_prop > 1.0) {
        return [
            'label' => 'Beresiko Gizi Lebih',
            'detail' => '> +1 s/d ≤ +2 SD (at risk)',
            'flags' => ['risk_over' => true],
            'color' => '#F59E0B'
        ];
    } elseif ($z_prop >= -2.0) {
        return [
            'label' => 'Gizi Baik',
            'detail' => '−2 s/d ≤ +1 SD (normal)',
            'flags' => [],
            'color' => '#10B981'
        ];
    } else {
        return [
            'label' => 'Gizi Baik',
            'detail' => 'Catatan: z < −2 di proporsi → cek wasting',
            'flags' => [],
            'color' => '#10B981'
        ];
    }
}

/**
 * ============================================================================
 * 6. KATEGORI TUNGGAL 8-ITEM (DENGAN PRIORITAS)
 * ============================================================================
 * 
 * Prioritas:
 * 1. TB/U (Stunting, Risiko Stunting)
 * 2. BB/U (Gizi Kurang, Beresiko Gizi Kurang)
 * 3. Adiposity (Obesitas, Gizi Lebih, Beresiko Gizi Lebih, Gizi Baik)
 */
function overall_8_category($tbu, $bbu, $adp) {
    // 1) Stunting axis (prioritas tertinggi)
    if ($tbu['label'] === 'Stunting') {
        return [
            'kategori' => 'Stunting',
            'source' => 'TB/U',
            'detail' => $tbu['detail'],
            'color' => $tbu['color'],
            'severity' => $tbu['severity']
        ];
    }
    
    if ($tbu['label'] === 'Risiko Stunting') {
        return [
            'kategori' => 'Risiko Stunting',
            'source' => 'TB/U',
            'detail' => $tbu['detail'],
            'color' => $tbu['color'],
            'severity' => $tbu['severity']
        ];
    }

    // 2) Underweight axis (prioritas kedua)
    if ($bbu['label'] === 'Gizi Kurang') {
        return [
            'kategori' => 'Gizi Kurang',
            'source' => 'BB/U',
            'detail' => $bbu['detail'],
            'color' => $bbu['color'],
            'severity' => $bbu['severity']
        ];
    }
    
    if ($bbu['label'] === 'Beresiko Gizi Kurang') {
        return [
            'kategori' => 'Beresiko Gizi Kurang',
            'source' => 'BB/U',
            'detail' => $bbu['detail'],
            'color' => $bbu['color'],
            'severity' => $bbu['severity']
        ];
    }

    // 3) Adiposity axis (prioritas terakhir)
    if (in_array($adp['label'], ['Obesitas', 'Gizi Lebih', 'Beresiko Gizi Lebih', 'Gizi Baik'])) {
        return [
            'kategori' => $adp['label'],
            'source' => 'BB/TB|IMT/U',
            'detail' => $adp['detail'],
            'color' => $adp['color'],
            'severity' => isset($adp['flags']['obesity']) ? 'severe' : 
                         (isset($adp['flags']['overweight']) ? 'moderate' : 
                         (isset($adp['flags']['risk_over']) ? 'risk' : null))
        ];
    }

    // Fallback (seharusnya tidak pernah sampai sini)
    return [
        'kategori' => 'Gizi Baik',
        'source' => 'BB/TB|IMT/U',
        'detail' => 'Default normal',
        'color' => '#10B981',
        'severity' => null
    ];
}

/**
 * ============================================================================
 * 7. VALIDASI BIOLOGIS (GUARDRAILS)
 * ============================================================================
 */
function validasi_biologis($age_m, $weight_kg, $height_cm) {
    $warnings = [];
    
    // Validasi tinggi badan berdasarkan usia
    if ($age_m >= 24 && $height_cm < 75) {
        $warnings[] = "Data tidak masuk akal: Usia ≥24 bulan tetapi tinggi < 75 cm. Periksa kembali data pengukuran.";
    }
    
    if ($age_m >= 36 && $height_cm < 80) {
        $warnings[] = "Data tidak masuk akal: Usia ≥36 bulan tetapi tinggi < 80 cm. Kemungkinan kesalahan input atau kondisi medis khusus.";
    }
    
    // Validasi berat badan berdasarkan usia
    if ($age_m > 18 && $weight_kg < 6) {
        $warnings[] = "Data tidak masuk akal: Usia >18 bulan tetapi berat < 6 kg. Periksa kembali timbangan atau satuan yang digunakan.";
    }
    
    if ($age_m < 48 && $weight_kg > 25) {
        $warnings[] = "Peringatan kuat: Usia <4 tahun tetapi berat > 25 kg. Pastikan timbangan terkalibrasi dan satuan benar (kg, bukan ons).";
    }
    
    // Validasi kombinasi tinggi-berat yang ekstrem
    if ($height_cm > 0 && $weight_kg > 0) {
        $bmi = $weight_kg / pow($height_cm / 100, 2);
        if ($bmi > 30 && $age_m < 36) {
            $warnings[] = "BMI sangat tinggi untuk usia balita (BMI = " . round($bmi, 1) . "). Verifikasi data pengukuran.";
        }
        if ($bmi < 10 && $age_m > 12) {
            $warnings[] = "BMI sangat rendah (BMI = " . round($bmi, 1) . "). Kemungkinan gizi buruk berat atau kesalahan input.";
        }
    }
    
    return $warnings;
}

/**
 * ============================================================================
 * 8. GENERATE FLAGS KLINIS
 * ============================================================================
 */
function generate_flags($z_tbu, $z_bbu, $z_bbtb, $z_imtu, $data_warnings) {
    return [
        'stunting' => ($z_tbu < -2.0),
        'wasting' => ($z_bbtb < -2.0 || $z_imtu < -2.0),
        'risk_over' => ($z_bbtb > 1.0 || $z_imtu > 1.0),
        'overweight' => ($z_bbtb > 2.0 || $z_imtu > 2.0),
        'obesity' => ($z_bbtb > 3.0 || $z_imtu > 3.0),
        'severely_stunted' => ($z_tbu < -3.0),
        'severely_underweight' => ($z_bbu < -3.0),
        'severely_wasted' => ($z_bbtb < -3.0 || $z_imtu < -3.0),
        'data_warning' => !empty($data_warnings) ? implode(' | ', $data_warnings) : null
    ];
}

/**
 * ============================================================================
 * 9. GENERATE INTERPRETASI KLINIS
 * ============================================================================
 */
function generate_interpretasi($overall, $flags, $zscore, $age_m, $sex) {
    $kategori = $overall['kategori'];
    $source = $overall['source'];
    
    $interpretasi = [];
    
    // 1. Status utama
    $interpretasi[] = "Anak tergolong **{$kategori}** berdasarkan indeks {$source}.";
    
    // 2. Detail z-score
    $z_details = [];
    $z_details[] = "TB/U = " . number_format($zscore['tbu'], 2) . " SD";
    $z_details[] = "BB/U = " . number_format($zscore['bbu'], 2) . " SD";
    $z_details[] = "BB/TB = " . number_format($zscore['bbtb'], 2) . " SD";
    $z_details[] = "IMT/U = " . number_format($zscore['imtu'], 2) . " SD";
    $interpretasi[] = "Nilai z-score: " . implode(", ", $z_details) . ".";
    
    // 3. Analisis kombinasi kondisi
    $kombinasi = [];
    if ($flags['stunting']) {
        $kombinasi[] = "stunting (TB/U < -2 SD)";
    }
    if ($flags['wasting']) {
        $kombinasi[] = "wasting/kurus (BB/TB < -2 SD)";
    }
    if ($flags['obesity']) {
        $kombinasi[] = "obesitas (BB/TB > +3 SD)";
    } elseif ($flags['overweight']) {
        $kombinasi[] = "gizi lebih (BB/TB > +2 SD)";
    } elseif ($flags['risk_over']) {
        $kombinasi[] = "beresiko gizi lebih (BB/TB > +1 SD)";
    }
    
    if (count($kombinasi) > 1) {
        $interpretasi[] = "⚠️ **PERHATIAN**: Anak mengalami kombinasi masalah gizi: " . implode(" + ", $kombinasi) . ". Kondisi ini memerlukan penanganan komprehensif.";
    }
    
    // 4. Rekomendasi spesifik
    if ($kategori === 'Stunting') {
        if ($flags['obesity']) {
            $interpretasi[] = "Meskipun stunting, anak mengalami obesitas. Fokus pada asupan gizi seimbang (protein, mikronutrien) sambil mengendalikan kalori berlebih. Rujuk ke ahli gizi.";
        } elseif ($flags['wasting']) {
            $interpretasi[] = "Stunting disertai wasting (gizi buruk). KONDISI KRITIS. Rujuk segera ke faskes untuk penanganan gizi buruk dan pemantauan intensif.";
        } else {
            $interpretasi[] = "Anjurkan pemantauan pertumbuhan linier, evaluasi asupan protein & mikronutrien (zinc, vitamin A), stimulasi psikososial, dan rujuk sesuai pedoman bila perlu.";
        }
    } elseif ($kategori === 'Risiko Stunting') {
        $interpretasi[] = "Anak berada dalam zona risiko stunting (TB/U antara -2 SD dan -1 SD). Lakukan intervensi pencegahan: tingkatkan asupan gizi seimbang, pantau pertumbuhan bulanan.";
    } elseif ($kategori === 'Gizi Kurang') {
        if ($flags['severely_underweight']) {
            $interpretasi[] = "Gizi kurang berat (BB/U < -3 SD). Kondisi ini memerlukan penanganan segera. Rujuk ke puskesmas/RS untuk evaluasi dan terapi gizi.";
        } else {
            $interpretasi[] = "Tingkatkan asupan kalori dan protein. Berikan makanan bergizi padat energi, pantau berat badan mingguan.";
        }
    } elseif ($kategori === 'Obesitas') {
        if ($flags['stunting']) {
            $interpretasi[] = "Obesitas pada anak stunting (stunted-obese). Butuh strategi khusus: jaga asupan protein untuk pertumbuhan linear sambil kurangi kalori kosong (gula, gorengan).";
        } else {
            $interpretasi[] = "Lakukan modifikasi pola makan (kurangi gula, gorengan, minuman manis), tingkatkan aktivitas fisik, konseling keluarga. Rujuk ke ahli gizi untuk program penurunan berat badan bertahap.";
        }
    } elseif ($kategori === 'Gizi Lebih' || $kategori === 'Beresiko Gizi Lebih') {
        $interpretasi[] = "Pantau pola makan, hindari makanan tinggi gula dan lemak, dorong aktivitas fisik. Konseling gizi untuk mencegah obesitas.";
    } elseif ($kategori === 'Gizi Baik') {
        $interpretasi[] = "Pertahankan pola makan seimbang dan aktivitas fisik yang cukup. Lakukan pemantauan pertumbuhan rutin setiap bulan di posyandu.";
    }
    
    // 5. Peringatan data jika ada
    if ($flags['data_warning']) {
        $interpretasi[] = "⚠️ **PERINGATAN DATA**: " . $flags['data_warning'];
    }
    
    return implode("\n\n", $interpretasi);
}

/**
 * ============================================================================
 * 10. PIPELINE HITUNG STATUS 8-ITEM (MAIN FUNCTION - UPGRADED)
 * ============================================================================
 */
function hitung_status_8_item($input) {
    // Normalisasi input
    $age_m   = $input['age_month'];
    $age_d   = $input['age_days'];
    $sex     = $input['sex'];  // 'L' atau 'P'
    $W       = (float)$input['weight_kg'];
    $H_raw   = (float)$input['height_cm'];
    $method  = $input['measure_method'];  // 'berdiri' atau 'berbaring'

    // VALIDASI BIOLOGIS (GUARDRAILS)
    $data_warnings = validasi_biologis($age_m, $W, $H_raw);

    // Koreksi tinggi PB↔TB (±0.7 cm)
    $height_corrected = koreksiTinggi($H_raw, $method);
    
    // Pilih referensi BB/TB berdasarkan umur
    $refBBTB = pilihReferensiBBTB($age_m);
    
    // Pilih tinggi yang sesuai untuk setiap indeks
    // TB/U: selalu gunakan TB (tinggi berdiri)
    $H_for_tbu = $height_corrected['TB'];
    
    // BB/TB: gunakan TB jika ≥24 bulan, PB jika <24 bulan
    $H_for_bbtb = ($age_m >= 24) ? $height_corrected['TB'] : $height_corrected['PB'];

    // Hitung Z-score menggunakan fungsi WHO LMS yang sudah ada
    // TB/U (Height-for-age)
    $z_tbu = hitungZScoreTBU_LMS($age_m, $H_for_tbu, $sex, $method);
    
    // BB/U (Weight-for-age)
    $z_bbu = hitungZScoreBBU_LMS($age_m, $W, $sex);
    
    // BMI
    $bmi = $W / pow($H_for_tbu / 100, 2);
    
    // BB/TB atau BB/PB (Weight-for-height/length)
    $z_bbtb = hitungZScoreBBTB_LMS($age_m, $W, $H_for_bbtb, $sex, $method);
    
    // IMT/U (BMI-for-age) - Gunakan z_bbtb sebagai proxy karena data BMIFA belum lengkap
    $z_imtu = $z_bbtb;

    // Klasifikasi per sumbu
    $tbu = classify_tbu_axis($z_tbu);
    $bbu = classify_bbu_axis($z_bbu);
    $adp = classify_adiposity_axis($z_bbtb);

    // Kategori 8-item tunggal
    $overall = overall_8_category($tbu, $bbu, $adp);
    
    // Z-score array
    $zscore = [
        'tbu' => round($z_tbu, 2),
        'bbu' => round($z_bbu, 2),
        'bbtb' => round($z_bbtb, 2),
        'imtu' => round($z_imtu, 2)
    ];

    // Generate FLAGS
    $flags = generate_flags($z_tbu, $z_bbu, $z_bbtb, $z_imtu, $data_warnings);
    
    // Generate INTERPRETASI
    $interpretasi = generate_interpretasi($overall, $flags, $zscore, $age_m, $sex);

    // Return hasil lengkap (FORMAT BARU)
    return [
        'input' => [
            'umur_bulan' => $age_m,
            'umur_hari' => $age_d,
            'jenis_kelamin' => $sex === 'L' ? 'Laki-laki' : 'Perempuan',
            'berat_kg' => $W,
            'tinggi_cm_raw' => $H_raw,
            'cara_ukur' => $method,
            'tinggi_corrected_PB' => $height_corrected['PB'],
            'tinggi_corrected_TB' => $height_corrected['TB'],
            'referensi_bbtb' => $refBBTB,
            'bmi' => round($bmi, 2)
        ],
        'zscore' => $zscore,
        'axis' => [
            'tbu' => [
                'label' => $tbu['label'],
                'catatan' => $tbu['detail'],
                'zscore' => $zscore['tbu']
            ],
            'bbu' => [
                'label' => $bbu['label'],
                'catatan' => $bbu['detail'],
                'zscore' => $zscore['bbu']
            ],
            'adiposity' => [
                'label' => $adp['label'],
                'catatan' => $adp['detail'],
                'zscore' => $zscore['bbtb']
            ]
        ],
        'overall_8' => [
            'kategori' => $overall['kategori'],
            'source' => $overall['source'],
            'detail' => $overall['detail'],
            'color' => $overall['color'],
            'severity' => $overall['severity']
        ],
        'flags' => $flags,
        'interpretasi' => $interpretasi,
        // BACKWARD COMPATIBILITY (untuk kode lama)
        'axes' => [
            'tbu' => $tbu,
            'bbu' => $bbu,
            'adiposity' => $adp
        ],
        'bmi' => round($bmi, 2)
    ];
}

/**
 * ============================================================================
 * 11. HELPER: GET COLOR CLASS FOR UI
 * ============================================================================
 */
function getStatusGiziColorClass($kategori) {
    $colorMap = [
        'Stunting' => 'badge-danger',
        'Risiko Stunting' => 'badge-warning',
        'Gizi Kurang' => 'badge-danger',
        'Beresiko Gizi Kurang' => 'badge-warning',
        'Gizi Baik' => 'badge-success',
        'Beresiko Gizi Lebih' => 'badge-warning',
        'Gizi Lebih' => 'badge-danger',
        'Obesitas' => 'badge-danger'
    ];
    
    return $colorMap[$kategori] ?? 'badge-info';
}

/**
 * ============================================================================
 * 12. UNIT TEST VALIDATION (untuk development)
 * ============================================================================
 */
function test_classification_thresholds() {
    $test_cases = [
        // TB/U tests (Stunting Axis)
        ['z' => -3.00, 'type' => 'tbu', 'expected' => 'Stunting', 'detail' => 'Sangat Pendek (Severely Stunted)'],
        ['z' => -2.50, 'type' => 'tbu', 'expected' => 'Stunting', 'detail' => 'Pendek (Stunted)'],
        ['z' => -2.01, 'type' => 'tbu', 'expected' => 'Stunting'],
        ['z' => -2.00, 'type' => 'tbu', 'expected' => 'Stunting'], // Boundary: exactly -2.0
        ['z' => -1.99, 'type' => 'tbu', 'expected' => 'Risiko Stunting'],
        ['z' => -1.50, 'type' => 'tbu', 'expected' => 'Risiko Stunting'],
        ['z' => -1.20, 'type' => 'tbu', 'expected' => 'Risiko Stunting'],
        ['z' => -1.01, 'type' => 'tbu', 'expected' => 'Risiko Stunting'],
        ['z' => -1.00, 'type' => 'tbu', 'expected' => 'Risiko Stunting'], // Boundary: exactly -1.0
        ['z' => -0.99, 'type' => 'tbu', 'expected' => 'Normal'],
        ['z' => -0.50, 'type' => 'tbu', 'expected' => 'Normal'],
        ['z' => 0.00, 'type' => 'tbu', 'expected' => 'Normal'],
        
        // BB/U tests (Underweight Axis)
        ['z' => -3.00, 'type' => 'bbu', 'expected' => 'Gizi Kurang', 'detail' => 'Severely Underweight'],
        ['z' => -2.50, 'type' => 'bbu', 'expected' => 'Gizi Kurang', 'detail' => 'Underweight'],
        ['z' => -2.01, 'type' => 'bbu', 'expected' => 'Gizi Kurang'],
        ['z' => -2.00, 'type' => 'bbu', 'expected' => 'Gizi Kurang'], // Boundary: exactly -2.0
        ['z' => -1.99, 'type' => 'bbu', 'expected' => 'Beresiko Gizi Kurang'],
        ['z' => -1.50, 'type' => 'bbu', 'expected' => 'Beresiko Gizi Kurang'],
        ['z' => -1.30, 'type' => 'bbu', 'expected' => 'Beresiko Gizi Kurang'],
        ['z' => -1.01, 'type' => 'bbu', 'expected' => 'Beresiko Gizi Kurang'],
        ['z' => -1.00, 'type' => 'bbu', 'expected' => 'Beresiko Gizi Kurang'], // Boundary: exactly -1.0
        ['z' => -0.99, 'type' => 'bbu', 'expected' => 'Normal'],
        
        // Adiposity tests (BB/TB atau IMT/U)
        ['z' => 3.10, 'type' => 'adp', 'expected' => 'Obesitas'],
        ['z' => 3.01, 'type' => 'adp', 'expected' => 'Obesitas'],
        ['z' => 3.00, 'type' => 'adp', 'expected' => 'Obesitas'], // Boundary: exactly +3.0
        ['z' => 2.99, 'type' => 'adp', 'expected' => 'Gizi Lebih'],
        ['z' => 2.50, 'type' => 'adp', 'expected' => 'Gizi Lebih'],
        ['z' => 2.01, 'type' => 'adp', 'expected' => 'Gizi Lebih'],
        ['z' => 2.00, 'type' => 'adp', 'expected' => 'Gizi Lebih'], // Boundary: exactly +2.0
        ['z' => 1.99, 'type' => 'adp', 'expected' => 'Beresiko Gizi Lebih'],
        ['z' => 1.50, 'type' => 'adp', 'expected' => 'Beresiko Gizi Lebih'],
        ['z' => 1.01, 'type' => 'adp', 'expected' => 'Beresiko Gizi Lebih'],
        ['z' => 1.00, 'type' => 'adp', 'expected' => 'Beresiko Gizi Lebih'], // Boundary: exactly +1.0
        ['z' => 0.99, 'type' => 'adp', 'expected' => 'Gizi Baik'],
        ['z' => 0.20, 'type' => 'adp', 'expected' => 'Gizi Baik'],
        ['z' => 0.00, 'type' => 'adp', 'expected' => 'Gizi Baik'],
        ['z' => -0.50, 'type' => 'adp', 'expected' => 'Gizi Baik'],
        ['z' => -1.99, 'type' => 'adp', 'expected' => 'Gizi Baik'],
        ['z' => -2.00, 'type' => 'adp', 'expected' => 'Gizi Baik'], // At -2.0, still normal but flag wasting
        ['z' => -2.10, 'type' => 'adp', 'expected' => 'Gizi Baik'], // Wasting flag will be true
    ];
    
    $results = [];
    $passed_count = 0;
    $failed_count = 0;
    
    foreach ($test_cases as $test) {
        $z = $test['z'];
        $type = $test['type'];
        
        if ($type === 'tbu') {
            $result = classify_tbu_axis($z);
        } elseif ($type === 'bbu') {
            $result = classify_bbu_axis($z);
        } else {
            $result = classify_adiposity_axis($z);
        }
        
        $passed = ($result['label'] === $test['expected']);
        
        if ($passed) {
            $passed_count++;
        } else {
            $failed_count++;
        }
        
        $results[] = [
            'z' => $z,
            'type' => $type,
            'expected' => $test['expected'],
            'got' => $result['label'],
            'detail' => $result['detail'],
            'passed' => $passed ? '✅' : '❌'
        ];
    }
    
    return [
        'summary' => [
            'total' => count($test_cases),
            'passed' => $passed_count,
            'failed' => $failed_count,
            'success_rate' => round(($passed_count / count($test_cases)) * 100, 1) . '%'
        ],
        'results' => $results
    ];
}

/**
 * ============================================================================
 * 13. TEST KASUS REAL (UNTUK VALIDASI)
 * ============================================================================
 */
function test_real_cases() {
    $cases = [
        [
            'name' => 'Anak 3y9b, stunting + obesitas',
            'input' => [
                'age_month' => 45,
                'age_days' => 1350,
                'sex' => 'L',
                'weight_kg' => 27,
                'height_cm' => 95,
                'measure_method' => 'berdiri'
            ],
            'expected' => [
                'kategori' => 'Stunting',
                'flags' => ['stunting' => true, 'obesity' => true]
            ]
        ],
        [
            'name' => 'Anak 3y7b, tinggi normal tapi kurus (wasting)',
            'input' => [
                'age_month' => 43,
                'age_days' => 1290,
                'sex' => 'P',
                'weight_kg' => 12.4,
                'height_cm' => 110,
                'measure_method' => 'berdiri'
            ],
            'expected' => [
                'kategori' => 'Gizi Baik', // Might be normal by priority, but wasting flag
                'flags' => ['wasting' => true]
            ]
        ],
        [
            'name' => 'Anak 2y8b, stunting + wasting + severely underweight',
            'input' => [
                'age_month' => 32,
                'age_days' => 960,
                'sex' => 'L',
                'weight_kg' => 5,
                'height_cm' => 78,
                'measure_method' => 'berdiri'
            ],
            'expected' => [
                'kategori' => 'Stunting',
                'flags' => ['stunting' => true, 'wasting' => true, 'severely_underweight' => true],
                'warnings' => ['Data tidak masuk akal: Usia >18 bulan tetapi berat < 6 kg']
            ]
        ],
        [
            'name' => 'Anak 2y9b, data mencurigakan (kemungkinan salah input)',
            'input' => [
                'age_month' => 33,
                'age_days' => 990,
                'sex' => 'P',
                'weight_kg' => 20,
                'height_cm' => 73,
                'measure_method' => 'berdiri'
            ],
            'expected' => [
                'warnings' => ['tinggi < 75 cm', 'berat > 25 kg']
            ]
        ]
    ];
    
    return $cases;
}

/**
 * ============================================================================
 * 14. FUNGSI UTAMA: HITUNG STATUS GIZI LENGKAP (UNTUK API)
 * ============================================================================
 * Fungsi wrapper untuk digunakan oleh children_list.php dan children_detail.php
 * 
 * @param float $umur_bulan Umur dalam bulan (bisa desimal)
 * @param float $berat_kg Berat badan dalam kg
 * @param float $tinggi_cm Tinggi badan dalam cm
 * @param string $jenis_kelamin 'L' atau 'P'
 * @param string $cara_ukur 'berdiri' atau 'berbaring'
 * @param float|null $lingkar_kepala Lingkar kepala dalam cm (opsional)
 * @return array Hasil lengkap dengan status_gizi dan status_gizi_detail
 */
function hitungStatusGiziLengkap($umur_bulan, $berat_kg, $tinggi_cm, $jenis_kelamin, $cara_ukur = 'berdiri', $lingkar_kepala = null) {
    try {
        // Validasi input - WHO Standard: 0-60 bulan (0 hingga tepat 5 tahun)
        if ($umur_bulan < 0 || $umur_bulan > 60) {
            return [
                'status_gizi' => 'Belum diukur',
                'status_gizi_detail' => null,
                'error' => 'Umur harus 0-60 bulan (0-5 tahun). Umur saat ini: ' . number_format($umur_bulan, 1) . ' bulan'
            ];
        }
        
        if ($berat_kg <= 0 || $tinggi_cm <= 0) {
            return [
                'status_gizi' => 'Belum diukur',
                'status_gizi_detail' => null,
                'error' => 'Berat dan tinggi harus > 0'
            ];
        }
        
        $sex = strtoupper(trim($jenis_kelamin));
        if (!in_array($sex, ['L', 'P'])) {
            return [
                'status_gizi' => 'Belum diukur',
                'status_gizi_detail' => null,
                'error' => 'Jenis kelamin harus L atau P'
            ];
        }
        
        // Hitung umur dalam hari
        $age_days = round($umur_bulan * 30.4375);
        $is_under24 = ($umur_bulan < 24);
        
        // Koreksi tinggi badan untuk <24 bulan jika berbaring
        $tb_for_age = $tinggi_cm;
        $adiposity_source = 'BB/TB';
        
        if ($is_under24 && $cara_ukur === 'berbaring') {
            $tb_for_age = max(0.1, $tinggi_cm - 0.7); // PB → TB: kurangi 0.7 cm
            $adiposity_source = 'BB/PB';
        }
        
        // Hitung z-scores
        $z_tbu  = hitungZScoreTBU_LMS($umur_bulan, $tb_for_age, $sex, $cara_ukur);
        $z_bbu  = hitungZScoreBBU_LMS($umur_bulan, $berat_kg, $sex);
        $z_bbtb = hitungZScoreBBTB_LMS($umur_bulan, $berat_kg, $tinggi_cm, $sex, $cara_ukur);

        // Sanitasi: ubah NAN/INF/null menjadi null agar JSON-safe
        $sanitizeZ = function($v) {
            if ($v === null) return null;
            if (!is_finite($v)) return null; // tangkap NAN dan INF
            return $v;
        };
        $z_tbu  = $sanitizeZ($z_tbu);
        $z_bbu  = $sanitizeZ($z_bbu);
        $z_bbtb = $sanitizeZ($z_bbtb);

        // Hitung BMI dan z-score IMT/U
        $bmi = $tb_for_age > 0 ? $berat_kg / pow($tb_for_age / 100, 2) : null;
        $z_imtu = $z_bbtb; // Gunakan z_bbtb sebagai proxy untuk IMT/U
        
        // Lingkar kepala (opsional)
        $z_lku = null;
        if ($lingkar_kepala !== null && $lingkar_kepala > 0) {
            // Belum ada fungsi untuk LK/U, set null
            $z_lku = null;
        }
        
        // Klasifikasi per sumbu (null-safe: jika z-score null, fallback ke 'Normal'/'Gizi Baik')
        $axis_tbu = $z_tbu  !== null ? classify_tbu_axis($z_tbu)       : ['label' => 'Tidak Dapat Dihitung', 'detail' => 'Data tidak cukup', 'flag_stunting' => false, 'severity' => null, 'color' => '#9CA3AF'];
        $axis_bbu = $z_bbu  !== null ? classify_bbu_axis($z_bbu)       : ['label' => 'Tidak Dapat Dihitung', 'detail' => 'Data tidak cukup', 'flag_under'    => false, 'severity' => null, 'color' => '#9CA3AF'];
        $axis_adp = $z_bbtb !== null ? classify_adiposity_axis($z_bbtb): ['label' => 'Tidak Dapat Dihitung', 'detail' => 'Data tidak cukup', 'flags'         => [],    'color' => '#9CA3AF'];
        
        // Overall 8-kategori
        $overall = overall_8_category($axis_tbu, $axis_bbu, $axis_adp);
        
        // Generate flags (null-safe: jika z-score null, anggap tidak memenuhi kondisi)
        $data_warnings = validasi_biologis($umur_bulan, $berat_kg, $tinggi_cm);
        $flags = [
            'stunting'  => ($z_tbu  !== null && $z_tbu  < -2.0),
            'wasting'   => ($z_bbtb !== null && $z_bbtb < -2.0) || ($z_imtu !== null && $z_imtu < -2.0),
            'risk_over' => ($z_bbtb !== null && $z_bbtb > 1.0)  || ($z_imtu !== null && $z_imtu > 1.0),
            'overweight'=> ($z_bbtb !== null && $z_bbtb > 2.0)  || ($z_imtu !== null && $z_imtu > 2.0),
            'obesity'   => ($z_bbtb !== null && $z_bbtb > 3.0)  || ($z_imtu !== null && $z_imtu > 3.0),
        ];
        
        // Klasifikasi LK/U (opsional)
        $lk_axis = null;
        if ($z_lku !== null) {
            $lk_axis = classify_lku_axis($z_lku);
        }
        
        // Return hasil sesuai format API
        return [
            'status_gizi' => $overall['kategori'],
            'status_gizi_detail' => [
                'zscore' => [
                    'tbu'  => $z_tbu  !== null ? round($z_tbu,  2) : null,
                    'bbu'  => $z_bbu  !== null ? round($z_bbu,  2) : null,
                    'bbtb' => $z_bbtb !== null ? round($z_bbtb, 2) : null,
                    'imtu' => $z_imtu !== null ? round($z_imtu, 2) : null,
                    'lku'  => $z_lku  !== null ? round($z_lku,  2) : null
                ],
                'axis' => [
                    'tbu' => [
                        'label' => $axis_tbu['label'],
                        'detail' => $axis_tbu['detail']
                    ],
                    'bbu' => [
                        'label' => $axis_bbu['label'],
                        'detail' => $axis_bbu['detail']
                    ],
                    'adiposity' => [
                        'label' => $axis_adp['label'],
                        'detail' => $axis_adp['detail'],
                        'source' => $adiposity_source
                    ],
                    'lku' => $lk_axis
                ],
                'overall_8' => $overall,
                'flags' => $flags
            ]
        ];
        
    } catch (Exception $e) {
        error_log("Error in hitungStatusGiziLengkap: " . $e->getMessage());
        return [
            'status_gizi' => 'Belum diukur',
            'status_gizi_detail' => null,
            'error' => $e->getMessage()
        ];
    }
}

/**
 * Klasifikasi LK/U axis (Head Circumference-for-Age) - Opsional
 */
function classify_lku_axis($z_lku) {
    if ($z_lku === null) return null;
    
    if ($z_lku < -3.0) {
        return [
            'label' => 'LK kecil sekali',
            'detail' => 'HCZ < -3 SD'
        ];
    } elseif ($z_lku < -2.0) {
        return [
            'label' => 'LK kecil',
            'detail' => 'HCZ -3..<-2 SD'
        ];
    } elseif ($z_lku > 2.0) {
        return [
            'label' => 'LK besar',
            'detail' => 'HCZ > +2 SD'
        ];
    } else {
        return [
            'label' => 'LK normal',
            'detail' => 'HCZ -2..+2 SD'
        ];
    }
}

/**
 * ============================================================================
 * MAIN FUNCTION: klasifikasiStatusGiziWHO()
 * ============================================================================
 * Fungsi utama untuk klasifikasi status gizi anak berdasarkan WHO 2006
 * 
 * @param string $jenis_kelamin 'L' atau 'P'
 * @param int $usia_hari Usia anak dalam hari
 * @param float $bb Berat badan dalam kg
 * @param float $tb Tinggi/panjang badan dalam cm
 * @param float|null $lk Lingkar kepala dalam cm (optional)
 * @return array Hasil klasifikasi lengkap dengan z-scores dan kategori
 */
function klasifikasiStatusGiziWHO($jenis_kelamin, $usia_hari, $bb, $tb, $lk = null) {
    // Konversi usia ke bulan
    $usia_bulan = $usia_hari / 30.4375;
    
    // Tentukan cara ukur berdasarkan usia
    $cara_ukur = $usia_bulan < 24 ? 'berbaring' : 'berdiri';
    
    // Hitung z-scores untuk semua indeks
    $z_tbu = hitungZScoreTBU_LMS($usia_bulan, $tb, $jenis_kelamin, $cara_ukur);
    $z_bbu = hitungZScoreBBU_LMS($usia_bulan, $bb, $jenis_kelamin);
    $z_bbtb = hitungZScoreBBTB_LMS($usia_bulan, $bb, $tb, $jenis_kelamin, $cara_ukur);
    
    // Hitung IMT dan z-IMT/U
    $imt = ($tb > 0) ? $bb / (($tb / 100) ** 2) : 0;
    $z_imtu = hitungZScoreIMTU_LMS($usia_bulan, $imt, $jenis_kelamin);
    
    // Hitung z-LK/U jika data tersedia (skip jika fungsi tidak ada)
    $z_lku = null;
    if ($lk !== null && $lk > 0) {
        // Function hitungZScoreLKU_LMS belum ada, skip untuk sementara
        // $z_lku = hitungZScoreLKU_LMS($usia_bulan, $lk, $jenis_kelamin);
    }
    
    // Klasifikasi per axis
    $tbu_axis = classify_tbu_axis($z_tbu);
    $bbu_axis = classify_bbu_axis($z_bbu);
    
    // Untuk adiposity, gunakan max z-score antara BB/TB dan IMT/U
    $z_adiposity = maxZ($z_bbtb, $z_imtu);
    $adiposity_axis = classify_adiposity_axis($z_adiposity);
    $lku_axis = classify_lku_axis($z_lku);
    
    // Tentukan kategori dominan (overall_8)
    $overall_kategori = 'Gizi Baik'; // Default
    $overall_source = 'Normal (semua indeks dalam batas normal)';
    $overall_detail = 'Tidak ada masalah gizi terdeteksi';
    
    // Prioritas 1: Stunting (TB/U < -2 SD)
    if ($z_tbu !== null && $z_tbu < -2.0) {
        if ($z_tbu < -3.0) {
            $overall_kategori = 'Stunting Berat';
            $overall_source = 'TB/U';
            $overall_detail = $tbu_axis['detail'];
        } else {
            $overall_kategori = 'Stunting';
            $overall_source = 'TB/U';
            $overall_detail = $tbu_axis['detail'];
        }
    }
    // Prioritas 2: Risiko Stunting (TB/U -2 s/d -1 SD)
    elseif ($z_tbu !== null && $z_tbu >= -2.0 && $z_tbu < -1.0) {
        $overall_kategori = 'Risiko Stunting';
        $overall_source = 'TB/U';
        $overall_detail = $tbu_axis['detail'];
    }
    // Prioritas 3: Gizi Kurang (BB/U < -2 SD)
    elseif ($z_bbu !== null && $z_bbu < -2.0) {
        if ($z_bbu < -3.0) {
            $overall_kategori = 'Gizi Kurang Berat';
            $overall_source = 'BB/U';
            $overall_detail = $bbu_axis['detail'];
        } else {
            $overall_kategori = 'Gizi Kurang';
            $overall_source = 'BB/U';
            $overall_detail = $bbu_axis['detail'];
        }
    }
    // Prioritas 4: Beresiko Gizi Kurang (BB/U -2 s/d -1 SD)
    elseif ($z_bbu !== null && $z_bbu >= -2.0 && $z_bbu < -1.0) {
        $overall_kategori = 'Beresiko Gizi Kurang';
        $overall_source = 'BB/U';
        $overall_detail = $bbu_axis['detail'];
    }
    // Prioritas 5: Obesitas (BB/TB atau IMT/U > +3 SD)
    elseif (($z_bbtb !== null && $z_bbtb > 3.0) || ($z_imtu !== null && $z_imtu > 3.0)) {
        $overall_kategori = 'Obesitas';
        $overall_source = 'Adiposity (BB/TB atau IMT/U)';
        $overall_detail = $adiposity_axis['detail'];
    }
    // Prioritas 6: Gizi Lebih (BB/TB atau IMT/U +2 s/d +3 SD)
    elseif (($z_bbtb !== null && $z_bbtb > 2.0) || ($z_imtu !== null && $z_imtu > 2.0)) {
        $overall_kategori = 'Gizi Lebih';
        $overall_source = 'Adiposity (BB/TB atau IMT/U)';
        $overall_detail = $adiposity_axis['detail'];
    }
    // Prioritas 7: Beresiko Gizi Lebih (BB/TB atau IMT/U +1 s/d +2 SD)
    elseif (($z_bbtb !== null && $z_bbtb > 1.0) || ($z_imtu !== null && $z_imtu > 1.0)) {
        $overall_kategori = 'Beresiko Gizi Lebih';
        $overall_source = 'Adiposity (BB/TB atau IMT/U)';
        $overall_detail = $adiposity_axis['detail'];
    }
    
    // Return hasil lengkap
    return [
        'overall_8' => [
            'kategori' => $overall_kategori,
            'source' => $overall_source,
            'detail' => $overall_detail
        ],
        'zscore' => [
            'tbu' => round($z_tbu, 2),
            'bbu' => round($z_bbu, 2),
            'bbtb' => round($z_bbtb, 2),
            'imtu' => round($z_imtu, 2),
            'lku' => $z_lku !== null ? round($z_lku, 2) : null
        ],
        'axis' => [
            'tbu' => $tbu_axis,
            'bbu' => $bbu_axis,
            'adiposity' => $adiposity_axis,
            'lku' => $lku_axis
        ],
        'measurements' => [
            'bb_kg' => $bb,
            'tb_cm' => $tb,
            'lk_cm' => $lk,
            'imt' => round($imt, 2),
            'cara_ukur' => $cara_ukur
        ],
        'metadata' => [
            'usia_hari' => $usia_hari,
            'usia_bulan' => round($usia_bulan, 1),
            'jenis_kelamin' => $jenis_kelamin
        ]
    ];
}
