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
}
