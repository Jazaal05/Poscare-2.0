<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /**
     * Tampilkan halaman login
     * Menggantikan: index.php
     */
    public function showLogin()
    {
        // Kalau sudah login, langsung redirect ke dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Proses login
     * Menggantikan: api_web/auth.php
     * 
     * CATATAN UNTUK DEVELOPER:
     * - Saat development: Semua role bisa login untuk testing
     * - Saat production: Uncomment role check untuk restrict orangtua
     */
    public function login(LoginRequest $request)
    {
        $email    = $request->email;
        $password = $request->password;
        $remember = $request->boolean('remember'); // fitur "Ingat Saya"

        // Cari user berdasarkan email
        $user = User::where('email', $email)->first();

        // Cek user ada dan password cocok
        if (!$user || !Hash::check($password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau kata sandi salah',
            ], 401);
        }

        // ═══════════════════════════════════════════════════════════════
        // ROLE CHECK - UNCOMMENT UNTUK PRODUCTION
        // ═══════════════════════════════════════════════════════════════
        // Saat production, uncomment code di bawah untuk restrict orangtua
        // Website hanya untuk Kader/Admin (admin = kader, kader = admin)
        
        /*
        if (!in_array($user->role, ['admin', 'kader'])) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Website ini hanya untuk Kader/Admin. Orangtua silakan gunakan aplikasi mobile.',
            ], 403);
        }
        */

        // Login berhasil - buat session
        Auth::login($user, $remember);

        // Regenerate session untuk keamanan (anti session fixation)
        $request->session()->regenerate();

        return response()->json([
            'success'  => true,
            'message'  => 'Login berhasil',
            'redirect' => route('dashboard'),
            'data'     => [
                'id'        => $user->id,
                'username'  => $user->username,
                'email'     => $user->email,
                'full_name' => $user->nama_lengkap,
                'role'      => $user->role,
            ],
        ]);
    }

    /**
     * Proses logout
     * Menggantikan: logout.php
     */
    public function logout(Request $request)
    {
        Auth::logout();

        // Hapus session dan regenerate token
        // Sama seperti session_destroy() di logout.php lama
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
