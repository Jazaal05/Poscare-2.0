<?php

namespace App\Http\Controllers\Lansia;

use App\Http\Controllers\Controller;
use App\Models\Lansia;
use App\Models\PemeriksaanLansia;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LansiaDataController extends Controller
{
    public function index()
    {
        return view('lansia.data.index');
    }

    // ── API: List lansia ───────────────────────────────────────────
    public function list(Request $request)
    {
        $search = trim($request->get('q', ''));
        $query  = Lansia::aktif()->with('pemeriksaanTerakhir');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        $data = $query->orderBy('nama_lengkap')->get()->map(function ($l) {
            $p = $l->pemeriksaanTerakhir;
            return [
                'id'              => $l->id,
                'nik'             => $l->nik,
                'nama_lengkap'    => $l->nama_lengkap,
                'jenis_kelamin'   => $l->jenis_kelamin,
                'tanggal_lahir'   => $l->tanggal_lahir?->format('Y-m-d'),
                'tempat_lahir'    => $l->tempat_lahir,
                'alamat'          => $l->alamat,
                'rt_rw'           => $l->rt_rw,
                'no_hp'           => $l->no_hp,
                'nama_wali'       => $l->nama_wali,
                'hubungan_wali'   => $l->hubungan_wali,
                'umur'            => $l->umur,
                // Pemeriksaan terakhir
                'berat_badan'     => $p?->berat_badan,
                'tinggi_badan'    => $p?->tinggi_badan,
                'tekanan_darah'   => $p?->tekanan_darah,
                'gula_darah'      => $p?->gula_darah,
                'asam_urat'       => $p?->asam_urat,
                'kolesterol'      => $p?->kolesterol,
                'tanggal_periksa' => $p?->tanggal_periksa?->format('Y-m-d'),
            ];
        });

        return response()->json(['success' => true, 'data' => $data]);
    }

    // ── API: Show detail ───────────────────────────────────────────
    public function show($id)
    {
        $lansia = Lansia::aktif()->with(['pemeriksaan' => fn($q) => $q->orderBy('tanggal_periksa', 'desc')->limit(10)])
                        ->findOrFail($id);

        return response()->json(['success' => true, 'data' => [
            'id'            => $lansia->id,
            'nik'           => $lansia->nik,
            'nama_lengkap'  => $lansia->nama_lengkap,
            'jenis_kelamin' => $lansia->jenis_kelamin,
            'tanggal_lahir' => $lansia->tanggal_lahir?->format('Y-m-d'),
            'tempat_lahir'  => $lansia->tempat_lahir,
            'alamat'        => $lansia->alamat,
            'rt_rw'         => $lansia->rt_rw,
            'no_hp'         => $lansia->no_hp,
            'nama_wali'     => $lansia->nama_wali,
            'hubungan_wali' => $lansia->hubungan_wali,
            'umur'          => $lansia->umur,
            'riwayat_periksa' => $lansia->pemeriksaan->map(fn($p) => [
                'id'            => $p->id,
                'tanggal_periksa'=> $p->tanggal_periksa?->format('Y-m-d'),
                'berat_badan'   => $p->berat_badan,
                'tinggi_badan'  => $p->tinggi_badan,
                'tekanan_darah' => $p->tekanan_darah,
                'gula_darah'    => $p->gula_darah,
                'asam_urat'     => $p->asam_urat,
                'kolesterol'    => $p->kolesterol,
                'catatan'       => $p->catatan,
            ]),
        ]]);
    }

    // ── API: Store lansia baru ─────────────────────────────────────
    public function store(Request $request)
    {
        $data = $request->validate([
            'nik'           => 'required|digits:16|unique:lansia,nik',
            'nama_lengkap'  => 'required|string|min:3|max:100',
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_lahir' => 'required|date|before_or_equal:today',
            'tempat_lahir'  => 'nullable|string|max:100',
            'alamat'        => 'nullable|string|max:255',
            'rt_rw'         => 'nullable|string|max:10',
            'no_hp'         => 'nullable|string|max:20',
            'nama_wali'     => 'nullable|string|max:100',
            'hubungan_wali' => 'nullable|string|max:50',
        ]);

        $data['created_by'] = Auth::id();
        $lansia = Lansia::create($data);

        // Simpan pemeriksaan awal jika ada
        $hasPemeriksaan = $request->filled('berat_badan') || $request->filled('tekanan_darah')
                       || $request->filled('gula_darah')  || $request->filled('asam_urat')
                       || $request->filled('kolesterol');

        if ($hasPemeriksaan) {
            PemeriksaanLansia::create([
                'lansia_id'      => $lansia->id,
                'tanggal_periksa'=> $request->tanggal_periksa ?? now()->toDateString(),
                'berat_badan'    => $request->berat_badan,
                'tinggi_badan'   => $request->tinggi_badan,
                'tekanan_darah'  => $request->tekanan_darah,
                'gula_darah'     => $request->gula_darah,
                'asam_urat'      => $request->asam_urat,
                'kolesterol'     => $request->kolesterol,
                'catatan'        => $request->catatan,
                'dicatat_oleh'   => Auth::id(),
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Data lansia berhasil ditambahkan!', 'data' => ['id' => $lansia->id]], 201);
    }

    // ── API: Update ────────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $lansia = Lansia::aktif()->findOrFail($id);
        $data   = $request->validate([
            'nik'           => "sometimes|digits:16|unique:lansia,nik,{$id}",
            'nama_lengkap'  => 'sometimes|string|min:3|max:100',
            'jenis_kelamin' => 'sometimes|in:L,P',
            'tanggal_lahir' => 'sometimes|date|before_or_equal:today',
            'tempat_lahir'  => 'nullable|string|max:100',
            'alamat'        => 'nullable|string|max:255',
            'rt_rw'         => 'nullable|string|max:10',
            'no_hp'         => 'nullable|string|max:20',
            'nama_wali'     => 'nullable|string|max:100',
            'hubungan_wali' => 'nullable|string|max:50',
        ]);

        $lansia->update($data);
        return response()->json(['success' => true, 'message' => 'Data lansia berhasil diperbarui!']);
    }

    // ── API: Soft delete ───────────────────────────────────────────
    public function destroy($id)
    {
        $lansia = Lansia::aktif()->findOrFail($id);
        $lansia->update(['is_deleted' => true]);
        return response()->json(['success' => true, 'message' => 'Data lansia berhasil dihapus!']);
    }

    // ── API: Tambah pemeriksaan ────────────────────────────────────
    public function storePemeriksaan(Request $request, $id)
    {
        Lansia::aktif()->findOrFail($id);
        $data = $request->validate([
            'tanggal_periksa' => 'required|date|before_or_equal:today',
            'berat_badan'     => 'nullable|numeric|min:20|max:200',
            'tinggi_badan'    => 'nullable|numeric|min:100|max:250',
            'tekanan_darah'   => 'nullable|string|max:20',
            'gula_darah'      => 'nullable|numeric|min:0|max:1000',
            'asam_urat'       => 'nullable|numeric|min:0|max:30',
            'kolesterol'      => 'nullable|numeric|min:0|max:1000',
            'catatan'         => 'nullable|string',
        ]);

        $data['lansia_id']    = $id;
        $data['dicatat_oleh'] = Auth::id();
        $p = PemeriksaanLansia::create($data);

        return response()->json(['success' => true, 'message' => 'Pemeriksaan berhasil dicatat!', 'data' => ['id' => $p->id]], 201);
    }
}
