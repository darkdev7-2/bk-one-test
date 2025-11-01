<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\GatewayType;
use App\Http\Controllers\Controller;
use App\Models\DepositMethod;
use App\Traits\NotifyTrait;
use App\Traits\Payment;

class GatewayController extends Controller
{
    use NotifyTrait, Payment;

    public function gateway($code)
    {
        $gateway = DepositMethod::code($code)->where('status', 1)->first();

        if ($gateway->type == GatewayType::Manual->value) {
            $fieldOptions = $gateway->field_options;
            $paymentDetails = $gateway->payment_details;
            $gateway = array_merge($gateway->toArray(), ['credentials' => view('frontend::gateway.include.manual', compact('fieldOptions', 'paymentDetails'))->render()]);
        } else {
            $gatewayCurrency = is_custom_rate($gateway->gateway->gateway_code) ?? $gateway->currency;
            $gateway['currency'] = $gatewayCurrency;
        }

        return $gateway;
    }

    public function getGateways($currency)
    {
        $gateways = DepositMethod::query()->where('currency', $currency)->where('status', 1)->get();

        $options = '<option selected>'.__('--Select Gateway--').'</option>';

        foreach ($gateways as $gateway) {
            $options .= sprintf('<option data-logo="%s" value="%s">%s</option>', asset($gateway->gateway->logo), $gateway->gateway_code, $gateway->name);
        }

        return response()->json([
            'options' => $options,
        ]);
    }

    // list json
    public function gatewayList()
    {
        $gateways = DepositMethod::where('status', 1)->get();

        return view('frontend::gateway.include.__list', compact('gateways'));
    }
}
