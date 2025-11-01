<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\FdrStatus;
use App\Enums\TxnStatus;
use App\Enums\TxnType;
use App\Http\Controllers\Controller;
use App\Models\Fdr;
use App\Models\FdrPlan;
use App\Services\FdrService;
use App\Traits\NotifyTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Txn;

class FdrController extends Controller
{
    use NotifyTrait;

    public function __construct(
        private FdrService $fdrService
    ) {}

    public function index()
    {
        if (! setting('user_fdr', 'permission') || ! Auth::user()->fdr_status) {
            notify()->error(__('FDR currently unavailable!'), 'Error');

            return to_route('user.dashboard');
        } elseif (! setting('kyc_fdr') && auth()->user()->kyc != 1) {
            notify()->error(__('Please verify your KYC.'), 'Error');

            return to_route('user.dashboard');
        }

        $plans = FdrPlan::active()->latest()->get();

        return view('frontend::fdr.index', compact('plans'));
    }

    public function subscribe(Request $request)
    {
        try {
            // Get user
            $user = auth()->user();
            // Get FDR Plan
            $plan = FdrPlan::find(decrypt($request->fdr_id));

            // Validate
            $this->fdrService->validate($user, $plan);
            // Subscribe
            $this->fdrService->subscribe($plan, $user, $request);

            notify()->success(__('FDR Plan Subscribed Successfully!'), 'Success');
        } catch (\Throwable $th) {
            notify()->error($th->getMessage(), 'Error');
        }

        return redirect()->route('user.fdr.history');
    }

    public function increment(Request $request, $id)
    {
        try {
            // Get FDR data
            $fdr = Fdr::findOrFail(decrypt($id));

            // Validate
            $this->fdrService->valdiateIncrement($request, $fdr);
            // Increment
            $this->fdrService->increment($request, $fdr);

            notify()->success(__('FDR Increased Successfully!'), 'Success');
        } catch (\Exception $e) {
            notify()->error($e->getMessage(), 'Error');
        }

        return back();
    }

    public function decrement(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'decrease_amount' => 'required|integer',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first(), 'Error');

            return back();
        }

        // Get FDR data
        $fdr = Fdr::findOrFail(decrypt($id));
        $plan = $fdr->plan;

        // Check FDR
        if (! $this->checkAbility($fdr)) {
            return back();
        }

        $amount = $request->integer('decrease_amount');
        // Get currency symbol
        $currency = setting('currency_symbol', 'global');

        // Check increase min amount & max amount
        $min_decrease_amount = $plan->min_decrement_amount;
        $max_decrease_amount = $plan->max_decrement_amount;

        if ($amount < $min_decrease_amount || $amount > $max_decrease_amount) {
            $message = __('You can decrease minimum amount is :minimum_amount and maximum is :maximum_amount', ['minimum_amount' => $currency.$min_decrease_amount, 'maximum_amount' => $currency.$max_decrease_amount]);
            notify()->error($message);

            return redirect()->back();
        }

        // Check user balance
        $total_amount = $plan->decrement_charge_type ? $request->decrease_amount + $plan->decrement_fee : $request->decrease_amount;
        if ($total_amount > $fdr->user->balance) {
            notify()->error(__('Insufficent Balance.'));

            return back();
        }

        // Limit check & decrease
        $charge = 0;
        if ($plan->decrement_type == 'unlimited' || $plan->decrement_type == 'fixed' && $plan->decrement_times > $fdr->decrement_count) {
            $fdr->decrement_count += 1;
            if ($plan->decrement_charge_type) {
                $charge = $plan->decrement_fee;
            }
        } else {
            notify()->error(__('You reached the decrement limit!'), 'Limit Reached!');

            return back();
        }

        // Decrease Amount
        $fdr->amount -= $request->integer('decrease_amount');
        $fdr->save();

        // Decrease amount added to user balance
        $fdr->user->increment('balance', $request->integer('decrease_amount') - $charge);

        Txn::new($request->integer('decrease_amount') - $charge, $charge, $total_amount, 'System', 'FDR Decreased #'.$fdr->fdr_id.'', TxnType::FdrDecrease, TxnStatus::Success, null, null, auth()->id(), null, 'User');

        // Calculate interest amount
        $intereset_amount = ($fdr->amount / 100) * $fdr->plan->interest_rate;

        // Update per installment fee in transactions data
        $fdr->transactions()->whereNull('paid_amount')->update([
            'given_amount' => $intereset_amount,
        ]);

        notify()->success(__('FDR Decreased Successfully!'), 'Success');

        return back();
    }

    protected function checkAbility($fdr)
    {
        $status = $fdr->status->value;

        if ($status == FdrStatus::Completed->value) {
            notify()->error(__('Sorry, Your FDR is completed!'), 'Error');

            return false;
        } elseif ($status == FdrStatus::Closed->value) {
            notify()->error(__('Your FDR is closed!'), 'Error');

            return false;
        }

        return true;
    }

    public function history()
    {

        $from_date = trim(@explode('-', request('daterange'))[0]);
        $to_date = trim(@explode('-', request('daterange'))[1]);

        $fdrs = Fdr::with(['user', 'plan', 'transactions'])
            ->where('user_id', auth()->id())
            ->when(request('fdr_id'), function ($query) {
                $query->where('fdr_id', 'LIKE', '%'.request('fdr_id').'%');
            })
            ->when(request('daterange'), function ($query) use ($from_date, $to_date) {
                $query->whereDate('created_at', '>=', Carbon::parse($from_date)->format('Y-m-d'));
                $query->whereDate('created_at', '<=', Carbon::parse($to_date)->format('Y-m-d'));
            })
            ->latest()
            ->paginate(request('limit', 15))
            ->withQueryString();

        return view('frontend::fdr.history', compact('fdrs'));
    }

    public function details($fdrId)
    {
        $fdr = Fdr::with(['transactions', 'plan', 'user'])->where('fdr_id', $fdrId)->where('user_id', auth()->id())->firstOrFail();

        return view('frontend::fdr.details', compact('fdr'));
    }

    public function cancel($fdrId)
    {
        try {
            // Get fdr data
            $fdr = Fdr::where('fdr_id', $fdrId)->where('user_id', auth()->id())->firstOrFail();

            // Cancel process
            $this->fdrService->checkFdrCancellationAbility($fdr);
            $this->fdrService->cancel($fdr);

            notify()->success(__('FDR Cancelled Successfully!'), 'Success');
        } catch (\Exception $e) {
            notify()->error($e->getMessage());
        }

        return redirect()->back();
    }
}
