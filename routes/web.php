<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ResetPasswordController;
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
use App\Http\Controllers\Lansia\LansiaDataController;
use App\Http\Controllers\Lansia\LansiaKunjunganController;
use App\Http\Controllers\Lansia\LansiaLaporanController;
use App\Http\Controllers\Lansia\LansiaJadwalController;
use App\Http\Controllers\Lansia\LansiaEdukasiController;
use App\Http\Controllers\Lansia\LansiaPengaturanController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/
// Landing page — bisa diakses siapa saja (guest & auth)
Route::get('/', fn() => view('landing'))->name('landing');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('auth.login');
    Route::post('/reset-password/request-otp', [ResetPasswordController::class, 'requestOtp'])->name('auth.request-otp');
    Route::post('/reset-password/reset', [ResetPasswordController::class, 'resetWithOtp'])->name('auth.reset-password');
});

/*
|--------------------------------------------------------------------------
| PROTECTED ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/home', fn() => redirect()->route('dashboard'));

    // ── Dashboard ──────────────────────────────────────────────────────
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/stats', [DashboardController::class, 'stats'])->name('dashboard.stats');

    // ── Data Anak ──────────────────────────────────────────────────────
    Route::get('/data-anak', [AnakController::class, 'index'])->name('anak.index');
    Route::get('/api/anak', [AnakController::class, 'list'])->name('anak.list');
    Route::get('/api/anak/{id}', [AnakController::class, 'show'])->name('anak.show');
    Route::post('/api/anak/registrasi', [AnakController::class, 'store'])->name('anak.store');
    Route::post('/api/anak/tambah', [AnakController::class, 'storeTambah'])->name('anak.storeTambah');
    Route::put('/api/anak/{id}', [AnakController::class, 'update'])->name('anak.update');
    Route::delete('/api/anak/{id}', [AnakController::class, 'destroy'])->name('anak.destroy');
    Route::post('/api/parents/search', [AnakController::class, 'parentsList'])->name('anak.parents');
    Route::get('/api/anak/children-count', [AnakController::class, 'getChildrenCount'])->name('anak.childrenCount');

    // ── Pengukuran / Grafik Pertumbuhan ────────────────────────────────
    Route::get('/grafik-pertumbuhan/{id}', [PengukuranController::class, 'grafik'])->name('pengukuran.grafik');
    Route::post('/api/pengukuran', [PengukuranController::class, 'store'])->name('pengukuran.store');
    Route::get('/api/pengukuran/{anakId}/riwayat', [PengukuranController::class, 'riwayat'])->name('pengukuran.riwayat');

    // ── Imunisasi ──────────────────────────────────────────────────────
    Route::get('/imunisasi', [ImunisasiController::class, 'index'])->name('imunisasi.index');
    Route::get('/api/imunisasi', [ImunisasiController::class, 'list'])->name('imunisasi.list');
    Route::post('/api/imunisasi/tandai', [ImunisasiController::class, 'tandai'])->name('imunisasi.tandai');
    Route::post('/api/imunisasi/undo', [ImunisasiController::class, 'undo'])->name('imunisasi.undo');

    // ── Jadwal ─────────────────────────────────────────────────────────
    Route::get('/jadwal', [JadwalController::class, 'index'])->name('jadwal.index');
    Route::get('/api/jadwal', [JadwalController::class, 'list'])->name('jadwal.list');
    Route::get('/api/jadwal/{id}', [JadwalController::class, 'show'])->name('jadwal.show');
    Route::post('/api/jadwal', [JadwalController::class, 'store'])->name('jadwal.store');
    Route::put('/api/jadwal/{id}', [JadwalController::class, 'update'])->name('jadwal.update');
    Route::delete('/api/jadwal/{id}', [JadwalController::class, 'destroy'])->name('jadwal.destroy');
    Route::post('/api/jadwal/post-mobile', [JadwalController::class, 'postMobile'])->name('jadwal.postMobile');

    // ── Laporan ────────────────────────────────────────────────────────
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/api/laporan', [LaporanController::class, 'list'])->name('laporan.list');
    Route::post('/api/laporan/export', [LaporanController::class, 'exportExcel'])->name('laporan.export');

    // ── Edukasi ────────────────────────────────────────────────────────
    Route::get('/edukasi', [EdukasiController::class, 'index'])->name('edukasi.index');
    Route::get('/api/edukasi', [EdukasiController::class, 'list'])->name('edukasi.list');
    Route::post('/api/edukasi', [EdukasiController::class, 'store'])->name('edukasi.store');
    Route::get('/api/edukasi/{id}', [EdukasiController::class, 'show'])->name('edukasi.show');
    Route::put('/api/edukasi/{id}', [EdukasiController::class, 'update'])->name('edukasi.update');
    Route::post('/api/edukasi/fetch-info', [EdukasiController::class, 'fetchInfo'])->name('edukasi.fetchInfo');
    Route::delete('/api/edukasi/{id}', [EdukasiController::class, 'destroy'])->name('edukasi.destroy');

    // ── Pengaturan ─────────────────────────────────────────────────────
    Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan.index');
    Route::get('/api/pengaturan/current-user', [PengaturanController::class, 'currentUser'])->name('pengaturan.currentUser');
    Route::put('/api/pengaturan/profil', [PengaturanController::class, 'updateProfil'])->name('pengaturan.profil');
    Route::put('/api/pengaturan/password', [PengaturanController::class, 'gantiPassword'])->name('pengaturan.password');
    Route::get('/api/pengaturan/users', [PengaturanController::class, 'usersList'])->name('pengaturan.users');
    Route::delete('/api/pengaturan/users/{id}', [PengaturanController::class, 'deleteUser'])->name('pengaturan.deleteUser');
    Route::post('/api/pengaturan/request-otp', [PengaturanController::class, 'requestOtpGantiPassword'])->name('pengaturan.requestOtp');
    Route::post('/api/pengaturan/verifikasi-otp', [PengaturanController::class, 'verifikasiOtpGantiPassword'])->name('pengaturan.verifikasiOtp');

    // ── Master Vaksin ──────────────────────────────────────────────────────
    Route::get('/master-vaksin', fn() => redirect()->route('imunisasi.index', ['tab' => 'vaksin']))->name('vaksin.index');
    Route::get('/api/vaksin', [VaksinController::class, 'list'])->name('vaksin.list');
    Route::post('/api/vaksin', [VaksinController::class, 'store'])->name('vaksin.store');
    Route::put('/api/vaksin/{id}', [VaksinController::class, 'update'])->name('vaksin.update');
    Route::delete('/api/vaksin/{id}', [VaksinController::class, 'destroy'])->name('vaksin.destroy');

    // ══════════════════════════════════════════════════════════════════════
    // LANSIA ROUTES
    // ══════════════════════════════════════════════════════════════════════
    Route::prefix('lansia')->name('lansia.')->group(function () {

        // ── Dashboard ──────────────────────────────────────────────
        Route::get('/dashboard', [LansiaDashboardController::class, 'index'])->name('dashboard');
        Route::get('/api/dashboard/stats', [LansiaDashboardController::class, 'stats'])->name('dashboard.stats');
        Route::get('/api/stats', [LansiaDashboardController::class, 'stats'])->name('stats');

        // ── API Lansia (untuk autocomplete kunjungan) ──────────────
        Route::get('/api/lansia', [LansiaDataController::class, 'list'])->name('lansia.list');
        Route::post('/api/lansia', [LansiaDataController::class, 'store'])->name('lansia.store');

        // ── Jadwal ─────────────────────────────────────────────────
        Route::get('/jadwal', [LansiaJadwalController::class, 'index'])->name('jadwal.index');

        // ── Edukasi ────────────────────────────────────────────────
        Route::get('/edukasi', [LansiaEdukasiController::class, 'index'])->name('edukasi.index');

        Route::get('/kunjungan', [LansiaKunjunganController::class, 'index'])->name('kunjungan.index');
        Route::get('/api/kunjungan', [LansiaKunjunganController::class, 'list'])->name('kunjungan.list');
        Route::post('/api/kunjungan', [LansiaKunjunganController::class, 'store'])->name('kunjungan.store');
        Route::put('/api/kunjungan/{id}', [LansiaKunjunganController::class, 'update'])->name('kunjungan.update');
        Route::delete('/api/kunjungan/{id}', [LansiaKunjunganController::class, 'destroy'])->name('kunjungan.destroy');
        Route::get('/api/kunjungan/{lansiaId}/riwayat', [LansiaKunjunganController::class, 'riwayat'])->name('kunjungan.riwayat');

        // ── Laporan ────────────────────────────────────────────────
        Route::get('/laporan', [LansiaLaporanController::class, 'index'])->name('laporan.index');
        Route::get('/api/laporan', [LansiaLaporanController::class, 'list'])->name('laporan.list');
        Route::post('/api/laporan/export', [LansiaLaporanController::class, 'exportExcel'])->name('laporan.export');

        // ── Pengaturan (pakai yang sama) ───────────────────────────
        Route::get('/pengaturan', [LansiaPengaturanController::class, 'index'])->name('pengaturan.index');
    });
});
