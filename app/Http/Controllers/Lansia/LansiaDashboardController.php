<?php

namespace App\Http\Controllers\Lansia;

use App\Http\Controllers\Controller;
use App\Models\KunjunganLansia;
use App\Models\Lansia;
use Carbon\Carbon;

class LansiaDashboardController extends Controller
{
    public function index()
    {
        return view('lansia.dashboard.index');
    }

    public function stats()
    {
        $total   = Lansia::aktif()->count();
        $totalL  = Lansia::aktif()->where('jenis_kelamin', 'L')->count();
        $totalP  = Lansia::aktif()->where('jenis_kelamin', 'P')->count();
        $rataUsia = round(Lansia::aktif()->get()->avg(fn($l) => $l->umur) ?? 0, 1);

        // Kunjungan bulan ini
        $kunjunganBulanIni = KunjunganLansia::whereMonth('tanggal_kunjungan', now()->month)
            ->whereYear('tanggal_kunjungan', now()->year)
            ->count();

        // Lansia dengan kondisi tidak normal (dari kunjungan terakhir)
        $lansiaIds = Lansia::aktif()->pluck('id');
        $tidakNormal = KunjunganLansia::whereIn('lansia_id', $lansiaIds)
            ->whereIn('lansia_id', function ($q) {
                $q->selectRaw('lansia_id')
                  ->from('kunjungan_lansia')
                  ->groupBy('lansia_id')
                  ->havingRaw('MAX(tanggal_kunjungan) = tanggal_kunjungan');
            })
            ->where(function ($q) {
                $q->whereIn('status_tensi', ['hipertensi1', 'hipertensi2'])
                  ->orWhereIn('status_gula', ['tinggi', 'sangat_tinggi'])
                  ->orWhere('status_kolesterol', 'tinggi')
                  ->orWhere('status_asam_urat', 'tinggi');
            })
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'total_lansia'       => $total,
                'total_laki'         => $totalL,
                'total_perempuan'    => $totalP,
                'rata_rata_usia'     => $rataUsia,
                'kunjungan_bulan_ini'=> $kunjunganBulanIni,
                'tidak_normal'       => $tidakNormal,
            ],
        ]);
    }

    // ── API: Chart Distribusi Usia ─────────────────────────────
    public function chartDistribusiUsia()
    {
        $lansia = Lansia::aktif()->get();
        
        $distribusi = [
            '60-64 tahun' => 0,
            '65-69 tahun' => 0,
            '70-74 tahun' => 0,
            '75-79 tahun' => 0,
            '80+ tahun'   => 0,
        ];

        foreach ($lansia as $l) {
            $umur = $l->umur;
            if ($umur >= 60 && $umur <= 64) {
                $distribusi['60-64 tahun']++;
            } elseif ($umur >= 65 && $umur <= 69) {
                $distribusi['65-69 tahun']++;
            } elseif ($umur >= 70 && $umur <= 74) {
                $distribusi['70-74 tahun']++;
            } elseif ($umur >= 75 && $umur <= 79) {
                $distribusi['75-79 tahun']++;
            } else {
                $distribusi['80+ tahun']++;
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'labels' => array_keys($distribusi),
                'values' => array_values($distribusi),
            ],
        ]);
    }

    // ── API: Chart Kondisi Kesehatan ───────────────────────────
    public function chartKondisiKesehatan()
    {
        $lansiaIds = Lansia::aktif()->pluck('id');
        
        // Ambil kunjungan terakhir setiap lansia
        $kunjunganTerakhir = KunjunganLansia::whereIn('lansia_id', $lansiaIds)
            ->whereIn('id', function ($q) {
                $q->selectRaw('MAX(id)')
                  ->from('kunjungan_lansia')
                  ->groupBy('lansia_id');
            })
            ->get();

        $kondisi = [
            'Normal'      => 0,
            'Hipertensi'  => 0,
            'Diabetes'    => 0,
            'Kolesterol Tinggi' => 0,
            'Asam Urat Tinggi'  => 0,
        ];

        foreach ($kunjunganTerakhir as $k) {
            $normal = true;
            
            if (in_array($k->status_tensi, ['hipertensi1', 'hipertensi2'])) {
                $kondisi['Hipertensi']++;
                $normal = false;
            }
            if (in_array($k->status_gula, ['tinggi', 'sangat_tinggi'])) {
                $kondisi['Diabetes']++;
                $normal = false;
            }
            if ($k->status_kolesterol === 'tinggi') {
                $kondisi['Kolesterol Tinggi']++;
                $normal = false;
            }
            if ($k->status_asam_urat === 'tinggi') {
                $kondisi['Asam Urat Tinggi']++;
                $normal = false;
            }
            
            if ($normal) {
                $kondisi['Normal']++;
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'labels' => array_keys($kondisi),
                'values' => array_values($kondisi),
            ],
        ]);
    }

    // ── API: Chart Trend Kunjungan (6 bulan terakhir) ──────────
    public function chartTrendKunjungan()
    {
        $bulan = [];
        $jumlah = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $bulan[] = $date->format('M Y');
            
            $count = KunjunganLansia::whereMonth('tanggal_kunjungan', $date->month)
                ->whereYear('tanggal_kunjungan', $date->year)
                ->count();
            
            $jumlah[] = $count;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'labels' => $bulan,
                'values' => $jumlah,
            ],
        ]);
    }
}
