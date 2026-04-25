<?php

namespace App\Http\Requests\Pengukuran;

use Illuminate\Foundation\Http\FormRequest;

class StorePengukuranRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'anak_id'     => 'required|integer|exists:anak,id',
            'tanggal_ukur'=> 'required|date|before_or_equal:today',
            'bb_kg'       => 'required|numeric|min:2|max:25',
            'tb_cm'       => 'required|numeric|min:45|max:120',
            'lk_cm'       => 'nullable|numeric|min:30|max:55',
            'cara_ukur'   => 'required|in:berdiri,berbaring',
        ];
    }

    public function messages(): array
    {
        return [
            'bb_kg.min'  => 'Berat badan minimal 2 kg',
            'bb_kg.max'  => 'Berat badan maksimal 25 kg',
            'tb_cm.min'  => 'Tinggi badan minimal 45 cm',
            'tb_cm.max'  => 'Tinggi badan maksimal 120 cm',
            'lk_cm.min'  => 'Lingkar kepala minimal 30 cm',
            'lk_cm.max'  => 'Lingkar kepala maksimal 55 cm',
            'cara_ukur.in' => 'Cara ukur harus berdiri atau berbaring',
            'tanggal_ukur.before_or_equal' => 'Tanggal ukur tidak boleh di masa depan',
        ];
    }
}
