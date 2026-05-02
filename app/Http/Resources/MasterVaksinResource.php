<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MasterVaksinResource extends JsonResource
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
            'nama_vaksin' => $this->nama_vaksin,
            'deskripsi' => $this->deskripsi,
            'umur_pemberian' => $this->umur_pemberian,
            'is_wajib' => $this->is_wajib,
            'imunisasi' => ImunisasiResource::collection($this->whenLoaded('imunisasi')),
        ];
    }
}