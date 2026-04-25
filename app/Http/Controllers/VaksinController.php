<?php

namespace App\Http\Controllers;

use App\Models\Imunisasi;
use App\Models\MasterVaksin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VaksinController extends Controller
{
    // =============================================
    // HALAMAN MANAJEMEN VAKSIN
    // =============================================
    public function index()
    {
        // Hanya admin yang boleh akses
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Hanya admin yang dapat mengelola master vaksin.');
        }

        return view('vaksin.index');
    }

    // =============================================
    // API: List semua vaksin
    // =============================================
    public function list()
    {
        $vaksin = MasterVaksin::orderBy('id')->get()->map(fn($v) => [
            'id'                  => $v->id,
            'nama_vaksin'         => $v->nama_vaksin,
            'usia_standar_bulan'  => $v->usia_standar_bulan,
            'usia_minimal_bulan'  => $v->usia_minimal_bulan,
            'usia_maksimal_bulan' => $v->usia_maksimal_bulan,
            'keterangan'          => $v->keterangan,
            'jumlah_imunisasi'    => Imunisasi::where('master_vaksin_id', $v->id)->count(),
        ]);

        return response()->json(['success' => true, 'data' => $vaksin]);
    }

    // =============================================
    // API: Tambah vaksin baru
    // Menggantikan: api_web/add_vaccine.php
    // =============================================
    public function store(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Hanya admin yang dapat menambah vaksin.'], 403);
        }

        $request->validate([
            'nama_vaksin'         => 'required|string|min:2|max:100',
            'usia_standar_bulan'  => 'required|integer|min:0|max:60',
            'usia_minimal_bulan'  => 'nullable|integer|min:0|max:60',
            'usia_maksimal_bulan' => 'nullable|integer|min:0|max:60',
            'keterangan'          => 'nullable|string|max:255',
        ]);

        $namaVaksin = strtoupper(trim($request->nama_vaksin));

        // Cek duplikat (normalisasi nama)
        $normalized = preg_replace('/[^A-Z0-9]+/', '', $namaVaksin);
        $existing   = MasterVaksin::all();

        foreach ($existing as $v) {
            $existingNorm = preg_replace('/[^A-Z0-9]+/', '', strtoupper($v->nama_vaksin));
            if ($normalized === $existingNorm) {
                return response()->json([
                    'success' => false,
                    'message' => "Vaksin dengan nama serupa sudah ada: '{$v->nama_vaksin}'",
                ], 409);
            }
        }

        $vaksin = MasterVaksin::create([
            'nama_vaksin'         => $namaVaksin,
            'usia_standar_bulan'  => $request->usia_standar_bulan,
            'usia_minimal_bulan'  => $request->usia_minimal_bulan ?? 0,
            'usia_maksimal_bulan' => $request->usia_maksimal_bulan ?? 60,
            'keterangan'          => $request->keterangan ?? 'Vaksin tambahan',
        ]);

        return response()->json([
            'success'      => true,
            'message'      => "Vaksin '{$namaVaksin}' berhasil ditambahkan!",
            'vaccine_id'   => $vaksin->id,
            'vaccine_name' => $vaksin->nama_vaksin,
        ], 201);
    }

    // =============================================
    // API: Update vaksin
    // =============================================
    public function update(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Hanya admin yang dapat mengubah vaksin.'], 403);
        }

        $vaksin = MasterVaksin::findOrFail($id);

        $request->validate([
            'nama_vaksin'         => 'sometimes|string|min:2|max:100',
            'usia_standar_bulan'  => 'sometimes|integer|min:0|max:60',
            'usia_minimal_bulan'  => 'nullable|integer|min:0|max:60',
            'usia_maksimal_bulan' => 'nullable|integer|min:0|max:60',
            'keterangan'          => 'nullable|string|max:255',
        ]);

        $data = $request->only(['usia_standar_bulan', 'usia_minimal_bulan', 'usia_maksimal_bulan', 'keterangan']);

        if ($request->has('nama_vaksin')) {
            $data['nama_vaksin'] = strtoupper(trim($request->nama_vaksin));
        }

        $vaksin->update($data);

        return response()->json([
            'success' => true,
            'message' => "Vaksin '{$vaksin->nama_vaksin}' berhasil diperbarui!",
        ]);
    }

    // =============================================
    // API: Hapus vaksin
    // Menggantikan: api_web/delete_vaccine.php
    // =============================================
    public function destroy($id)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Hanya admin yang dapat menghapus vaksin.'], 403);
        }

        $vaksin = MasterVaksin::findOrFail($id);

        // Hitung berapa data imunisasi yang akan ikut terhapus
        $jumlahImunisasi = Imunisasi::where('master_vaksin_id', $id)->count();

        DB::beginTransaction();
        try {
            // Hapus data imunisasi terkait dulu
            Imunisasi::where('master_vaksin_id', $id)->delete();

            // Hapus vaksin
            $vaksin->delete();

            DB::commit();

            return response()->json([
                'success'                  => true,
                'message'                  => "Vaksin '{$vaksin->nama_vaksin}' berhasil dihapus.",
                'deleted_imunisasi_count'  => $jumlahImunisasi,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus vaksin: ' . $e->getMessage(),
            ], 500);
        }
    }
}
