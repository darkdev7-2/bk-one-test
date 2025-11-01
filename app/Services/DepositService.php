<?php

namespace App\Services;

use App\Enums\TxnStatus;
use App\Enums\TxnType;
use App\Facades\Txn\Txn;
use App\Http\Requests\DepositRequest;
use App\Models\DepositMethod;
use App\Models\User;
use App\Traits\ImageUpload;
use App\Traits\NotifyTrait;
use App\Traits\Payment;
use Illuminate\Validation\ValidationException;

class DepositService
{
    use ImageUpload, NotifyTrait, Payment;

    public function validate(User $user, DepositRequest $input)
    {
        if (! setting('user_deposit', 'permission') || ! $user->deposit_status) {
            throw ValidationException::withMessages(['error' => __('Deposit currently unavailable!')]);
        }

        if (! setting('kyc_deposit') && $user->kyc != 1) {
            throw ValidationException::withMessages(['error' => __('Please verify your KYC.')]);
        }

        $input->validated();

        $gatewayInfo = DepositMethod::code($input['gateway_code'])->first();
        $amount = $input['amount'];

        if ($amount < $gatewayInfo->minimum_deposit || $amount > $gatewayInfo->maximum_deposit) {
            $currencySymbol = setting('currency_symbol', 'global');
            $message = 'Please Deposit the Amount within the range '.$currencySymbol.$gatewayInfo->minimum_deposit.' to '.$currencySymbol.$gatewayInfo->maximum_deposit;
            throw ValidationException::withMessages(['error' => $message]);
        }
    }

    public function process(User $user, DepositRequest $input, $walletType = 'default')
    {
        $gatewayInfo = DepositMethod::code($input['gateway_code'])->where('status', 1)->first();
        $amount = $input['amount'];
        $charge = $gatewayInfo->charge_type == 'percentage' ? (($gatewayInfo->charge / 100) * $amount) : $gatewayInfo->charge;
        $finalAmount = (float) $amount + (float) $charge;
        $payAmount = $finalAmount * $gatewayInfo->rate;
        $type = TxnType::Deposit;

        $manualData = [];

        if (isset($input['manual_data'])) {
            $type = TxnType::ManualDeposit;
            foreach ($input['manual_data'] as $key => $value) {
                if (is_file($value)) {
                    $manualData[$key] = self::imageUploadTrait($value);
                } else {
                    $manualData[$key] = $value;
                }
            }
        }

        $currency = $gatewayInfo->currency;
        $txnInfo = (new Txn)->new($amount, $charge, $finalAmount, $gatewayInfo->gateway_code, 'Deposit With '.$gatewayInfo->name, $type, TxnStatus::Pending, $currency, $payAmount, $user->id, null, 'User', $manualData, $walletType);

        $result = self::depositAutoGateway($gatewayInfo->gateway_code, $txnInfo);

        if (request()->expectsJson()) {
            return [
                'redirect_url' => $result instanceof \Illuminate\Http\RedirectResponse ? $result->getTargetUrl() : null,
                'is_redirect' => $result instanceof \Illuminate\Http\RedirectResponse,
                'tnx' => $txnInfo['tnx'],
                'amount' => $amount.' '.$currency,
            ];
        }

        return $result;
    }
}
