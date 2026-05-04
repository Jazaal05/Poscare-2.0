<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AnakController;
use App\Http\Controllers\PengukuranController;
use App\Http\Controllers\ImunisasiController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\EdukasiController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\VaksinController;
use App\Http\Controllers\Lansia\LansiaDashboardController;
use App\Http\Controllers\Lansia\LansiaKunjunganController;
use App\Http\Controllers\Lansia\LansiaLaporanController;
use App\Http\Controllers\Lansia\LansiaJadwalController;
use App\Http\Controllers\Lansia\LansiaEdukasiController;
use App\Http\Controllers\Lansia\LansiaPengaturanController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| API V1 Routes - Protected by Sanctum Auth
|--------------------------------------------------------------------------
| API ini digunakan oleh aplikasi mobile
| - Kader: Full access (CRUD semua data)
| - Orangtua: Read-only access (hanya data anak sendiri)
|--------------------------------------------------------------------------
*/
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    
    // ═══════════════════════════════════════════════════════════════════
    // ROUTES KHUSUS UNTUK KADER (ADMIN) - FULL ACCESS
    // ═══════════════════════════════════════════════════════════════════
    Route::middleware('role:kader')->group(function () {
        
        // ── Dashboard Stats (hanya kader) ──────────────────────────────
        Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
        
        // ── Data Anak - Full CRUD ──────────────────────────────────────
        Route::post('/anak/store', [AnakController::class, 'store']);
        Route::post('/anak/registrasi', [AnakController::class, 'registrasi']);
        Route::put('/anak/{id}', [AnakController::class, 'update']);
        Route::delete('/anak/{id}', [AnakController::class, 'destroy']);
        Route::get('/anak/{id}/export-pdf', [AnakController::class, 'exportPdf']);
        
        // ── Pengukuran - Input & Delete ────────────────────────────────
        Route::post('/pengukuran/store', [PengukuranController::class, 'store']);
        Route::delete('/pengukuran/{id}', [PengukuranController::class, 'destroy']);
        
        // ── Imunisasi - Input & Delete ─────────────────────────────────
        Route::post('/imunisasi/store', [ImunisasiController::class, 'store']);
        Route::delete('/imunisasi/{id}', [ImunisasiController::class, 'destroy']);
        
        // ── Master Vaksin - Full CRUD ──────────────────────────────────
        Route::post('/vaksin/store', [VaksinController::class, 'store']);
        Route::put('/vaksin/{id}', [VaksinController::class, 'update']);
        Route::delete('/vaksin/{id}', [VaksinController::class, 'destroy']);
        
        // ── Jadwal - Full CRUD ─────────────────────────────────────────
        Route::post('/jadwal/store', [JadwalController::class, 'store']);
        Route::put('/jadwal/{id}', [JadwalController::class, 'update']);
        Route::delete('/jadwal/{id}', [JadwalController::class, 'destroy']);
        Route::patch('/jadwal/{id}/status', [JadwalController::class, 'updateStatus']);
        
        // ── Laporan - Full CRUD ────────────────────────────────────────
        Route::post('/laporan/store', [LaporanController::class, 'store']);
        Route::put('/laporan/{id}', [LaporanController::class, 'update']);
        Route::delete('/laporan/{id}', [LaporanController::class, 'destroy']);
        Route::get('/laporan/export-excel', [LaporanController::class, 'exportExcel']);
        
        // ── Edukasi - Full CRUD ────────────────────────────────────────
        Route::post('/edukasi/store', [EdukasiController::class, 'store']);
        Route::put('/edukasi/{id}', [EdukasiController::class, 'update']);
        Route::delete('/edukasi/{id}', [EdukasiController::class, 'destroy']);
        
        // ── Pengaturan - Admin Functions ───────────────────────────────
        Route::post('/pengaturan/upload-avatar', [PengaturanController::class, 'uploadAvatar']);
        
        // ── LANSIA MODULE - FULL ACCESS ────────────────────────────────
        Route::prefix('lansia')->group(function () {
            
            // Dashboard Lansia
            Route::get('/dashboard/stats', [LansiaDashboardController::class, 'stats']);
            
            // Data Lansia - Full CRUD
            Route::post('/data/store', [LansiaKunjunganController::class, 'store']);
            Route::put('/data/{id}', [LansiaKunjunganController::class, 'update']);
            Route::delete('/data/{id}', [LansiaKunjunganController::class, 'destroy']);
            Route::get('/data/{id}/export-pdf', [LansiaKunjunganController::class, 'show']);
            
            // Kunjungan Lansia - Full CRUD
            Route::post('/kunjungan/store', [LansiaKunjunganController::class, 'store']);
            Route::put('/kunjungan/{id}', [LansiaKunjunganController::class, 'update']);
            Route::delete('/kunjungan/{id}', [LansiaKunjunganController::class, 'destroy']);
            
            // Laporan Lansia - Full CRUD
            Route::post('/laporan/store', [LansiaLaporanController::class, 'store']);
            Route::put('/laporan/{id}', [LansiaLaporanController::class, 'update']);
            Route::delete('/laporan/{id}', [LansiaLaporanController::class, 'destroy']);
            Route::get('/laporan/export-excel', [LansiaLaporanController::class, 'exportExcel']);
            
            // Jadwal Lansia - Full CRUD
            Route::post('/jadwal/store', [LansiaJadwalController::class, 'store']);
            Route::put('/jadwal/{id}', [LansiaJadwalController::class, 'update']);
            Route::delete('/jadwal/{id}', [LansiaJadwalController::class, 'destroy']);
            Route::patch('/jadwal/{id}/status', [LansiaJadwalController::class, 'updateStatus']);
            
            // Edukasi Lansia - Full CRUD
            Route::post('/edukasi/store', [LansiaEdukasiController::class, 'store']);
            Route::put('/edukasi/{id}', [LansiaEdukasiController::class, 'update']);
            Route::delete('/edukasi/{id}', [LansiaEdukasiController::class, 'destroy']);
        });
    });
    
    // ═══════════════════════════════════════════════════════════════════
    // ROUTES UNTUK KADER DAN ORANGTUA - READ ACCESS
    // Orangtua hanya bisa lihat data anak sendiri (filter di controller)
    // ═══════════════════════════════════════════════════════════════════
    Route::middleware('role:kader,orangtua')->group(function () {
        
        // ── Data Anak - Read Only ──────────────────────────────────────
        // Controller akan filter: kader lihat semua, orangtua hanya anak sendiri
        Route::get('/anak/list', [AnakController::class, 'list']);
        Route::get('/anak/{id}', [AnakController::class, 'show']);
        
        // ── Pengukuran - Read Only ─────────────────────────────────────
        Route::get('/pengukuran/anak/{anakId}/list', [PengukuranController::class, 'list']);
        Route::get('/pengukuran/anak/{anakId}/grafik-data', [PengukuranController::class, 'grafikData']);
        
        // ── Imunisasi - Read Only ──────────────────────────────────────
        Route::get('/imunisasi/anak/{anakId}/list', [ImunisasiController::class, 'list']);
        
        // ── Vaksin - Read Only ─────────────────────────────────────────
        Route::get('/vaksin/list', [VaksinController::class, 'list']);
        
        // ── Jadwal - Read Only ─────────────────────────────────────────
        Route::get('/jadwal/list', [JadwalController::class, 'list']);
        Route::get('/jadwal/{id}', [JadwalController::class, 'show']);
        
        // ── Laporan - Read Only ────────────────────────────────────────
        Route::get('/laporan/list', [LaporanController::class, 'list']);
        
        // ── Edukasi - Read Only ────────────────────────────────────────
        Route::get('/edukasi/list', [EdukasiController::class, 'list']);
        Route::get('/edukasi/{id}', [EdukasiController::class, 'show']);
        
        // ── Profile Management (Semua User) ────────────────────────────
        Route::get('/pengaturan/profile', [PengaturanController::class, 'getProfile']);
        Route::put('/pengaturan/profile', [PengaturanController::class, 'updateProfile']);
        Route::put('/pengaturan/password', [PengaturanController::class, 'updatePassword']);
        
        // ── LANSIA MODULE - READ ONLY ──────────────────────────────────
        Route::prefix('lansia')->group(function () {
            
            // Data Lansia - Read Only
            Route::get('/data/list', [LansiaKunjunganController::class, 'list']);
            Route::get('/data/{id}', [LansiaKunjunganController::class, 'show']);
            
            // Kunjungan Lansia - Read Only
            Route::get('/kunjungan/list', [LansiaKunjunganController::class, 'list']);
            Route::get('/kunjungan/{id}', [LansiaKunjunganController::class, 'show']);
            Route::get('/kunjungan/lansia/{lansiaId}/riwayat', [LansiaKunjunganController::class, 'riwayat']);
            
            // Laporan Lansia - Read Only
            Route::get('/laporan/list', [LansiaLaporanController::class, 'list']);
            
            // Jadwal Lansia - Read Only
            Route::get('/jadwal/list', [LansiaJadwalController::class, 'list']);
            
            // Edukasi Lansia - Read Only
            Route::get('/edukasi/list', [LansiaEdukasiController::class, 'list']);
            
            // Pengaturan Lansia
            Route::get('/pengaturan/profile', [LansiaPengaturanController::class, 'getProfile']);
            Route::put('/pengaturan/profile', [LansiaPengaturanController::class, 'updateProfile']);
            Route::put('/pengaturan/password', [LansiaPengaturanController::class, 'updatePassword']);
        });
    });
});
