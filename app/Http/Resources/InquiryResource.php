<?php

namespace App\Http\Resources;

use App\Services\InquiryService;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InquiryResource extends JsonResource
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
            'ref_code' => $this->ref_code,
            'redirect_url' => InquiryService::whatsappRedirect($this->resource),
        ];
    }
}
