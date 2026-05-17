<?php

namespace App\Http\Controllers;

use App\Models\Lansia;
use Illuminate\Http\Request;

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
                'user_id' => 'nullable|exists:users,id', // akun wali yang terhubung
            ]);

            $validated['dicatat_oleh'] = auth()->id();
            $validated['is_deleted'] = false;
            $validated['status_kesehatan'] = 'Sehat'; // Default
            $validated['tanggal_pemeriksaan_terakhir'] = now();

            // Jika ada user_id yang dikirim, kaitkan langsung
            // Jika tidak, coba cari user berdasarkan NIK wali
            if (empty($validated['user_id']) && !empty($validated['nik_wali'])) {
                $userWali = \App\Models\User::where('nik', $validated['nik_wali'])->first();
                if ($userWali) {
                    $validated['user_id'] = $userWali->id;
                }
            }

            $lansia = Lansia::create($validated);

            // Update role user jika perlu
            if (!empty($validated['user_id'])) {
                $userWali = \App\Models\User::find($validated['user_id']);
                if ($userWali) {
                    // Jika user sudah punya balita (orangtua), upgrade ke orangtua_lansia
                    if ($userWali->role === 'orangtua') {
                        $userWali->update(['role' => 'orangtua_lansia']);
                    }
                    // Jika user belum punya role apapun atau role default, set wali_lansia
                    elseif (!in_array($userWali->role, ['orangtua_lansia', 'wali_lansia', 'kader', 'admin'])) {
                        $userWali->update(['role' => 'wali_lansia']);
                    }
                }
            }

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
                    'tanggal_lahir' => $lansia->tgl_lahir ? \Carbon\Carbon::parse($lansia->tgl_lahir)->format('Y-m-d') : null,
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
                    'tanggal_pemeriksaan_terakhir' => $lansia->tanggal_pemeriksaan_terakhir?->format('d/m/Y'),
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

            // Update user_id dan role jika nik_wali berubah
            if (!empty($validated['nik_wali'])) {
                $userWali = \App\Models\User::where('nik', $validated['nik_wali'])->first();
                if ($userWali) {
                    // Kaitkan lansia ke user
                    $lansia->update(['user_id' => $userWali->id]);
                    // Update role user
                    if ($userWali->role === 'orangtua') {
                        $userWali->update(['role' => 'orangtua_lansia']);
                    } elseif (!in_array($userWali->role, ['orangtua_lansia', 'wali_lansia', 'kader', 'admin'])) {
                        $userWali->update(['role' => 'wali_lansia']);
                    }
                }
            }

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
