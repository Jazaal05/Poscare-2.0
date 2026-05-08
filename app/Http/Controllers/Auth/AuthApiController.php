<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseApiController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthApiController extends BaseApiController
{
    /**
     * Login untuk aplikasi mobile
     * Menghasilkan Sanctum token untuk autentikasi API
     *
     * POST /api/v1/auth/login
     * Body: { "email": "...", "password": "..." }
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|string', // bisa email atau username
            'password' => 'required|string',
        ]);

        // Cari user berdasarkan email ATAU username
        $user = User::where('email', $request->email)
            ->orWhere('username', $request->email)
            ->first();

        // Cek user ada dan password cocok
        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->errorResponse('Email/Username atau kata sandi salah', 401);
        }

        // Hapus token lama agar tidak menumpuk
        $user->tokens()->delete();

        // Buat token baru berdasarkan role
        $tokenName = match($user->role) {
            'kader', 'admin' => 'kader-mobile-token',
            'orangtua'       => 'orangtua-mobile-token',
            'wali_lansia'    => 'wali-lansia-mobile-token',
            default          => 'mobile-token',
        };

        $token = $user->createToken($tokenName)->plainTextToken;

        return $this->successResponse([
            'token'      => $token,
            'token_type' => 'Bearer',
            'user'       => [
                'id'           => $user->id,
                'username'     => $user->username,
                'nama_lengkap' => $user->nama_lengkap,
                'email'        => $user->email,
                'no_telp'      => $user->no_telp,
                'nik'          => $user->nik,
                'role'         => $user->role,
                'profile_image_url' => $user->profile_image_url,
            ],
        ], 'Login berhasil');
    }

    /**
     * Logout dari aplikasi mobile
     * Menghapus token yang sedang dipakai
     *
     * POST /api/v1/auth/logout
     * Header: Authorization: Bearer {token}
     */
    public function logout(Request $request)
    {
        // Hapus hanya token yang sedang dipakai
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(null, 'Logout berhasil');
    }

    /**
     * Cek token masih valid & ambil data user terbaru
     * Dipakai Flutter saat app dibuka untuk validasi session
     *
     * GET /api/v1/auth/me
     * Header: Authorization: Bearer {token}
     */
    public function me(Request $request)
    {
        $user = $request->user();

        return $this->successResponse([
            'id'           => $user->id,
            'username'     => $user->username,
            'nama_lengkap' => $user->nama_lengkap,
            'email'        => $user->email,
            'no_telp'      => $user->no_telp,
            'nik'          => $user->nik,
            'role'         => $user->role,
            'profile_image_url' => $user->profile_image_url,
        ], 'Data user berhasil diambil');
    }

    /**
     * Register akun orangtua baru via aplikasi mobile
     *
     * POST /api/v1/auth/register
     * Body: { "nama_lengkap", "username", "email", "password", "no_telp", "nik" }
     */
    public function register(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'username'     => 'required|string|max:100|unique:users,username',
            'email'        => 'required|email|unique:users,email',
            'password'     => 'required|string|min:6',
            'no_telp'      => 'required|string|max:20',
            'nik'          => 'required|string|size:16|unique:users,nik',
        ]);

        $user = User::create([
            'nama_lengkap' => $request->nama_lengkap,
            'username'     => $request->username,
            'email'        => $request->email,
            'password'     => Hash::make($request->password),
            'no_telp'      => $request->no_telp,
            'nik'          => $request->nik,
            'role'         => 'orangtua', // default role untuk registrasi mobile
        ]);

        $token = $user->createToken('orangtua-mobile-token')->plainTextToken;

        return $this->successResponse([
            'token'      => $token,
            'token_type' => 'Bearer',
            'user'       => [
                'id'           => $user->id,
                'username'     => $user->username,
                'nama_lengkap' => $user->nama_lengkap,
                'email'        => $user->email,
                'no_telp'      => $user->no_telp,
                'nik'          => $user->nik,
                'role'         => $user->role,
            ],
        ], 'Registrasi berhasil', 201);
    }

    /**
     * Request OTP untuk reset password via mobile
     *
     * POST /api/v1/auth/forgot-password
     * Body: { "email": "..." }
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        $otp       = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = now()->addMinutes(10);

        $user->update([
            'reset_otp_code'       => $otp,
            'reset_otp_expires_at' => $expiresAt,
        ]);

        try {
            \Illuminate\Support\Facades\Mail::to($user->email)
                ->send(new \App\Mail\OtpResetPassword($otp));
        } catch (\Exception $e) {
            \Log::error('Gagal kirim OTP email: ' . $e->getMessage());
            return $this->errorResponse('Gagal mengirim email OTP. Silakan coba lagi.', 500);
        }

        return $this->successResponse(null, 'Kode OTP telah dikirim ke email Anda. Silakan cek inbox atau folder spam.');
    }

    /**
     * Reset password dengan OTP via mobile
     *
     * POST /api/v1/auth/reset-password
     * Body: { "email": "...", "otp": "...", "new_password": "..." }
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'        => 'required|email|exists:users,email',
            'otp'          => 'required|digits:6',
            'new_password' => 'required|string|min:6',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user->reset_otp_code !== $request->otp) {
            return $this->errorResponse('Kode OTP salah', 401);
        }

        if (now()->gt($user->reset_otp_expires_at)) {
            return $this->errorResponse('Kode OTP sudah kedaluwarsa. Silakan minta kode baru.', 401);
        }

        $user->update([
            'password'             => \Illuminate\Support\Facades\Hash::make($request->new_password),
            'reset_otp_code'       => null,
            'reset_otp_expires_at' => null,
        ]);

        return $this->successResponse(null, 'Kata sandi berhasil diatur ulang! Silakan masuk dengan kata sandi baru Anda.');
    }
}
