<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
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
            'nama' => $this->nama,
            'slug' => $this->slug,
            'lokasi' => $this->lokasi,
            'thumbnail_url' => $this->thumbnail_path ? asset('storage/'.$this->thumbnail_path) : null,
            // Pakai atribut withCount bila tersedia (index/show sudah withCount);
            // fallback ke koleksi bila resource dipakai tanpa withCount.
            'kavling_total' => $this->kavlings_count ?? $this->kavlings->count(),
            'kavling_available' => $this->kavling_available ?? $this->kavlings->where('status', 'available')->count(),
        ];
    }
}
