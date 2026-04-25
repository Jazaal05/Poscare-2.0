<?php
/**
 * ============================================================================
 * FUNGSI PERHITUNGAN STATUS GIZI ANAK
 * ============================================================================
 * 
 * File ini berisi fungsi untuk menghitung status gizi anak menggunakan
 * standar WHO Child Growth Standards 2006.
 * 
 * METODE PERHITUNGAN:
 * 1. Linear interpolation untuk nilai X (umur/tinggi) yang tidak tepat di tabel
 * 2. Perhitungan z-score berdasarkan posisi nilai aktual terhadap median dan SD
 * 3. Validasi input untuk memastikan data yang dihitung berada dalam rentang wajar
 * 
 * FUNGSI UTAMA:
 * - hitungZScore() : Menghitung z-score dengan interpolasi linear
 * - getKategoriGizi() : Mengkonversi z-score ke kategori gizi
 * - validateInput() : Validasi input data anak
 * 
 * FUNGSI PENDUKUNG:
 * - getStatusLabel() : Label singkat status gizi
 * - getKeterangan() : Penjelasan detail dan rekomendasi
 * - getStatusClass() : CSS class untuk styling
 * - prepareChartData() : Persiapan data untuk grafik pertumbuhan
 * - formatAngka() : Format angka dengan separator ribuan
 * 
 * Last Updated: 2025-01-15
 * ============================================================================
 */

require_once 'data_who.php';

/**
 * ============================================================================
 * PERHITUNGAN Z-SCORE SESUAI STANDAR WHO-KEMENKES 2025
 * ============================================================================
 * 
 * Menghitung Z-score menggunakan interpolasi linear berdasarkan tabel WHO 2006
 * dengan klasifikasi status gizi sesuai Permenkes RI
 * 
 * Z-score = (Nilai Aktual - Median) / SD
 * 
 * @param float $x_value Nilai X (umur bulan untuk BB/U & TB/U, tinggi cm untuk BB/TB)
 * @param float $y_value Nilai Y (berat kg atau tinggi cm)
 * @param array $data_standar Array standar WHO [x, -3SD, -2SD, -1SD, Median, +1SD, +2SD, +3SD]
 * @return float Z-score presisi
 */
function hitungZScore($x_value, $y_value, $data_standar) {
    // 1. Cari dua titik data yang mengapit nilai X
    $x0 = null;
    $x1 = null;
    $row0 = null;
    $row1 = null;
    
    foreach ($data_standar as $row) {
        if ($row[0] <= $x_value) {
            $x0 = $row[0];
            $row0 = $row;
        }
        if ($row[0] >= $x_value && $x1 === null) {
            $x1 = $row[0];
            $row1 = $row;
            break;
        }
    }
    
    // Jika nilai X tepat ada di tabel (tidak perlu interpolasi)
    if ($x0 === $x_value) {
        $sd_values = array_slice($row0, 1); // [-3SD, -2SD, -1SD, Median, +1SD, +2SD, +3SD]
    }
    // Jika nilai X di luar range tabel
    elseif ($x0 === null || $x1 === null) {
        // Gunakan nilai terdekat tanpa extrapolasi
        $nearest = $x0 === null ? $row1 : $row0;
        $sd_values = array_slice($nearest, 1);
    }
    // Interpolasi linear antara dua titik
    else {
        $frac = ($x_value - $x0) / ($x1 - $x0); // Rasio posisi antara x0 dan x1
        $sd_values = [];
        
        // Interpolasi untuk setiap kolom SD
        for ($i = 1; $i <= 7; $i++) {
            $v0 = $row0[$i];
            $v1 = $row1[$i];
            $sd_values[] = $v0 + $frac * ($v1 - $v0);
        }
    }
    
    // 2. Hitung z-score berdasarkan posisi y_value terhadap median dan SD
    $median = $sd_values[3]; // Index 3 = Median (0-indexed dari slice)
    
    // Jika nilai aktual sama dengan median
    if ($y_value == $median) {
        return 0.0;
    }
    
    // Jika nilai aktual di atas median
    if ($y_value > $median) {
        $sd1_plus = $sd_values[4]; // +1SD
        $sd2_plus = $sd_values[5]; // +2SD
        $sd3_plus = $sd_values[6]; // +3SD
        
        if ($y_value <= $sd1_plus) {
            // Antara median dan +1SD
            return ($y_value - $median) / ($sd1_plus - $median);
        } elseif ($y_value <= $sd2_plus) {
            // Antara +1SD dan +2SD
            return 1.0 + ($y_value - $sd1_plus) / ($sd2_plus - $sd1_plus);
        } elseif ($y_value <= $sd3_plus) {
            // Antara +2SD dan +3SD
            return 2.0 + ($y_value - $sd2_plus) / ($sd3_plus - $sd2_plus);
        } else {
            // Di atas +3SD
            return 3.0 + ($y_value - $sd3_plus) / ($sd3_plus - $sd2_plus);
        }
    }
    // Jika nilai aktual di bawah median
    else {
        $sd1_minus = $sd_values[2]; // -1SD
        $sd2_minus = $sd_values[1]; // -2SD
        $sd3_minus = $sd_values[0]; // -3SD
        
        if ($y_value >= $sd1_minus) {
            // Antara median dan -1SD
            return ($y_value - $median) / ($median - $sd1_minus);
        } elseif ($y_value >= $sd2_minus) {
            // Antara -1SD dan -2SD
            return -1.0 + ($y_value - $sd1_minus) / ($sd1_minus - $sd2_minus);
        } elseif ($y_value >= $sd3_minus) {
            // Antara -2SD dan -3SD
            return -2.0 + ($y_value - $sd2_minus) / ($sd2_minus - $sd3_minus);
        } else {
            // Di bawah -3SD
            return -3.0 + ($y_value - $sd3_minus) / ($sd2_minus - $sd3_minus);
        }
    }
}

/**
 * Konversi Z-Score ke Kategori Gizi
 * 
 * @param float $z_score Nilai z-score hasil perhitungan
 * @param string $indikator Tipe indikator ('bbu', 'tbu', 'bbtb')
 * @return array ['kategori' => string, 'kode' => string, 'deskripsi' => string]
 */
