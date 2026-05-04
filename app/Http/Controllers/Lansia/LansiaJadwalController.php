<?php

namespace App\Http\Controllers\Lansia;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LansiaJadwalController extends Controller
{
    public function index()
    {
        return view('lansia.jadwal.index');
    }

    // ── API: List jadwal lansia ────────────────────────────────
    public function list(Request $request)
    {
        $year  = $request->get('year', date('Y'));
        $month = $request->get('month', date('m'));

        $jadwal = Jadwal::where('layanan', 'lansia')
            ->whereYear('tanggal_kegiatan', $year)
            ->whereMonth('tanggal_kegiatan', $month)
            ->orderBy('tanggal_kegiatan')
            ->orderBy('waktu_mulai')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $jadwal,
        ]);
    }

    // ── API: Show single jadwal ────────────────────────────────
    public function show($id)
    {
        $jadwal = Jadwal::where('layanan', 'lansia')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $jadwal,
        ]);
    }

    // ── API: Store jadwal baru ─────────────────────────────────
    public function store(Request $request)
    {
        $data = $request->validate([
            'judul_kegiatan'   => 'required|string|max:255',
            'tanggal_kegiatan' => 'required|date',
            'waktu_mulai'      => 'required',
            'waktu_selesai'    => 'nullable',
            'lokasi'           => 'nullable|string|max:255',
            'keterangan'       => 'nullable|string',
            'layanan'          => 'required|in:balita,lansia',
        ]);

        $data['dibuat_oleh'] = Auth::id();

        $jadwal = Jadwal::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Jadwal berhasil ditambahkan!',
            'data'    => $jadwal,
        ], 201);
    }

    // ── API: Update jadwal ─────────────────────────────────────
    public function update(Request $request, $id)
    {
        $jadwal = Jadwal::where('layanan', 'lansia')->findOrFail($id);

        $data = $request->validate([
            'judul_kegiatan'   => 'sometimes|string|max:255',
            'tanggal_kegiatan' => 'sometimes|date',
            'waktu_mulai'      => 'sometimes',
            'waktu_selesai'    => 'nullable',
            'lokasi'           => 'nullable|string|max:255',
            'keterangan'       => 'nullable|string',
        ]);

        $jadwal->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Jadwal berhasil diperbarui!',
            'data'    => $jadwal,
        ]);
    }

    // ── API: Delete jadwal ─────────────────────────────────────
    public function destroy($id)
    {
        $jadwal = Jadwal::where('layanan', 'lansia')->findOrFail($id);
        $jadwal->delete();

        return response()->json([
            'success' => true,
            'message' => 'Jadwal berhasil dihapus!',
        ]);
    }
}
