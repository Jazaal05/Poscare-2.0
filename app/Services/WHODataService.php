<?php

namespace App\Services;

/**
 * ============================================================================
 * WHO CHILD GROWTH STANDARDS 2006
 * Data Standar Pertumbuhan Anak WHO
 * ============================================================================
 * 
 * Standar: WHO Child Growth Standards 2006 (valid hingga 2025)
 * Sumber: https://www.who.int/tools/child-growth-standards
 * 
 * INDIKATOR:
 * 1. BB/U (Berat Badan menurut Umur) - 0-60 bulan
 * 2. TB/U (Tinggi/Panjang Badan menurut Umur) - 0-60 bulan  
 * 3. BB/TB (Berat Badan menurut Tinggi Badan)
 * 
 * FORMAT DATA:
 * [x, -3SD, -2SD, -1SD, Median, +1SD, +2SD, +3SD]
 */

class WHODataService
{
    // BB/U LAKI-LAKI (0-60 bulan)
    public static $WHO_BB_U_LAKI = [
        [0, 2.1, 2.5, 2.9, 3.3, 3.9, 4.4, 5.0],
        [1, 2.9, 3.4, 3.9, 4.5, 5.1, 5.8, 6.6],
        [2, 3.8, 4.3, 4.9, 5.6, 6.3, 7.1, 8.0],
        [3, 4.4, 5.0, 5.7, 6.4, 7.2, 8.0, 9.0],
        [4, 4.9, 5.6, 6.2, 7.0, 7.8, 8.7, 9.7],
        [5, 5.3, 6.0, 6.7, 7.5, 8.4, 9.3, 10.4],
        [6, 5.7, 6.4, 7.1, 7.9, 8.8, 9.8, 10.9],
        [7, 5.9, 6.7, 7.4, 8.3, 9.2, 10.3, 11.4],
        [8, 6.2, 6.9, 7.7, 8.6, 9.6, 10.7, 11.9],
        [9, 6.4, 7.1, 8.0, 8.9, 9.9, 11.0, 12.3],
        [10, 6.6, 7.4, 8.2, 9.2, 10.2, 11.4, 12.7],
        [11, 6.8, 7.6, 8.4, 9.4, 10.5, 11.7, 13.0],
        [12, 6.9, 7.7, 8.6, 9.6, 10.8, 12.0, 13.3],
        [24, 8.6, 9.7, 10.8, 12.2, 13.6, 15.3, 17.1],
        [36, 10.0, 11.3, 12.7, 14.3, 16.2, 18.3, 20.7],
        [48, 11.2, 12.7, 14.4, 16.3, 18.6, 21.2, 24.2],
        [60, 12.4, 14.1, 16.0, 18.3, 21.0, 24.2, 27.9]
    ];

    // BB/U PEREMPUAN (0-60 bulan)
    public static $WHO_BB_U_PEREMPUAN = [
        [0, 2.0, 2.4, 2.8, 3.2, 3.7, 4.2, 4.8],
        [1, 2.7, 3.2, 3.6, 4.2, 4.8, 5.5, 6.2],
        [2, 3.4, 3.9, 4.5, 5.1, 5.8, 6.6, 7.5],
        [3, 4.0, 4.5, 5.2, 5.8, 6.6, 7.5, 8.5],
        [4, 4.4, 5.0, 5.7, 6.4, 7.3, 8.2, 9.3],
        [5, 4.8, 5.4, 6.1, 6.9, 7.8, 8.8, 10.0],
        [6, 5.1, 5.7, 6.5, 7.3, 8.2, 9.3, 10.6],
        [12, 6.3, 7.0, 7.9, 8.9, 10.1, 11.5, 13.1],
        [24, 8.1, 9.0, 10.2, 11.5, 13.0, 14.8, 17.0],
        [36, 9.6, 10.8, 12.2, 13.9, 15.8, 18.1, 20.9],
        [48, 10.9, 12.3, 14.0, 16.1, 18.5, 21.5, 25.2],
        [60, 12.1, 13.7, 15.8, 18.2, 21.2, 24.9, 29.5]
    ];

