<?php

namespace App\Services;

use App\Enums\TransferType;
use App\Enums\TxnStatus;
use App\Enums\TxnType;
use App\Facades\Txn\Txn;
use App\Models\Beneficiary;
use App\Models\OthersBank;
use App\Models\Transaction;
use App\Models\User;
use App\Traits\NotifyTrait;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class TransferService
{
    use NotifyTrait;

    public function validate(User $user, array $input, $walletType = 'default')
    {
        if (! setting('transfer_status', 'permission') || ! $user->transfer_status) {
            throw ValidationException::withMessages(['error' => __('Fund transfer currently unavailable!')]);
        }

        if (! setting('kyc_fund_transfer') && ! $user->kyc) {
            throw ValidationException::withMessages(['error' => __('Please verify your KYC.')]);
        }

        $amount = $input['amount'];
        $bankId = $input['bank_id'];
        $bankInfo = OthersBank::find($bankId);
        $userWallet = $user->wallets()->find($walletType);
        $currencyCode = $walletType == 'default' ? setting('site_currency', 'global') : $userWallet->currency->code;

        if ($bankId != 0) {
            $query = Transaction::where('user_id', $user->id)
                ->where('bank_id', $bankInfo->id)
                ->where('type', TxnType::FundTransfer)
                ->whereIn('transfer_type', [TransferType::OtherBankTransfer, TransferType::OwnBankTransfer]);

            $todayAmount = CurrencyService::convert((clone $query)->whereDate('created_at', Carbon::today())->sum('amount'), setting('site_currency', 'global'), $currencyCode);
            $todayCount = (clone $query)->whereDate('created_at', Carbon::today())->count();
            $monthAmount = CurrencyService::convert((clone $query)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('amount'), setting('site_currency', 'global'), $currencyCode);
            $monthCount = (clone $query)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();

            if ($todayCount >= $bankInfo->daily_limit_maximum_count) {
                throw ValidationException::withMessages(['error' => __('Daily transaction count limit exceeded.')]);
            }
            if ($todayAmount >= CurrencyService::convert($bankInfo->daily_limit_maximum_amount, setting('site_currency', 'global'), $currencyCode)) {
                throw ValidationException::withMessages(['error' => __('Daily transaction amount limit exceeded.')]);
            }
            if ($monthAmount >= CurrencyService::convert($bankInfo->monthly_limit_maximum_amount, setting('site_currency', 'global'), $currencyCode)) {
                throw ValidationException::withMessages(['error' => __('Monthly transaction amount limit exceeded.')]);
            }
            if ($monthCount >= $bankInfo->monthly_limit_maximum_count) {
                throw ValidationException::withMessages(['error' => __('Monthly transaction count limit exceeded.')]);
            }

            // Check limits
            $min = CurrencyService::convert($bankInfo->minimum_transfer, setting('site_currency', 'global'), $currencyCode);
            $max = CurrencyService::convert($bankInfo->maximum_transfer, setting('site_currency', 'global'), $currencyCode);

            if ($amount < $min || $amount > $max) {
                throw ValidationException::withMessages([
                    'error' => __('Please Transfer the Amount within the range :min to :max', [
                        'min' => $min.' '.$currencyCode,
                        'max' => $max.' '.$currencyCode,
                    ]),
                ]);
            }
        } else {
            $min = CurrencyService::convert(setting('min_fund_transfer', 'fee'), setting('site_currency', 'global'), $currencyCode);
            $max = CurrencyService::convert(setting('max_fund_transfer', 'fee'), setting('site_currency', 'global'), $currencyCode);
            if ($amount < $min || $amount > $max) {
                throw ValidationException::withMessages(['error' => __('Transfer amount must be between :min and :max', ['min' => $min.' '.$currencyCode, 'max' => $max.' '.$currencyCode])]);
            }
        }

        if ($bankId == 0) {
            $accountNumber = $input['manual_data']['account_number'] ?? null;
            $beneficiaryId = $input['beneficiary_id'] ?? null;
            if ($beneficiaryId) {
                $beneficiary = Beneficiary::find($beneficiaryId);
                $accountNumber = $beneficiary->account_number ?? $accountNumber;
            }
            $receiver = User::where('account_number', sanitizeAccountNumber($accountNumber))->first();
            if (! $receiver) {
                throw ValidationException::withMessages(['error' => __('Receiver Account not found!')]);
            }
        }
    }

    public function process(User $user, array $input, $walletType = 'default')
    {
        $amount = $input['amount'];
        $bankId = $input['bank_id'];
        $bankInfo = OthersBank::find($bankId);
        $currency = setting('site_currency', 'global');
        $userWallet = $user->wallets()->find($walletType);
        $currencyCode = $walletType == 'default' ? setting('site_currency', 'global') : $userWallet->currency->code;

        $manualData = $input['manual_data'] ?? [];
        $beneficiary = Beneficiary::find($input['beneficiary_id'] ?? null);
        $accountNumber = $beneficiary?->account_number ?? $manualData['account_number'];
        $receiver = User::where('account_number', sanitizeAccountNumber($accountNumber))->first();

        $charge = $this->calculateTransferCharge($bankInfo, $amount, $currencyCode);

        $finalAmount = $amount + $charge;
        $walletType = $walletType;

        if ($walletType !== 'default') {
            $wallet = $userWallet;
            $walletType = $wallet?->id;
            $wallet = $wallet;
            $balance = round($wallet?->balance, 2);
        } else {
            $wallet = null;
            $balance = $user->balance;
        }

        if ($balance < $finalAmount) {
            throw ValidationException::withMessages(['error' => __('Insufficient balance.')]);
        }

        $txnType = TxnType::FundTransfer;
        $transferType = $bankId == 0 ? TransferType::OwnBankTransfer : TransferType::OtherBankTransfer;

        $txnInfo = Txn::transfer($amount, $charge, $finalAmount, 'Transfer to '.$accountNumber, $txnType, TxnStatus::Pending, $currency, $finalAmount, $user->id, null, 'User', $beneficiary?->id, $bankId, $input['purpose'], $transferType, $manualData, $wallet->id ?? 'default');

        if ($bankId == 0 && $receiver) {

            $receiverWallet = $receiver->wallets()->whereRelation('currency', 'code', $currencyCode)->first();

            if ($walletType !== 'default' && setting('multiple_currency', 'permission') && $receiverWallet == null) {
                throw ValidationException::withMessages(['error' => __('Receiver wallet not found')]);
            }

            $transaction = Transaction::tnx($txnInfo['tnx']);
            $transaction->update(['status' => TxnStatus::Success]);
            $receiverWallet ? $receiverWallet->increment('balance', $amount) : $receiver->increment('balance', $amount);
            (new Txn)->new($amount, $charge, $finalAmount, 'System', 'Received money from '.$user->full_name, TxnType::ReceiveMoney, TxnStatus::Success, $currency, $finalAmount, $receiver->id, null, 'User', [], $wallet->id ?? 'default', approvalCause: $input['purpose'] ?? '');
        }

        $wallet ? $wallet->decrement('balance', $finalAmount) : $user->decrement('balance', $finalAmount);

        $this->sendNotification($user, $txnInfo, $accountNumber, $manualData);

        return [
            'amount' => $amount,
            'currency' => $currencyCode,
            'account' => $accountNumber,
            'tnx' => $txnInfo['tnx'],
        ];
    }

    public function sendNotification($user, $txnInfo, $account_number, $manual_data)
    {
        $shortcodes = [
            '[[full_name]]' => $user->full_name,
            '[[email]]' => $user->email,
            '[[charge]]' => $txnInfo->charge,
            '[[amount]]' => $txnInfo->amount,
            '[[total_amount]]' => $txnInfo->final_amount,
            '[[status]]' => $txnInfo->status->value,
            '[[account_number]]' => $account_number,
            '[[account_name]]' => data_get($manual_data, 'account_name'),
            '[[branch_name]]' => data_get($manual_data, 'branch_name'),
            '[[site_title]]' => setting('site_title', 'global'),
            '[[site_url]]' => route('home'),
        ];

        $this->pushNotify('fund_transfer_request', $shortcodes, route('admin.fund.transfer.pending'), auth()->id(), 'Admin');
        $this->mailNotify($txnInfo->user->email, 'fund_transfer_request', $shortcodes);
        $this->smsNotify('fund_transfer_request', $shortcodes, $txnInfo->user->phone);
    }

    protected function calculateTransferCharge($bankInfo, $amount, $currencyCode)
    {
        $siteCurrency = setting('site_currency', 'global');
        $needsCurrencyConversion = $currencyCode !== $siteCurrency;

        if ($bankInfo) {
            $chargeType = $bankInfo->charge_type;
            $baseCharge = $bankInfo->charge;
        } else {
            $chargeType = setting('fund_transfer_charge_type', 'fee');
            $baseCharge = setting('fund_transfer_charge', 'fee');
        }

        if ($needsCurrencyConversion && $chargeType === 'fixed') {
            $baseCharge = CurrencyService::convert(
                $baseCharge,
                $siteCurrency,
                $currencyCode
            );
        }

        return ($chargeType === 'percentage')
            ? ($baseCharge / 100) * $amount
            : $baseCharge;
    }
}
