<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoanTransactionResource extends JsonResource
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
            'installment_date' => $this->installment_date->format('d M Y'),
            'given_date' => $this->given_date?->format('d M Y') ?? __('Yet to pay'),
            'deferment' => $this->deferment.' '.__('Days'),
            'paid_amount' => $this->paid_amount,
            'charge' => $this->charge,
            'final_amount' => $this->final_amount,
        ];
    }
}
