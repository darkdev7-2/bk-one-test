<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FdrPlanResource extends JsonResource
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
            'profit_rate' => $this->interest_rate.'%',
            'profit_intervel' => $this->intervel.' '.__('Days'),
            'maturity_fee' => $this->maturity_platform_fee.' '.$currency,
            'locked' => $this->locked.' '.__('Days'),
            'compounding' => $this->is_compounding ? __('Yes') : __('No'),
            'can_cancel' => $this->can_cancel,
            'cancel_in' => $this->cancel_type == 'anytime' ? __('Anytime') : $this->cancel_days.' '.__('Days'),
            'cancel_fee' => $this->cancel_fee_type == 'percentage' ? $this->cancel_fee.'%' : $this->cancel_fee.' '.$currency,
            'is_increase' => $this->is_add_fund_fdr,
            'increase_limit' => $this->increment_type == 'unlimited' ? __('Unlimited') : $this->increment_times.' '.__('Times'),
            'increment_charge' => $this->increment_charge_type ? $this->increment_fee.' '.$currency : 0 .' '.$currency,
            'min_increase_amount' => $this->min_increment_amount.' '.$currency,
            'max_increase_amount' => $this->max_increment_amount.' '.$currency,
            'is_decrease' => $this->is_deduct_fund_fdr,
            'decrease_limit' => $this->decrement_type == 'unlimited' ? __('Unlimited') : $this->decrement_times.' '.__('Times'),
            'decrement_charge' => $this->decrement_charge_type ? $this->decrement_fee.' '.$currency : 0 .' '.$currency,
            'min_decrease_amount' => $this->min_decrement_amount.' '.$currency,
            'max_decrease_amount' => $this->max_decrement_amount.' '.$currency,
            'badge' => $this->badge,
        ];
    }
}
