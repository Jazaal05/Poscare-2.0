<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class StorePengukuranRequest extends FormRequest
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
            'anak_id' => 'required|exists:anak,id',
            'tanggal_ukur' => 'required|date|before_or_equal:today',
            'bb_kg' => 'required|numeric|min:0.1|max:200',
            'tb_pb_cm' => 'required|numeric|min:10|max:250',
            'lk_cm' => 'nullable|numeric|min:10|max:100',
            'cara_ukur' => 'required|in:berdiri,berbaring',
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
            'anak_id.required' => 'ID anak wajib diisi',
            'anak_id.exists' => 'Data anak tidak ditemukan',
            'tanggal_ukur.required' => 'Tanggal pengukuran wajib diisi',
            'tanggal_ukur.before_or_equal' => 'Tanggal pengukuran tidak boleh lebih dari hari ini',
            'bb_kg.required' => 'Berat badan wajib diisi',
            'bb_kg.numeric' => 'Berat badan harus berupa angka',
            'bb_kg.min' => 'Berat badan minimal 0.1 kg',
            'bb_kg.max' => 'Berat badan maksimal 200 kg',
            'tb_pb_cm.required' => 'Tinggi/panjang badan wajib diisi',
            'tb_pb_cm.numeric' => 'Tinggi/panjang badan harus berupa angka',
            'tb_pb_cm.min' => 'Tinggi/panjang badan minimal 10 cm',
            'tb_pb_cm.max' => 'Tinggi/panjang badan maksimal 250 cm',
            'cara_ukur.required' => 'Cara pengukuran wajib dipilih',
            'cara_ukur.in' => 'Cara pengukuran harus berdiri atau berbaring',
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