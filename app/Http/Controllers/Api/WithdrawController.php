<?php

namespace App\Http\Controllers\Api;

use App\Enums\TxnStatus;
use App\Enums\TxnType;
use App\Facades\Txn\Txn;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\WithdrawAccount;
use App\Models\WithdrawalSchedule;
use App\Traits\NotifyTrait;
use App\Traits\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class WithdrawController extends Controller
{
    use NotifyTrait, Payment;

    public function __invoke(Request $request)
    {
        if (! setting('user_withdraw', 'permission') || ! Auth::user()->withdraw_status) {
            return response()->json([
                'status' => false,
                'message' => __('Withdraw currently unavailable!'),
            ], 422);
        }

        $withdrawOffDays = WithdrawalSchedule::where('status', 0)->pluck('name')->toArray();
        $date = Carbon::now();
        $today = $date->format('l');

        if (in_array($today, $withdrawOffDays)) {
            return response()->json([
                'status' => false,
                'message' => __('Today is the off day of withdraw'),
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'amount' => ['required', 'regex:/^[0-9]+(\.[0-9][0-9]?)?$/'],
            'withdraw_account_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        // daily limit
        $todayTransaction = Transaction::whereIn('type', [TxnType::Withdraw, TxnType::WithdrawAuto])->whereDate('created_at', Carbon::today())->count();
        $dayLimit = (float) setting('withdraw_day_limit', 'fee');
        if ($todayTransaction >= $dayLimit) {
            return response()->json([
                'status' => false,
                'message' => __('Today Withdraw limit has been reached'),
            ], 422);
        }

        $input = $request->all();
        $amount = (float) $input['amount'];

        $withdrawAccount = WithdrawAccount::find($input['withdraw_account_id']);
        $withdrawMethod = $withdrawAccount->method;

        if ($amount < $withdrawMethod->min_withdraw || $amount > $withdrawMethod->max_withdraw) {
            $currencySymbol = setting('currency_symbol', 'global');

            $message = __('Please withdraw the Amount within the range :min to :max', [
                'min' => $currencySymbol.$withdrawMethod->min_withdraw,
                'max' => $currencySymbol.$withdrawMethod->max_withdraw,
            ]);

            return response()->json([
                'status' => false,
                'message' => $message,
            ], 422);
        }

        $charge = $withdrawMethod->charge_type == 'percentage' ? (($withdrawMethod->charge / 100) * $amount) : $withdrawMethod->charge;
        $totalAmount = $amount + (float) $charge;

        $user = Auth::user();
        if ($user->balance < $totalAmount) {
            return response()->json([
                'status' => false,
                'message' => __('Insufficient Balance'),
            ], 422);
        }

        $user->decrement('balance', $totalAmount);

        $payAmount = $amount * $withdrawMethod->rate;

        $type = $withdrawMethod->type == 'auto' ? TxnType::WithdrawAuto : TxnType::Withdraw;

        $txnInfo = (new Txn)->new(
            $input['amount'],
            $charge,
            $totalAmount,
            $withdrawMethod->name,
            'Withdraw With '.$withdrawAccount->method_name,
            $type,
            TxnStatus::Pending,
            $withdrawMethod->currency,
            $payAmount,
            $user->id,
            null,
            'User',
            json_decode($withdrawAccount->credentials, true)
        );

        if ($withdrawMethod->type == 'auto') {
            $gatewayCode = $withdrawMethod->gateway->gateway_code;

            self::withdrawAutoGateway($gatewayCode, $txnInfo, true);
        }

        $symbol = setting('currency_symbol', 'global');

        $shortcodes = [
            '[[full_name]]' => $txnInfo->user->full_name,
            '[[txn]]' => $txnInfo->tnx,
            '[[method_name]]' => $withdrawMethod->name,
            '[[withdraw_amount]]' => $txnInfo->amount.setting('site_currency', 'global'),
            '[[site_title]]' => setting('site_title', 'global'),
            '[[site_url]]' => route('home'),
        ];

        $this->mailNotify(setting('site_email', 'global'), 'withdraw_request', $shortcodes);
        $this->pushNotify('withdraw_request', $shortcodes, route('admin.withdraw.pending'), $user->id);
        $this->smsNotify('withdraw_request', $shortcodes, $user->phone);

        return response()->json([
            'status' => true,
            'message' => __('Withdraw request successful'),
            'data' => $txnInfo,
        ]);
    }
}
