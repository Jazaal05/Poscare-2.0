<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RiwayatPengukuranResource extends JsonResource
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
            'anak_id' => $this->anak_id,
            'tanggal_ukur' => $this->tanggal_ukur?->format('Y-m-d'),
            'umur_bulan' => $this->umur_bulan,
            'bb_kg' => $this->bb_kg,
            'tb_pb_cm' => $this->tb_pb_cm,
            'lk_cm' => $this->lk_cm,
            'cara_ukur' => $this->cara_ukur,
            'imt' => $this->imt,
            'z_tbu' => $this->z_tbu,
            'z_bbu' => $this->z_bbu,
            'z_bbtb' => $this->z_bbtb,
            'kat_tbu' => $this->kat_tbu,
            'kat_bbu' => $this->kat_bbu,
            'kat_bbtb' => $this->kat_bbtb,
            'overall_8' => $this->overall_8,
            'anak' => new AnakResource($this->whenLoaded('anak')),
        ];
    }
}