<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\LoanPlan;
use App\Services\LoanService;
use App\Traits\ImageUpload;
use App\Traits\NotifyTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    use ImageUpload, NotifyTrait;

    public function __construct(
        private LoanService $loanService
    ) {}

    public function index()
    {
        if (! setting('user_loan', 'permission') || ! Auth::user()->loan_status) {
            notify()->error(__('Loan currently unavailable!'), 'Error');

            return to_route('user.dashboard');
        } elseif (! setting('kyc_loan') && auth()->user()->kyc != 1) {
            notify()->error(__('Please verify your KYC.'), 'Error');

            return to_route('user.dashboard');
        }

        $plans = LoanPlan::active()->get();

        return view('frontend::loan.index', compact('plans'));
    }

    public function application(Request $request, $id)
    {
        // Check loan available or not
        if (! setting('user_loan', 'permission') || ! Auth::user()->loan_status) {
            notify()->error(__('Loan currently unavailable!'), 'Error');

            return to_route('user.dashboard');
        } elseif (! setting('kyc_loan') && auth()->user()->kyc != 1) {
            notify()->error(__('Please verify your KYC.'), 'Error');

            return to_route('user.dashboard');
        }

        $plan = LoanPlan::findOrFail(decrypt($id));

        // Get plan minimum & maximum amount range
        $min = (int) $plan->minimum_amount;
        $max = (int) $plan->maximum_amount;
        // Get loan amount
        $amount = (int) $request->amount;
        // Get currency symbol from setting
        $currency = setting('currency_symbol', 'global');

        // Check minimum & maximun requirement
        if ($amount < $min || $max < $amount) {
            $message = __('You must choice minimum '.$currency.$plan->minimum_amount.' and maximum '.$currency.$plan->maximum_amount);
            notify()->error($message, 'Error');

            return redirect()->back();
        }

        return view('frontend::loan.application', compact('plan', 'request'));
    }

    public function subscribe(Request $request)
    {
        try {
            $user = auth()->user();

            $plan = LoanPlan::find(decrypt($request->loan_id));

            $this->loanService->validate($user, $plan, $request->amount, $request);
            $this->loanService->subscribe($user, $plan, $request->amount, $request);

            notify()->success(__('Loan applied successfully!'), 'Success');
        } catch (\Exception $e) {
            notify()->error($e->getMessage(), 'Error');
        }

        return redirect()->route('user.loan.history');
    }

    public function history()
    {
        $from_date = trim(@explode('-', request('daterange'))[0]);
        $to_date = trim(@explode('-', request('daterange'))[1]);

        $loans = Loan::with('transactions', 'plan', 'user')
            ->where('user_id', auth()->id())
            ->when(request('loan_id'), function ($query) {
                $query->where('loan_no', 'LIKE', '%'.request('loan_id').'%');
            })
            ->when(request('daterange'), function ($query) use ($from_date, $to_date) {
                $query->whereDate('created_at', '>=', Carbon::parse($from_date)->format('Y-m-d'));
                $query->whereDate('created_at', '<=', Carbon::parse($to_date)->format('Y-m-d'));
            })
            ->latest()
            ->paginate(request('limit', 15))
            ->withQueryString();

        return view('frontend::loan.history', compact('loans'));
    }

    public function details($loanNo)
    {
        $loan = Loan::with('transactions', 'plan', 'user')->where('loan_no', $loanNo)->where('user_id', auth()->id())->firstOrFail();

        return view('frontend::loan.details', compact('loan'));
    }

    public function cancel($loan_id)
    {
        // Get loan data
        $loan = Loan::where('loan_no', $loan_id)->where('user_id', auth()->id())->firstOrFail();

        try {

            $this->loanService->cancel(auth()->user(), $loan);

            notify()->success(__('Loan request cancelled successfully!'), 'Success');
        } catch (\Exception $e) {
            notify()->error($e->getMessage(), 'Error');
        }

        return redirect()->route('user.loan.history');
    }

    public function payInstallment($loan_id, $trans_id = null)
    {
        try {
            DB::beginTransaction();

            $user = Auth::user();

            $loan = Loan::query()
                ->with('transactions')
                ->where('user_id', $user->id)
                ->findOrFail(decrypt($loan_id));

            foreach ($loan->transactions as $loanTransaction) {

                if ($trans_id && $loanTransaction->id != decrypt($trans_id)) {
                    continue;
                }

                $this->loanService->payInstallment($user, $loan, $loanTransaction);
            }

            DB::commit();

            notify()->success(__('User Loan Installment Successfully Done'));

            return redirect()->back();
        } catch (\Throwable $e) {
            DB::rollBack();
            notify()->error($e->getMessage(), 'Error');

            return redirect()->back();
        }
    }
}