    // TB/U LAKI-LAKI (0-60 bulan)
    public static $WHO_TB_U_LAKI = [
        [0, 44.2, 46.1, 48.0, 49.9, 51.8, 53.7, 55.6],
        [1, 48.9, 50.8, 52.8, 54.7, 56.7, 58.6, 60.6],
        [2, 52.4, 54.4, 56.4, 58.4, 60.4, 62.4, 64.4],
        [3, 55.3, 57.3, 59.4, 61.4, 63.5, 65.5, 67.6],
        [6, 61.2, 63.3, 65.5, 67.6, 69.8, 71.9, 74.0],
        [12, 68.6, 71.0, 73.4, 75.7, 78.1, 80.5, 82.9],
        [24, 78.7, 81.7, 84.8, 87.8, 90.9, 93.9, 97.0],
        [36, 85.0, 88.7, 92.4, 96.1, 99.8, 103.5, 107.2],
        [48, 90.7, 94.9, 99.1, 103.3, 107.5, 111.7, 115.9],
        [60, 96.1, 100.7, 105.3, 110.0, 114.6, 119.2, 123.9]
    ];

    // TB/U PEREMPUAN (0-60 bulan)
    public static $WHO_TB_U_PEREMPUAN = [
        [0, 43.6, 45.4, 47.3, 49.1, 51.0, 52.9, 54.7],
        [1, 47.8, 49.8, 51.7, 53.7, 55.6, 57.6, 59.5],
        [2, 51.0, 53.0, 55.0, 57.1, 59.1, 61.1, 63.2],
        [6, 58.9, 61.2, 63.5, 65.7, 68.0, 70.3, 72.5],
        [12, 66.3, 68.9, 71.4, 74.0, 76.6, 79.2, 81.7],
        [24, 76.7, 80.0, 83.2, 86.4, 89.6, 92.9, 96.1],
        [36, 83.6, 87.4, 91.2, 95.1, 98.9, 102.7, 106.5],
        [48, 89.8, 94.1, 98.4, 102.7, 107.0, 111.3, 115.7],
        [60, 95.2, 99.9, 104.7, 109.4, 114.2, 118.9, 123.7]
    ];

    // BB/TB LAKI-LAKI
    public static $WHO_BB_TB_LAKI = [
        [45.0, 1.9, 2.0, 2.2, 2.4, 2.7, 3.0, 3.3],
        [50.0, 2.6, 2.9, 3.2, 3.6, 4.0, 4.4, 4.9],
        [55.0, 3.6, 4.0, 4.4, 4.9, 5.5, 6.1, 6.7],
        [60.0, 4.8, 5.3, 5.9, 6.5, 7.2, 8.0, 8.8],
        [65.0, 5.8, 6.5, 7.2, 8.0, 8.9, 9.9, 11.0],
        [70.0, 6.6, 7.4, 8.3, 9.2, 10.3, 11.4, 12.7],
        [75.0, 7.4, 8.3, 9.3, 10.4, 11.6, 12.9, 14.3],
        [80.0, 8.2, 9.2, 10.3, 11.5, 12.8, 14.3, 15.9],
        [85.0, 8.9, 10.0, 11.2, 12.5, 14.0, 15.6, 17.4],
        [90.0, 9.6, 10.8, 12.1, 13.5, 15.1, 16.9, 18.8],
        [95.0, 10.3, 11.5, 12.9, 14.5, 16.2, 18.1, 20.2],
        [100.0, 10.9, 12.2, 13.7, 15.4, 17.2, 19.3, 21.5],
        [105.0, 11.5, 12.9, 14.5, 16.2, 18.2, 20.4, 22.8],
        [110.0, 12.1, 13.6, 15.2, 17.1, 19.2, 21.5, 24.1],
        [115.0, 12.7, 14.2, 16.0, 17.9, 20.1, 22.6, 25.3],
        [120.0, 13.3, 14.9, 16.7, 18.7, 21.0, 23.6, 26.5]
    ];

