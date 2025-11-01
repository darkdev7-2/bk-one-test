<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BillServiceResource extends JsonResource
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
            'code' => $this->code,
            'country' => $this->country,
            'country_code' => $this->country_code,
            'fields' => json_decode($this->label),
            'currency' => $this->currency,
            'rate' => $this->getConversionRate(),
            'amount' => $this->amount,
            'min_amount' => $this->min_amount,
            'max_amount' => $this->max_amount,
            'charge' => $this->charge,
            'charge_type' => $this->charge_type,
        ];
    }

    protected function getConversionRate()
    {
        $currencyRates = json_decode(plugin_active(ucfirst($this->method))->data, true);

        $rate = data_get($currencyRates, 'currencies.'.$this->currency, 0);

        return round($rate, 2);
    }
}