function getKategoriGizi($z_score, $indikator = 'bbu') {
    // Standar WHO untuk klasifikasi status gizi
    if ($z_score < -3) {
        $kategori = 'Sangat Kurang';
        $kode = 'sangat_kurang';
        
        if ($indikator == 'bbu') {
            $deskripsi = 'Gizi buruk (severely underweight). Anak memerlukan intervensi gizi segera.';
        } elseif ($indikator == 'tbu') {
            $deskripsi = 'Sangat pendek (severely stunted). Terjadi gangguan pertumbuhan kronis.';
        } else {
            $deskripsi = 'Sangat kurus (severely wasted). Kekurangan gizi akut yang memerlukan penanganan segera.';
        }
    } 
    elseif ($z_score >= -3 && $z_score < -2) {
        $kategori = 'Kurang';
        $kode = 'kurang';
        
        if ($indikator == 'bbu') {
            $deskripsi = 'Berat badan kurang (underweight). Perlu perbaikan pola makan dan pemantauan.';
        } elseif ($indikator == 'tbu') {
            $deskripsi = 'Pendek (stunted). Indikasi gangguan pertumbuhan yang perlu diperhatikan.';
        } else {
            $deskripsi = 'Kurus (wasted). Kekurangan gizi akut, perlu peningkatan asupan nutrisi.';
        }
    } 
    elseif ($z_score >= -2 && $z_score <= 2) {
        $kategori = 'Normal';
        $kode = 'normal';
        
        if ($indikator == 'bbu') {
            $deskripsi = 'Berat badan normal sesuai umur. Status gizi baik.';
        } elseif ($indikator == 'tbu') {
            $deskripsi = 'Tinggi badan normal sesuai umur. Pertumbuhan linear baik.';
        } else {
            $deskripsi = 'Proporsi berat dan tinggi badan normal. Status gizi baik.';
        }
    } 
    elseif ($z_score > 2 && $z_score <= 3) {
        $kategori = 'Lebih';
        $kode = 'lebih';
        
        if ($indikator == 'bbu') {
            $deskripsi = 'Berat badan lebih (possible risk of overweight). Perlu pengaturan pola makan.';
        } elseif ($indikator == 'tbu') {
            $deskripsi = 'Tinggi badan di atas normal. Umumnya tidak bermasalah.';
        } else {
            $deskripsi = 'Berat badan berlebih (possible risk of overweight). Berisiko obesitas.';
        }
    } 
    else { // z_score > 3
        $kategori = 'Sangat Lebih';
        $kode = 'sangat_lebih';
        
        if ($indikator == 'bbu') {
            $deskripsi = 'Obesitas (obese). Memerlukan pengaturan diet dan aktivitas fisik.';
        } elseif ($indikator == 'tbu') {
            $deskripsi = 'Sangat tinggi. Umumnya tidak bermasalah, bisa jadi faktor genetik.';
        } else {
            $deskripsi = 'Obesitas (obese). Berisiko tinggi masalah kesehatan. Perlu intervensi diet dan gaya hidup.';
        }
    }
    
    return [
        'kategori' => $kategori,
        'kode' => $kode,
        'deskripsi' => $deskripsi,
        'z_score' => round($z_score, 2)
    ];
}

/**
 * Validasi Input Data Anak
 * 
 * @param float $umur_bulan Umur dalam bulan (0-60)
 * @param float $berat_kg Berat badan dalam kg
 * @param float $tinggi_cm Tinggi/panjang badan dalam cm
 * @param string $jenis_kelamin Jenis kelamin ('L' atau 'P')
 * @return array ['valid' => bool, 'errors' => array]
 */
function validateInput($umur_bulan, $berat_kg, $tinggi_cm, $jenis_kelamin) {
    $errors = [];
    
    // Pastikan nilai numerik sebelum validasi
    if (!is_numeric($umur_bulan) || $umur_bulan === null) {
        $errors[] = 'Umur tidak valid';
        return ['valid' => false, 'errors' => $errors];
    }
    if (!is_numeric($berat_kg) || $berat_kg === null) {
        $errors[] = 'Berat badan tidak valid';
        return ['valid' => false, 'errors' => $errors];
    }
    if (!is_numeric($tinggi_cm) || $tinggi_cm === null) {
        $errors[] = 'Tinggi badan tidak valid';
        return ['valid' => false, 'errors' => $errors];
    }
    
    // Validasi umur (0-60 bulan)
    if ($umur_bulan < 0 || $umur_bulan > 60) {
        $errors[] = 'Umur harus antara 0-60 bulan (0-5 tahun)';
    }
    
    // Validasi berat badan (1-30 kg adalah rentang realistis untuk anak 0-5 tahun)
    if ($berat_kg < 1 || $berat_kg > 30) {
        $errors[] = 'Berat badan harus antara 1-30 kg';
    }
    
    // Validasi tinggi badan (40-120 cm adalah rentang realistis untuk anak 0-5 tahun)
    if ($tinggi_cm < 40 || $tinggi_cm > 120) {
        $errors[] = 'Tinggi badan harus antara 40-120 cm';
    }
    
    // Validasi jenis kelamin
    if (!in_array($jenis_kelamin, ['L', 'P'])) {
        $errors[] = 'Jenis kelamin harus L (Laki-laki) atau P (Perempuan)';
    }
    
    // Validasi konsistensi data (berat terlalu tinggi/rendah untuk tinggi tertentu)
    if ($tinggi_cm > 0 && $berat_kg > 0) {
        $bmi = $berat_kg / (($tinggi_cm / 100) ** 2);
        if ($bmi < 10 || $bmi > 35) {
            $errors[] = 'Kombinasi berat dan tinggi tidak realistis (BMI diluar rentang normal)';
        }
    }
    
    return [
        'valid' => empty($errors),
        'errors' => $errors
    ];
}

/**
 * FUNGSI WRAPPER: Hitung Status Gizi Lengkap
 * 
 * Fungsi ini adalah wrapper yang menggabungkan perhitungan z-score dan kategori gizi.
 * Dipertahankan untuk backward compatibility dengan kode lama.
 * 
 * @param float $x_value Nilai X (umur dalam bulan atau tinggi dalam cm)
 * @param float $y_value Nilai Y (berat dalam kg atau tinggi dalam cm)
 * @param array $data_standar Array data standar WHO
 * @param string $tipe Tipe indikator ('BB/U', 'TB/U', 'BB/TB')
 * @return array Data status gizi lengkap
 */
