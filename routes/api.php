<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthApiController;
use App\Http\Controllers\AnakController;
use App\Http\Controllers\PengukuranController;
use App\Http\Controllers\ImunisasiController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\EdukasiController;
use App\Http\Controllers\VaksinController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\Lansia\LansiaDashboardController;
use App\Http\Controllers\Lansia\LansiaKunjunganController;
use App\Http\Controllers\Lansia\LansiaLaporanController;
use App\Http\Controllers\Lansia\LansiaJadwalController;
use App\Http\Controllers\Lansia\LansiaEdukasiController;
use App\Http\Controllers\Lansia\LansiaPengaturanController;
use App\Http\Controllers\Api\MobileController;

/*
|--------------------------------------------------------------------------
| API Routes - PosCare Mobile
|--------------------------------------------------------------------------
| Base URL: https://poscare.pbltifnganjuk.com/api/
|--------------------------------------------------------------------------
*/

// ═══════════════════════════════════════════════════════════════════════
// AUTH - Publik (tidak butuh token)
// ═══════════════════════════════════════════════════════════════════════
Route::prefix('auth')->group(function () {
    Route::post('/login',           [AuthApiController::class, 'login']);
    Route::post('/register',        [AuthApiController::class, 'register']);
    Route::post('/forgot-password', [AuthApiController::class, 'forgotPassword']);
    Route::post('/reset-password',  [AuthApiController::class, 'resetPassword']);
});

