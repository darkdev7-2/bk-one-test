<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletResource extends JsonResource
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
            'name' => $this->currency->name,
            'account_no' => null,
            'balance' => $this->currency->symbol.$this->balance,
            'code' => $this->currency->code,
            'symbol' => $this->currency->symbol,
        ];
    }

    public static function collectionWithDefault($resource, $user)
    {
        $currency = setting('site_currency', 'global');
        $currency_symbol = setting('currency_symbol', 'global');

        $defaultWallet = [
            'id' => 0,
            'name' => __('Default Wallet'),
            'account_no' => $user->account_number,
            'balance' => $currency_symbol.$user->balance,
            'code' => $currency,
            'symbol' => $currency_symbol,
        ];

        return collect([$defaultWallet])->merge(self::collection($resource));
    }
}
