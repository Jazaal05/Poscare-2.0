<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class KunjunganLansiaResource extends JsonResource
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
            'tanggal_kunjungan' => $this->tanggal_kunjungan?->format('Y-m-d'),
            'berat_badan' => $this->berat_badan,
            'tekanan_darah' => $this->tekanan_darah,
            'status_tensi' => $this->status_tensi,
            'gula_darah' => $this->gula_darah,
            'status_gula' => $this->status_gula,
            'kolesterol' => $this->kolesterol,
            'status_kolesterol' => $this->status_kolesterol,
            'asam_urat' => $this->asam_urat,
            'status_asam_urat' => $this->status_asam_urat,
            'ada_keluhan' => $this->ada_keluhan,
            'keluhan' => $this->keluhan,
            'obat_diberikan' => $this->obat_diberikan,
            'vitamin_diberikan' => $this->vitamin_diberikan,
            'catatan_bidan' => $this->catatan_bidan,
            'dicatat_oleh' => $this->dicatat_oleh,
            'lansia' => new LansiaResource($this->whenLoaded('lansia')),
        ];
    }
}