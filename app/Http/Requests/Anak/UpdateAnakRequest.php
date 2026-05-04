<?php

namespace App\Http\Requests\Anak;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAnakRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('id') ?? $this->input('id');

        return [
            'nama_anak'                    => 'sometimes|string|min:3|max:100|regex:/[a-zA-Z]/',
            'nik_anak'                     => [
                'sometimes',
                'digits:16',
                Rule::unique('anak', 'nik_anak')
                    ->ignore($id)
                    ->where('is_deleted', 0),
            ],
            'jenis_kelamin'                => 'sometimes|in:L,P',
            'tanggal_lahir'                => 'sometimes|date|before_or_equal:today',
            'tempat_lahir'                 => 'sometimes|string|max:100',
            'anak_ke'                      => 'sometimes|integer|min:1|max:20',
            'nama_ibu'                     => 'sometimes|string|min:3|max:100',
            'nik_ibu'                      => 'sometimes|digits:16',
            'nama_ayah'                    => 'sometimes|string|max:100',
            'nik_ayah'                     => 'sometimes|digits:16',
            'nama_kk'                      => 'sometimes|string|max:100',
            'hp_kontak_ortu'               => 'sometimes|string|min:10|max:15',
            'alamat_domisili'              => 'sometimes|string|min:5|max:255',
            'rt_rw'                        => 'sometimes|string',
            'berat_badan'                  => 'sometimes|nullable|numeric|min:0|max:200',
            'tinggi_badan'                 => 'sometimes|nullable|numeric|min:0|max:250',
            'lingkar_kepala'               => 'sometimes|nullable|numeric|min:0|max:100',
            'cara_ukur'                    => 'sometimes|nullable|in:berdiri,berbaring',
            'tanggal_penimbangan_terakhir' => 'sometimes|nullable|date',
        ];
    }
}
