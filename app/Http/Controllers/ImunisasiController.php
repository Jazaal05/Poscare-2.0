<?php

namespace App\Http\Controllers;

use App\Models\Anak;
use App\Models\Imunisasi;
use App\Models\MasterVaksin;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ImunisasiController extends Controller
{
    // =============================================
    // HALAMAN IMUNISASI
    // Menggantikan: pages/imunisasi.php
    // =============================================
    public function index()
    {
        return view('imunisasi.index');
    }

    // =============================================
    // API: List Imunisasi (pivot per vaksin)
    // Menggantikan: api_web/immunization_list.php
    // =============================================
    public function list(Request $request)
    {
        $user   = Auth::user();
        $search = trim($request->get('q', ''));

        // Ambil semua vaksin
        $vaccines = MasterVaksin::orderBy('id')->get();

        // Query anak aktif
        $query = Anak::aktif()->with(['imunisasi' => fn($q) => $q->whereNotNull('tanggal')]);

        if ($user->role !== 'admin') {
            $query->where('user_id', $user->id);
        }

        if (!empty($search)) {
            $query->where('nama_anak', 'like', "%{$search}%");
        }

        $children = $query->orderBy('nama_anak')->get();

        // Build pivot data (sama seperti immunization_list.php lama)
        $data = $children->map(function ($anak) use ($vaccines) {
            $umurBulan = Carbon::parse($anak->tanggal_lahir)->diffInMonths(now());
            $row = [
                'id'           => $anak->id,
                'nama_anak'    => $anak->nama_anak,
                'tanggal_lahir'=> $anak->tanggal_lahir,
                'jenis_kelamin'=> $anak->jenis_kelamin,
                'umur_bulan'   => $umurBulan,
            ];

            // Pivot: satu kolom per vaksin
            foreach ($vaccines as $v) {
                $key = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '_', $v->nama_vaksin));
                $imun = $anak->imunisasi->firstWhere('master_vaksin_id', $v->id);
                $row[$key . '_date'] = $imun?->tanggal ? $imun->tanggal->format('Y-m-d') : null;
            }

            return $row;
        });

        // Vaccine list untuk frontend
        $vaccineList = $vaccines->map(fn($v) => [
            'id'    => $v->id,
            'key'   => strtolower(preg_replace('/[^a-zA-Z0-9]+/', '_', $v->nama_vaksin)),
            'label' => strtoupper($v->nama_vaksin),
        ]);

        return response()->json([
            'success'  => true,
            'data'     => $data,
            'vaccines' => $vaccineList,
        ]);
    }

    // =============================================
    // TANDAI IMUNISASI SUDAH DILAKUKAN
    // Menggantikan: api_web/save_jadwal_imunisasi.php
    // =============================================
    public function tandai(Request $request)
    {
        $request->validate([
            'anak_id'         => 'required|integer|exists:anak,id',
            'master_vaksin_id'=> 'required|integer|exists:master_vaksin,id',
            'tanggal'         => 'required|date|before_or_equal:today',
        ]);

        $user  = Auth::user();
        $query = Anak::aktif()->where('id', $request->anak_id);
        if ($user->role !== 'admin') $query->where('user_id', $user->id);

        if (!$query->exists()) {
            return response()->json(['success' => false, 'message' => 'Data anak tidak ditemukan.'], 404);
        }

        // Hitung umur saat imunisasi
        $anak      = Anak::find($request->anak_id);
        $umurBulan = Carbon::parse($anak->tanggal_lahir)->diffInMonths(Carbon::parse($request->tanggal));

        // Upsert: update jika sudah ada, insert jika belum
        Imunisasi::updateOrCreate(
            ['anak_id' => $request->anak_id, 'master_vaksin_id' => $request->master_vaksin_id],
            ['tanggal' => $request->tanggal, 'umur_bulan' => $umurBulan]
        );

        $vaksin = MasterVaksin::find($request->master_vaksin_id);

        return response()->json([
            'success' => true,
            'message' => "Imunisasi {$vaksin->nama_vaksin} berhasil dicatat!",
        ]);
    }

    // =============================================
    // UNDO IMUNISASI
    // Menggantikan: api_web/immunization_undo.php
    // =============================================
    public function undo(Request $request)
    {
        $request->validate([
            'anak_id'         => 'required|integer|exists:anak,id',
            'master_vaksin_id'=> 'required|integer|exists:master_vaksin,id',
        ]);

        $user  = Auth::user();
        $query = Anak::aktif()->where('id', $request->anak_id);
        if ($user->role !== 'admin') $query->where('user_id', $user->id);

        if (!$query->exists()) {
            return response()->json(['success' => false, 'message' => 'Data anak tidak ditemukan.'], 404);
        }

        $deleted = Imunisasi::where('anak_id', $request->anak_id)
            ->where('master_vaksin_id', $request->master_vaksin_id)
            ->delete();

        if (!$deleted) {
            return response()->json(['success' => false, 'message' => 'Data imunisasi tidak ditemukan.'], 404);
        }

        $vaksin = MasterVaksin::find($request->master_vaksin_id);

        return response()->json([
            'success' => true,
            'message' => "Imunisasi {$vaksin->nama_vaksin} berhasil dibatalkan.",
        ]);
    }
}
