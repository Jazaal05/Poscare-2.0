<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'username' => $this->username,
            'nama_lengkap' => $this->nama_lengkap,
            'email' => $this->email,
            'no_telp' => $this->no_telp,
            'nik' => $this->nik,
            'role' => $this->role,
            'profile_image_url' => $this->profile_image_url,
            // Jangan expose password dan OTP
            'anak_count' => $this->whenCounted('anak'),
            'anak' => AnakResource::collection($this->whenLoaded('anak')),
        ];
    }
}