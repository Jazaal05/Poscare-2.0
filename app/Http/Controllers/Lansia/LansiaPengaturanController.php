<?php

namespace App\Http\Controllers\Lansia;

use App\Http\Controllers\Controller;
use App\Mail\OtpResetPassword;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class LansiaPengaturanController extends Controller
{
    // Info posyandu — bisa diubah sesuai kebutuhan
    const POSYANDU_NAMA    = 'Posyandu Bagor Wetan';
    const POSYANDU_ALAMAT  = 'Desa Sukomoro, Kec. Bagor Wetan, Kab. Nganjuk';

    public function index()
    {
        $user = Auth::user();
        return view('lansia.pengaturan.index', [
            'user'           => $user,
            'posyanduNama'   => self::POSYANDU_NAMA,
            'posyanduAlamat' => self::POSYANDU_ALAMAT,
        ]);
    }

    // ── Ambil info user login ──────────────────────────────
    public function currentUser()
    {
        $user = Auth::user();
        return response()->json([
            'success' => true,
            'data'    => [
                'id'           => $user->id,
                'username'     => $user->username,
                'email'        => $user->email,
                'nama_lengkap' => $user->nama_lengkap,
                'no_telp'      => $user->no_telp,
                'nik'          => $user->nik,
                'role'         => $user->role,
            ],
        ]);
    }

    // ── Update profil ──────────────────────────────────────
    public function updateProfil(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'nama_lengkap' => 'sometimes|string|max:100',
            'email'        => "sometimes|email|unique:users,email,{$user->id}",
            'no_telp'      => 'sometimes|string|max:15',
        ]);

        $user->update($data);
        return response()->json(['success' => true, 'message' => 'Profil berhasil diperbarui!']);
    }

    // ── Ganti password langsung (dengan password lama) ─────
    public function gantiPassword(Request $request)
    {
        $request->validate([
            'password_lama' => 'required|string',
            'password_baru' => 'required|string|min:6|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->password_lama, $user->password)) {
            return response()->json(['success' => false, 'message' => 'Password lama tidak sesuai.'], 422);
        }

        $user->update(['password' => Hash::make($request->password_baru)]);
        return response()->json(['success' => true, 'message' => 'Password berhasil diubah!']);
    }

    // ── Request OTP untuk ganti password ──
    public function requestOtpGantiPassword()
    {
        $user = Auth::user();

        if (!$user->email) {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda tidak memiliki email terdaftar. Hubungi admin.',
            ], 422);
        }

        // Generate OTP 6 digit
        $otp       = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = now()->addMinutes(10);

        $user->update([
            'reset_otp_code'       => $otp,
            'reset_otp_expires_at' => $expiresAt,
        ]);

        try {
            Mail::to($user->email)->send(new OtpResetPassword($otp));
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim email OTP. Coba lagi.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Kode OTP telah dikirim ke email Anda. Berlaku 10 menit.',
            'email'   => substr($user->email, 0, 3) . '***@' . explode('@', $user->email)[1],
        ]);
    }

    // ── Verifikasi OTP & ganti password ───────────────────
    public function verifikasiOtpGantiPassword(Request $request)
    {
        $request->validate([
            'otp'          => 'required|digits:6',
            'password_baru'=> 'required|string|min:6|confirmed',
        ]);

        $user = Auth::user();

        if ($user->reset_otp_code !== $request->otp) {
            return response()->json(['success' => false, 'message' => 'Kode OTP salah.'], 401);
        }

        if (now()->gt($user->reset_otp_expires_at)) {
            return response()->json(['success' => false, 'message' => 'Kode OTP sudah kedaluwarsa. Minta kode baru.'], 401);
        }

        $user->update([
            'password'             => Hash::make($request->password_baru),
            'reset_otp_code'       => null,
            'reset_otp_expires_at' => null,
        ]);

        return response()->json(['success' => true, 'message' => 'Password berhasil diubah!']);
    }

    // ── List user wali lansia (dari aplikasi mobile) ──────────
    public function usersList()
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Hanya admin yang dapat melihat daftar pengguna.'], 403);
        }

        // Hanya tampilkan user dengan role 'wali_lansia' (dari aplikasi mobile)
        $users = User::select('id', 'username', 'email', 'nama_lengkap', 'no_telp', 'nik', 'role')
            ->where('role', 'wali_lansia')
            ->orderBy('username')
            ->get();

        return response()->json(['success' => true, 'data' => $users]);
    }

    // ── Hapus user (admin only) ────────────────────────────
    public function deleteUser($id)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        if ($id == Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Tidak bisa menghapus akun sendiri.'], 422);
        }

        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['success' => true, 'message' => "User {$user->username} berhasil dihapus."]);
    }
}
