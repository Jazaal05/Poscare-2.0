<?php

namespace App\Http\Controllers;

use App\Models\Anak;
use App\Models\Pengukuran;
use App\Services\WHODataService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GrafikPertumbuhanController extends Controller
{
    /**
     * Tampilkan grafik pertumbuhan anak
     */
    public function show($id)
    {
        $anak = Anak::findOrFail($id);
        $riwayat = Pengukuran::where('anak_id', $id)
            ->orderBy('tanggal_ukur')
            ->get();

        // Hitung z-score untuk setiap pengukuran
        $riwayat_dengan_zscore = $riwayat->map(function ($record) use ($anak) {
            $umur_bulan = $this->hitungUmurBulan($anak->tanggal_lahir, $record->tanggal_ukur);
            
            // Get WHO data sesuai jenis kelamin
            $who_bbu = WHODataService::getWHOData('BB_U', $anak->jenis_kelamin);
            $who_tbu = WHODataService::getWHOData('TB_U', $anak->jenis_kelamin);
            $who_bbtb = WHODataService::getWHOData('BB_TB', $anak->jenis_kelamin);
            
            // Hitung z-score
            $z_bbu = WHODataService::hitungZScore($umur_bulan, $record->bb_kg, $who_bbu);
            $z_tbu = WHODataService::hitungZScore($umur_bulan, $record->tb_pb_cm, $who_tbu);
            $z_bbtb = WHODataService::hitungZScore($record->tb_pb_cm, $record->bb_kg, $who_bbtb);
            
            // Hitung status gizi
            $status_bbu = WHODataService::getKategoriGizi($z_bbu, 'bbu');
            $status_tbu = WHODataService::getKategoriGizi($z_tbu, 'tbu');
            $status_bbtb = WHODataService::getKategoriGizi($z_bbtb, 'bbtb');
            
            // Tentukan status gizi overall (dari BB/TB yang paling akurat)
            $overall_status = $status_bbtb['kode'];
            
            return [
                'id' => $record->id,
                'tanggal_ukur' => $record->tanggal_ukur,
                'umur_bulan' => $umur_bulan,
                'bb_kg' => $record->bb_kg,
                'tb_pb_cm' => $record->tb_pb_cm,
                'lk_cm' => $record->lk_cm,
                'z_bbu' => $z_bbu,
                'z_tbu' => $z_tbu,
                'z_bbtb' => $z_bbtb,
                'status_bbu' => $status_bbu['kategori'],
                'status_tbu' => $status_tbu['kategori'],
                'status_bbtb' => $status_bbtb['kategori'],
                'overall_8' => $overall_status,
            ];
        });

        return view('pengukuran.grafik', [
            'anak' => $anak,
            'riwayat' => $riwayat_dengan_zscore,
        ]);
    }

    /**
     * API: Get chart data untuk AJAX
     */
    public function getChartData(Request $request, $id)
    {
        $anak = Anak::findOrFail($id);
        $tipe = $request->get('tipe', 'bbu'); // bbu, tbu, bbtb
        
        $riwayat = Pengukuran::where('anak_id', $id)
            ->orderBy('tanggal_ukur')
            ->get();

        // Get WHO data
        $who_data = WHODataService::getWHOData(
            $tipe === 'bbu' ? 'BB_U' : ($tipe === 'tbu' ? 'TB_U' : 'BB_TB'),
            $anak->jenis_kelamin
        );

        // Prepare data historis untuk chart
        $data_historis = [];
        foreach ($riwayat as $record) {
            $umur_bulan = $this->hitungUmurBulan($anak->tanggal_lahir, $record->tanggal_ukur);
            
            if ($tipe === 'bbu') {
                $data_historis[] = ['x' => $umur_bulan, 'y' => $record->bb_kg];
            } elseif ($tipe === 'tbu') {
                $data_historis[] = ['x' => $umur_bulan, 'y' => $record->tb_pb_cm];
            } else { // bbtb
                $data_historis[] = ['x' => $record->tb_pb_cm, 'y' => $record->bb_kg];
            }
        }

        // Prepare chart data
        $datasets = WHODataService::prepareChartData($who_data, $data_historis);

        return response()->json([
            'success' => true,
            'datasets' => $datasets,
            'tipe' => $tipe,
        ]);
    }

    /**
     * Hitung umur dalam bulan
     */
    private function hitungUmurBulan($tanggal_lahir, $tanggal_ukur)
    {
        $lahir = Carbon::parse($tanggal_lahir);
        $ukur = Carbon::parse($tanggal_ukur);
        
        return $lahir->diffInMonths($ukur);
    }
}
