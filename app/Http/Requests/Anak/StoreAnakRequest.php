<?php

namespace App\Http\Requests\Anak;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAnakRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'parent_id'      => 'required|integer|min:1',
            'nama_anak'      => 'required|string|min:3|max:100|regex:/[a-zA-Z]/',
            'nik_anak'       => [
                'required',
                'digits:16',
                Rule::unique('anak', 'nik_anak')->where('is_deleted', 0),
            ],
            'jenis_kelamin'  => 'required|in:L,P',
            'tanggal_lahir'  => 'required|date|before_or_equal:today',
            'tempat_lahir'   => 'required|string|max:100',
            'berat_badan'    => 'nullable|numeric|min:1.5|max:30',
            'tinggi_badan'   => 'nullable|numeric|min:40|max:130',
            'lingkar_kepala' => 'nullable|numeric|min:20|max:60',
            'cara_ukur'      => 'nullable|in:berdiri,berbaring',
        ];
    }

    public function messages(): array
    {
        return [
            'nik_anak.digits'     => 'NIK Anak harus 16 digit angka',
            'nik_anak.unique'     => 'NIK Anak sudah terdaftar dalam sistem',
            'nama_anak.regex'     => 'Nama anak tidak boleh hanya berupa angka',
            'tanggal_lahir.before_or_equal' => 'Tanggal lahir tidak boleh di masa depan',
            'jenis_kelamin.in'    => 'Jenis kelamin harus L atau P',
        ];
    }
}
