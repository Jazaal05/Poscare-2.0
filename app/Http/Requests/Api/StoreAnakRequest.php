<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class StoreAnakRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'nik_anak' => 'nullable|string|max:16|unique:anak,nik_anak',
            'nama_anak' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date|before_or_equal:today',
            'tempat_lahir' => 'nullable|string|max:100',
            'jenis_kelamin' => 'required|in:L,P',
            'anak_ke' => 'nullable|integer|min:1',
            'alamat_domisili' => 'nullable|string',
            'rt_rw' => 'nullable|string|max:10',
            'nama_kk' => 'nullable|string|max:100',
            'nama_ayah' => 'nullable|string|max:100',
            'nama_ibu' => 'required|string|max:100',
            'nik_ayah' => 'nullable|string|max:16',
            'nik_ibu' => 'nullable|string|max:16',
            'tanggal_lahir_ibu' => 'nullable|date|before:today',
            'hp_kontak_ortu' => 'nullable|string|max:15',
            'user_id' => 'nullable|exists:users,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'nama_anak.required' => 'Nama anak wajib diisi',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi',
            'tanggal_lahir.before_or_equal' => 'Tanggal lahir tidak boleh lebih dari hari ini',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih',
            'jenis_kelamin.in' => 'Jenis kelamin harus L atau P',
            'nama_ibu.required' => 'Nama ibu wajib diisi',
            'nik_anak.unique' => 'NIK anak sudah terdaftar',
            'user_id.exists' => 'User tidak ditemukan',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422)
        );
    }
}