// ═══════════════════════════════════════════════════════════════════════
// PROTECTED ROUTES - Butuh token Sanctum
// ═══════════════════════════════════════════════════════════════════════
Route::middleware('auth:sanctum')->group(function () {

    // ── Auth ──────────────────────────────────────────────────────────
    Route::post('/auth/logout', [AuthApiController::class, 'logout']);
    Route::get('/auth/me',      [AuthApiController::class, 'me']);

    // ── Profil & Akun ─────────────────────────────────────────────────
    Route::get('/profile',          [MobileController::class, 'getProfile']);
    Route::post('/profile/update',  [MobileController::class, 'updateProfile']);
    Route::put('/password',         [MobileController::class, 'changePassword']);
    Route::post('/fcm-token',       [MobileController::class, 'saveFcmToken']);

    // ── Notifikasi ────────────────────────────────────────────────────
    Route::get('/notifikasi',             [MobileController::class, 'getNotifikasi']);
    Route::get('/notifikasi/unread',      [MobileController::class, 'unreadCount']);
    Route::post('/notifikasi/mark-read',  [MobileController::class, 'markRead']);

    // ── Data BBU WHO (untuk grafik KMS) ───────────────────────────────
    Route::get('/bbu', [MobileController::class, 'getBbu']);

    // ═══════════════════════════════════════════════════════════════════
    // MODUL BALITA — orangtua & kader
    // ═══════════════════════════════════════════════════════════════════
    Route::middleware('role:orangtua,kader')->group(function () {

        // Data Anak
        Route::get('/anak',      [AnakController::class, 'list']);
        Route::get('/anak/{id}', [AnakController::class, 'show']);

        // Pengukuran / Riwayat Pertumbuhan
        Route::get('/pengukuran/{anakId}/riwayat', [PengukuranController::class, 'riwayat']);
        Route::post('/pengukuran',                 [PengukuranController::class, 'store']);

        // Vaksin & Imunisasi
        Route::get('/vaksin',          [VaksinController::class, 'list']);
        Route::get('/vaksin/count',    [MobileController::class, 'getVaccinesCount']);
        Route::get('/vaksin/next',     [MobileController::class, 'getNextVaccine']);
        Route::get('/vaksin/history',  [MobileController::class, 'getVaccineHistory']);

        // Jadwal Posyandu Balita
        Route::get('/jadwal', [JadwalController::class, 'list']);

        // Edukasi Balita
        Route::get('/edukasi',      [EdukasiController::class, 'list']);
        Route::get('/edukasi/{id}', [EdukasiController::class, 'show']);
    });

    // ═══════════════════════════════════════════════════════════════════
    // MODUL LANSIA — wali_lansia & kader
    // ═══════════════════════════════════════════════════════════════════
    Route::middleware('role:wali_lansia,kader')->prefix('lansia')->group(function () {

        // Riwayat Kunjungan — HARUS di atas /{id} agar tidak tertimpa
        Route::get('/{lansiaId}/kunjungan', [LansiaKunjunganController::class, 'riwayat']);

        // Jadwal Lansia
        Route::get('/jadwal', [LansiaJadwalController::class, 'list']);

        // Edukasi Lansia
        Route::get('/edukasi',      [LansiaEdukasiController::class, 'list']);
        Route::get('/edukasi/{id}', [LansiaEdukasiController::class, 'show']);

        // Dashboard Stats
        Route::get('/dashboard/stats', [LansiaDashboardController::class, 'stats']);

        // Data Lansia — generic routes TERAKHIR
        Route::get('/',      [LansiaKunjunganController::class, 'list']);
        Route::get('/{id}',  [LansiaKunjunganController::class, 'show']);
    });

    // ═══════════════════════════════════════════════════════════════════
    // KADER ONLY — Full CRUD (web dashboard & mobile kader)
    // ═══════════════════════════════════════════════════════════════════
    Route::middleware('role:kader')->group(function () {

        // Dashboard
        Route::get('/dashboard/stats', [DashboardController::class, 'stats']);

        // Anak - CRUD
        Route::post('/anak/registrasi', [AnakController::class, 'store']);
        Route::put('/anak/{id}',        [AnakController::class, 'update']);
        Route::delete('/anak/{id}',     [AnakController::class, 'destroy']);

        // Pengukuran - Delete
        Route::delete('/pengukuran/{id}', [PengukuranController::class, 'destroy']);

        // Imunisasi - Tandai & Undo
        Route::post('/imunisasi/tandai', [ImunisasiController::class, 'tandai']);
        Route::post('/imunisasi/undo',   [ImunisasiController::class, 'undo']);

        // Vaksin - CRUD
        Route::post('/vaksin',       [VaksinController::class, 'store']);
        Route::put('/vaksin/{id}',   [VaksinController::class, 'update']);
        Route::delete('/vaksin/{id}',[VaksinController::class, 'destroy']);

        // Jadwal - CRUD
        Route::post('/jadwal',              [JadwalController::class, 'store']);
        Route::put('/jadwal/{id}',          [JadwalController::class, 'update']);
        Route::delete('/jadwal/{id}',       [JadwalController::class, 'destroy']);
        Route::patch('/jadwal/{id}/status', [JadwalController::class, 'updateStatus']);

        // Edukasi - CRUD
        Route::post('/edukasi',       [EdukasiController::class, 'store']);
        Route::put('/edukasi/{id}',   [EdukasiController::class, 'update']);
        Route::delete('/edukasi/{id}',[EdukasiController::class, 'destroy']);

        // Laporan
        Route::get('/laporan',              [LaporanController::class, 'list']);
        Route::get('/laporan/export-excel', [LaporanController::class, 'exportExcel']);

        // Lansia - CRUD (kader)
        Route::prefix('lansia')->group(function () {
            Route::post('/kunjungan',       [LansiaKunjunganController::class, 'store']);
            Route::put('/kunjungan/{id}',   [LansiaKunjunganController::class, 'update']);
            Route::delete('/kunjungan/{id}',[LansiaKunjunganController::class, 'destroy']);

            Route::post('/jadwal',              [LansiaJadwalController::class, 'store']);
            Route::put('/jadwal/{id}',          [LansiaJadwalController::class, 'update']);
            Route::delete('/jadwal/{id}',       [LansiaJadwalController::class, 'destroy']);
            Route::patch('/jadwal/{id}/status', [LansiaJadwalController::class, 'updateStatus']);

            Route::post('/edukasi',       [LansiaEdukasiController::class, 'store']);
            Route::put('/edukasi/{id}',   [LansiaEdukasiController::class, 'update']);
            Route::delete('/edukasi/{id}',[LansiaEdukasiController::class, 'destroy']);

            Route::get('/laporan/stats',   [LansiaLaporanController::class, 'stats']);
            Route::get('/laporan/export',  [LansiaLaporanController::class, 'exportExcel']);
        });
    });
});
