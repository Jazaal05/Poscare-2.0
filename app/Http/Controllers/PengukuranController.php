<?php

namespace App\Http\Controllers;

use App\Http\Requests\Pengukuran\StorePengukuranRequest;
use App\Models\Anak;
use App\Models\RiwayatPengukuran;
use App\Services\WhoService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PengukuranController extends Controller
{
    public function __construct(private WhoService $who) {}

    // =============================================
    // HALAMAN GRAFIK PERTUMBUHAN
    // Menggantikan: pages/grafik_pertumbuhan.php
    // =============================================
    public function grafik(Request $request, $anakId)
    {
        $user  = Auth::user();
        $query = Anak::aktif()->where('id', $anakId);
        if ($user->role !== 'admin') $query->where('user_id', $user->id);

        $anak = $query->firstOrFail();

        $riwayat = RiwayatPengukuran::where('anak_id', $anakId)
            ->orderBy('tanggal_ukur', 'asc')
            ->get();

        return view('pengukuran.grafik', compact('anak', 'riwayat'));
    }

    // =============================================
    // STORE - Input Pengukuran Baru
    // Menggantikan: api_web/growth_insert.php
    // =============================================
    public function store(StorePengukuranRequest $request)
    {
        $data    = $request->validated();
        $anakId  = $data['anak_id'];
        $user    = Auth::user();

        // Cek ownership
        $query = Anak::aktif()->where('id', $anakId);
        if ($user->role !== 'admin') $query->where('user_id', $user->id);
        $anak = $query->first();

        if (!$anak) {
            return response()->json(['success' => false, 'message' => 'Data anak tidak ditemukan.'], 404);
        }

        $tanggalUkur = $data['tanggal_ukur'];
        $bbKg        = (float) $data['bb_kg'];
        $tbCm        = (float) $data['tb_cm'];
        $lkCm        = isset($data['lk_cm']) ? (float) $data['lk_cm'] : null;
        $caraUkur    = $data['cara_ukur'];

        // Validasi tanggal ukur tidak sebelum tanggal lahir
        if (Carbon::parse($tanggalUkur)->lt(Carbon::parse($anak->tanggal_lahir))) {
            return response()->json(['success' => false, 'message' => 'Tanggal ukur tidak boleh sebelum tanggal lahir.'], 422);
        }

        // Hitung umur
        $birthDate   = Carbon::parse($anak->tanggal_lahir);
        $measureDate = Carbon::parse($tanggalUkur);
        $umurHari    = $birthDate->diffInDays($measureDate);
        $umurBulan   = round($umurHari / 30.4375, 2);

        // Validasi usia 0-60 bulan
        if ($umurBulan > 60) {
            return response()->json(['success' => false, 'message' => "Usia anak {$umurBulan} bulan melebihi batas sistem (0-60 bulan)."], 422);
        }

        // Normalisasi TB/PB sesuai WHO
        $tbPbCm = $tbCm;
        if ($umurBulan < 24 && $caraUkur === 'berdiri') {
            $tbPbCm = $tbCm + 0.7;
        } elseif ($umurBulan >= 24 && $caraUkur === 'berbaring') {
            $tbPbCm = $tbCm - 0.7;
        }

        // Hitung IMT
        $imt = $bbKg / (($tbPbCm / 100) ** 2);

        // Cek duplikat (data identik dengan entry terakhir)
        $lastEntry = RiwayatPengukuran::where('anak_id', $anakId)
            ->orderBy('tanggal_ukur', 'desc')->orderBy('id', 'desc')->first();

        if ($lastEntry) {
            $threshold = 0.01;
            $bbSame    = abs((float)$lastEntry->bb_kg - $bbKg) < $threshold;
            $tbSame    = abs((float)$lastEntry->tb_pb_cm - $tbPbCm) < $threshold;
            $caraSame  = $lastEntry->cara_ukur === $caraUkur;
            $lkSame    = ($lkCm === null && $lastEntry->lk_cm === null)
                || ($lkCm !== null && $lastEntry->lk_cm !== null && abs((float)$lastEntry->lk_cm - $lkCm) < $threshold);

            if ($bbSame && $tbSame && $caraSame && $lkSame) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data pengukuran tidak berubah dari entry terakhir.',
                    'caused_by' => 'duplicate_measurement',
                ], 400);
            }
        }

        DB::beginTransaction();
        try {
            // Hitung z-score WHO
            $zResult = $this->who->hitungStatusGiziLengkap(
                $umurBulan, $bbKg, $tbPbCm,
                $anak->jenis_kelamin, $caraUkur, $lkCm
            );

            if (isset($zResult['error'])) {
                throw new \Exception('Gagal menghitung z-score: ' . $zResult['error']);
            }

            $detail   = $zResult['status_gizi_detail'];
            $zscore   = $detail['zscore'];
            $axis     = $detail['axis'];
            $overall8 = $detail['overall_8'];

            $kategori = [
                'tbu'  => $axis['tbu']['label'],
                'bbu'  => $axis['bbu']['label'],
                'bbtb' => $axis['adiposity']['label'],
                'imtu' => $axis['adiposity']['label'],
            ];

            // Insert riwayat pengukuran
            $riwayat = RiwayatPengukuran::create([
                'anak_id'       => $anakId,
                'tanggal_ukur'  => $tanggalUkur,
                'umur_hari'     => $umurHari,
                'umur_bulan'    => $umurBulan,
                'bb_kg'         => $bbKg,
                'tb_pb_cm'      => $tbPbCm,
                'lk_cm'         => $lkCm,
                'cara_ukur'     => $caraUkur,
                'imt'           => round($imt, 2),
                'z_tbu'         => $zscore['tbu'],
                'z_bbu'         => $zscore['bbu'],
                'z_bbtb'        => $zscore['bbtb'],
                'z_imtu'        => $zscore['imtu'],
                'kat_tbu'       => $kategori['tbu'],
                'kat_bbu'       => $kategori['bbu'],
                'kat_bbtb'      => $kategori['bbtb'],
                'kat_imtu'      => $kategori['imtu'],
                'overall_8'     => $overall8['kategori'],
                'overall_source'=> 'WHO-2006',
            ]);

            // Update ringkasan di tabel anak
            $anak->update([
                'berat_badan'                  => $bbKg,
                'tinggi_badan'                 => $tbPbCm,
                'lingkar_kepala'               => $lkCm,
                'cara_ukur'                    => $caraUkur,
                'tanggal_penimbangan_terakhir' => $tanggalUkur,
                'status_gizi'                  => $overall8['kategori'],
                'status_gizi_detail'           => json_encode([
                    'zscore'   => $zscore,
                    'overall_8'=> $overall8,
                ]),
            ]);

            DB::commit();

            return response()->json([
                'success'      => true,
                'message'      => 'Pengukuran berhasil disimpan!',
                'data'         => ['id' => $riwayat->id, 'anak_id' => $anakId],
                'status_gizi'  => $overall8['kategori'],
                'status_gizi_detail' => [
                    'zscore'   => $zscore,
                    'kategori' => $kategori,
                    'overall_8'=> $overall8,
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // =============================================
    // GET RIWAYAT - Ambil riwayat pengukuran anak
    // =============================================
    public function riwayat(Request $request, $anakId)
    {
        $user  = Auth::user();
        $query = Anak::aktif()->where('id', $anakId);
        if ($user->role !== 'admin') $query->where('user_id', $user->id);

        if (!$query->exists()) {
            return response()->json(['success' => false, 'message' => 'Data anak tidak ditemukan.'], 404);
        }

        $riwayat = RiwayatPengukuran::where('anak_id', $anakId)
            ->orderBy('tanggal_ukur', 'desc')
            ->orderBy('id', 'desc')
            ->limit(50)
            ->get()
            ->map(fn($r) => [
                'id'          => $r->id,
                'tanggal_ukur'=> $r->tanggal_ukur,
                'umur_bulan'  => (float) $r->umur_bulan,
                'bb_kg'       => (float) $r->bb_kg,
                'tb_pb_cm'    => (float) $r->tb_pb_cm,
                'lk_cm'       => $r->lk_cm ? (float) $r->lk_cm : null,
                'cara_ukur'   => $r->cara_ukur,
                'imt'         => $r->imt ? (float) $r->imt : null,
                'z_tbu'       => $r->z_tbu ? (float) $r->z_tbu : null,
                'z_bbu'       => $r->z_bbu ? (float) $r->z_bbu : null,
                'z_bbtb'      => $r->z_bbtb ? (float) $r->z_bbtb : null,
                'kat_tbu'     => $r->kat_tbu,
                'kat_bbu'     => $r->kat_bbu,
                'kat_bbtb'    => $r->kat_bbtb,
                'overall_8'   => $r->overall_8,
            ]);

        return response()->json(['success' => true, 'data' => $riwayat]);
    }
}
