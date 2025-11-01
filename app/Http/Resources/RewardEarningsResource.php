<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RewardEarningsResource extends JsonResource
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
            'amount_of_transactions' => $this->amount_of_transactions.' '.$currency,
            'point' => $this->point.' '.__('Points'),
        ];
    }
}
