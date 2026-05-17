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
use App\Http\Controllers\LansiaController;
use App\Http\Controllers\Lansia\LansiaDashboardController;
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
Route::get('/', fn() => view('landing'))->name('landing');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('auth.login');
    Route::post('/reset-password/request-otp', [ResetPasswordController::class, 'requestOtp'])->name('auth.request-otp');
    Route::post('/reset-password/reset', [ResetPasswordController::class, 'resetWithOtp'])->name('auth.reset-password');
});

Route::middleware(['auth', 'kader.only'])->group(function () {

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/home', fn() => redirect()->route('dashboard'));

    // ── Dashboard ──────────────────────────────────────────────────────
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/stats', [DashboardController::class, 'stats'])->name('dashboard.stats');

    // ── Data Anak ──────────────────────────────────────────────────────
    Route::get('/data-anak', [AnakController::class, 'index'])->name('anak.index');
    Route::get('/web/anak', [AnakController::class, 'list'])->name('anak.list');
    Route::get('/web/anak/children-count', [AnakController::class, 'getChildrenCount'])->name('anak.childrenCount');
    Route::get('/web/anak/{id}', [AnakController::class, 'show'])->name('anak.show');
    Route::post('/web/anak/registrasi', [AnakController::class, 'store'])->name('anak.store');
    Route::post('/web/anak/tambah', [AnakController::class, 'storeTambah'])->name('anak.storeTambah');
    Route::put('/web/anak/{id}', [AnakController::class, 'update'])->name('anak.update');
    Route::delete('/web/anak/{id}', [AnakController::class, 'destroy'])->name('anak.destroy');
    Route::post('/web/parents/search', [AnakController::class, 'parentsList'])->name('anak.parents');

    // ── Pengukuran ────────────────────────────────────────────────────
    Route::get('/grafik-pertumbuhan/{id}', [PengukuranController::class, 'grafik'])->name('pengukuran.grafik');
    Route::post('/web/pengukuran', [PengukuranController::class, 'store'])->name('pengukuran.store');
    Route::get('/web/pengukuran/{anakId}/riwayat', [PengukuranController::class, 'riwayat'])->name('pengukuran.riwayat');
    Route::delete('/web/pengukuran/{id}', [PengukuranController::class, 'destroy'])->name('pengukuran.destroy');

    // ── Imunisasi ──────────────────────────────────────────────────────
    Route::get('/imunisasi', [ImunisasiController::class, 'index'])->name('imunisasi.index');
    Route::get('/web/imunisasi', [ImunisasiController::class, 'list'])->name('imunisasi.list');
    Route::post('/web/imunisasi/tandai', [ImunisasiController::class, 'tandai'])->name('imunisasi.tandai');
    Route::post('/web/imunisasi/undo', [ImunisasiController::class, 'undo'])->name('imunisasi.undo');

    // ── Jadwal ─────────────────────────────────────────────────────────
    Route::get('/jadwal', [JadwalController::class, 'index'])->name('jadwal.index');
    Route::get('/web/jadwal', [JadwalController::class, 'list'])->name('jadwal.list');
    Route::get('/web/jadwal/{id}', [JadwalController::class, 'show'])->name('jadwal.show');
    Route::post('/web/jadwal', [JadwalController::class, 'store'])->name('jadwal.store');
    Route::put('/web/jadwal/{id}', [JadwalController::class, 'update'])->name('jadwal.update');
    Route::delete('/web/jadwal/{id}', [JadwalController::class, 'destroy'])->name('jadwal.destroy');
    Route::post('/web/jadwal/post-mobile', [JadwalController::class, 'postMobile'])->name('jadwal.postMobile');

    // ── Laporan ────────────────────────────────────────────────────────
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/web/laporan', [LaporanController::class, 'list'])->name('laporan.list');
    Route::post('/web/laporan/export', [LaporanController::class, 'exportExcel'])->name('laporan.export');

    // ── Edukasi ────────────────────────────────────────────────────────
    Route::get('/edukasi', [EdukasiController::class, 'index'])->name('edukasi.index');
    Route::get('/web/edukasi', [EdukasiController::class, 'list'])->name('edukasi.list');
    Route::post('/web/edukasi', [EdukasiController::class, 'store'])->name('edukasi.store');
    Route::get('/web/edukasi/{id}', [EdukasiController::class, 'show'])->name('edukasi.show');
    Route::put('/web/edukasi/{id}', [EdukasiController::class, 'update'])->name('edukasi.update');
    Route::delete('/web/edukasi/{id}', [EdukasiController::class, 'destroy'])->name('edukasi.destroy');
    Route::post('/web/edukasi/fetch-info', [EdukasiController::class, 'fetchInfo'])->name('edukasi.fetchInfo');

    // ── Pengaturan ─────────────────────────────────────────────────────
    Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan.index');
    Route::get('/web/pengaturan/current-user', [PengaturanController::class, 'currentUser'])->name('pengaturan.currentUser');
    Route::put('/web/pengaturan/profil', [PengaturanController::class, 'updateProfil'])->name('pengaturan.profil');
    Route::put('/web/pengaturan/password', [PengaturanController::class, 'gantiPassword'])->name('pengaturan.password');
    Route::get('/web/pengaturan/users', [PengaturanController::class, 'usersList'])->name('pengaturan.users');
    Route::delete('/web/pengaturan/users/{id}', [PengaturanController::class, 'deleteUser'])->name('pengaturan.deleteUser');
    Route::post('/web/pengaturan/request-otp', [PengaturanController::class, 'requestOtpGantiPassword'])->name('pengaturan.requestOtp');
    Route::post('/web/pengaturan/verifikasi-otp', [PengaturanController::class, 'verifikasiOtpGantiPassword'])->name('pengaturan.verifikasiOtp');

    // ── Master Vaksin ──────────────────────────────────────────────────
    Route::get('/master-vaksin', fn() => redirect()->route('imunisasi.index', ['tab' => 'vaksin']))->name('vaksin.index');
    Route::get('/web/vaksin', [VaksinController::class, 'list'])->name('vaksin.list');
    Route::post('/web/vaksin', [VaksinController::class, 'store'])->name('vaksin.store');
    Route::put('/web/vaksin/{id}', [VaksinController::class, 'update'])->name('vaksin.update');
    Route::delete('/web/vaksin/{id}', [VaksinController::class, 'destroy'])->name('vaksin.destroy');

    // ══════════════════════════════════════════════════════════════════════
    // LANSIA ROUTES
    // ══════════════════════════════════════════════════════════════════════
    Route::get('/lansia', function() {
        return redirect()->route('lansia.kunjungan.index');
    })->name('lansia.index');

    Route::get('/web/lansia/list', [LansiaController::class, 'list'])->name('lansia.list');
    Route::post('/lansia', [LansiaController::class, 'store'])->name('lansia.store');
    Route::get('/web/lansia/{id}', [LansiaController::class, 'show'])->name('lansia.show');
    Route::put('/web/lansia/{id}', [LansiaController::class, 'update'])->name('lansia.update');
    Route::delete('/web/lansia/{id}', [LansiaController::class, 'destroy'])->name('lansia.destroy');

    Route::prefix('lansia')->name('lansia.')->group(function () {

        // ── Dashboard ──────────────────────────────────────────────
        Route::get('/dashboard', [LansiaDashboardController::class, 'index'])->name('dashboard');
        Route::get('/web/dashboard/stats', [LansiaDashboardController::class, 'stats'])->name('dashboard.stats');
        Route::get('/web/dashboard/chart-distribusi-usia', [LansiaDashboardController::class, 'chartDistribusiUsia'])->name('dashboard.chart-distribusi-usia');
        Route::get('/web/dashboard/chart-kondisi-kesehatan', [LansiaDashboardController::class, 'chartKondisiKesehatan'])->name('dashboard.chart-kondisi-kesehatan');
        Route::get('/web/dashboard/chart-trend-kunjungan', [LansiaDashboardController::class, 'chartTrendKunjungan'])->name('dashboard.chart-trend-kunjungan');
        Route::get('/web/stats', [LansiaDashboardController::class, 'stats'])->name('stats');

        // ── Kunjungan ──────────────────────────────────────────────
        Route::get('/kunjungan', [LansiaKunjunganController::class, 'index'])->name('kunjungan.index');
        Route::get('/web/kunjungan', [LansiaKunjunganController::class, 'list'])->name('kunjungan.list');
        Route::post('/web/kunjungan', [LansiaKunjunganController::class, 'store'])->name('kunjungan.store');
        Route::post('/web/kunjungan-selanjutnya/{lansiaId}', [LansiaKunjunganController::class, 'kunjunganSelanjutnya'])->name('kunjungan.selanjutnya');
        Route::get('/web/kunjungan/{id}', [LansiaKunjunganController::class, 'show'])->name('kunjungan.show');
        Route::get('/web/riwayat-kunjungan/{lansiaId}', [LansiaKunjunganController::class, 'riwayat'])->name('kunjungan.riwayat');
        Route::put('/web/kunjungan/{id}', [LansiaKunjunganController::class, 'update'])->name('kunjungan.update');
        Route::delete('/web/kunjungan/{id}', [LansiaKunjunganController::class, 'destroy'])->name('kunjungan.destroy');

        // ── Jadwal ─────────────────────────────────────────────────
        Route::get('/jadwal', [LansiaJadwalController::class, 'index'])->name('jadwal.index');
        Route::get('/web/jadwal', [LansiaJadwalController::class, 'list'])->name('jadwal.list');
        Route::get('/web/jadwal/{id}', [LansiaJadwalController::class, 'show'])->name('jadwal.show');
        Route::post('/web/jadwal', [LansiaJadwalController::class, 'store'])->name('jadwal.store');
        Route::put('/web/jadwal/{id}', [LansiaJadwalController::class, 'update'])->name('jadwal.update');
        Route::delete('/web/jadwal/{id}', [LansiaJadwalController::class, 'destroy'])->name('jadwal.destroy');

        // ── Laporan ────────────────────────────────────────────────
        Route::get('/laporan', [LansiaLaporanController::class, 'index'])->name('laporan.index');
        Route::get('/web/laporan/stats', [LansiaLaporanController::class, 'stats'])->name('laporan.stats');
        Route::get('/web/laporan/export', [LansiaLaporanController::class, 'exportExcel'])->name('laporan.export');

        // ── Edukasi ────────────────────────────────────────────────
        Route::get('/edukasi', [LansiaEdukasiController::class, 'index'])->name('edukasi.index');
        Route::get('/web/edukasi', [LansiaEdukasiController::class, 'list'])->name('edukasi.list');
        Route::get('/web/edukasi/{id}', [LansiaEdukasiController::class, 'show'])->name('edukasi.show');
        Route::post('/web/edukasi', [LansiaEdukasiController::class, 'store'])->name('edukasi.store');
        Route::post('/web/edukasi/fetch-info', [LansiaEdukasiController::class, 'fetchInfo'])->name('edukasi.fetchInfo');
        Route::put('/web/edukasi/{id}', [LansiaEdukasiController::class, 'update'])->name('edukasi.update');
        Route::delete('/web/edukasi/{id}', [LansiaEdukasiController::class, 'destroy'])->name('edukasi.destroy');

        // ── Pengaturan ─────────────────────────────────────────────
        Route::get('/pengaturan', [LansiaPengaturanController::class, 'index'])->name('pengaturan.index');
        Route::get('/web/pengaturan/current-user', [LansiaPengaturanController::class, 'currentUser'])->name('pengaturan.currentUser');
        Route::put('/web/pengaturan/profil', [LansiaPengaturanController::class, 'updateProfil'])->name('pengaturan.profil');
        Route::put('/web/pengaturan/password', [LansiaPengaturanController::class, 'gantiPassword'])->name('pengaturan.password');
        Route::get('/web/pengaturan/users', [LansiaPengaturanController::class, 'usersList'])->name('pengaturan.users');
        Route::delete('/web/pengaturan/users/{id}', [LansiaPengaturanController::class, 'deleteUser'])->name('pengaturan.deleteUser');
        Route::post('/web/pengaturan/request-otp', [LansiaPengaturanController::class, 'requestOtpGantiPassword'])->name('pengaturan.requestOtp');
        Route::post('/web/pengaturan/verifikasi-otp', [LansiaPengaturanController::class, 'verifikasiOtpGantiPassword'])->name('pengaturan.verifikasiOtp');
    });
});
