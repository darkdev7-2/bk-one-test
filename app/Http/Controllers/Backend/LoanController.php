<?php

namespace App\Http\Controllers\Backend;

use App\Enums\LoanStatus;
use App\Enums\TxnStatus;
use App\Enums\TxnType;
use App\Http\Controllers\Controller;
use App\Models\LevelReferral;
use App\Models\Loan;
use App\Models\LoanPlan;
use App\Models\LoanTransaction;
use App\Models\Transaction;
use App\Models\User;
use App\Traits\ImageUpload;
use App\Traits\NotifyTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Txn;

class LoanController extends Controller
{
    use ImageUpload, NotifyTrait;

    public function __construct()
    {
        $this->middleware('permission:pending-loan', ['only' => ['request']]);
        $this->middleware('permission:running-loan', ['only' => ['approved']]);
        $this->middleware('permission:due-loan', ['only' => ['payable']]);
        $this->middleware('permission:paid-loan', ['only' => ['completed']]);
        $this->middleware('permission:rejected-loan', ['only' => ['rejected']]);
        $this->middleware('permission:all-loan', ['only' => ['all']]);
        $this->middleware('permission:view-loan-details', ['only' => ['details']]);
        $this->middleware('permission:loan-approval', ['only' => ['approvalAction']]);
        $this->middleware('permission:subscribe-user-loan', ['only' => ['createLoanRequest', 'subscribeLoanRequest']]);
    }

    public function all(Request $request)
    {
        $search = $request->search;
        $loan = Loan::with(['plan', 'user'])
            ->search($search)
            ->when(in_array($request->sort_field, ['loan_no', 'created_at', 'amount', 'status']), function ($query) {
                $query->orderBy(request('sort_field'), request('sort_dir'));
            })
            ->latest()
            ->paginate(10);

        $statusForFrontend = __('All');

        return view('backend.loan.index', compact('loan', 'statusForFrontend'));
    }

    public function request(Request $request)
    {
        $search = $request->search;
        $loan = Loan::with(['plan', 'user'])
            ->reviewing()
            ->search($search)
            ->when(in_array($request->sort_field, ['loan_no', 'created_at', 'amount', 'status']), function ($query) {
                $query->orderBy(request('sort_field'), request('sort_dir'));
            })
            ->latest()
            ->paginate(10);

        $statusForFrontend = __('Requested');

        return view('backend.loan.index', compact('loan', 'statusForFrontend'));
    }

    public function rejected(Request $request)
    {
        $search = $request->search;

        $loan = Loan::with(['plan', 'user'])
            ->rejected()
            ->search($search)
            ->when(in_array($request->sort_field, ['loan_no', 'created_at', 'amount', 'status']), function ($query) {
                $query->orderBy(request('sort_field'), request('sort_dir'));
            })
            ->latest()
            ->paginate(10);

        $statusForFrontend = __('Rejected');

        return view('backend.loan.index', compact('loan', 'statusForFrontend'));
    }

    public function approved(Request $request)
    {
        $search = $request->search;

        $loan = Loan::with(['plan', 'user'])
            ->running()
            ->search($search)
            ->when(in_array($request->sort_field, ['loan_no', 'created_at', 'amount', 'status']), function ($query) {
                $query->orderBy(request('sort_field'), request('sort_dir'));
            })
            ->latest()
            ->paginate(10);

        $statusForFrontend = __('Approved');

        return view('backend.loan.index', compact('loan', 'statusForFrontend'));
    }

    public function payable(Request $request)
    {
        $search = $request->search;

        $loan = Loan::with(['plan', 'user'])
            ->due()
            ->search($search)
            ->when(in_array($request->sort_field, ['loan_no', 'created_at', 'amount', 'status']), function ($query) {
                $query->orderBy(request('sort_field'), request('sort_dir'));
            })
            ->latest()
            ->paginate(10);

        $statusForFrontend = __('Payable');

        return view('backend.loan.index', compact('loan', 'statusForFrontend'));
    }