function hitungStatusGizi($x_value, $y_value, $data_standar, $tipe = 'BB/U') {
    // Hitung z-score dengan interpolasi linear
    $z_score = hitungZScore($x_value, $y_value, $data_standar);
    
    // Tentukan kode indikator untuk getKategoriGizi
    $indikator_map = [
        'BB/U' => 'bbu',
        'TB/U' => 'tbu',
        'BB/TB' => 'bbtb'
    ];
    $indikator = $indikator_map[$tipe] ?? 'bbu';
    
    // Dapatkan kategori gizi
    $kategori_data = getKategoriGizi($z_score, $indikator);
    
    // Kembalikan hasil lengkap
    return [
        'z_score' => $z_score,
        'kategori' => $kategori_data['kategori'],
        'kode_kategori' => $kategori_data['kode'],
        'deskripsi' => $kategori_data['deskripsi'],
        'tipe_indikator' => $tipe,
        'nilai_x' => $x_value,
        'nilai_y' => $y_value
    ];
}

/**
 * Get Status Label (Label Singkat)
 */
function getStatusLabel($kategori) {
    $labels = [
        'sangat_kurang' => 'Sangat Kurang',
        'kurang' => 'Kurang',
        'normal' => 'Normal',
        'lebih' => 'Lebih',
        'sangat_lebih' => 'Sangat Lebih'
    ];
    return $labels[$kategori] ?? 'Tidak Diketahui';
}

/**
 * Get Keterangan Detail Status Gizi
 */
function getKeterangan($kategori, $indikator = 'bbu') {
    $keterangan = [
        'bbu' => [
            'sangat_kurang' => 'Anak mengalami gizi buruk (severely underweight). Segera konsultasi dengan tenaga kesehatan untuk intervensi gizi dan pemeriksaan kesehatan menyeluruh.',
            'kurang' => 'Berat badan anak berada di bawah standar. Tingkatkan asupan gizi seimbang dengan protein, karbohidrat, dan lemak yang cukup. Pantau pertumbuhan secara rutin.',
            'normal' => 'Berat badan anak sesuai dengan usianya. Pertahankan pola makan gizi seimbang dan aktivitas fisik yang teratur.',
            'lebih' => 'Berat badan anak cenderung berlebih. Perhatikan pola makan, kurangi makanan tinggi gula dan lemak jenuh, tingkatkan aktivitas fisik.',
            'sangat_lebih' => 'Anak mengalami obesitas. Konsultasi dengan ahli gizi untuk program diet dan perubahan gaya hidup yang tepat.'
        ],
        'tbu' => [
            'sangat_kurang' => 'Anak mengalami stunting (sangat pendek). Ini menunjukkan kekurangan gizi kronis. Konsultasi segera dengan tenaga kesehatan.',
            'kurang' => 'Tinggi badan anak di bawah standar. Dapat disebabkan oleh kekurangan gizi kronis. Tingkatkan asupan nutrisi dan pantau pertumbuhan.',
            'normal' => 'Tinggi badan anak sesuai dengan usianya. Pertahankan asupan gizi yang baik untuk mendukung pertumbuhan optimal.',
            'lebih' => 'Tinggi badan anak di atas rata-rata. Ini umumnya tidak bermasalah dan dapat dipengaruhi oleh faktor genetik.',
            'sangat_lebih' => 'Tinggi badan anak sangat tinggi. Umumnya normal dan dapat dipengaruhi oleh faktor keturunan.'
        ],
        'bbtb' => [
            'sangat_kurang' => 'Anak sangat kurus (severely wasted). Mengalami kekurangan gizi akut yang memerlukan penanganan medis segera.',
            'kurang' => 'Anak kurus (wasted). Kekurangan gizi akut. Tingkatkan asupan kalori dan protein. Konsultasi dengan tenaga kesehatan.',
            'normal' => 'Proporsi berat terhadap tinggi badan anak normal. Status gizi baik. Pertahankan pola makan sehat.',
            'lebih' => 'Anak cenderung gemuk. Risiko kelebihan berat badan. Atur pola makan dan tingkatkan aktivitas fisik.',
            'sangat_lebih' => 'Anak obesitas. Berisiko tinggi masalah kesehatan. Konsultasi ahli gizi untuk program penurunan berat badan yang aman.'
        ]
    ];
    
    return $keterangan[$indikator][$kategori] ?? 'Keterangan tidak tersedia.';
}

/**
 * Get CSS Class untuk Status Gizi
 */
function getStatusClass($kategori) {
    $classes = [
        'sangat_kurang' => 'status-danger',
        'kurang' => 'status-warning',
        'normal' => 'status-success',
        'lebih' => 'status-warning',
        'sangat_lebih' => 'status-danger'
    ];
    return $classes[$kategori] ?? 'status-default';
}

/**
 * Prepare Chart Data untuk Grafik Pertumbuhan (BB/U dan TB/U)
 * 
 * @param array $data_standar Array data standar WHO
 * @param array $data_historis Array data penimbangan historis anak
 * @param string $tipe Tipe data ('bb' untuk berat badan, 'tb' untuk tinggi badan)
 * @param string $jenis_kelamin Jenis kelamin anak ('L' atau 'P')
 * @return array Data untuk chart.js
 */
