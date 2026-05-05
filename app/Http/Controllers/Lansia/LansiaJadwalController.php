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
            ->whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->orderBy('tanggal')
            ->orderBy('waktu_mulai')
            ->get()
            ->map(fn($j) => $this->formatJadwal($j));

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
            'data'    => $this->formatJadwal($jadwal),
        ]);
    }

    // ── Helper: format jadwal untuk response ──────────────────
    private function formatJadwal(Jadwal $j): array
    {
        // Ambil tanggal dari kolom tanggal (kolom utama), fallback ke tanggal_kegiatan
        $tgl = $j->tanggal ?? $j->tanggal_kegiatan;
        $tglStr = $tgl instanceof \Carbon\Carbon
            ? $tgl->format('Y-m-d')
            : (is_string($tgl) ? substr($tgl, 0, 10) : null);

        return [
            'id'               => $j->id,
            'judul_kegiatan'   => $j->judul_kegiatan ?: $j->nama_kegiatan,
            'nama_kegiatan'    => $j->nama_kegiatan,
            'tanggal'          => $tglStr,
            'tanggal_kegiatan' => $tglStr,   // alias agar JS tidak perlu bedakan
            'waktu_mulai'      => substr($j->waktu_mulai ?? '', 0, 5),
            'lokasi'           => $j->lokasi,
            'keterangan'       => $j->keterangan,
            'status'           => $j->status,
            'layanan'          => $j->layanan,
        ];
    }

    // ── API: Store jadwal baru ─────────────────────────────────
    public function store(Request $request)
    {
        $data = $request->validate([
            'judul_kegiatan'   => 'required|string|max:255',
            'tanggal_kegiatan' => 'required|date|after_or_equal:today',
            'waktu_mulai'      => 'required|date_format:H:i',
            'lokasi'           => 'nullable|string|max:255',
            'keterangan'       => 'nullable|string',
        ]);

        $jadwal = Jadwal::create([
            'judul_kegiatan'   => $data['judul_kegiatan'],
            'nama_kegiatan'    => $data['judul_kegiatan'],   // sync ke kolom lama
            'tanggal_kegiatan' => $data['tanggal_kegiatan'],
            'tanggal'          => $data['tanggal_kegiatan'], // sync ke kolom lama
            'waktu_mulai'      => $data['waktu_mulai'],
            'lokasi'           => $data['lokasi'] ?? null,
            'keterangan'       => $data['keterangan'] ?? null,
            'layanan'          => 'lansia',
            'status'           => 'Terjadwal',
            'dibuat_oleh'      => Auth::id(),
            'created_by'       => Auth::id(),
        ]);

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
            'tanggal_kegiatan' => 'sometimes|date|after_or_equal:today',
            'waktu_mulai'      => 'sometimes|date_format:H:i',
            'lokasi'           => 'nullable|string|max:255',
            'keterangan'       => 'nullable|string',
        ]);

        $update = [];
        if (isset($data['judul_kegiatan'])) {
            $update['judul_kegiatan'] = $data['judul_kegiatan'];
            $update['nama_kegiatan']  = $data['judul_kegiatan'];
        }
        if (isset($data['tanggal_kegiatan'])) {
            $update['tanggal_kegiatan'] = $data['tanggal_kegiatan'];
            $update['tanggal']          = $data['tanggal_kegiatan'];
        }
        if (isset($data['waktu_mulai']))  $update['waktu_mulai'] = $data['waktu_mulai'];
        if (array_key_exists('lokasi', $data))     $update['lokasi']     = $data['lokasi'];
        if (array_key_exists('keterangan', $data)) $update['keterangan'] = $data['keterangan'];

        $jadwal->update($update);

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
