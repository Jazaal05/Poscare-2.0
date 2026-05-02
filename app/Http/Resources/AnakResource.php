<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AnakResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'no_registrasi' => $this->no_registrasi,
            'nik_anak' => $this->nik_anak,
            'nama_anak' => $this->nama_anak,
            'tanggal_lahir' => $this->tanggal_lahir?->format('Y-m-d'),
            'tempat_lahir' => $this->tempat_lahir,
            'jenis_kelamin' => $this->jenis_kelamin,
            'anak_ke' => $this->anak_ke,
            'alamat_domisili' => $this->alamat_domisili,
            'rt_rw' => $this->rt_rw,
            'nama_kk' => $this->nama_kk,
            'nama_ayah' => $this->nama_ayah,
            'nama_ibu' => $this->nama_ibu,
            'nik_ayah' => $this->nik_ayah,
            'nik_ibu' => $this->nik_ibu,
            'tanggal_lahir_ibu' => $this->tanggal_lahir_ibu?->format('Y-m-d'),
            'hp_kontak_ortu' => $this->hp_kontak_ortu,
            'berat_badan' => $this->berat_badan,
            'tinggi_badan' => $this->tinggi_badan,
            'lingkar_kepala' => $this->lingkar_kepala,
            'cara_ukur' => $this->cara_ukur,
            'status_gizi' => $this->status_gizi,
            'status_gizi_detail' => $this->status_gizi_detail,
            'tanggal_penimbangan_terakhir' => $this->tanggal_penimbangan_terakhir?->format('Y-m-d'),
            'umur_bulan' => $this->umur_bulan,
            'umur_tahun' => $this->umur_tahun,
            'user' => new UserResource($this->whenLoaded('user')),
            'riwayat_pengukuran' => RiwayatPengukuranResource::collection($this->whenLoaded('riwayatPengukuran')),
            'imunisasi' => ImunisasiResource::collection($this->whenLoaded('imunisasi')),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}