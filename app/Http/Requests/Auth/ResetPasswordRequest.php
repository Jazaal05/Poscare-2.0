<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'        => 'required|email|exists:users,email',
            'otp'          => 'required|digits:6',
            'new_password' => 'required|string|min:6',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required'        => 'Email harus diisi',
            'email.email'           => 'Format email tidak valid',
            'email.exists'          => 'Email tidak ditemukan',
            'otp.required'          => 'Kode OTP harus diisi',
            'otp.digits'            => 'Kode OTP harus 6 digit angka',
            'new_password.required' => 'Kata sandi baru harus diisi',
            'new_password.min'      => 'Kata sandi baru minimal 6 karakter',
        ];
    }
}
