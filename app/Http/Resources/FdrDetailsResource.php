<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FdrDetailsResource extends JsonResource
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
            'fdr_id' => $this->fdr_id,
            'fdr_name' => $this->plan->name,
            'status' => $this->status->value,
            'amount' => $this->amount.' '.$currency,
            'profit' => $this->profit().' '.$currency,
            'profit_period' => __('Every :days days', ['days' => $this->plan->intervel]),
            'total_returns' => $this->totalInstallment().' '.__('Times'),
            'given_returns' => $this->givenInstallemnt().' '.__('Times'),
            'total_profit' => $this->totalMatureAmount.' '.$currency,
            'next_receive_date' => \App\Models\FDRTransaction::where('fdr_id', $this->id)
                ->where('paid_amount', null)
                ->first()?->given_date->format('d M Y') ?? '--',
            'total_profit_amount' => $this->transactions->sum('given_amount').' '.$currency,
            'is_increase' => $this->plan->is_add_fund_fdr && $this->status != \App\Enums\FdrStatus::Closed,
            'min_increase_amount' => $this->plan->min_increment_amount.' '.$currency,
            'max_increase_amount' => $this->plan->max_increment_amount.' '.$currency,
            'is_decrease' => $this->plan->is_deduct_fund_fdr && $this->status != \App\Enums\FdrStatus::Closed,
            'min_decrease_amount' => $this->plan->min_decrement_amount.' '.$currency,
            'max_decrease_amount' => $this->plan->max_decrement_amount.' '.$currency,
        ];
    }
}