function prepareChartData($data_standar, $data_historis = [], $tipe = 'bb', $jenis_kelamin = 'L') {
    $labels = [];
    $median_data = [];
    $sd_minus_1 = [];
    $sd_minus_2 = [];
    $sd_minus_3 = [];
    $sd_plus_1 = [];
    $sd_plus_2 = [];
    $sd_plus_3 = [];
    
    // Ekstrak data standar WHO dengan format Chart.js {x, y}
    foreach ($data_standar as $row) {
        $umur = $row[0]; // X (umur dalam bulan)
        $labels[] = $umur;
        
        // Format Chart.js: {x: umur, y: nilai} - 7 kurva SD lengkap
        $sd_minus_3[] = ['x' => $umur, 'y' => $row[1]]; // -3SD
        $sd_minus_2[] = ['x' => $umur, 'y' => $row[2]]; // -2SD
        $sd_minus_1[] = ['x' => $umur, 'y' => $row[3]]; // -1SD
        $median_data[] = ['x' => $umur, 'y' => $row[4]]; // Median
        $sd_plus_1[] = ['x' => $umur, 'y' => $row[5]];  // +1SD
        $sd_plus_2[] = ['x' => $umur, 'y' => $row[6]];  // +2SD
        $sd_plus_3[] = ['x' => $umur, 'y' => $row[7]];  // +3SD
    }
    
    // Siapkan data anak (plot poin data penimbangan)
    $anak_data = [];
    $anak_labels = [];
    
    if (!empty($data_historis) && is_array($data_historis)) {
        foreach ($data_historis as $record) {
            // Skip jika data tidak lengkap
            if (!isset($record['tanggal_lahir']) || !isset($record['tanggal'])) {
                continue;
            }
            
            // Skip jika tanggal kosong
            if (empty($record['tanggal_lahir']) || empty($record['tanggal'])) {
                continue;
            }
            
            try {
                // Hitung umur dalam bulan saat penimbangan
                $tanggal_lahir = new DateTime($record['tanggal_lahir']);
                $tanggal_penimbangan = new DateTime($record['tanggal']);
                $umur_bulan = $tanggal_lahir->diff($tanggal_penimbangan)->m + 
                             ($tanggal_lahir->diff($tanggal_penimbangan)->y * 12);
                
                $anak_labels[] = $umur_bulan;
                
                // Ambil nilai sesuai tipe (bb atau tb)
                if ($tipe === 'bb') {
                    // Validasi berat_badan ada dan > 0
                    if (isset($record['berat_badan']) && $record['berat_badan'] > 0) {
                        $anak_data[] = ['x' => $umur_bulan, 'y' => floatval($record['berat_badan'])];
                    }
                } else {
                    // Validasi tinggi_badan ada dan > 0
                    if (isset($record['tinggi_badan']) && $record['tinggi_badan'] > 0) {
                        $anak_data[] = ['x' => $umur_bulan, 'y' => floatval($record['tinggi_badan'])];
                    }
                }
            } catch (Exception $e) {
                // Log error tapi tetap lanjut untuk record lainnya
                error_log("prepareChartData: Invalid date in record - " . $e->getMessage());
                continue;
            }
        }
    }
    
    // Tentukan label sumbu Y
    $label_y = ($tipe === 'bb') ? 'Berat Badan (kg)' : 'Tinggi Badan (cm)';
    
    $datasets = [
        [
            'label' => '+3 SD',
            'data' => $sd_plus_3,
            'borderColor' => 'rgba(255, 99, 132, 1)',
            'backgroundColor' => 'rgba(255, 99, 132, 0.1)',
            'fill' => false,
            'borderWidth' => 1,
            'pointRadius' => 0,
            'tension' => 0.4
        ],
        [
            'label' => '+2 SD',
            'data' => $sd_plus_2,
            'borderColor' => 'rgba(255, 206, 86, 1)',
            'backgroundColor' => 'rgba(255, 206, 86, 0.1)',
            'fill' => false,
            'borderWidth' => 1,
            'pointRadius' => 0,
            'tension' => 0.4
        ],
        [
            'label' => '+1 SD',
            'data' => $sd_plus_1,
            'borderColor' => 'rgba(250, 204, 21, 1)',
            'backgroundColor' => 'rgba(250, 204, 21, 0.1)',
            'fill' => false,
            'borderWidth' => 1,
            'pointRadius' => 0,
            'tension' => 0.4
        ],
        [
            'label' => 'Median',
            'data' => $median_data,
            'borderColor' => 'rgba(75, 192, 192, 1)',
            'backgroundColor' => 'rgba(75, 192, 192, 0.1)',
            'fill' => false,
            'borderWidth' => 2,
            'pointRadius' => 0,
            'tension' => 0.4
        ],
        [
            'label' => '-1 SD',
            'data' => $sd_minus_1,
            'borderColor' => 'rgba(250, 204, 21, 1)',
            'backgroundColor' => 'rgba(250, 204, 21, 0.1)',
            'fill' => false,
            'borderWidth' => 1,
            'pointRadius' => 0,
            'tension' => 0.4
        ],
        [
            'label' => '-2 SD',
            'data' => $sd_minus_2,
            'borderColor' => 'rgba(255, 206, 86, 1)',
            'backgroundColor' => 'rgba(255, 206, 86, 0.1)',
            'fill' => false,
            'borderWidth' => 1,
            'pointRadius' => 0,
            'tension' => 0.4
        ],
        [
            'label' => '-3 SD',
            'data' => $sd_minus_3,
            'borderColor' => 'rgba(255, 99, 132, 1)',
            'backgroundColor' => 'rgba(255, 99, 132, 0.1)',
            'fill' => false,
            'borderWidth' => 1,
            'pointRadius' => 0,
            'tension' => 0.4
        ]
    ];
    
    // Tambahkan data anak jika ada
    if (!empty($anak_data)) {
        $datasets[] = [
            'label' => 'Data Anak',
            'data' => $anak_data,
            'borderColor' => 'rgba(59, 130, 246, 1)',
            'backgroundColor' => 'rgba(59, 130, 246, 1)',
            'fill' => false,
            'borderWidth' => 3,
            'pointRadius' => 6,
            'pointHoverRadius' => 8,
            'showLine' => true,
            'tension' => 0.2
        ];
    }
    
    // Return format untuk JavaScript: flat structure di root
    return [
        'sd_minus_3' => $sd_minus_3,
        'sd_minus_2' => $sd_minus_2,
        'sd_minus_1' => $sd_minus_1,
        'median' => $median_data,
        'sd_plus_1' => $sd_plus_1,
        'sd_plus_2' => $sd_plus_2,
        'sd_plus_3' => $sd_plus_3,
        'anak_data' => $anak_data
    ];
}

/**
 * Prepare Chart Data untuk BB/TB (Berat menurut Tinggi Badan)
 * 
 * @param array $data_standar Array data standar WHO BB/TB
 * @param array $data_historis Array data penimbangan historis anak
 * @return array Data untuk chart.js
 */
