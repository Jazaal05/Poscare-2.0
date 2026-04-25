<?php

namespace App\Http\Controllers\Lansia;

use App\Http\Controllers\Controller;
use App\Models\Lansia;
use App\Models\PengobatanLansia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LansiaPengobatanController extends Controller
{
    // Daftar obat dan vitamin default
    const DAFTAR_OBAT = [
        'Paracetamol', 'Amlodipin', 'Metformin', 'Captopril',
        'Simvastatin', 'Antasida', 'Vitamin B Complex', 'Asam Mefenamat',
        'Glibenklamid', 'Furosemid',
    ];

    const DAFTAR_VITAMIN = [
        'Vitamin C', 'Vitamin D', 'Vitamin B12', 'Kalsium',
        'Asam Folat', 'Zinc', 'Vitamin E', 'Omega-3',
    ];

    public function index()
    {
        return view('lansia.pengobatan.index');
    }

    // ── API: List pengobatan ───────────────────────────────────────
    public function list(Request $request)
    {
        $search = trim($request->get('q', ''));
        $query  = PengobatanLansia::with('lansia')
            ->whereHas('lansia', fn($q) => $q->where('is_deleted', false));

        if ($search) {
            $query->whereHas('lansia', fn($q) => $q->where('nama_lengkap', 'like', "%{$search}%"));
        }

        $data = $query->orderBy('tanggal', 'desc')->limit(100)->get()->map(fn($p) => [
            'id'               => $p->id,
            'lansia_id'        => $p->lansia_id,
            'nama_lansia'      => $p->lansia?->nama_lengkap,
            'tanggal'          => $p->tanggal?->format('Y-m-d'),
            'ada_keluhan'      => $p->ada_keluhan,
            'keluhan'          => $p->keluhan ?? [],
            'obat_diberikan'   => $p->obat_diberikan ?? [],
            'vitamin_diberikan'=> $p->vitamin_diberikan ?? [],
            'catatan'          => $p->catatan,
        ]);

        return response()->json([
            'success'       => true,
            'data'          => $data,
            'daftar_obat'   => self::DAFTAR_OBAT,
            'daftar_vitamin'=> self::DAFTAR_VITAMIN,
        ]);
    }

    // ── API: Store pengobatan ──────────────────────────────────────
    public function store(Request $request)
    {
        $data = $request->validate([
            'lansia_id'        => 'required|integer|exists:lansia,id',
            'tanggal'          => 'required|date|before_or_equal:today',
            'ada_keluhan'      => 'required|boolean',
            'keluhan'          => 'nullable|array',
            'obat_diberikan'   => 'nullable|array',
            'vitamin_diberikan'=> 'nullable|array',
            'catatan'          => 'nullable|string',
        ]);

        $data['dicatat_oleh'] = Auth::id();
        $p = PengobatanLansia::create($data);

        return response()->json(['success' => true, 'message' => 'Data pengobatan berhasil disimpan!', 'data' => ['id' => $p->id]], 201);
    }

    // ── API: Update ────────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $pengobatan = PengobatanLansia::findOrFail($id);
        $data = $request->validate([
            'tanggal'          => 'sometimes|date|before_or_equal:today',
            'ada_keluhan'      => 'sometimes|boolean',
            'keluhan'          => 'nullable|array',
            'obat_diberikan'   => 'nullable|array',
            'vitamin_diberikan'=> 'nullable|array',
            'catatan'          => 'nullable|string',
        ]);

        $pengobatan->update($data);
        return response()->json(['success' => true, 'message' => 'Data pengobatan berhasil diperbarui!']);
    }

    // ── API: Delete ────────────────────────────────────────────────
    public function destroy($id)
    {
        PengobatanLansia::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Data pengobatan berhasil dihapus!']);
    }
}