    public function completed(Request $request)
    {
        $search = $request->search;

        $loan = Loan::with(['plan', 'user'])
            ->completed()
            ->search($search)
            ->when(in_array($request->sort_field, ['loan_no', 'created_at', 'amount', 'status']), function ($query) {
                $query->orderBy(request('sort_field'), request('sort_dir'));
            })
            ->latest()
            ->paginate(10);

        $statusForFrontend = __('Completed');

        return view('backend.loan.index', compact('loan', 'statusForFrontend'));
    }

    public function details($id)
    {
        $loan = Loan::with(['user', 'plan', 'transactions'])->find($id);

        return view('backend.loan.details', compact('loan'));
    }

    public function approvalAction(Request $request)
    {
        $loan = Loan::findOrFail($request->id);

        $loan->update([
            'status' => $request->status,
        ]);

        $plan = $loan->plan;

        $shortcodes = [
            '[[site_title]]' => setting('site_title', 'global'),
            '[[site_url]]' => route('home'),
            '[[plan_name]]' => $loan->plan->name,
            '[[user_name]]' => $loan->user->full_name,
            '[[loan_id]]' => $loan->loan_no,
            '[[given_installment]]' => 0,
            '[[total_installment]]' => $loan->plan->total_installment,
            '[[next_installment_date]]' => nextInstallment($loan->id, \App\Models\LoanTransaction::class, 'loan_id'),
            '[[loan_amount]]' => $loan->amount.' '.setting('site_currency', 'global'),
            '[[installment_interval]]' => $loan->plan->installment_intervel,
            '[[installment_rate]]' => $loan->plan->installment_rate,
        ];

        if ($request->status == 'running') {
            $loanTransactions = [];

            for ($i = 1; $i <= $plan->total_installment; $i++) {
                $loanTransactions[] = [
                    'loan_id' => $loan->id,
                    'installment_date' => Carbon::now()->addDays($plan->installment_intervel * $i),
                    'paid_amount' => $loan->perInstallment(),
                    'created_at' => now(),
                ];
            }

            LoanTransaction::insert($loanTransactions);

            $loan->user->increment('balance', $loan->amount);

            // Create Transaction
            Txn::new($loan->amount, 0, $loan->amount, 'System', 'Loan Approved #'.$loan->loan_no.'', TxnType::Loan, TxnStatus::Success, 'System', null, $loan->user_id, null, 'User');

            $this->smsNotify('loan_approved', $shortcodes, $loan->user->phone);
            $this->mailNotify($loan->user->email, 'loan_approved', $shortcodes);
            $this->pushNotify('loan_approved', $shortcodes, route('user.loan.details', $loan->loan_no), $loan->user_id);

            // Level referral
            if (setting('loan_level')) {
                $level = LevelReferral::where('type', 'loan')->max('the_order') + 1;
                creditReferralBonus($loan->user, 'loan', $loan->amount, $level);
            }

            $message = __('Loan request approved successfully!');
        } else {

            $transaction = Transaction::find($loan->txn_id);

            $transaction?->update([
                'status' => TxnStatus::Failed,
            ]);

            $loan->user->increment('balance', $transaction->charge);

            $message = __('Loan request rejected successfully!');

            $this->smsNotify('loan_rejected', $shortcodes, $loan->user->phone);
            $this->mailNotify($loan->user->email, 'loan_rejected', $shortcodes);
            $this->pushNotify('loan_rejected', $shortcodes, route('user.loan.details', $loan->loan_no), $loan->user_id);
        }

        notify()->success($message, 'Success');

        return redirect()->route('admin.loan.all');
    }

    public function createLoanRequest(Request $request)
    {
        $loanPlans = LoanPlan::query()
            ->active()
            ->get();

        $selectLoanPlan = $request->filled('loan_plan_id') ? LoanPlan::query()->active()
            ->where('id', $request->loan_plan_id)
            ->first() : [];

        return view('backend.loan.subscribe', compact('loanPlans', 'selectLoanPlan'));
    }

