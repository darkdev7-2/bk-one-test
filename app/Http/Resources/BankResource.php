<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BankResource extends JsonResource
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
            'name' => $this->name,
            'processing_time' => __('Transfer in: ').$this->processing_time.' '.$this->processing_type,
            'charge_type' => $this->charge_type,
            'charge' => (float) $this->charge,
            'minimum_transfer' => (float) $this->minimum_transfer,
            'maximum_transfer' => (float) $this->maximum_transfer,
        ];
    }

    public static function collectionWithDefault($resource, $user)
    {
        $defaultWallet = [
            'id' => 0,
            'name' => __('Own Bank'),
            'processing_time' => __('Transfer: Instant'),
            'charge_type' => setting('fund_transfer_charge_type', 'fee'),
            'charge' => (float) setting('fund_transfer_charge', 'fee'),
            'minimum_transfer' => (float) setting('min_fund_transfer', 'fee'),
            'maximum_transfer' => (float) setting('max_fund_transfer', 'fee'),
        ];

        return collect([$defaultWallet])->merge(self::collection($resource));
    }
}
