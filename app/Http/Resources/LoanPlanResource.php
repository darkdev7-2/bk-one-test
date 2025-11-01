<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoanPlanResource extends JsonResource
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
            'id' => $this->id,
            'name' => $this->name,
            'minimum_amount' => $this->minimum_amount.' '.$currency,
            'maximum_amount' => $this->maximum_amount.' '.$currency,
            'installment_rate' => $this->per_installment.'%',
            'installment_intervel' => $this->installment_intervel.' '.__('Days'),
            'total_installment' => $this->total_installment.' '.__('Times'),
            'loan_fee' => $this->loan_fee.' '.$currency,
            'fields' => $this->field_options,
            'instructions' => strip_tags($this->instructions),
            'plan_data' => [
                'interest_rate' => $this->per_installment,
                'total_installment' => $this->total_installment,
                'loan_fee' => $this->loan_fee,
            ],
        ];
    }
}
