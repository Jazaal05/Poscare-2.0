<?php

namespace App\Http\Controllers\Examples;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\Api\StoreAnakRequest;
use App\Http\Resources\AnakResource;
use App\Models\Anak;
use Illuminate\Http\Request;

/**
 * CONTOH IMPLEMENTASI CONTROLLER DENGAN ROLE-BASED ACCESS
 * 
 * Controller ini menunjukkan cara mengimplementasikan:
 * 1. Filter data berdasarkan role (kader vs orangtua)
 * 2. Menggunakan API Resources untuk response
 * 3. Menggunakan Base API Controller untuk response konsisten
 * 4. Menggunakan Form Request untuk validation
 */
class AnakControllerExample extends BaseApiController
{
    /**
     * List Data Anak
     * 
     * KADER: Bisa lihat semua data anak
     * ORANGTUA: Hanya bisa lihat data anak sendiri
     */
    public function list(Request $request)
    {
        $user = auth()->user();
        $search = $request->get('q', '');
        
        // Query dasar
        $query = Anak::aktif()->with('user');
        
        // FILTER BERDASARKAN ROLE
        if ($user->role === 'kader') {
            // Kader bisa lihat semua anak
            if ($search) {
                $query->where('nama_anak', 'like', "%{$search}%");
            }
        } else {
            // Orangtua hanya bisa lihat anak sendiri
            $query->where('user_id', $user->id);
            
            if ($search) {
                $query->where('nama_anak', 'like', "%{$search}%");
            }
        }
        
        $anak = $query->orderBy('created_at', 'desc')->get();
        
        // Return dengan API Resource untuk format konsisten
        return $this->successResponse([
            'data' => AnakResource::collection($anak),
            'total' => $anak->count(),
            'user_role' => $user->role,
        ], 'Data anak berhasil diambil');
    }
    
    /**
     * Show Detail Anak
     * 
     * KADER: Bisa lihat detail anak siapa saja
     * ORANGTUA: Hanya bisa lihat detail anak sendiri
     */
    public function show($id)
    {
        $user = auth()->user();
        
        // Query dasar
        $query = Anak::aktif()->with(['user', 'riwayatPengukuran', 'imunisasi']);
        
        // FILTER BERDASARKAN ROLE
        if ($user->role !== 'kader') {
            // Orangtua hanya bisa lihat anak sendiri
            $query->where('user_id', $user->id);
        }
        
        $anak = $query->find($id);
        
        if (!$anak) {
            return $this->notFoundResponse('Data anak tidak ditemukan atau Anda tidak memiliki akses');
        }
        
        return $this->successWithResource(
            new AnakResource($anak),
            'Detail anak berhasil diambil'
        );
    }
    
    /**
     * Store Data Anak Baru
     * 
     * HANYA KADER: Route ini sudah diproteksi dengan middleware role:kader
     * Orangtua tidak bisa akses endpoint ini
     */
    public function store(StoreAnakRequest $request)
    {
        try {
            // Data sudah tervalidasi dari StoreAnakRequest
            $data = $request->validated();
            
            // Jika user_id tidak diisi, gunakan user yang login
            if (!isset($data['user_id'])) {
                $data['user_id'] = auth()->id();
            }
            
            $anak = Anak::create($data);
            
            // Trigger event jika ada
            // event(new AnakCreated($anak));
            
            return $this->successWithResource(
                new AnakResource($anak->load('user')),
                'Data anak berhasil ditambahkan',
                201
            );
            
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Gagal menambahkan data anak: ' . $e->getMessage(),
                500
            );
        }
    }
    
    /**
     * Update Data Anak
     * 
     * HANYA KADER: Route ini sudah diproteksi dengan middleware role:kader
     */
    public function update(StoreAnakRequest $request, $id)
    {
        try {
            $anak = Anak::aktif()->find($id);
            
            if (!$anak) {
                return $this->notFoundResponse('Data anak tidak ditemukan');
            }
            
            $anak->update($request->validated());
            
            return $this->successWithResource(
                new AnakResource($anak->load('user')),
                'Data anak berhasil diperbarui'
            );
            
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Gagal memperbarui data anak: ' . $e->getMessage(),
                500
            );
        }
    }
    
    /**
     * Delete Data Anak (Soft Delete)
     * 
     * HANYA KADER: Route ini sudah diproteksi dengan middleware role:kader
     */
    public function destroy($id)
    {
        try {
            $anak = Anak::aktif()->find($id);
            
            if (!$anak) {
                return $this->notFoundResponse('Data anak tidak ditemukan');
            }
            
            // Soft delete dengan flag is_deleted
            $anak->update(['is_deleted' => true]);
            
            return $this->successResponse(
                null,
                'Data anak berhasil dihapus'
            );
            
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Gagal menghapus data anak: ' . $e->getMessage(),
                500
            );
        }
    }
    
    /**
     * Get My Children (Khusus Orangtua)
     * 
     * Endpoint khusus untuk orangtua melihat daftar anak sendiri
     */
    public function myChildren(Request $request)
    {
        $user = auth()->user();
        
        // Hanya ambil anak dari user yang login
        $anak = Anak::aktif()
            ->where('user_id', $user->id)
            ->with(['riwayatPengukuran' => function($q) {
                $q->latest()->limit(5); // 5 pengukuran terakhir
            }, 'imunisasi'])
            ->orderBy('tanggal_lahir', 'desc')
            ->get();
        
        return $this->successResponse([
            'data' => AnakResource::collection($anak),
            'total' => $anak->count(),
        ], 'Data anak Anda berhasil diambil');
    }
}