function prepareChartDataBBTB($data_standar, $data_historis = []) {
    $labels = [];
    $median_data = [];
    $sd_minus_1 = [];
    $sd_minus_2 = [];
    $sd_minus_3 = [];
    $sd_plus_1 = [];
    $sd_plus_2 = [];
    $sd_plus_3 = [];
    
    // Ekstrak data standar WHO dengan format Chart.js {x, y}
    foreach ($data_standar as $row) {
        $tinggi = $row[0]; // X (tinggi dalam cm)
        $labels[] = $tinggi;
        
        // Format Chart.js: {x: tinggi, y: berat} - 7 kurva SD lengkap
        $sd_minus_3[] = ['x' => $tinggi, 'y' => $row[1]]; // -3SD
        $sd_minus_2[] = ['x' => $tinggi, 'y' => $row[2]]; // -2SD
        $sd_minus_1[] = ['x' => $tinggi, 'y' => $row[3]]; // -1SD
        $median_data[] = ['x' => $tinggi, 'y' => $row[4]]; // Median
        $sd_plus_1[] = ['x' => $tinggi, 'y' => $row[5]];  // +1SD
        $sd_plus_2[] = ['x' => $tinggi, 'y' => $row[6]];  // +2SD
        $sd_plus_3[] = ['x' => $tinggi, 'y' => $row[7]];  // +3SD
    }
    
    // Siapkan data anak (plot poin data penimbangan)
    $anak_data = [];
    
    if (!empty($data_historis) && is_array($data_historis)) {
        foreach ($data_historis as $record) {
            // Validasi data lengkap dan nilai valid
            if (isset($record['tinggi_badan']) && isset($record['berat_badan']) &&
                $record['tinggi_badan'] > 0 && $record['berat_badan'] > 0) {
                $anak_data[] = [
                    'x' => floatval($record['tinggi_badan']),
                    'y' => floatval($record['berat_badan'])
                ];
            }
        }
    }
    
    $datasets = [
        [
            'label' => '+3 SD',
            'data' => $sd_plus_3,
            'borderColor' => 'rgba(255, 99, 132, 1)',
            'backgroundColor' => 'rgba(255, 99, 132, 0.1)',
            'fill' => false,
            'borderWidth' => 1,
            'pointRadius' => 0,
            'tension' => 0.4
        ],
        [
            'label' => '+2 SD',
            'data' => $sd_plus_2,
            'borderColor' => 'rgba(255, 206, 86, 1)',
            'backgroundColor' => 'rgba(255, 206, 86, 0.1)',
            'fill' => false,
            'borderWidth' => 1,
            'pointRadius' => 0,
            'tension' => 0.4
        ],
        [
            'label' => '+1 SD',
            'data' => $sd_plus_1,
            'borderColor' => 'rgba(250, 204, 21, 1)',
            'backgroundColor' => 'rgba(250, 204, 21, 0.1)',
            'fill' => false,
            'borderWidth' => 1,
            'pointRadius' => 0,
            'tension' => 0.4
        ],
        [
            'label' => 'Median',
            'data' => $median_data,
            'borderColor' => 'rgba(75, 192, 192, 1)',
            'backgroundColor' => 'rgba(75, 192, 192, 0.1)',
            'fill' => false,
            'borderWidth' => 2,
            'pointRadius' => 0,
            'tension' => 0.4
        ],
        [
            'label' => '-1 SD',
            'data' => $sd_minus_1,
            'borderColor' => 'rgba(250, 204, 21, 1)',
            'backgroundColor' => 'rgba(250, 204, 21, 0.1)',
            'fill' => false,
            'borderWidth' => 1,
            'pointRadius' => 0,
            'tension' => 0.4
        ],
        [
            'label' => '-2 SD',
            'data' => $sd_minus_2,
            'borderColor' => 'rgba(255, 206, 86, 1)',
            'backgroundColor' => 'rgba(255, 206, 86, 0.1)',
            'fill' => false,
            'borderWidth' => 1,
            'pointRadius' => 0,
            'tension' => 0.4
        ],
        [
            'label' => '-3 SD',
            'data' => $sd_minus_3,
            'borderColor' => 'rgba(255, 99, 132, 1)',
            'backgroundColor' => 'rgba(255, 99, 132, 0.1)',
            'fill' => false,
            'borderWidth' => 1,
            'pointRadius' => 0,
            'tension' => 0.4
        ]
    ];
    
    // Tambahkan data anak jika ada
    if (!empty($anak_data)) {
        $datasets[] = [
            'label' => 'Data Anak',
            'data' => $anak_data,
            'borderColor' => 'rgba(59, 130, 246, 1)',
            'backgroundColor' => 'rgba(59, 130, 246, 1)',
            'fill' => false,
            'borderWidth' => 3,
            'pointRadius' => 6,
            'pointHoverRadius' => 8,
            'showLine' => true,
            'tension' => 0.2
        ];
    }
    
    // Return format untuk JavaScript: flat structure di root
    return [
        'sd_minus_3' => $sd_minus_3,
        'sd_minus_2' => $sd_minus_2,
        'sd_minus_1' => $sd_minus_1,
        'median' => $median_data,
        'sd_plus_1' => $sd_plus_1,
        'sd_plus_2' => $sd_plus_2,
        'sd_plus_3' => $sd_plus_3,
        'anak_data' => $anak_data
    ];
}

/**
 * Format Angka dengan Separator Ribuan
 */
function formatAngka($angka, $desimal = 1) {
    return number_format($angka, $desimal, ',', '.');
}

/**
 * ============================================================================
 * FUNGSI PERHITUNGAN STATUS GIZI - WHO CHILD GROWTH STANDARDS 2006
 * ============================================================================
 * Menggunakan metode LMS (Box-Cox transformation)
 * Sesuai standar WHO yang berlaku hingga 2025+
 * ============================================================================
 */

require_once __DIR__ . '/who_lms_data.php';

/**
 * INTERPOLASI LINEAR LMS
 * Untuk nilai umur/tinggi yang berada di antara dua titik tabel
 * 
 * @param float $x Nilai yang dicari (umur bulan atau tinggi cm)
 * @param array $table Tabel LMS
 * @return array ['L', 'M', 'S'] dengan fallback jika data tidak valid
 */
function interpLMS($x, $table) {
    // ✅ VALIDASI INPUT - Cegah error ksort dengan null/non-array
    if (!is_array($table) || empty($table)) {
        error_log("interpLMS ERROR: Table is not an array or is empty");
        return ['L' => 1, 'M' => 0, 'S' => 1]; // Fallback array instead of null
    }
    
    ksort($table);
    $keys = array_keys($table);

    // Jika di bawah nilai minimum
    if ($x <= $keys[0]) return $table[$keys[0]];
    
    // Jika di atas nilai maksimum
    if ($x >= end($keys)) return $table[end($keys)];

    // Interpolasi linear
    for ($i = 0; $i < count($keys) - 1; $i++) {
        $x0 = $keys[$i];
        $x1 = $keys[$i + 1];
        
        if ($x >= $x0 && $x <= $x1) {
            $t = ($x - $x0) / ($x1 - $x0);
            
            $L = $table[$x0]['L'] + $t * ($table[$x1]['L'] - $table[$x0]['L']);
            $M = $table[$x0]['M'] + $t * ($table[$x1]['M'] - $table[$x0]['M']);
            $S = $table[$x0]['S'] + $t * ($table[$x1]['S'] - $table[$x0]['S']);
            
            return ['L' => $L, 'M' => $M, 'S' => $S];
        }
    }

    return $table[end($keys)];
}


