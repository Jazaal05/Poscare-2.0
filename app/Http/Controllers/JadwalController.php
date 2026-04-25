<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JadwalController extends Controller
{
    public function index()
    {
        return view('jadwal.index');
    }

    public function list(Request $request)
    {
        $jadwal = Jadwal::orderBy('tanggal', 'desc')->get()->map(fn($j) => [
            'id'             => $j->id,
            'nama_kegiatan'  => $j->nama_kegiatan,
            'jenis_kegiatan' => $j->jenis_kegiatan,
            'tanggal'        => $j->tanggal,
            'waktu_mulai'    => $j->waktu_mulai ? substr($j->waktu_mulai, 0, 5) : null,
            'lokasi'         => $j->lokasi,
            'keterangan'     => $j->keterangan,
            'status'         => $j->status ?? 'Terjadwal',
            'is_posted'      => (bool) ($j->is_posted ?? false),
        ]);

        return response()->json(['success' => true, 'data' => $jadwal]);
    }

    public function show($id)
    {
        $jadwal = Jadwal::findOrFail($id);
        return response()->json(['success' => true, 'data' => $jadwal]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_kegiatan'  => 'required|string|max:100',
            'jenis_kegiatan' => 'nullable|in:Penimbangan,Imunisasi,Penyuluhan,Lainnya',
            'tanggal'        => 'required|date',
            'waktu_mulai'    => 'required|date_format:H:i',
            'lokasi'         => 'required|string|max:200',
            'keterangan'     => 'nullable|string',
            'status'         => 'nullable|in:Terjadwal,Selesai,Dibatalkan',
        ]);

        // Validasi 1: Tanggal tidak boleh di masa lalu
        if (Carbon::parse($data['tanggal'])->startOfDay()->lt(Carbon::today())) {
            return response()->json([
                'success' => false,
                'message' => 'Tanggal tidak boleh di masa lalu! Pilih hari ini atau masa depan.',
            ], 400);
        }

        // Validasi 2: Waktu operasional 07:00 - 17:00
        $jam = (int) explode(':', $data['waktu_mulai'])[0];
        if ($jam < 7 || $jam >= 17) {
            return response()->json([
                'success' => false,
                'message' => 'Waktu tidak realistis! Posyandu beroperasi antara jam 07:00 - 17:00.',
            ], 400);
        }

        // Validasi 3: Cek konflik jadwal (tanggal + lokasi + waktu overlap)
        $konflik = $this->cekKonflik($data['tanggal'], $data['waktu_mulai'], $data['lokasi']);
        if ($konflik) {
            return response()->json([
                'success'          => false,
                'message'          => 'KONFLIK JADWAL! Sudah ada jadwal lain di waktu dan lokasi yang sama.',
                'conflict_details' => $konflik,
            ], 409);
        }

        $data['created_by'] = Auth::id();
        $data['status']     = $data['status'] ?? 'Terjadwal';
        $jadwal = Jadwal::create($data);

        return response()->json(['success' => true, 'message' => 'Jadwal berhasil disimpan!', 'data' => $jadwal], 201);
    }

    public function update(Request $request, $id)
    {
        $jadwal = Jadwal::findOrFail($id);
        $data   = $request->validate([
            'nama_kegiatan'  => 'sometimes|string|max:100',
            'jenis_kegiatan' => 'nullable|in:Penimbangan,Imunisasi,Penyuluhan,Lainnya',
            'tanggal'        => 'sometimes|date',
            'waktu_mulai'    => 'sometimes|date_format:H:i',
            'lokasi'         => 'sometimes|string|max:200',
            'keterangan'     => 'nullable|string',
            'status'         => 'nullable|in:Terjadwal,Selesai,Dibatalkan',
        ]);

        // Validasi tanggal tidak di masa lalu (jika diubah)
        if (isset($data['tanggal']) && Carbon::parse($data['tanggal'])->startOfDay()->lt(Carbon::today())) {
            return response()->json([
                'success' => false,
                'message' => 'Tanggal tidak boleh di masa lalu!',
            ], 400);
        }

        // Validasi waktu operasional (jika diubah)
        if (isset($data['waktu_mulai'])) {
            $jam = (int) explode(':', $data['waktu_mulai'])[0];
            if ($jam < 7 || $jam >= 17) {
                return response()->json([
                    'success' => false,
                    'message' => 'Waktu tidak realistis! Posyandu beroperasi antara jam 07:00 - 17:00.',
                ], 400);
            }
        }

        // Cek konflik (kecuali jadwal itu sendiri)
        $tanggal    = $data['tanggal']    ?? $jadwal->tanggal;
        $waktu      = $data['waktu_mulai'] ?? $jadwal->waktu_mulai;
        $lokasi     = $data['lokasi']     ?? $jadwal->lokasi;
        $konflik    = $this->cekKonflik($tanggal, $waktu, $lokasi, $id);
        if ($konflik) {
            return response()->json([
                'success'          => false,
                'message'          => 'KONFLIK JADWAL! Sudah ada jadwal lain di waktu dan lokasi yang sama.',
                'conflict_details' => $konflik,
            ], 409);
        }

        $jadwal->update($data);
        return response()->json(['success' => true, 'message' => 'Jadwal berhasil diperbarui!']);
    }

    public function destroy($id)
    {
        Jadwal::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Jadwal berhasil dihapus!']);
    }

    // =============================================
    // POST KE MOBILE — menggantikan post_jadwal.php
    // =============================================
    public function postMobile(Request $request)
    {
        $request->validate(['id' => 'required|integer']);
        $jadwal = Jadwal::findOrFail($request->id);

        // Cek sudah diposting
        if ($jadwal->is_posted) {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal sudah diposting sebelumnya.',
                'data'    => ['already_posted' => true],
            ]);
        }

        $jadwal->update(['is_posted' => 1]);

        return response()->json([
            'success' => true,
            'message' => 'Jadwal berhasil diposting ke aplikasi mobile!',
            'data'    => [
                'jadwal_id'         => $jadwal->id,
                'notification_sent' => true,
                'notification'      => [
                    'title'   => 'Jadwal Posyandu Baru!',
                    'message' => $jadwal->nama_kegiatan . ' - ' . Carbon::parse($jadwal->tanggal)->format('d/m/Y') . ' pukul ' . substr($jadwal->waktu_mulai, 0, 5),
                    'lokasi'  => $jadwal->lokasi,
                ],
            ],
        ]);
    }

    // =============================================
    // PRIVATE: Cek konflik jadwal
    // =============================================
    private function cekKonflik(string $tanggal, string $waktu, string $lokasi, ?int $excludeId = null): ?array
    {
        $query = Jadwal::where('tanggal', $tanggal)
            ->where('lokasi', $lokasi)
            ->where('status', '!=', 'Dibatalkan');

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $existing = $query->get();
        $newTime  = strtotime($waktu);
        $duration = 3 * 3600; // asumsi 3 jam per kegiatan

        foreach ($existing as $j) {
            $existTime = strtotime($j->waktu_mulai);
            $existEnd  = $existTime + $duration;
            $newEnd    = $newTime + $duration;

            $overlap = ($newTime >= $existTime && $newTime < $existEnd)
                || ($newEnd > $existTime && $newEnd <= $existEnd)
                || ($newTime <= $existTime && $newEnd >= $existEnd);

            if ($overlap) {
                return [
                    'existing_schedule' => $j->nama_kegiatan,
                    'existing_time'     => substr($j->waktu_mulai, 0, 5),
                    'location'          => $j->lokasi,
                    'date'              => $tanggal,
                    'suggestion'        => 'Pilih waktu atau lokasi yang berbeda',
                ];
            }
        }

        return null;
    }
}
