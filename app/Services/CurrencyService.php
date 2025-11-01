<?php

namespace App\Services;

use App\Models\Currency;

class CurrencyService
{
    public static function convert($amount, $from, $to)
    {
        if ($from == $to) {
            return $amount;
        }

        $rate = static::getRate($to);
        $fromRate = static::getRate($from);

        return round(((float) $amount / $fromRate) * $rate, 2);
    }

    public static function getRate($code): float
    {
        $currency = $code instanceof Currency ? $code : Currency::where('code', $code)->first();

        return $currency?->rate ?? 1;
    }
}
