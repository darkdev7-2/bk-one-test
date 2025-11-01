<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PortfolioResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->portfolio_name,
            'icon' => asset($this->icon),
            'description' => $this->description,
            'is_locked' => $this->isLocked(),
        ];
    }

    private function isLocked()
    {
        $unlockedPortoflio = json_decode(auth()->user()->portfolios, true);

        return ! in_array($this->id, $unlockedPortoflio);
    }
}
