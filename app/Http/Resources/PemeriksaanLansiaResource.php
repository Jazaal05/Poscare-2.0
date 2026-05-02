<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PemeriksaanLansiaResource extends JsonResource
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
            'lansia_id' => $this->lansia_id,
            'tanggal_periksa' => $this->tanggal_periksa?->format('Y-m-d'),
            'berat_badan' => $this->berat_badan,
            'tinggi_badan' => $this->tinggi_badan,
            'tekanan_darah' => $this->tekanan_darah,
            'gula_darah' => $this->gula_darah,
            'asam_urat' => $this->asam_urat,
            'kolesterol' => $this->kolesterol,
            'catatan' => $this->catatan,
            'dicatat_oleh' => $this->dicatat_oleh,
            'lansia' => new LansiaResource($this->whenLoaded('lansia')),
        ];
    }
}