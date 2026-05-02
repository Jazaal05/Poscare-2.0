<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ImunisasiResource extends JsonResource
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
            'master_vaksin_id' => $this->master_vaksin_id,
            'tanggal' => $this->tanggal?->format('Y-m-d'),
            'umur_bulan' => $this->umur_bulan,
            'anak' => new AnakResource($this->whenLoaded('anak')),
            'vaksin' => new MasterVaksinResource($this->whenLoaded('masterVaksin')),
        ];
    }
}