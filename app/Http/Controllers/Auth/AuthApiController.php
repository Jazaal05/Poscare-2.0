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

        // Auto-link data balita & lansia yang belum terhubung ke akun ini
        if ($user->nik) {
            \App\Models\Anak::where('nik_ibu', $user->nik)
                ->where('is_deleted', 0)
                ->where(function ($q) use ($user) {
                    $q->whereNull('user_id')->orWhere('user_id', '!=', $user->id);
                })
                ->update(['user_id' => $user->id]);

            \App\Models\Lansia::where('nik_wali', $user->nik)
                ->whereNull('user_id')
                ->update(['user_id' => $user->id]);
        }

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
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'username.required'     => 'Username wajib diisi.',
            'username.unique'       => 'Username sudah digunakan, silakan pilih username lain.',
            'email.required'        => 'Email wajib diisi.',
            'email.email'           => 'Format email tidak valid.',
            'email.unique'          => 'Email sudah terdaftar, silakan gunakan email lain.',
            'password.required'     => 'Kata sandi wajib diisi.',
            'password.min'          => 'Kata sandi minimal 6 karakter.',
            'no_telp.required'      => 'Nomor telepon wajib diisi.',
            'nik.required'          => 'NIK wajib diisi.',
            'nik.size'              => 'NIK harus 16 digit.',
            'nik.unique'            => 'NIK ini sudah memiliki akun terdaftar. Silakan masuk menggunakan akun yang sudah ada.',
        ]);

        // Cek apakah NIK terdaftar sebagai NIK ibu di data balita
        // ATAU sebagai NIK wali di data lansia
        $nikTerdaftarBalita = \App\Models\Anak::where('nik_ibu', $request->nik)
            ->where('is_deleted', 0)
            ->exists();

        $nikTerdaftarLansia = \App\Models\Lansia::where('nik_wali', $request->nik)
            ->where('is_deleted', 0)
            ->exists();

        if (!$nikTerdaftarBalita && !$nikTerdaftarLansia) {
            return $this->errorResponse(
                'NIK Anda belum terdaftar. Hubungi kader terdekat untuk mendaftarkan akun Anda.',
                422
            );
        }

        $user = User::create([
            'nama_lengkap' => $request->nama_lengkap,
            'username'     => $request->username,
            'email'        => $request->email,
            'password'     => Hash::make($request->password),
            'no_telp'      => $request->no_telp,
            'nik'          => $request->nik,
            'role'         => 'orangtua', // default role untuk registrasi mobile
        ]);

        // Tentukan role berdasarkan data yang terdaftar
        if ($nikTerdaftarBalita && $nikTerdaftarLansia) {
            $user->update(['role' => 'orangtua_lansia']);
        } elseif ($nikTerdaftarLansia) {
            $user->update(['role' => 'wali_lansia']);
        }
        // orangtua tetap default jika hanya punya balita

        // Hubungkan data balita ke akun ini jika ada
        if ($nikTerdaftarBalita) {
            \App\Models\Anak::where('nik_ibu', $request->nik)
                ->where('is_deleted', 0)
                ->update(['user_id' => $user->id]);
        }

        // Hubungkan data lansia ke akun ini jika ada
        if ($nikTerdaftarLansia) {
            \App\Models\Lansia::where('nik_wali', $request->nik)
                ->whereNull('user_id')
                ->update(['user_id' => $user->id]);
        }

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
     * Verifikasi identitas untuk reset password via mobile
     * Cukup NIK + No. Telepon, tanpa OTP
     *
     * POST /api/v1/auth/forgot-password
     * Body: { "nik": "...", "no_telp": "..." }
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'nik'     => 'required|string|size:16',
            'no_telp' => 'required|string',
        ]);

        // Normalisasi no_telp input: hapus semua non-digit
        $noTelpInput = preg_replace('/[^0-9]/', '', $request->no_telp);

        // Cari user berdasarkan NIK
        $user = User::where('nik', $request->nik)->first();

        if (!$user) {
            return $this->errorResponse('NIK tidak ditemukan dalam sistem.', 404);
        }

        // Normalisasi no_telp di database juga
        $noTelpDb = preg_replace('/[^0-9]/', '', $user->no_telp ?? '');

        // Bandingkan dari belakang (8 digit terakhir) agar toleran terhadap perbedaan format
        $inputSuffix = substr($noTelpInput, -8);
        $dbSuffix    = substr($noTelpDb, -8);

        if ($inputSuffix !== $dbSuffix || strlen($inputSuffix) < 8) {
            return $this->errorResponse('NIK dan nomor telepon tidak cocok.', 401);
        }

        // Identitas valid — kembalikan user_id sebagai token sementara untuk reset
        return $this->successResponse([
            'user_id' => $user->id,
        ], 'Identitas terverifikasi. Silakan buat kata sandi baru.');
    }

    /**
     * Reset password setelah verifikasi NIK + No. Telp
     *
     * POST /api/v1/auth/reset-password
     * Body: { "user_id": ..., "new_password": "..." }
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'user_id'      => 'required|integer|exists:users,id',
            'new_password' => 'required|string|min:6',
        ]);

        $user = User::findOrFail($request->user_id);

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return $this->successResponse(null, 'Kata sandi berhasil diatur ulang! Silakan masuk dengan kata sandi baru Anda.');
    }
}
