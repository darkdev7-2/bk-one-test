<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WithdrawAccountResource extends JsonResource
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
            'method_name' => $this->method_name,
            'currency' => $this->method->currency,
            'method' => [
                'id' => $this->method->id,
                'name' => $this->method->name,
                'icon' => $this->getLogo(),
                'type' => $this->method->type,
                'min_withdraw' => (float) $this->method->min_withdraw,
                'max_withdraw' => (float) $this->method->max_withdraw,
                'charge' => $this->method->charge,
                'rate' => (float) $this->method->rate,
                'charge_type' => $this->method->charge_type,
                'time' => $this->method->required_time.' '.$this->method->required_time_format,
            ],
            'fields' => $this->fields,
        ];
    }

    public function getLogo()
    {
        $icon = $this->method->icon;
        if ($this->method->gateway_id != null && $this->method->icon == '') {
            $icon = $this->method->gateway->logo;
        }

        return asset($icon);
    }
}
