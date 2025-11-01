<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DpsDetailsReosurce extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        $currency = setting('site_currency', 'global');

        return [
            'id' => $this->id,
            'plan_name' => $this->plan->name,
            'dps_id' => $this->dps_id,
            'status' => $this->status->value,
            'interest_rate' => $this->plan->interest_rate,
            'per_installment' => $this->per_installment.' '.$currency,
            'installment_interval' => $this->plan->interval,
            'total_installment' => $this->plan->total_installment,
            'given_installment' => $this->given_installment,
            'next_installment' => nextInstallment($this->id, \App\Models\DpsTransaction::class, 'dps_id'),
            'deferment_days' => $this->plan->delay_days,
            'deferment_charge' => $this->plan->charge.' '.($this->plan->charge_type == 'percentage' ? '%' : $currency),
            'profit_amount' => $this->plan->user_profit.' '.$currency,
            'total_mature_amount' => getTotalMature($this).' '.$currency,
            'is_increase' => $this->plan->is_upgrade && $this->status != \App\Enums\DpsStatus::Closed,
            'min_increase_amount' => $this->plan->min_increment_amount.' '.$currency,
            'max_increase_amount' => $this->plan->max_increment_amount.' '.$currency,
            'is_decrease' => $this->plan->is_downgrade && $this->status != \App\Enums\DpsStatus::Closed,
            'min_decrease_amount' => $this->plan->min_decrement_amount.' '.$currency,
            'max_decrease_amount' => $this->plan->max_decrement_amount.' '.$currency,
        ];
    }
}
