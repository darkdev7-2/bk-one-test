<?php

namespace App\Services;

use App\Enums\FdrStatus;
use App\Enums\TxnStatus;
use App\Enums\TxnType;
use App\Facades\Txn\Txn;
use App\Models\Fdr;
use App\Models\FDRTransaction;
use App\Models\LevelReferral;
use App\Traits\NotifyTrait;
use App\Traits\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class FdrService
{
    use NotifyTrait, Payment;

    public function validate($user, $plan)
    {
        if (! setting('user_fdr', 'permission') || ! $user->fdr_status) {
            throw ValidationException::withMessages(['error' => __('FDR currently unavailable!')]);
        } elseif (! setting('kyc_fdr') && $user->kyc != 1) {
            throw ValidationException::withMessages(['error' => __('Please verify your KYC.')]);
        }

        // When plan not found then throw error
        if (! $plan) {
            throw ValidationException::withMessages(['error' => __('FDR Plan Not found.')]);
        }
    }

    public function subscribe($plan, $user, Request $request)
    {
        if ($plan->minimum_amount > $request->amount || $plan->maximum_amount < $request->amount) {
            throw ValidationException::withMessages(['error' => __('You can FDR minimum :minimum_amount and maximum :maximum_amount', ['minimum_amount' => $plan->minimum_amount, 'maximum_amount' => $plan->maximum_amount])]);
        }

        if ($user->balance < $request->amount) {
            throw ValidationException::withMessages(['error' => __('Insufficient Balance.')]);
        }

        $currency = setting('site_currency', 'global');

        $fdr = Fdr::create([
            'fdr_id' => 'F'.random_int(10000000, 99999999),
            'user_id' => $user->id,
            'fdr_plan_id' => $plan->id,
            'amount' => $request->amount,
            'end_date' => now()->addDays($plan->locked),
        ]);

        $total_installment = (int) $plan->locked / (int) $plan->intervel;

        $interest = ($fdr->amount / 100) * $plan->interest_rate;

        $fdrTransactions = [];

        for ($i = 1; $i <= (int) $total_installment; $i++) {

            if ($plan->is_compounding) {

                $fdr->amount += $interest;
            }

            $fdrTransactions[] = [
                'fdr_id' => $fdr->id,
                'given_date' => now()->addDays($plan->intervel * $i),
                'given_amount' => $interest,
            ];
        }

        FDRTransaction::insert($fdrTransactions);

        if (setting('fdr_level')) {
            $level = LevelReferral::where('type', 'fdr')->max('the_order') + 1;
            creditReferralBonus($user, 'fdr', $request->amount, $level);
        }

        $user->balance -= $request->amount;
        $user->save();

        (new Txn)->new($request->amount, 0, $request->amount, 'System', 'FDR Plan Subscribed #'.$fdr->fdr_id.'', TxnType::Fdr, TxnStatus::Success, '', null, $user->id, null, 'User');

        $trx = \App\Models\FDRTransaction::where('fdr_id', $fdr->id)->where('paid_amount', null)->first();

        $shortcodes = [
            '[[site_title]]' => setting('site_title', 'global'),
            '[[site_url]]' => route('home'),
            '[[plan_name]]' => $fdr->plan->name,
            '[[user_name]]' => $user->full_name,
            '[[full_name]]' => $user->full_name,
            '[[fdr_id]]' => $fdr->fdr_id,
            '[[per_installment]]' => '',
            '[[interest_rate]]' => $fdr->plan->interest_rate,
            '[[given_installment]]' => 0,
            '[[total_installment]]' => count($fdr->transactions),
            '[[amount]]' => $fdr->amount.' '.$currency,
            '[[installment_interval]]' => $fdr->plan->intervel,
            '[[next_installment_date]]' => $trx?->given_date->format('d M Y'),
        ];

        $this->smsNotify('fdr_opened', $shortcodes, $fdr->user->phone);
        $this->mailNotify($fdr->user->email, 'fdr_opened', $shortcodes);
        $this->pushNotify('fdr_opened', $shortcodes, route('admin.fdr.details', $fdr->id), $fdr->user_id, 'Admin');
    }

    public function valdiateIncrement(Request $request, Fdr $fdr)
    {
        $validator = Validator::make($request->all(), [
            'increase_amount' => 'required|integer',
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages(['error' => $validator->errors()->first()]);
        }

        // Check decrement ability
        if (! $fdr->plan->is_deduct_fund_fdr) {
            throw ValidationException::withMessages(['error' => __('You can\'t decrease amount for this plan.')]);
        }

        // Check fdr
        $this->checkAbility($fdr);

        // Check limits
        $amount = $request->integer('increase_amount');
        // Get plan
        $plan = $fdr->plan;
        // Get currency symbol
        $currency = setting('currency_symbol', 'global');

        // Check increase min amount & max amount
        $min_increase_amount = $plan->min_increment_amount;
        $max_increment_amount = $plan->max_increment_amount;

        if ($amount < $min_increase_amount || $amount > $max_increment_amount) {
            $message = __('You can increase minimum amount is :minimum_amount and maximum is :maximum_amount', ['minimum_amount' => $currency.$min_increase_amount, 'maximum_amount' => $currency.$max_increment_amount]);
            throw ValidationException::withMessages(['error' => $message]);
        }
    }

    public function increment(Request $request, Fdr $fdr)
    {
        // Check limit
        $amount = $request->integer('increase_amount');
        // Get plan
        $plan = $fdr->plan;
        // Get currency symbol
        $currency = setting('currency_symbol', 'global');

        // Check user balance
        $total_amount = $plan->increment_charge_type ? $request->increase_amount + $plan->increment_fee : $request->increase_amount;
        if ($total_amount >= $fdr->user->balance) {
            throw ValidationException::withMessages(['error' => __('Insufficent Balance.')]);
        }

        // Limit check & increase
        if ($plan->increment_type == 'unlimited' || $plan->increment_type == 'fixed' && $plan->increment_times > $fdr->increment_count) {
            $fdr->increment_count += 1;
            if ($plan->increment_charge_type) {
                $fdr->user->decrement('balance', $plan->increment_fee);
            }
        } else {
            throw ValidationException::withMessages(['error' => __('You reached the increment limit!')]);
        }

        // Increase FDR Amount
        $fdr->amount += $request->integer('increase_amount');
        $fdr->save();

        // Deduct balance from user
        $fdr->user->decrement('balance', $request->integer('increase_amount'));

        // Calculate interest amount
        $intereset_amount = ($fdr->amount / 100) * $plan->interest_rate;

        // Update per installment fee in transactions data
        $fdr->transactions()->whereNull('paid_amount')->update([
            'given_amount' => $intereset_amount,
        ]);

        (new Txn)->new($request->integer('increase_amount'), $plan->increment_charge_type ? $plan->increment_fee : 0, $total_amount, 'System', 'FDR Increased #'.$fdr->fdr_id.'', TxnType::FdrIncrease, TxnStatus::Success, null, null, auth()->id(), null, 'User');
    }

    public function valdiateDecrement(Request $request, Fdr $fdr)
    {
        $validator = Validator::make($request->all(), [
            'decrease_amount' => 'required|integer',
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages(['error' => $validator->errors()->first()]);
        }

        // Check decrement ability
        if (! $fdr->plan->is_deduct_fund_fdr) {
            throw ValidationException::withMessages(['error' => __('You can\'t decrease amount for this plan.')]);
        }

        // Check fdr
        $this->checkAbility($fdr);

        // Check limits
        $amount = $request->integer('decrease_amount');
        // Get plan
        $plan = $fdr->plan;
        // Get currency symbol
        $currency = setting('currency_symbol', 'global');

        // Check decrease min amount & max amount
        $min_decrease_amount = $plan->min_decrement_amount;
        $max_decrease_amount = $plan->max_decrement_amount;

        if ($amount < $min_decrease_amount || $amount > $max_decrease_amount) {
            $message = __('You can decrease minimum amount is :minimum_amount and maximum is :maximum_amount', ['minimum_amount' => $currency.$min_decrease_amount, 'maximum_amount' => $currency.$max_decrease_amount]);
            throw ValidationException::withMessages(['error' => $message]);
        }

        if ($fdr->amount <= $request->integer('decrease_amount')) {
            throw ValidationException::withMessages([
                'error' => __('Decrease amount should be equal to or less than fdr amount.'),
            ]);
        }
    }

    public function decrement(Request $request, Fdr $fdr)
    {
        // Get plan
        $plan = $fdr->plan;
        // Get currency symbol
        $currency = setting('currency_symbol', 'global');

        // Check user balance
        $total_amount = $plan->decrement_charge_type ? $request->decrease_amount + $plan->decrement_fee : $request->decrease_amount;
        if ($total_amount >= $fdr->user->balance) {
            throw ValidationException::withMessages(['error' => __('Insufficent Balance.')]);
        }

        // Limit check & decrease
        $charge = 0;
        if ($plan->decrement_type == 'unlimited' || $plan->decrement_type == 'fixed' && $plan->decrement_times > $fdr->decrement_count) {
            $fdr->decrement_count += 1;
            if ($plan->decrement_charge_type) {
                $charge = $plan->decrement_fee;
            }
        } else {
            throw ValidationException::withMessages(['error' => __('You reached the decrement limit!')]);
        }

        // Decrease Amount
        $fdr->amount -= $request->integer('decrease_amount');
        $fdr->save();

        // Decrease amount added to user balance
        $fdr->user->increment('balance', $request->integer('decrease_amount') - $charge);

        (new Txn)->new($request->integer('decrease_amount'), $charge, $total_amount, 'System', 'FDR Decreased #'.$fdr->fdr_id.'', TxnType::FdrDecrease, TxnStatus::Success, null, null, $fdr->user_id, null, 'User');

        // Calculate interest amount
        $intereset_amount = ($fdr->amount / 100) * $fdr->plan->interest_rate;

        // Update per installment fee in transactions data
        $fdr->transactions()->whereNull('paid_amount')->update([
            'given_amount' => $intereset_amount,
        ]);
    }

    protected function checkAbility($fdr)
    {
        if ($fdr->status == FdrStatus::Completed) {
            throw ValidationException::withMessages(['error' => __('Sorry, Your FDR is completed!')]);
        } elseif ($fdr->status == FdrStatus::Closed) {
            throw ValidationException::withMessages(['error' => __('Your FDR is closed!')]);
        }
    }

    public function checkFdrCancellationAbility($fdr)
    {
        $this->checkAbility($fdr);

        if (! $fdr->plan->can_cancel) {
            throw ValidationException::withMessages(['error' => __('You can\'t cancel this plan.')]);
        }

        // Check if the FDR is within the days window for cancellation
        $cancellationDays = (int) $fdr->plan->cancel_days;
        $creationDate = Carbon::parse($fdr->created_at);
        $currentDate = Carbon::now();

        if ($fdr->plan->cancel_type == 'fixed' && $currentDate->diffInDays($creationDate) > $cancellationDays) {
            throw ValidationException::withMessages(['error' => __('FDR cancellation days is over!')]);
        }
    }

    public function cancel($fdr)
    {
        // Calculate cancel fee
        $cancel_fee = $fdr->plan->cancel_fee_type == 'percentage' ? (($fdr->plan->cancel_fee / 100) * $fdr->amount) : $fdr->plan->cancel_fee;

        // FDR amount back to user balance
        $refund_amount = $fdr->amount - $cancel_fee;
        $fdr->user->increment('balance', $refund_amount);

        // Save fdr cancel info
        $fdr->cancel_date = now();
        $fdr->cancel_fee = $cancel_fee;
        $fdr->status = FdrStatus::Closed;
        $fdr->save();

        (new Txn)->new($refund_amount, $cancel_fee, $fdr->amount + $cancel_fee, 'System', 'FDR Cancelled #'.$fdr->fdr_id.'', TxnType::FdrCancelled, TxnStatus::Success, null, null, $fdr->user_id, null, 'User');

        $trx = \App\Models\FDRTransaction::where('fdr_id', $fdr->id)->where('paid_amount', null)->first();

        $shortcodes = [
            '[[site_title]]' => setting('site_title', 'global'),
            '[[site_url]]' => route('home'),
            '[[plan_name]]' => $fdr->plan?->name,
            '[[user_name]]' => $fdr->user->full_name,
            '[[full_name]]' => $fdr->user->full_name,
            '[[fdr_id]]' => $fdr->fdr_id,
            '[[per_installment]]' => '',
            '[[interest_rate]]' => $fdr->plan?->interest_rate,
            '[[given_installment]]' => $fdr->givenInstallemnt() ?? 0,
            '[[total_installment]]' => count($fdr->transactions),
            '[[amount]]' => $fdr->amount.' '.setting('site_currency', 'global'),
            '[[installment_interval]]' => $fdr->plan?->intervel,
            '[[next_installment_date]]' => $trx?->given_date->format('d M Y'),
        ];

        $this->smsNotify('fdr_closed', $shortcodes, $fdr->user->phone);
        $this->mailNotify($fdr->user->email, 'fdr_closed', $shortcodes);
        $this->pushNotify('fdr_closed', $shortcodes, route('user.fdr.details', $fdr->id), $fdr->user_id);
        $this->pushNotify('fdr_closed', $shortcodes, route('admin.fdr.details', $fdr->id), $fdr->user_id, 'Admin');
    }
}
