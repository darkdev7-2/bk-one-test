<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RewardRedeemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $currency = setting('site_currency', 'global');

        return [
            'portfolio' => $this->portfolio->portfolio_name,
            'point' => $this->point.' '.__('Points'),
            'amount' => $this->amount.' '.$currency,
        ];
    }
}
