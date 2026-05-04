<?php

namespace App\Http\Controllers;

use App\Models\Lansia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LansiaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('lansia.index');
    }

    /**
     * Get list of lansia (API)
     */
    public function list(Request $request)
    {
        try {
            $query = Lansia::aktif(); // Menggunakan scope aktif

            // Search
            if ($request->has('q') && $request->q) {
                $search = $request->q;
                $query->where(function($q) use ($search) {
                    $q->where('nama_lengkap', 'like', "%{$search}%")
                      ->orWhere('nik_lansia', 'like', "%{$search}%")
                      ->orWhere('nama_wali', 'like', "%{$search}%")
                      ->orWhere('alamat_domisili', 'like', "%{$search}%");
                });
            }

            $limit = $request->get('limit', 200);
            $lansia = $query->orderBy('id', 'desc')->limit($limit)->get();

            // Transform data untuk frontend
            $data = $lansia->map(function($item) {
                return [
                    'id' => $item->id,
                    'nama_lansia' => $item->nama_lengkap ?: 'Tanpa Nama',
                    'nik_lansia' => $item->nik_lansia,
                    'jenis_kelamin' => $item->jenis_kelamin,
                    'tanggal_lahir' => $item->tgl_lahir?->format('Y-m-d'),
                    'tempat_lahir' => $item->tempat_lahir,
                    'usia' => $item->umur_display,
                    'umur' => $item->umur,
                    'berat_badan' => $item->berat_badan,
                    'tinggi_badan' => $item->tinggi_badan,
                    'tekanan_darah' => $item->tekanan_darah,
                    'gula_darah' => $item->gula_darah,
                    'kolesterol' => $item->kolesterol,
                    'asam_urat' => $item->asam_urat,
                    'status_kesehatan' => $item->status_kesehatan,
                    'alamat_domisili' => $item->alamat_domisili,
                    'rt_rw' => $item->rt_rw,
                    'nama_kk' => $item->nama_kk,
                    'nama_wali' => $item->nama_wali,
                    'nik_wali' => $item->nik_wali,
                    'hp_kontak_wali' => $item->hp_kontak_wali,
                    'umur_display' => $item->umur_display,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Data lansia berhasil dimuat'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('lansia.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nik_lansia' => 'nullable|string|size:16|unique:lansia,nik_lansia',
                'nama_lengkap' => 'required|string|max:255',
                'tgl_lahir' => 'required|date|before:today',
                'tempat_lahir' => 'nullable|string|max:100',
                'jenis_kelamin' => 'required|in:L,P',
                'alamat_domisili' => 'nullable|string',
                'rt_rw' => 'nullable|string|max:10',
                'nama_kk' => 'nullable|string|max:255',
                'nama_wali' => 'nullable|string|max:255',
                'nik_wali' => 'nullable|string|size:16',
                'hp_kontak_wali' => 'nullable|string|max:20',
                'berat_badan' => 'nullable|numeric|min:30|max:150',
                'tinggi_badan' => 'nullable|numeric|min:100|max:200',
                'tekanan_darah' => 'nullable|string|regex:/^\d{2,3}\/\d{2,3}$/',
                'gula_darah' => 'nullable|numeric|min:50|max:500',
                'kolesterol' => 'nullable|numeric|min:100|max:400',
                'asam_urat' => 'nullable|numeric|min:1|max:15',
            ]);

            $validated['dicatat_oleh'] = auth()->id();
            $validated['is_deleted'] = false;
            $validated['status_kesehatan'] = 'Sehat'; // Default
            $validated['tanggal_pemeriksaan_terakhir'] = now();

            $lansia = Lansia::create($validated);

            return response()->json([
                'success' => true,
                'data' => $lansia,
                'message' => 'Data lansia berhasil disimpan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $lansia = Lansia::aktif()->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $lansia->id,
                    'nama_lansia' => $lansia->nama_lengkap,
                    'nik_lansia' => $lansia->nik_lansia,
                    'jenis_kelamin' => $lansia->jenis_kelamin,
                    'tanggal_lahir' => $lansia->tgl_lahir?->format('Y-m-d'),
                    'tempat_lahir' => $lansia->tempat_lahir,
                    'umur' => $lansia->umur,
                    'umur_display' => $lansia->umur_display,
                    'berat_badan' => $lansia->berat_badan,
                    'tinggi_badan' => $lansia->tinggi_badan,
                    'tekanan_darah' => $lansia->tekanan_darah,
                    'gula_darah' => $lansia->gula_darah,
                    'kolesterol' => $lansia->kolesterol,
                    'asam_urat' => $lansia->asam_urat,
                    'status_kesehatan' => $lansia->status_kesehatan,
                    'alamat_domisili' => $lansia->alamat_domisili,
                    'rt_rw' => $lansia->rt_rw,
                    'nama_kk' => $lansia->nama_kk,
                    'nama_wali' => $lansia->nama_wali,
                    'nik_wali' => $lansia->nik_wali,
                    'hp_kontak_wali' => $lansia->hp_kontak_wali,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $lansia = Lansia::where('is_deleted', 0)->findOrFail($id);
        return view('lansia.edit', compact('lansia'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $lansia = Lansia::aktif()->findOrFail($id);

            $validated = $request->validate([
                'nik_lansia' => 'nullable|string|size:16|unique:lansia,nik_lansia,' . $id,
                'nama_lengkap' => 'required|string|max:255',
                'tgl_lahir' => 'required|date|before:today',
                'tempat_lahir' => 'nullable|string|max:100',
                'jenis_kelamin' => 'required|in:L,P',
                'alamat_domisili' => 'nullable|string',
                'rt_rw' => 'nullable|string|max:10',
                'nama_kk' => 'nullable|string|max:255',
                'nama_wali' => 'nullable|string|max:255',
                'nik_wali' => 'nullable|string|size:16',
                'hp_kontak_wali' => 'nullable|string|max:20',
                'berat_badan' => 'nullable|numeric|min:30|max:150',
                'tinggi_badan' => 'nullable|numeric|min:100|max:200',
                'tekanan_darah' => 'nullable|string|regex:/^\d{2,3}\/\d{2,3}$/',
                'gula_darah' => 'nullable|numeric|min:50|max:500',
                'kolesterol' => 'nullable|numeric|min:100|max:400',
                'asam_urat' => 'nullable|numeric|min:1|max:15',
            ]);

            $lansia->update($validated);

            return response()->json([
                'success' => true,
                'data' => $lansia,
                'message' => 'Data lansia berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage (soft delete).
     */
    public function destroy($id)
    {
        try {
            $lansia = Lansia::findOrFail($id);
            $lansia->update(['is_deleted' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Data lansia berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }
    }

    /**
     * Calculate status kesehatan
     */
    private function calculateStatusKesehatan($request)
    {
        $issues = [];

        // Check tekanan darah
        if ($request->filled('tekanan_darah')) {
            list($sistolik, $diastolik) = explode('/', $request->tekanan_darah);
            if ($sistolik >= 140 || $diastolik >= 90) {
                $issues[] = 'Hipertensi';
            }
        }

        // Check gula darah
        if ($request->filled('gula_darah') && $request->gula_darah >= 126) {
            $issues[] = 'Diabetes';
        }

        // Check kolesterol
        if ($request->filled('kolesterol') && $request->kolesterol >= 240) {
            $issues[] = 'Kolesterol Tinggi';
        }

        // Check asam urat
        if ($request->filled('asam_urat')) {
            $jk = $request->jenis_kelamin;
            if (($jk == 'L' && $request->asam_urat >= 7) || ($jk == 'P' && $request->asam_urat >= 6)) {
                $issues[] = 'Asam Urat Tinggi';
            }
        }

        return empty($issues) ? 'Sehat' : implode(', ', $issues);
    }

    /**
     * Calculate status kesehatan detail (JSON)
     */
    private function calculateStatusKesehatanDetail($request)
    {
        $detail = [];

        // Tekanan darah
        if ($request->filled('tekanan_darah')) {
            list($sistolik, $diastolik) = explode('/', $request->tekanan_darah);
            $kategori = 'Normal';
            if ($sistolik >= 180 || $diastolik >= 120) $kategori = 'Krisis Hipertensi';
            elseif ($sistolik >= 140 || $diastolik >= 90) $kategori = 'Hipertensi Stage 2';
            elseif ($sistolik >= 130 || $diastolik >= 80) $kategori = 'Hipertensi Stage 1';
            elseif ($sistolik >= 120) $kategori = 'Elevated';

            $detail['tekanan_darah'] = [
                'nilai' => $request->tekanan_darah,
                'kategori' => $kategori
            ];
        }

        // Gula darah
        if ($request->filled('gula_darah')) {
            $kategori = 'Normal';
            if ($request->gula_darah >= 126) $kategori = 'Diabetes';
            elseif ($request->gula_darah >= 100) $kategori = 'Prediabetes';

            $detail['gula_darah'] = [
                'nilai' => $request->gula_darah,
                'kategori' => $kategori
            ];
        }

        // Kolesterol
        if ($request->filled('kolesterol')) {
            $kategori = 'Normal';
            if ($request->kolesterol >= 240) $kategori = 'Tinggi';
            elseif ($request->kolesterol >= 200) $kategori = 'Borderline High';

            $detail['kolesterol'] = [
                'nilai' => $request->kolesterol,
                'kategori' => $kategori
            ];
        }

        // Asam urat
        if ($request->filled('asam_urat')) {
            $jk = $request->jenis_kelamin;
            $kategori = 'Normal';
            if (($jk == 'L' && $request->asam_urat >= 7) || ($jk == 'P' && $request->asam_urat >= 6)) {
                $kategori = 'Tinggi';
            }

            $detail['asam_urat'] = [
                'nilai' => $request->asam_urat,
                'kategori' => $kategori
            ];
        }

        // BMI
        if ($request->filled(['berat_badan', 'tinggi_badan'])) {
            $bmi = $request->berat_badan / (($request->tinggi_badan / 100) ** 2);
            $kategori = 'Normal';
            if ($bmi >= 30) $kategori = 'Obesitas';
            elseif ($bmi >= 25) $kategori = 'Overweight';
            elseif ($bmi < 18.5) $kategori = 'Underweight';

            $detail['bmi'] = [
                'nilai' => round($bmi, 2),
                'kategori' => $kategori
            ];
        }

        return json_encode($detail);
    }
}
