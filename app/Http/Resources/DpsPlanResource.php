<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DpsPlanResource extends JsonResource
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
            'dps_name' => $this->name,
            'per_installment' => $this->per_installment,
            'installment_days' => $this->interval.' '.__('Days'),
            'total_installment' => $this->total_installment.' '.__('Times'),
            'interest_rate' => $this->interest_rate.'%',
            'total_deposit' => $this->total_deposit.' '.$currency,
            'total_mature_amount' => $this->total_mature_amount.' '.$currency,
            'maturity_fee' => $this->maturity_platform_fee.' '.$currency,
            'cancel_in' => $this->cancel_type == 'anytime' ? __('Anytime') : $this->cancel_days.' '.__('Days'),
            'cancel_fee' => $this->cancel_fee.' '.$currency,
            'is_increase' => $this->is_upgrade,
            'increase_limit' => $this->increment_type == 'unlimited' ? __('Unlimited') : $this->increment_times.' '.__('Times'),
            'min_increase_amount' => $this->min_increment_amount.' '.$currency,
            'max_increase_amount' => $this->max_increment_amount.' '.$currency,
            'increase_charge' => $this->increment_charge_type ? $this->increment_fee.' '.$currency : 0 .' '.$currency,
            'is_decrease' => $this->is_downgrade,
            'decrease_limit' => $this->decrement_type == 'unlimited' ? __('Unlimited') : $this->decrement_times.' '.__('Times'),
            'min_decrease_amount' => $this->min_decrement_amount.' '.$currency,
            'max_decrease_amount' => $this->max_decrement_amount.' '.$currency,
            'decrease_charge' => $this->decrement_charge_type ? $this->decrement_fee.' '.$currency : 0 .' '.$currency,
            'badge' => $this->badge,
        ];
    }
}
