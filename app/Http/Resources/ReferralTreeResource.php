<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReferralTreeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->fullName,
            'avatar' => $this->avatarPath,
            'is_me' => $this->when($this->id === auth()->id(), $this->id === auth()->id()),
            'children' => ReferralTreeResource::collection($this->referrals),
        ];
    }
}
