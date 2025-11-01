<?php

namespace App\Services;

use App\Enums\LoanStatus;
use App\Enums\TxnStatus;
use App\Enums\TxnType;
use App\Facades\Txn\Txn;
use App\Models\Loan;
use App\Models\LoanPlan;
use App\Models\LoanTransaction;
use App\Models\User;
use App\Traits\ImageUpload;
use App\Traits\NotifyTrait;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class LoanService
{
    use ImageUpload, NotifyTrait;

    public function validate(User $user, LoanPlan $plan, $amount, $request)
    {
        if (! setting('user_loan', 'permission') || ! $user->loan_status) {
            throw ValidationException::withMessages(['error' => __('Loan currently unavailable!')]);
        }

        if (! setting('kyc_loan') && ! $user->kyc) {
            throw ValidationException::withMessages(['error' => __('Please verify your KYC.')]);
        }

        $validator = Validator::make($request->all(), [
            'submitted_data' => 'required|array',
            'amount' => ['required', 'regex:/^[0-9]+(\.[0-9][0-9]?)?$/'],
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages(['error' => $validator->errors()->first()]);
        }

        if (! $plan) {
            throw ValidationException::withMessages(['error' => __('Loan plan not found!')]);
        }

        if ($plan->minimum_amount > $amount || $plan->maximum_amount < $amount) {
            throw ValidationException::withMessages(['error' => __('You can loan minimum :minimum_amount and maximum :maximum_amount', ['minimum_amount' => $plan->minimum_amount, 'maximum_amount' => $plan->maximum_amount])]);
        }

        if ($user->balance < $plan->loan_fee) {
            throw ValidationException::withMessages(['error' => __('Insufficient balance!')]);
        }
    }

    public function subscribe(User $user, LoanPlan $plan, $amount, $request)
    {
        // Loan application process
        $submitted_data = [];

        foreach ($request->submitted_data ?? [] as $key => $value) {

            if (is_file($value)) {
                $submitted_data[$key] = self::imageUploadTrait($value);
            } else {
                $submitted_data[$key] = $value;
            }
        }

        // Create loan request
        $loan = Loan::create([
            'loan_no' => 'L'.random_int(10000000, 99999999),
            'txn_id' => 0,
            'loan_plan_id' => $plan->id,
            'user_id' => $user->id,
            'submitted_data' => json_encode($submitted_data),
            'amount' => $amount,
            'status' => LoanStatus::Reviewing,
        ]);

        if ($plan->loan_fee_type == 'percentage') {
            $loan_fee = ($amount / 100) * $plan->loan_fee;
        } else {
            $loan_fee = $plan->loan_fee;
        }

        $user->decrement('balance', $loan_fee);

        $txn = (new Txn)->new(0, $loan_fee, $amount + $plan->loan_fee, 'System', 'Loan Applied #'.$loan->loan_no.'', TxnType::LoanApply, TxnStatus::Success, '', null, $user->id, null, 'User');

        $loan->update([
            'txn_id' => $txn->id,
        ]);

        $shortcodes = [
            '[[site_title]]' => setting('site_title', 'global'),
            '[[site_url]]' => route('home'),
            '[[plan_name]]' => $plan->name,
            '[[user_name]]' => $user->full_name,
            '[[full_name]]' => $user->full_name,
            '[[loan_id]]' => $loan->loan_no,
            '[[loan_amount]]' => $loan->amount.' '.setting('site_currency', 'global'),
            '[[installment_interval]]' => $plan->installment_intervel,
            '[[installment_rate]]' => $plan->installment_rate,
        ];
        $this->smsNotify('loan_apply', $shortcodes, $user->phone);
        $this->mailNotify(setting('support_email', 'global'), 'loan_apply', $shortcodes);
        $this->pushNotify('loan_apply', $shortcodes, route('user.loan.details', $loan->loan_no), $user->id);
        $this->pushNotify('loan_apply', $shortcodes, route('admin.loan.details', $loan->id), $user->id, 'Admin');

        return $loan;
    }

    public function cancel(User $user, Loan $loan)
    {
        if ($loan->status !== LoanStatus::Reviewing) {
            throw ValidationException::withMessages(['error' => __('Loan request already approved!')]);
        }

        $loan->update([
            'cancel_date' => now(),
            'status' => LoanStatus::Cancelled,
        ]);

        $user->increment('balance', $loan->plan->loan_fee);

        return $loan;
    }

    public function payInstallment(User $user, Loan $loan, LoanTransaction $loanTransaction)
    {
        $plan = $loan->plan;

        $perInstallment = $loanTransaction->paid_amount;

        if ($loanTransaction->deferment != 0 && $loanTransaction->deferment >= $plan->delay_days) {
            $charge = $plan->charge_type == 'percentage' ? (($plan->charge / 100) * $perInstallment) : $plan->charge;
        } else {
            $charge = 0;
        }

        $amount = $perInstallment;

        $finalAmount = $amount + $charge;

        if ($user->balance < $finalAmount) {
            throw ValidationException::withMessages(['error' => __('Insufficient balance!')]);
        }

        $loanTransaction->given_date = now();
        $loanTransaction->paid_amount = $amount;
        $loanTransaction->charge = $charge;
        $loanTransaction->final_amount = $finalAmount;
        $loanTransaction->save();

        $user->balance -= $finalAmount;
        $user->save();

        $totalInstallments = count($loan->transactions);
        $givenInstallments = $loan->transactions->whereNotNull('given_date')->count();

        (new Txn)->new($amount, $charge, $finalAmount, 'User', 'Loan Installment #'.$loan->loan_no.'', TxnType::LoanInstallment, TxnStatus::Success, '', null, $user->id, null, 'User');

        $status = $totalInstallments == $givenInstallments ? LoanStatus::Completed : LoanStatus::Running;

        $loan->status = $status;

        $loan->save();

        $shortcodes = [
            '[[site_title]]' => setting('site_title', 'global'),
            '[[site_url]]' => route('home'),
            '[[plan_name]]' => $loan->plan->name,
            '[[user_name]]' => $loan->user->full_name,
            '[[full_name]]' => $loan->user->full_name,
            '[[loan_id]]' => $loan->loan_no,
            '[[given_installment]]' => $givenInstallments,
            '[[total_installment]]' => count($loan->transactions),
            '[[next_installment_date]]' => nextInstallment($loan->id, LoanTransaction::class, 'loan_id'),
            '[[loan_amount]]' => $loan->amount.' '.setting('site_currency', 'global'),
            '[[installment_amount]]' => $perInstallment.' '.setting('site_currency', 'global'),
            '[[delay_charge]]' => $charge.' '.setting('site_currency', 'global'),
            '[[installment_interval]]' => $loan->plan->installment_intervel,
            '[[installment_rate]]' => $loan->plan->installment_rate,
        ];

        $this->smsNotify('loan_installment', $shortcodes, $loan->user->phone);
        $this->mailNotify($loan->user->email, 'loan_installment', $shortcodes);
        $this->pushNotify('loan_installment', $shortcodes, route('user.loan.details', $loan->loan_no), $loan->user_id);
        $this->pushNotify('loan_installment', $shortcodes, route('admin.loan.details', $loan->id), $loan->user_id, 'Admin');

    }
}
