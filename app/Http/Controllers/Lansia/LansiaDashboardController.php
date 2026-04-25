<?php

namespace App\Http\Controllers\Lansia;

use App\Http\Controllers\Controller;
use App\Models\Lansia;
use App\Models\PemeriksaanLansia;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LansiaDashboardController extends Controller
{
    public function index()
    {
        return view('lansia.dashboard.index');
    }

    public function stats()
    {
        $total      = Lansia::aktif()->count();
        $totalL     = Lansia::aktif()->where('jenis_kelamin', 'L')->count();
        $totalP     = Lansia::aktif()->where('jenis_kelamin', 'P')->count();

        // Rata-rata usia
        $rataUsia = Lansia::aktif()->get()->avg(fn($l) => Carbon::parse($l->tanggal_lahir)->age);

        // Pemeriksaan bulan ini
        $bulanIni = PemeriksaanLansia::whereMonth('tanggal_periksa', now()->month)
            ->whereYear('tanggal_periksa', now()->year)
            ->count();

        // Statistik tekanan darah (normal: sistolik < 140)
        $hipertensi = PemeriksaanLansia::whereIn('lansia_id',
            Lansia::aktif()->pluck('id')
        )->whereNotNull('tekanan_darah')
         ->get()
         ->filter(function ($p) {
             $parts = explode('/', $p->tekanan_darah);
             return isset($parts[0]) && (int)$parts[0] >= 140;
         })->count();

        return response()->json([
            'success' => true,
            'data' => [
                'total_lansia'    => $total,
                'total_laki'      => $totalL,
                'total_perempuan' => $totalP,
                'rata_usia'       => round($rataUsia ?? 0, 1),
                'periksa_bulan_ini' => $bulanIni,
                'hipertensi'      => $hipertensi,
            ],
        ]);
    }
}