/**
 * HITUNG Z-SCORE METODE LMS (WHO)
 * Formula Box-Cox transformation
 * 
 * Z = [(Y/M)^L - 1] / (L × S)  jika L ≠ 0
 * Z = ln(Y/M) / S              jika L = 0
 * 
 * @param float $Y Nilai observasi (berat kg atau tinggi cm)
 * @param float $L Lambda (Box-Cox power)
 * @param float $M Mu (Median)
 * @param float $S Sigma (Coefficient of variation)
 * @return float Z-score
 */
function zscoreLMS($Y, $L, $M, $S) {
    // Validasi input
    if ($Y <= 0 || $M <= 0 || $S <= 0) {
        return NAN;
    }
    
    // Jika L mendekati 0, gunakan formula logaritma
    if (abs($L) < 0.01) {
        return log($Y / $M) / $S;
    }
    
    // Formula standard LMS
    return (pow($Y / $M, $L) - 1) / ($L * $S);
}

/**
 * HITUNG UMUR DALAM BULAN
 * Sesuai standar WHO: pembulatan ke bulan terdekat
 * 
 * @param string $tgl_lahir Format: YYYY-MM-DD
 * @param string $tgl_ukur Format: YYYY-MM-DD (default: hari ini)
 * @return int Umur dalam bulan
 */
function hitungUmurBulanWHO($tgl_lahir, $tgl_ukur = null) {
    if ($tgl_ukur === null) {
        $tgl_ukur = date('Y-m-d');
    }
    
    $lahir = new DateTime($tgl_lahir);
    $ukur = new DateTime($tgl_ukur);
    $interval = $lahir->diff($ukur);
    
    // Hitung umur dalam bulan
    $bulan = $interval->y * 12 + $interval->m;
    $hari = $interval->d;
    
    // Pembulatan: jika hari >= 15, bulatkan ke atas
    if ($hari >= 15) {
        $bulan += 1;
    }
    
    return $bulan;
}

/**
 * KOREKSI TINGGI BADAN
 * Konversi antara length (berbaring) dan height (berdiri)
 * 
 * WHO Rules:
 * - Umur < 24 bulan: gunakan length (berbaring)
 * - Umur ≥ 24 bulan: gunakan height (berdiri)
 * - Jika salah ukur: tambah/kurangi 0.7 cm
 * 
 * @param float $tinggi_cm Tinggi yang diukur
 * @param int $umur_bulan Umur anak
 * @param string $cara_ukur 'berbaring' atau 'berdiri'
 * @return float Tinggi yang sudah dikoreksi
 */
function koreksiTinggi_legacy($tinggi_cm, $umur_bulan, $cara_ukur = 'berdiri') {
    $cara_ukur = strtolower($cara_ukur);
    
    if ($umur_bulan < 24) {
        // Seharusnya berbaring (length)
        if ($cara_ukur === 'berdiri') {
            // Diukur berdiri, konversi ke berbaring: tambah 0.7 cm
            return $tinggi_cm + 0.7;
        }
    } else {
        // Seharusnya berdiri (height)
        if ($cara_ukur === 'berbaring') {
            // Diukur berbaring, konversi ke berdiri: kurangi 0.7 cm
            return $tinggi_cm - 0.7;
        }
    }
    
    return $tinggi_cm;
}

/**
 * PILIH TABEL BB/TB YANG TEPAT
 * 
 * @param int $umur_bulan
 * @param string $sex 'L' atau 'P'
 * @return array Tabel WFL atau WFH
 */
function pilihTabelBBTB($umur_bulan, $sex) {
    global $WFL_BOYS, $WFL_GIRLS, $WFH_BOYS, $WFH_GIRLS;
    
    if ($umur_bulan < 24) {
        // Gunakan Weight-for-Length (0-24 bulan)
        return ($sex === 'L') ? $WFL_BOYS : $WFL_GIRLS;
    } else {
        // Gunakan Weight-for-Height (24-60 bulan)
        return ($sex === 'L') ? $WFH_BOYS : $WFH_GIRLS;
    }
}

/**
 * ============================================================================
 * FUNGSI UTAMA: Hitung Z-Score dengan WHO LMS Method
 * ============================================================================
 */

/**
 * Hitung Z-Score BB/U (Berat Badan menurut Umur) dengan WHO LMS
 */
function hitungZScoreBBU_LMS($umur_bulan, $berat_kg, $sex) {
    global $WFA_BOYS, $WFA_GIRLS;
    $table = ($sex === 'L') ? $WFA_BOYS : $WFA_GIRLS;
    $lms = interpLMS($umur_bulan, $table);
    
    // ✅ Validasi hasil interpLMS
    if ($lms === null || !isset($lms['L'], $lms['M'], $lms['S'])) {
        error_log("hitungZScoreBBU_LMS ERROR: Invalid LMS data for age=$umur_bulan, sex=$sex");
        return null;
    }
    
    return zscoreLMS($berat_kg, $lms['L'], $lms['M'], $lms['S']);
}

/**
 * Hitung Z-Score TB/U (Tinggi Badan menurut Umur) dengan WHO LMS
 */
function hitungZScoreTBU_LMS($umur_bulan, $tinggi_cm, $sex, $cara_ukur = 'berdiri') {
    global $HFA_BOYS, $HFA_GIRLS;
    $tinggi_corrected = koreksiTinggi_legacy($tinggi_cm, $umur_bulan, $cara_ukur);
    $table = ($sex === 'L') ? $HFA_BOYS : $HFA_GIRLS;
    $lms = interpLMS($umur_bulan, $table);
    
    // ✅ Validasi hasil interpLMS
    if ($lms === null || !isset($lms['L'], $lms['M'], $lms['S'])) {
        error_log("hitungZScoreTBU_LMS ERROR: Invalid LMS data for age=$umur_bulan, sex=$sex");
        return null;
    }
    
    return zscoreLMS($tinggi_corrected, $lms['L'], $lms['M'], $lms['S']);
}

/**
 * Hitung Z-Score BB/TB (Berat Badan menurut Tinggi Badan) dengan WHO LMS
 */
function hitungZScoreBBTB_LMS($umur_bulan, $berat_kg, $tinggi_cm, $sex, $cara_ukur = 'berdiri') {
    $tinggi_corrected = koreksiTinggi_legacy($tinggi_cm, $umur_bulan, $cara_ukur);
    $table = pilihTabelBBTB($umur_bulan, $sex);
    $lms = interpLMS($tinggi_corrected, $table);
    
    // ✅ Validasi hasil interpLMS
    if ($lms === null || !isset($lms['L'], $lms['M'], $lms['S'])) {
        error_log("hitungZScoreBBTB_LMS ERROR: Invalid LMS data for height=$tinggi_corrected, sex=$sex");
        return null;
    }
    
    return zscoreLMS($berat_kg, $lms['L'], $lms['M'], $lms['S']);
}