    // BB/TB PEREMPUAN
    public static $WHO_BB_TB_PEREMPUAN = [
        [45.0, 1.9, 2.1, 2.3, 2.5, 2.7, 3.0, 3.3],
        [50.0, 2.5, 2.7, 3.0, 3.3, 3.7, 4.1, 4.5],
        [55.0, 3.2, 3.5, 3.9, 4.3, 4.8, 5.3, 5.9],
        [60.0, 3.9, 4.3, 4.8, 5.3, 5.9, 6.6, 7.3],
        [65.0, 4.5, 5.0, 5.6, 6.2, 6.9, 7.7, 8.6],
        [70.0, 5.1, 5.7, 6.3, 7.0, 7.8, 8.7, 9.7],
        [75.0, 5.7, 6.3, 7.0, 7.8, 8.7, 9.7, 10.8],
        [80.0, 6.3, 7.0, 7.7, 8.6, 9.6, 10.7, 11.9],
        [85.0, 6.8, 7.6, 8.4, 9.4, 10.5, 11.7, 13.0],
        [90.0, 7.4, 8.2, 9.1, 10.1, 11.3, 12.6, 14.0],
        [95.0, 7.9, 8.8, 9.7, 10.9, 12.1, 13.5, 15.0],
        [100.0, 8.4, 9.3, 10.4, 11.5, 12.9, 14.4, 16.0],
        [105.0, 8.9, 9.9, 11.0, 12.2, 13.7, 15.3, 17.0],
        [110.0, 9.4, 10.4, 11.6, 12.9, 14.4, 16.1, 18.0],
        [115.0, 9.9, 11.0, 12.2, 13.6, 15.2, 17.0, 19.0],
        [120.0, 10.4, 11.5, 12.8, 14.3, 16.0, 17.8, 19.9]
    ];

    /**
     * Get data WHO berdasarkan indikator dan jenis kelamin
     */
    public static function getWHOData($indikator, $jenis_kelamin = 'L')
    {
        $key = "WHO_{$indikator}_" . ($jenis_kelamin === 'L' ? 'LAKI' : 'PEREMPUAN');
        return self::$$key ?? [];
    }

    /**
     * Hitung Z-Score dengan interpolasi linear
     */
    public static function hitungZScore($x_value, $y_value, $data_standar)
    {
        // Cari dua titik data yang mengapit nilai X
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
        
        // Jika nilai X tepat ada di tabel
        if ($x0 === $x_value) {
            $sd_values = array_slice($row0, 1);
        }
        // Jika nilai X di luar range tabel
        elseif ($x0 === null || $x1 === null) {
            $nearest = $x0 === null ? $row1 : $row0;
            $sd_values = array_slice($nearest, 1);
        }
        // Interpolasi linear antara dua titik
        else {
            $frac = ($x_value - $x0) / ($x1 - $x0);
            $sd_values = [];
            
            for ($i = 1; $i <= 7; $i++) {
                $v0 = $row0[$i];
                $v1 = $row1[$i];
                $sd_values[] = $v0 + $frac * ($v1 - $v0);
            }
        }
        
        // Hitung z-score
        $median = $sd_values[3];
        
        if ($y_value == $median) {
            return 0.0;
        }
        
        if ($y_value > $median) {
            $sd1_plus = $sd_values[4];
            $sd2_plus = $sd_values[5];
            $sd3_plus = $sd_values[6];
            
            if ($y_value <= $sd1_plus) {
                return ($y_value - $median) / ($sd1_plus - $median);
            } elseif ($y_value <= $sd2_plus) {
                return 1.0 + ($y_value - $sd1_plus) / ($sd2_plus - $sd1_plus);
            } elseif ($y_value <= $sd3_plus) {
                return 2.0 + ($y_value - $sd2_plus) / ($sd3_plus - $sd2_plus);
            } else {
                return 3.0 + ($y_value - $sd3_plus) / ($sd3_plus - $sd2_plus);
            }
        } else {
            $sd1_minus = $sd_values[2];
            $sd2_minus = $sd_values[1];
            $sd3_minus = $sd_values[0];
            
            if ($y_value >= $sd1_minus) {
                return ($y_value - $median) / ($median - $sd1_minus);
            } elseif ($y_value >= $sd2_minus) {
                return -1.0 + ($y_value - $sd1_minus) / ($sd1_minus - $sd2_minus);
            } elseif ($y_value >= $sd3_minus) {
                return -2.0 + ($y_value - $sd2_minus) / ($sd2_minus - $sd3_minus);
            } else {
                return -3.0 + ($y_value - $sd3_minus) / ($sd2_minus - $sd3_minus);
            }
        }
    }

