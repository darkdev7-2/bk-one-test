<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DirectReferralsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'username' => $this->username,
            'avatar' => $this->avatarPath,
            'portfolio' => $this->portfolio?->portfolio_name ?? __('N/A'),
            'status' => $this->status == 1,
            'created_at' => $this->created_at,
        ];
    }
}
