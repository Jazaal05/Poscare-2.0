<?php
/**
 * ============================================================================
 * FUNGSI WRAPPER: Hitung Semua Indikator Sekaligus dengan WHO LMS
 * ============================================================================
 * File ini berisi fungsi wrapper untuk menghitung semua indikator gizi
 * menggunakan WHO LMS Method (Box-Cox transformation)
 */

require_once __DIR__ . '/who_lms_data.php';
require_once __DIR__ . '/fungsi_gizi.php';
require_once __DIR__ . '/klasifikasi_gizi_who_kemenkes.php';
require_once __DIR__ . '/data_who.php';

/**
 * Helper: Get color from class name
 */
function getColorFromClass($class) {
    $colors = [
        'status-danger' => '#EF4444',
        'status-warning' => '#F59E0B',
        'status-success' => '#10B981',
        'status-info' => '#3B82F6',
        'status-normal' => '#10B981'
    ];
    return $colors[$class] ?? '#6B7280';
}

/**
 * Helper: Get category code from status label
 */
function getKategoriFromStatus($status) {
    $map = [
        'Gizi Buruk' => 'sangat_kurang',
        'Gizi Kurang' => 'kurang',
        'Gizi Baik' => 'normal',
        'Beresiko Gizi Lebih' => 'lebih',
        'Gizi Lebih' => 'lebih',
        'Obesitas' => 'sangat_lebih',
        'Sangat Pendek' => 'sangat_kurang',
        'Pendek' => 'kurang',
        'Normal' => 'normal'
    ];
    return $map[$status] ?? 'normal';
}

/**
 * Hitung Semua Indikator Sekaligus dengan WHO LMS
 * 
 * @param int $umur_bulan Umur dalam bulan (0-60)
 * @param float $berat_kg Berat badan dalam kg
 * @param float $tinggi_cm Tinggi badan dalam cm
 * @param string $jenis_kelamin 'L' atau 'P'
 * @param string $cara_ukur 'berbaring' atau 'berdiri'
 * @return array Hasil lengkap dengan klasifikasi, Z-score, dan data chart
 */
