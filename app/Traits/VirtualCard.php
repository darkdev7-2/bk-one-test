<?php

namespace App\Traits;

use Card\Flutterwave\FlutterwaveCard;
use Card\Stripe\StripeCard;
use Card\Ufitpay\UfitpayCard;

trait VirtualCard
{
    public function cardProviderMap($code)
    {
        $providers = [
            'stripe' => StripeCard::class,
            'flutterwave' => FlutterwaveCard::class,
            'ufitpay' => UfitpayCard::class,
        ];
        if (array_key_exists($code, $providers)) {
            return app($providers[$code]);
        }

        notify()->error(__('No provider found!'));

        return back();
    }
}
