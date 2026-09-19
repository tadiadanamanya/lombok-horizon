<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KavlingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'nomor' => $this->nomor,
            'luas_m2' => $this->luas_m2,
            'harga' => $this->harga,
            'status' => $this->status,
            // Note: intentionally excluding koordinat_bidang as per PRD Section 9
        ];
    }
}