function hitungSemuaIndikator($umur_bulan, $berat_kg, $tinggi_cm, $jenis_kelamin, $cara_ukur = 'berdiri') {
    // Normalisasi jenis kelamin
    $sex = strtoupper($jenis_kelamin);
    
    // Validasi input
    if ($umur_bulan < 0 || $umur_bulan > 60) {
        return ['success' => false, 'error' => 'Umur harus 0-60 bulan'];
    }
    if ($berat_kg <= 0 || $tinggi_cm <= 0) {
        return ['success' => false, 'error' => 'Berat dan tinggi harus > 0'];
    }
    if (!in_array($sex, ['L', 'P'])) {
        return ['success' => false, 'error' => 'Jenis kelamin harus L atau P'];
    }
    
    try {
        // Hitung Z-score dengan WHO LMS Method
        $z_bbu = hitungZScoreBBU_LMS($umur_bulan, $berat_kg, $sex);
        $z_tbu = hitungZScoreTBU_LMS($umur_bulan, $tinggi_cm, $sex, $cara_ukur);
        $z_bbtb = hitungZScoreBBTB_LMS($umur_bulan, $berat_kg, $tinggi_cm, $sex, $cara_ukur);
        
        // Klasifikasi menggunakan WHO-Kemenkes 2025 (6-3-6 categories)
        $klasifikasi_bbu = klasifikasiStatusGizi($z_bbu, 'bbu');
        $klasifikasi_tbu = klasifikasiStatusGizi($z_tbu, 'tbu');
        $klasifikasi_bbtb = klasifikasiStatusGizi($z_bbtb, 'bbtb');
        
        // Siapkan data chart (gunakan data WHO standar)
        global $WHO_BB_U_LAKI, $WHO_BB_U_PEREMPUAN, $WHO_TB_U_LAKI, $WHO_TB_U_PEREMPUAN;
        global $WHO_BB_TB_LAKI, $WHO_BB_TB_PEREMPUAN;
        
        $data_bb_u = ($sex == 'L') ? $WHO_BB_U_LAKI : $WHO_BB_U_PEREMPUAN;
        $data_tb_u = ($sex == 'L') ? $WHO_TB_U_LAKI : $WHO_TB_U_PEREMPUAN;
        $data_bb_tb = ($sex == 'L') ? $WHO_BB_TB_LAKI : $WHO_BB_TB_PEREMPUAN;
        
        $chart_bbu = prepareChartData($data_bb_u, [['x' => $umur_bulan, 'y' => $berat_kg]], 'bb', $sex);
        $chart_tbu = prepareChartData($data_tb_u, [['x' => $umur_bulan, 'y' => $tinggi_cm]], 'tb', $sex);
        $chart_bbtb = prepareChartDataBBTB($data_bb_tb, [['x' => $tinggi_cm, 'y' => $berat_kg]]);
        
        // Interpretasi gabungan
        $interpretasi = generateInterpretasi($klasifikasi_bbu, $klasifikasi_tbu, $klasifikasi_bbtb);
        
        return [
            'success' => true,
            'bb_u' => [
                'z_score' => number_format($z_bbu, 2),
                'label' => $klasifikasi_bbu['status'], //  Fixed: use 'status' not 'label'
                'class' => $klasifikasi_bbu['class'],
                'color' => getColorFromClass($klasifikasi_bbu['class']),
                'deskripsi' => $klasifikasi_bbu['keterangan'],
                'kategori' => getKategoriFromStatus($klasifikasi_bbu['status']),
                'chart_data' => $chart_bbu
            ],
            'tb_u' => [
                'z_score' => number_format($z_tbu, 2),
                'label' => $klasifikasi_tbu['status'],
                'class' => $klasifikasi_tbu['class'],
                'color' => getColorFromClass($klasifikasi_tbu['class']),
                'deskripsi' => $klasifikasi_tbu['keterangan'],
                'kategori' => getKategoriFromStatus($klasifikasi_tbu['status']),
                'chart_data' => $chart_tbu
            ],
            'bb_tb' => [
                'z_score' => number_format($z_bbtb, 2),
                'label' => $klasifikasi_bbtb['status'],
                'class' => $klasifikasi_bbtb['class'],
                'color' => getColorFromClass($klasifikasi_bbtb['class']),
                'deskripsi' => $klasifikasi_bbtb['keterangan'],
                'kategori' => getKategoriFromStatus($klasifikasi_bbtb['status']),
                'chart_data' => $chart_bbtb
            ],
            'interpretasi' => $interpretasi
        ];
        
    } catch (Exception $e) {
        error_log("Error in hitungSemuaIndikator: " . $e->getMessage());
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

/**
 * Generate interpretasi gabungan dari 3 indikator
 */
function generateInterpretasi($bbu, $tbu, $bbtb) {
    $status_bbu = $bbu['kategori'];
    $status_tbu = $tbu['kategori'];
    $status_bbtb = $bbtb['kategori'];
    
    // BB/TB adalah indikator utama (primary indicator)
    if ($status_bbtb === 'sangat_kurang') {
        return 'Anak mengalami gizi buruk akut (severely wasted). Diperlukan penanganan medis segera dan pemberian makanan tambahan. Konsultasi dengan dokter atau ahli gizi.';
    } elseif ($status_bbtb === 'kurang') {
        return 'Anak mengalami gizi kurang akut (wasted). Tingkatkan asupan kalori dan protein. Monitor pertumbuhan secara berkala dan konsultasi dengan tenaga kesehatan.';
    } elseif ($status_bbtb === 'normal') {
        // Cek stunting
        if ($status_tbu === 'sangat_kurang' || $status_tbu === 'kurang') {
            return 'Status gizi anak BAIK berdasarkan proporsi berat terhadap tinggi (BB/TB normal). Namun anak mengalami stunting (pendek). Ini menunjukkan kekurangan gizi kronis di masa lalu. Tingkatkan asupan nutrisi berkualitas dan pantau pertumbuhan secara rutin.';
        } else {
            return 'Status gizi anak BAIK. Pertumbuhan anak sesuai dengan standar WHO. Pertahankan pola makan gizi seimbang, aktivitas fisik teratur, dan lakukan pemantauan pertumbuhan berkala.';
        }
    } elseif ($status_bbtb === 'lebih') {
        return 'Anak berisiko gizi lebih. Perhatikan pola makan, kurangi makanan tinggi gula dan lemak jenuh, perbanyak sayur dan buah, serta tingkatkan aktivitas fisik.';
    } elseif ($status_bbtb === 'sangat_lebih') {
        return 'Anak mengalami obesitas. Berisiko tinggi mengalami masalah kesehatan seperti diabetes dan penyakit jantung. Konsultasi dengan ahli gizi untuk program penurunan berat badan yang aman dan sesuai usia anak.';
    }
    
    return 'Terus pantau pertumbuhan anak secara berkala dan konsultasi dengan tenaga kesehatan untuk evaluasi lebih lanjut.';
}
