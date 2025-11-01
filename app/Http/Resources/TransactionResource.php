<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'description' => $this->description,
            'tnx' => $this->tnx,
            'is_plus' => isPlusTransaction($this->type),
            'type' => ucwords(str_replace('_', ' ', $this->type->value)),
            'amount' => $this->amount.' '.$this->currency,
            'beneficiary' => $this->when($this->beneficiary, function () {
                return [
                    'account_number' => $this->beneficiary->account_number,
                    'account_name' => $this->beneficiary->account_name,
                    'bank_name' => $this->beneficiary->bank->name ?? 'Own Bank',
                    'branch_name' => $this->beneficiary->branch_name,
                ];
            }),
            'charge' => $this->charge.' '.$this->currency,
            'final_amount' => $this->final_amount.' '.$this->currency,
            'status' => ucwords($this->status->value),
            'method' => $this->method,
            'created_at' => $this->created_at,
        ];
    }
}
