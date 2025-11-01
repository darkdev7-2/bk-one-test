<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FdrTransactionResource extends JsonResource
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
            'return_date' => $this->given_date->format('d M Y'),
            'interest_amount' => $this->given_amount.' '.$currency,
            'paid_amount' => $this->paid_amount == null ? __('N/A') : $this->paid_amount.' '.$currency,
        ];
    }
}
