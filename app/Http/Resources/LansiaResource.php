<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LansiaResource extends JsonResource
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
            'nik' => $this->nik,
            'nama_lengkap' => $this->nama_lengkap,
            'jenis_kelamin' => $this->jenis_kelamin,
            'tanggal_lahir' => $this->tanggal_lahir?->format('Y-m-d'),
            'tempat_lahir' => $this->tempat_lahir,
            'alamat' => $this->alamat,
            'rt_rw' => $this->rt_rw,
            'no_hp' => $this->no_hp,
            'nama_wali' => $this->nama_wali,
            'hubungan_wali' => $this->hubungan_wali,
            'umur' => $this->umur,
            'kunjungan' => KunjunganLansiaResource::collection($this->whenLoaded('kunjungan')),
            'kunjungan_terakhir' => new KunjunganLansiaResource($this->whenLoaded('kunjunganTerakhir')),
            'pemeriksaan' => PemeriksaanLansiaResource::collection($this->whenLoaded('pemeriksaan')),
            'pemeriksaan_terakhir' => new PemeriksaanLansiaResource($this->whenLoaded('pemeriksaanTerakhir')),
        ];
    }
}