/**
 * Hitung Z-Score IMT/U (Indeks Massa Tubuh menurut Umur) dengan WHO LMS
 */
function hitungZScoreIMTU_LMS($umur_bulan, $bmi, $sex) {
    global $BMIFA_BOYS, $BMIFA_GIRLS;
    $table = ($sex === 'L') ? $BMIFA_BOYS : $BMIFA_GIRLS;
    $lms = interpLMS($umur_bulan, $table);
    
    // ✅ Validasi hasil interpLMS
    if ($lms === null || !isset($lms['L'], $lms['M'], $lms['S'])) {
        error_log("hitungZScoreIMTU_LMS ERROR: Invalid LMS data for age=$umur_bulan, sex=$sex");
        return null;
    }
    
    return zscoreLMS($bmi, $lms['L'], $lms['M'], $lms['S']);
}

// ============================================================================
// FUNGSI WRAPPER UNTUK 8 KATEGORI STATUS GIZI WHO
// ============================================================================

/**
 * DEPRECATED: Fungsi ini sudah tidak digunakan lagi.
 * Gunakan hitungStatusGiziLengkap() dari fungsi_klasifikasi_who.php
 * 
 * @deprecated Gunakan fungsi dari fungsi_klasifikasi_who.php
 */
function hitungStatusGiziLengkap_OLD_DEPRECATED($umur_bulan, $berat_kg, $tinggi_cm, $sex, $cara_ukur = 'berdiri') {
    // ✅ VALIDASI INPUT WAJIB
    if (empty($umur_bulan) || empty($berat_kg) || empty($tinggi_cm) || empty($sex)) {
        error_log("hitungStatusGiziLengkap: Data tidak lengkap - umur=$umur_bulan, berat=$berat_kg, tinggi=$tinggi_cm, sex=$sex");
        return [
            'status_gizi' => 'Belum diukur',
            'zscore' => ['tbu' => null, 'bbu' => null, 'bbtb' => null, 'imtu' => null],
            'overall_8' => [
                'kategori' => 'Belum diukur',
                'axis_dominan' => '-',
                'source' => 'WHO 2006 Child Growth Standards',
                'detail' => 'Data tidak lengkap untuk perhitungan'
            ]
        ];
    }
    
    // ✅ VALIDASI NILAI POSITIF
    if ($berat_kg <= 0 || $tinggi_cm <= 0 || $umur_bulan < 0) {
        error_log("hitungStatusGiziLengkap: Nilai tidak valid - berat=$berat_kg, tinggi=$tinggi_cm, umur=$umur_bulan");
        return [
            'status_gizi' => 'Belum diukur',
            'zscore' => ['tbu' => null, 'bbu' => null, 'bbtb' => null, 'imtu' => null],
            'overall_8' => [
                'kategori' => 'Belum diukur',
                'axis_dominan' => '-',
                'source' => 'WHO 2006 Child Growth Standards',
                'detail' => 'Nilai antropometri tidak valid'
            ]
        ];
    }
    
    // ✅ VALIDASI JENIS KELAMIN
    if (!in_array($sex, ['L', 'P'])) {
        error_log("hitungStatusGiziLengkap: Jenis kelamin tidak valid - sex=$sex");
        return [
            'status_gizi' => 'Belum diukur',
            'zscore' => ['tbu' => null, 'bbu' => null, 'bbtb' => null, 'imtu' => null],
            'overall_8' => [
                'kategori' => 'Belum diukur',
                'axis_dominan' => '-',
                'source' => 'WHO 2006 Child Growth Standards',
                'detail' => 'Jenis kelamin tidak valid'
            ]
        ];
    }
    
    // ✅ VALIDASI RANGE UMUR WHO (0-60 bulan)
    if ($umur_bulan > 60) {
        error_log("hitungStatusGiziLengkap: Umur di luar range WHO - umur=$umur_bulan bulan");
        return [
            'status_gizi' => 'Belum diukur',
            'zscore' => ['tbu' => null, 'bbu' => null, 'bbtb' => null, 'imtu' => null],
            'overall_8' => [
                'kategori' => 'Belum diukur',
                'axis_dominan' => '-',
                'source' => 'WHO 2006 Child Growth Standards',
                'detail' => 'Umur di luar range standar WHO (0-60 bulan)'
            ]
        ];
    }
    
    // ✅ HITUNG SEMUA Z-SCORE DENGAN ERROR HANDLING
    try {
        $z_tbu  = hitungZScoreTBU_LMS($umur_bulan, $tinggi_cm, $sex, $cara_ukur);
        $z_bbu  = hitungZScoreBBU_LMS($umur_bulan, $berat_kg, $sex);
        $z_bbtb = hitungZScoreBBTB_LMS($umur_bulan, $berat_kg, $tinggi_cm, $sex, $cara_ukur);
        
        $bmi = $berat_kg / pow($tinggi_cm / 100, 2);
        $z_imtu = hitungZScoreIMTU_LMS($umur_bulan, $bmi, $sex);
        
        // ✅ CEK JIKA ADA Z-SCORE YANG NULL (error perhitungan)
        if ($z_tbu === null || $z_bbu === null || $z_bbtb === null || $z_imtu === null) {
            error_log("hitungStatusGiziLengkap: Perhitungan z-score menghasilkan null - tbu=$z_tbu, bbu=$z_bbu, bbtb=$z_bbtb, imtu=$z_imtu");
            return [
                'status_gizi' => 'Belum diukur',
                'zscore' => [
                    'tbu'  => $z_tbu !== null ? round($z_tbu, 2) : null,
                    'bbu'  => $z_bbu !== null ? round($z_bbu, 2) : null,
                    'bbtb' => $z_bbtb !== null ? round($z_bbtb, 2) : null,
                    'imtu' => $z_imtu !== null ? round($z_imtu, 2) : null
                ],
                'overall_8' => [
                    'kategori' => 'Belum diukur',
                    'axis_dominan' => '-',
                    'source' => 'WHO 2006 Child Growth Standards',
                    'detail' => 'Gagal menghitung z-score (data tidak lengkap atau di luar range)'
                ]
            ];
        }
        
        $zscore = [
            'tbu'  => round($z_tbu, 2),
            'bbu'  => round($z_bbu, 2),
            'bbtb' => round($z_bbtb, 2),
            'imtu' => round($z_imtu, 2)
        ];
        
        // ✅ TENTUKAN KATEGORI 8-LEVEL (gunakan fungsi modern dari fungsi_klasifikasi_who.php)
        // $kategori = tentukan_kategori_8level_OLD_DEPRECATED($zscore); // DEPRECATED - jangan gunakan
        
        // Gunakan klasifikasi sederhana untuk backward compatibility
        $kategori = [
            'kategori' => 'Belum diukur',
            'axis_dominan' => '-',
            'source' => 'WHO 2006',
            'detail' => 'Gunakan klasifikasiStatusGiziWHO() untuk hasil lengkap'
        ];
        
        return [
            'status_gizi' => $kategori['kategori'],
            'zscore' => $zscore,
            'overall_8' => $kategori
        ];
        
    } catch (Throwable $e) {
        error_log("hitungStatusGiziLengkap EXCEPTION: " . $e->getMessage());
        return [
            'status_gizi' => 'Belum diukur',
            'zscore' => ['tbu' => null, 'bbu' => null, 'bbtb' => null, 'imtu' => null],
            'overall_8' => [
                'kategori' => 'Belum diukur',
                'axis_dominan' => '-',
                'source' => 'WHO 2006 Child Growth Standards',
                'detail' => 'Error perhitungan: ' . $e->getMessage()
            ]
        ];
    }
}

