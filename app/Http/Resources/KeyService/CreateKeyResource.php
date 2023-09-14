<?php

namespace App\Http\Resources\KeyService;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CreateKeyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "marketplace" => $this->marketplace,
            "key" => $this->key,
            "type" => $this->type,
            "lk" => $this->lk_id
        ];
    }
}