    public function subscribeLoanRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id',
            'loan_plan_id' => 'required|integer|exists:loan_plans,id',
            'loan_amount' => 'required',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first(), 'Error');

            return redirect()->back();
        }

        $user = User::find($request->user_id);

        if (! $user) {
            notify()->error(__('User not found'), 'Error');

            return redirect()->back();
        }

        $plan = LoanPlan::find($request->loan_plan_id);

        if (! $plan) {
            notify()->error(__('Loan Plan Not found.'), 'Error');

            return redirect()->back();
        }

        $amount = (float) $request->loan_amount;

        $currency = setting('currency_symbol', 'global');

        $min = (int) $plan->minimum_amount;
        $max = (int) $plan->maximum_amount;

        if ($amount < $min || $max < $amount) {
            $message = __('You must choice minimum :minimum and maximum :maximum', ['minimum' => $currency.$plan->minimum_amount, 'maximum' => $currency.$plan->maximum_amount]);
            notify()->error($message, 'Error');

            return redirect()->back();
        }

        $loan_fee = $plan->loan_fee;

        if ($user->balance < $loan_fee) {
            notify()->error(__('User balance is low.'), 'Error');

            return redirect()->back();
        }

        $submitted_data = [];

        foreach ($request->submitted_data ?? [] as $key => $value) {

            if (is_file($value)) {
                $submitted_data[$key] = self::imageUploadTrait($value);
            } else {
                $submitted_data[$key] = $value;
            }
        }

        try {

            DB::beginTransaction();

            $loan = Loan::create([
                'loan_no' => 'L'.random_int(10000000, 99999999),
                'txn_id' => 0,
                'loan_plan_id' => $plan->id,
                'user_id' => $user->id,
                'submitted_data' => json_encode($submitted_data),
                'amount' => $amount,
                'status' => LoanStatus::Running,
            ]);

            if ($loan_fee) {
                $user->decrement('balance', $loan_fee);
            }

            $loanTransactions = [];

            for ($i = 1; $i <= $plan->total_installment; $i++) {
                $loanTransactions[] = [
                    'loan_id' => $loan->id,
                    'installment_date' => Carbon::now()->addDays($plan->installment_intervel * $i),
                ];
            }

            LoanTransaction::insert($loanTransactions);

            $user->increment('balance', $loan->amount);

            $txn = Txn::new($amount, $loan_fee, $amount + $loan_fee, 'System', 'Loan Applied #'.$loan->loan_no.'', TxnType::Loan, TxnStatus::Success, '', null, $user->id, null, 'User');

            $loan->update([
                'txn_id' => $txn->id,
            ]);

            $shortcodes = [
                '[[site_title]]' => setting('site_title', 'global'),
                '[[site_url]]' => route('home'),
                '[[plan_name]]' => $loan->plan->name,
                '[[user_name]]' => $loan->user->full_name,
                '[[full_name]]' => $loan->user->full_name,
                '[[loan_id]]' => $loan->loan_no,
                '[[loan_amount]]' => $loan->amount.' '.setting('site_currency', 'global'),
                '[[installment_interval]]' => $loan->plan->installment_intervel,
                '[[installment_rate]]' => $loan->plan->installment_rate,
            ];

            $this->smsNotify('loan_approved', $shortcodes, $loan->user->phone);
            $this->mailNotify(setting('support_email', 'global'), 'loan_approved', $shortcodes);
            $this->pushNotify('loan_approved', $shortcodes, route('user.loan.details', $loan->loan_no), $loan->user_id);
            $this->pushNotify('loan_approved', $shortcodes, route('admin.loan.details', $loan->id), $loan->user_id, 'Admin');

            DB::commit();

            notify()->success(__('Loan has been created successfully!'), 'Success');

            return to_route('admin.loan.all');
        } catch (\Throwable $e) {
            DB::rollBack();
            notify()->error(__('Sorry! Something went wrong. Please try again'), 'Error');

            return redirect()->back();
        }
    }
}
