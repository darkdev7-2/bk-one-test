<?php

namespace App\Services;

use App\Enums\TransferType;
use App\Enums\TxnStatus;
use App\Enums\TxnType;
use App\Facades\Txn\Txn;
use App\Models\Transaction;
use App\Models\User;
use App\Models\WireTransfar;
use App\Traits\ImageUpload;
use App\Traits\NotifyTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class WireTransferService
{
    use ImageUpload, NotifyTrait;

    public function validate(User $user, Request $request)
    {
        if (!setting('transfer_status', 'permission') || !$user->transfer_status) {
            throw ValidationException::withMessages(['error' => __('Fund transfer currently unavailable!')]);
        }

        if (!setting('kyc_fund_transfer') && !$user->kyc) {
            throw ValidationException::withMessages(['error' => __('Please verify your KYC.')]);
        }

        $input = $request->all();
        $amount = $input['amount'];
        $wireTransfer = WireTransfar::first();
        $currencySymbol = setting('currency_symbol', 'global');

        if (($amount < $wireTransfer->minimum_transfer || $amount > $wireTransfer->maximum_transfer)) {

            $message = __('Please Transfer the Amount within the range :symbol:min to :symbol:max', [
                'symbol' => $currencySymbol,
                'min' => $wireTransfer->minimum_transfer,
                'max' => $wireTransfer->maximum_transfer,
            ]);

            throw ValidationException::withMessages(['error' => $message]);
        }

        // Check daily transfer limit
        $todayTotalTransCount = Transaction::query()
            ->where('user_id', auth()->id())
            ->whereDate('created_at', Carbon::today())
            ->where('type', TxnType::FundTransfer)
            ->where('transfer_type', TransferType::WireTransfer)
            ->count();

        if ($todayTotalTransCount >= $wireTransfer->daily_limit_maximum_count) {
            throw ValidationException::withMessages(['error' => __('Daily wire transfer limit exceeded.')]);
        }

        // Check monthly transfer limit
        $monthlyTotalTransCount = Transaction::query()
            ->where('user_id', auth()->id())
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->where('type', TxnType::FundTransfer)
            ->where('transfer_type', TransferType::WireTransfer)
            ->count();

        if ($monthlyTotalTransCount >= $wireTransfer->monthly_limit_maximum_count) {
            throw ValidationException::withMessages(['error' => __('Monthly wire transfer limit exceeded.')]);
        }

        // Check daily transfer amount limit
        $dailyTotalAmountTrans = Transaction::query()
            ->where('user_id', auth()->id())
            ->whereDate('created_at', Carbon::today())
            ->where('type', TxnType::FundTransfer)
            ->where('transfer_type', TransferType::WireTransfer)
            ->sum('amount');
        if ($dailyTotalAmountTrans >= $wireTransfer->daily_limit_maximum_amount) {
            throw ValidationException::withMessages(['error' => __('Daily wire transfer amount limit exceeded.')]);
        }

        // Check monthly transfer amount limit
        $monthlyTotalAmountTrans = Transaction::query()
            ->where('user_id', auth()->id())
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->where('type', TxnType::FundTransfer)
            ->where('transfer_type', TransferType::WireTransfer)
            ->sum('amount');

        if ($monthlyTotalAmountTrans >= $wireTransfer->monthly_limit_maximum_amount) {
            throw ValidationException::withMessages(['error' => __('Monthly wire transfer amount limit exceeded.')]);
        }

        $validator = Validator::make($request->all(), [
            'data' => 'required|array|min:1',
            'amount' => ['required', 'regex:/^[0-9]+(\.[0-9][0-9]?)?$/'],
        ]);

        if ($validator->fails()) {
            return ValidationException::withMessages(['error' => $validator->errors()->first()]);
        }
    }

    public function process(Request $request)
    {
        $input = $request->all();
        $amount = $input['amount'];
        $wireTransfer = WireTransfar::first();
        $currency = setting('currency', 'global');
        $currencySymbol = setting('currency_symbol', 'global');
        $charge = $wireTransfer->charge_type == 'percentage' ? (($wireTransfer->charge / 100) * $amount) : $wireTransfer->charge;
        $finalAmount = (float) $amount + (float) $charge;
        $payAmount = $finalAmount;
        $type = TxnType::FundTransfer;
        $transferType = TransferType::WireTransfer;


        $manualField = $input['data'] ?? [];
        foreach ($manualField as $key => $value) {
            if (is_file($value)) {
                $manualField[$key] = self::imageUploadTrait($value);
            }
        }

        $customDataAsText = collect($input['data'])->transform(function ($value, $key) {
            return "$key: $value";
        })->implode(', ');

        $txnInfo = Txn::transfer($input['amount'], $charge, $finalAmount, 'Wire Transfer to ' . $customDataAsText, $type, TxnStatus::Pending, $currency, $payAmount, auth()->id(), null, 'User', null, null, null, $transferType, $manualField);


        $user = auth()->user();

        $shortcodes = [
            '[[full_name]]' => $user->full_name,
            '[[email]]' => $user->email,
            '[[charge]]' => $txnInfo->charge,
            '[[amount]]' => $txnInfo->amount,
            '[[total_amount]]' => $txnInfo->final_amount,
            '[[status]]' => $txnInfo->status->value,
            '[[site_title]]' => setting('site_title', 'global'),
            '[[site_url]]' => route('home'),
        ];

        // decrement the balance

        $user->decrement('balance', $finalAmount);


        $this->mailNotify($txnInfo->user->email, 'wire_transfer', $shortcodes);
        $this->smsNotify('wire_transfer', $shortcodes, $txnInfo->user->phone);
        $this->pushNotify('wire_transfer_request', $shortcodes, route('admin.fund.transfer.pending'), $txnInfo->user->id, 'Admin');

        return [
            'amount' => $currencySymbol . $amount,
            'account' => $customDataAsText,
            'tnx' => $txnInfo['tnx'],
            'currency' => $currency,
            'status' => $txnInfo['status']->value,
            'charge' => $txnInfo->charge,
            'total_amount' => $txnInfo->final_amount,
        ];
    }
}
