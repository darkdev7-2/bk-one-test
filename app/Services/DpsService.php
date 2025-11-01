<?php

namespace App\Services;

use App\Enums\DpsStatus;
use App\Enums\TxnStatus;
use App\Enums\TxnType;
use App\Facades\Txn\Txn;
use App\Models\Dps;
use App\Models\DpsTransaction;
use App\Models\LevelReferral;
use App\Traits\NotifyTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class DpsService
{
    use NotifyTrait;

    public function validate($user, $plan)
    {
        if (! setting('user_dps', 'permission') || ! $user->dps_status) {
            throw ValidationException::withMessages(['error' => __('DPS currently unavailable!')]);
        } elseif (! setting('kyc_dps') && $user->kyc != 1) {
            throw ValidationException::withMessages(['error' => __('Please verify your KYC.')]);
        }

        // When plan not found then throw error
        if (! $plan) {
            throw ValidationException::withMessages(['error' => __('Dps Plan Not found.')]);
        }

        // Get per installment amount
        $amount = $plan->per_installment;
        // Get currency symbol
        $currency = setting('currency_symbol', 'global');

        // If user balance is low then get error
        if ($user->balance <= $amount) {
            $message = __('Insufficient Balance. Your balance must be upper than '.$currency.$amount);

            throw ValidationException::withMessages(['error' => $message]);
        }
    }

    public function subscribe($user, $plan)
    {
        // Create dps for user
        $dps = Dps::create([
            'dps_id' => mt_rand(10000000, 99999999),
            'plan_id' => $plan->id,
            'user_id' => $user->id,
            'per_installment' => $plan->per_installment,
        ]);

        // Get per installment
        $amount = $plan->per_installment;

        // Store all installments
        $installments = [];
        for ($i = 0; $i < $plan->total_installment; $i++) {
            $installments[] = [
                'dps_id' => $dps->id,
                'paid_amount' => $dps->per_installment,
                'installment_date' => Carbon::parse($dps->created_at)->addDays($plan->interval * $i),
            ];
        }

        // Insert installments into dps transaction table
        DpsTransaction::insert($installments);

        // paid first installment
        $transaction = DpsTransaction::where('dps_id', $dps->id)->first();
        $transaction->given_date = today();
        $transaction->paid_amount = $amount;
        $transaction->charge = 0;
        $transaction->final_amount = $amount;
        $transaction->save();

        // Balance deducted from user
        $user->decrement('balance', $amount);

        (new Txn)->new($amount, 0, $amount, 'System', 'DPS Plan Subscribed #'.$dps->dps_id.'', TxnType::DpsInstallment, TxnStatus::Success, '', null, $user->id, null, 'User');

        // Level referral
        if (setting('dps_level')) {
            $level = LevelReferral::where('type', 'dps')->max('the_order') + 1;
            creditReferralBonus($user, 'dps', $transaction->paid_amount, $level);
        }

        $dps->given_installment = 1;
        $dps->save();

        $shortcodes = [
            '[[site_title]]' => setting('site_title', 'global'),
            '[[site_url]]' => route('home'),
            '[[plan_name]]' => $dps->plan->name,
            '[[user_name]]' => $user->full_name,
            '[[full_name]]' => $user->full_name,
            '[[dps_id]]' => $dps->dps_id,
            '[[per_installment]]' => $dps->per_installment,
            '[[interest_rate]]' => $dps->plan->interest_rate,
            '[[given_installment]]' => $dps->given_installment,
            '[[total_installment]]' => count($dps->transactions),
            '[[matured_amount]]' => getTotalMature($dps),
        ];

        $this->smsNotify('dps_opened', $shortcodes, $dps->user->phone);
        $this->mailNotify($dps->user->email, 'dps_opened', $shortcodes);
        $this->pushNotify('dps_opened', $shortcodes, route('admin.dps.details', $dps->id), $dps->user_id, 'Admin');
    }

    public function cancel($dps)
    {
        // Calculate cancel fee
        $cancel_fee = $dps->plan->cancel_fee_type == 'percentage' ? (($dps->plan->cancel_fee / 100) * $dps->plan->per_installment) : $dps->plan->cancel_fee;

        // DPS amount back to user balance
        $refund_amount = $dps->per_installment - $cancel_fee;
        $dps->user->increment('balance', $refund_amount);

        // Save dps cancel info
        $dps->cancel_date = now();
        $dps->cancel_fee = $cancel_fee;
        $dps->status = DpsStatus::Closed;
        $dps->save();

        (new Txn)->new($cancel_fee, 0, $cancel_fee, 'System', 'DPS Cancelled #'.$dps->dps_id.'', TxnType::DpsCancelled, TxnStatus::Success, '', null, $dps->user_id, null, 'User');

        // Shortcodes for notification
        $shortcodes = [
            '[[site_title]]' => setting('site_title', 'global'),
            '[[site_url]]' => route('home'),
            '[[plan_name]]' => $dps->plan->name,
            '[[full_name]]' => $dps->user->full_name,
            '[[dps_id]]' => $dps->dps_id,
            '[[cancel_fee]]' => $cancel_fee,
            '[[per_installment]]' => $dps->per_installment,
            '[[interest_rate]]' => $dps->plan->interest_rate,
            '[[given_installment]]' => $dps->given_installment,
            '[[total_installment]]' => count($dps->transactions),
            '[[matured_amount]]' => getTotalMature($dps),
        ];

        $this->smsNotify('dps_closed', $shortcodes, $dps->user->phone);
        $this->mailNotify($dps->user->email, 'dps_closed', $shortcodes);
        $this->pushNotify('dps_closed', $shortcodes, route('user.dps.history', $dps->dps_id), $dps->user_id);
        $this->pushNotify('dps_closed', $shortcodes, route('admin.dps.details', $dps->id), $dps->user_id, 'Admin');
    }

    public function checkDpsCancellationAbility($dps)
    {
        $cancellationDays = (int) $dps->plan->cancel_days;
        $creationDate = Carbon::parse($dps->created_at);
        $currentDate = Carbon::now();

        if ($dps->plan->cancel_type == 'fixed' && $currentDate->diffInDays($creationDate) > $cancellationDays) {
            throw ValidationException::withMessages([
                'error' => __('DPS cancellation days is over!'),
            ]);
        }

        if ($dps->status == DpsStatus::Mature) {
            throw ValidationException::withMessages([
                'error' => __('Sorry, Your DPS is completed!'),
            ]);
        } elseif ($dps->status == DpsStatus::Closed) {
            throw ValidationException::withMessages([
                'error' => __('Your DPS is closed!'),
            ]);
        }
    }

    public function increase($dps, Request $request)
    {
        $plan = $dps->plan;

        // Check user balance
        $total_amount = $plan->increment_charge_type ? $request->increase_amount + $plan->increment_fee : $request->increase_amount;
        if ($total_amount > $dps->user->balance) {
            throw ValidationException::withMessages(['error' => __('Insufficent Balance.')]);
        }

        // Limit check & increase
        if ($dps->plan->increment_type == 'unlimited' || $dps->plan->increment_type == 'fixed' && $dps->plan->increment_times > $dps->increment_count) {
            $dps->increment_count += 1;
        } else {
            throw ValidationException::withMessages(['error' => __('You reached the increment limit!')]);
        }

        // Increase amount deducted from user balance
        $dps->user->decrement('balance', $total_amount);

        (new Txn)->new($request->integer('increase_amount'), $plan->increment_charge_type ? $plan->increment_fee : 0, $total_amount, 'System', 'DPS Increased #'.$dps->dps_id.'', TxnType::DpsIncrease, TxnStatus::Success, '', null, $dps->user_id, null, 'User');

        // Increase Amount
        $dps->per_installment += $request->integer('increase_amount');
        $dps->save();

        // Update per installment fee in transactions data
        $dps->transactions()->whereNull('given_date')->update([
            'paid_amount' => $dps->per_installment,
        ]);
    }

    public function validateIncrease($dps, Request $request)
    {
        if (! $dps->plan->is_upgrade) {
            throw ValidationException::withMessages([
                'error' => __('You can\'t increase amount for this plan.'),
            ]);
        }

        if ($dps->status == DpsStatus::Mature || $dps->status == DpsStatus::Closed) {
            throw ValidationException::withMessages([
                'error' => __('Sorry, Your DPS is completed or closed!'),
            ]);
        }

        $plan = $dps->plan;

        // Get currency symbol
        $currency = setting('currency_symbol', 'global');

        $min_increase_amount = $plan->min_increment_amount;
        $max_increment_amount = $plan->max_increment_amount;
        $message = __('You can increase minimum amount is :minimum_amount and maximum is :maximum_amount', ['minimum_amount' => $currency.$min_increase_amount, 'maximum_amount' => $currency.$max_increment_amount]);

        $validator = Validator::make($request->all(), [
            'increase_amount' => ['required', 'integer', 'min:'.$min_increase_amount, 'max:'.$max_increment_amount],
        ], [
            'increase_amount.min' => $message,
            'increase_amount.max' => $message,
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages([
                'error' => $validator->errors()->first(),
            ]);
        }
    }

    public function validateDecrease($dps, Request $request)
    {
        // Get plan
        $plan = $dps->plan;

        // Check decrement ability
        if (! $plan->is_downgrade) {
            notify()->error(__('You can\'t decrease amount for this plan.'), 'Error');

            return back();
        }

        // Check dps status
        if ($dps->status == DpsStatus::Mature || $dps->status == DpsStatus::Closed) {
            throw ValidationException::withMessages([
                'error' => __('Sorry, Your DPS is completed or closed!'),
            ]);
        }

        // Get currency symbol
        $currency = setting('currency_symbol', 'global');

        $min_decrement_amount = $plan->min_decrement_amount;
        $max_decrement_amount = $plan->max_decrement_amount;
        $message = __('You can decrease minimum amount is :minimum_amount and maximum is :maximum_amount', ['minimum_amount' => $currency.$min_decrement_amount, 'maximum_amount' => $currency.$max_decrement_amount]);

        $validator = Validator::make($request->all(), [
            'decrease_amount' => ['required', 'integer', 'min:'.$min_decrement_amount, 'max:'.$max_decrement_amount],
        ], [
            'decrease_amount.min' => $message,
            'decrease_amount.max' => $message,
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages([
                'error' => $validator->errors()->first(),
            ]);
        }

        if ($dps->per_installment <= $request->integer('decrease_amount')) {
            throw ValidationException::withMessages([
                'error' => __('Decrease amount should be equal to or less than per installation fee.'),
            ]);
        }
    }

    public function decrease($dps, Request $request)
    {
        // Get plan
        $plan = $dps->plan;

        // Limit check & decrease
        $charge = 0;
        if ($plan->decrement_type == 'unlimited' || $plan->decrement_type == 'fixed' && $plan->decrement_times > $dps->decrement_count) {
            $dps->decrement_count += 1;
            if ($plan->decrement_charge_type) {
                $charge = $plan->decrement_fee;
            }
        } else {
            throw ValidationException::withMessages([
                'error' => __('You reached the decrement limit!'),
            ]);
        }

        // Decrease amount added to user balance
        $dps->user->increment('balance', $request->integer('decrease_amount') - $charge);

        (new Txn)->new($request->integer('decrease_amount') - $charge, $charge, $request->integer('decrease_amount'), 'System', 'DPS Decreased #'.$dps->dps_id.'', TxnType::DpsDecrease, TxnStatus::Success, '', null, $dps->user_id, null, 'User');

        // Decrease Amount
        $dps->per_installment -= $request->integer('decrease_amount');
        $dps->save();

        // Update per installment fee in transactions data
        $dps->transactions()->whereNull('given_date')->update([
            'paid_amount' => $dps->per_installment,
        ]);
    }
}