    /**
     * Konversi Z-Score ke Kategori Gizi
     */
    public static function getKategoriGizi($z_score, $indikator = 'bbu')
    {
        if ($z_score < -3) {
            $kategori = 'Sangat Kurang';
            $kode = 'sangat_kurang';
        } elseif ($z_score >= -3 && $z_score < -2) {
            $kategori = 'Kurang';
            $kode = 'kurang';
        } elseif ($z_score >= -2 && $z_score <= 2) {
            $kategori = 'Normal';
            $kode = 'normal';
        } elseif ($z_score > 2 && $z_score <= 3) {
            $kategori = 'Lebih';
            $kode = 'lebih';
        } else {
            $kategori = 'Sangat Lebih';
            $kode = 'sangat_lebih';
        }
        
        return [
            'kategori' => $kategori,
            'kode' => $kode,
            'z_score' => round($z_score, 2)
        ];
    }

    /**
     * Prepare Chart Data untuk Grafik
     */
    public static function prepareChartData($data_standar, $data_historis = [])
    {
        $datasets = [];
        
        // Kurva WHO
        $sd_minus_3 = [];
        $sd_minus_2 = [];
        $sd_minus_1 = [];
        $median_data = [];
        $sd_plus_1 = [];
        $sd_plus_2 = [];
        $sd_plus_3 = [];
        
        foreach ($data_standar as $row) {
            $x = $row[0];
            $sd_minus_3[] = ['x' => $x, 'y' => $row[1]];
            $sd_minus_2[] = ['x' => $x, 'y' => $row[2]];
            $sd_minus_1[] = ['x' => $x, 'y' => $row[3]];
            $median_data[] = ['x' => $x, 'y' => $row[4]];
            $sd_plus_1[] = ['x' => $x, 'y' => $row[5]];
            $sd_plus_2[] = ['x' => $x, 'y' => $row[6]];
            $sd_plus_3[] = ['x' => $x, 'y' => $row[7]];
        }
        
        // Tambah kurva WHO ke datasets
        $datasets[] = ['label' => '-3 SD', 'data' => $sd_minus_3, 'borderColor' => '#DC2626', 'borderDash' => [6,3], 'borderWidth' => 2];
        $datasets[] = ['label' => '-2 SD (Kurang)', 'data' => $sd_minus_2, 'borderColor' => '#F59E0B', 'borderDash' => [4,3], 'borderWidth' => 2];
        $datasets[] = ['label' => 'Median (Normal)', 'data' => $median_data, 'borderColor' => '#10B981', 'borderWidth' => 2.5];
        $datasets[] = ['label' => '+2 SD (Lebih)', 'data' => $sd_plus_2, 'borderColor' => '#F59E0B', 'borderDash' => [4,3], 'borderWidth' => 2];
        $datasets[] = ['label' => '+3 SD', 'data' => $sd_plus_3, 'borderColor' => '#DC2626', 'borderDash' => [6,3], 'borderWidth' => 2];
        
        // Tambah data anak
        $anak_data = [];
        foreach ($data_historis as $record) {
            if (isset($record['x']) && isset($record['y'])) {
                $anak_data[] = ['x' => $record['x'], 'y' => $record['y']];
            }
        }
        
        if (!empty($anak_data)) {
            $datasets[] = [
                'label' => 'Data Anak',
                'data' => $anak_data,
                'borderColor' => '#246BCE',
                'backgroundColor' => '#246BCE',
                'borderWidth' => 2,
                'pointRadius' => 6,
                'pointBackgroundColor' => '#246BCE',
                'pointBorderColor' => '#fff',
                'pointBorderWidth' => 2,
                'pointHoverRadius' => 8,
                'fill' => false,
                'tension' => 0.4,
            ];
        }
        
        return $datasets;
    }
}
