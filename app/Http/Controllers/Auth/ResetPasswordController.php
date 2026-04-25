<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RequestOtpRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Mail\OtpResetPassword;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ResetPasswordController extends Controller
{
    /**
     * Kirim OTP ke email
     * Menggantikan: action 'request_otp' di reset_password.php
     */
    public function requestOtp(RequestOtpRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        // Generate OTP 6 digit (cryptographically secure)
        // Sama seperti random_int() di reset_password.php lama
        $otp       = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = now()->addMinutes(10); // Berlaku 10 menit

        // Simpan OTP ke database
        $user->update([
            'reset_otp_code'       => $otp,
            'reset_otp_expires_at' => $expiresAt,
        ]);

        // Kirim email OTP via Laravel Mail
        // Menggantikan: sendOTPEmail() + PHPMailer manual di reset_password.php
        try {
            Mail::to($user->email)->send(new OtpResetPassword($otp));
        } catch (\Exception $e) {
            \Log::error('Gagal kirim OTP email: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim email OTP. Silakan coba lagi.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Kode OTP telah dikirim ke email Anda. Silakan cek inbox atau folder spam.',
        ]);
    }

    /**
     * Verifikasi OTP dan reset password
     * Menggantikan: action 'reset_with_otp' di reset_password.php
     */
    public function resetWithOtp(ResetPasswordRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        // Validasi OTP cocok
        if ($user->reset_otp_code !== $request->otp) {
            return response()->json([
                'success' => false,
                'message' => 'Kode OTP salah',
            ], 401);
        }

        // Validasi OTP belum kadaluarsa
        if (now()->gt($user->reset_otp_expires_at)) {
            return response()->json([
                'success' => false,
                'message' => 'Kode OTP sudah kedaluwarsa. Silakan minta kode baru.',
            ], 401);
        }

        // Update password dan hapus OTP
        // Sama seperti password_hash() + UPDATE di reset_password.php lama
        $user->update([
            'password'             => Hash::make($request->new_password),
            'reset_otp_code'       => null,
            'reset_otp_expires_at' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kata sandi berhasil diatur ulang! Silakan masuk dengan kata sandi baru Anda.',
        ]);
    }
}