/**
 * DEPRECATED: Fungsi ini sudah tidak digunakan lagi.
 * Gunakan tentukan_kategori_8level() dari fungsi_klasifikasi_who.php
 * 
 * @deprecated Gunakan fungsi dari fungsi_klasifikasi_who.php
 */
function tentukan_kategori_8level_OLD_DEPRECATED($zscore) {
    $tbu  = $zscore['tbu'];
    $bbu  = $zscore['bbu'];
    $bbtb = $zscore['bbtb'];
    $imtu = $zscore['imtu'];
    
    // ========== 1. CEK STUNTING (TB/U) - PRIORITAS TERTINGGI ==========
    if ($tbu !== null) {
        if ($tbu < -2) {
            return [
                'kategori' => 'Stunting',
                'axis_dominan' => 'TB/U',
                'source' => 'WHO 2006 Child Growth Standards',
                'detail' => "TB/U z-score: $tbu SD (< -2 SD = Stunting)"
            ];
        }
        if ($tbu >= -2 && $tbu < -1) {
            return [
                'kategori' => 'Risiko Stunting',
                'axis_dominan' => 'TB/U',
                'source' => 'WHO 2006 Child Growth Standards',
                'detail' => "TB/U z-score: $tbu SD (-2 s/d -1 SD = Risiko Stunting)"
            ];
        }
    }
    
    // ========== 2. CEK OBESITAS (BB/TB atau IMT/U > +3 SD) ==========
    $axis_gizi = $imtu ?? $bbtb; // Prioritas IMT/U, fallback BB/TB
    $axis_name = $imtu !== null ? 'IMT/U' : 'BB/TB';
    
    if ($axis_gizi !== null && $axis_gizi > 3) {
        return [
            'kategori' => 'Obesitas',
            'axis_dominan' => $axis_name,
            'source' => 'WHO 2006 Child Growth Standards',
            'detail' => "$axis_name z-score: $axis_gizi SD (> +3 SD = Obesitas)"
        ];
    }
    
    // ========== 3. CEK GIZI LEBIH (BB/TB atau IMT/U +2 s/d +3 SD) ==========
    if ($axis_gizi !== null && $axis_gizi > 2 && $axis_gizi <= 3) {
        return [
            'kategori' => 'Gizi Lebih',
            'axis_dominan' => $axis_name,
            'source' => 'WHO 2006 Child Growth Standards',
            'detail' => "$axis_name z-score: $axis_gizi SD (+2 s/d +3 SD = Gizi Lebih)"
        ];
    }
    
    // ========== 4. CEK BERESIKO GIZI LEBIH (BB/TB atau IMT/U +1 s/d +2 SD) ==========
    if ($axis_gizi !== null && $axis_gizi > 1 && $axis_gizi <= 2) {
        return [
            'kategori' => 'Beresiko Gizi Lebih',
            'axis_dominan' => $axis_name,
            'source' => 'WHO 2006 Child Growth Standards',
            'detail' => "$axis_name z-score: $axis_gizi SD (+1 s/d +2 SD = Beresiko Gizi Lebih)"
        ];
    }
    
    // ========== 5. CEK GIZI KURANG (BB/U atau BB/TB < -2 SD) ==========
    if (($bbu !== null && $bbu < -2) || ($bbtb !== null && $bbtb < -2)) {
        $detail_axis = $bbu !== null && $bbu < -2 ? "BB/U: $bbu SD" : "BB/TB: $bbtb SD";
        $axis = $bbu !== null && $bbu < -2 ? 'BB/U' : 'BB/TB';
        return [
            'kategori' => 'Gizi Kurang',
            'axis_dominan' => $axis,
            'source' => 'WHO 2006 Child Growth Standards',
            'detail' => "$detail_axis (< -2 SD = Gizi Kurang / Underweight)"
        ];
    }
    
    // ========== 6. CEK BERESIKO GIZI KURANG (BB/U atau BB/TB -2 s/d -1 SD) ==========
    if (($bbu !== null && $bbu >= -2 && $bbu < -1) || ($bbtb !== null && $bbtb >= -2 && $bbtb < -1)) {
        $detail_axis = $bbu !== null && $bbu >= -2 && $bbu < -1 ? "BB/U: $bbu SD" : "BB/TB: $bbtb SD";
        $axis = $bbu !== null && $bbu >= -2 && $bbu < -1 ? 'BB/U' : 'BB/TB';
        return [
            'kategori' => 'Beresiko Gizi Kurang',
            'axis_dominan' => $axis,
            'source' => 'WHO 2006 Child Growth Standards',
            'detail' => "$detail_axis (-2 s/d -1 SD = Beresiko Gizi Kurang)"
        ];
    }
    
    // ========== 7. GIZI BAIK (Default - semua indikator normal) ==========
    return [
        'kategori' => 'Gizi Baik',
        'axis_dominan' => 'Semua indikator',
        'source' => 'WHO 2006 Child Growth Standards',
        'detail' => 'Semua indikator berada dalam rentang normal (-1 s/d +1 SD)'
    ];
}
