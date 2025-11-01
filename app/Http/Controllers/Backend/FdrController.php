<?php

namespace App\Http\Controllers\Backend;

use App\Enums\TxnStatus;
use App\Enums\TxnType;
use App\Http\Controllers\Controller;
use App\Models\Fdr;
use App\Models\FdrPlan;
use App\Models\FDRTransaction;
use App\Models\LevelReferral;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Txn;

class FdrController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:ongoing-fdr', ['only' => ['ongoing']]);
        $this->middleware('permission:payable-fdr', ['only' => ['payable']]);
        $this->middleware('permission:complete-fdr', ['only' => ['completed']]);
        $this->middleware('permission:closed-fdr', ['only' => ['close']]);
        $this->middleware('permission:all-fdr', ['only' => ['all']]);
        $this->middleware('permission:view-fdr-details', ['only' => ['details']]);
        $this->middleware('permission:subscribe-user-fdr', ['only' => ['createFdrRequest', 'subscribeFdrRequest']]);
    }

    public function ongoing(Request $request)
    {
        $search = $request->search;

        $lists = Fdr::with(['plan', 'user'])
            ->ongoing()
            ->search($search)
            ->when(in_array($request->sort_field, ['fdr_id', 'created_at', 'amount', 'status']), function ($query) {
                $query->orderBy(request('sort_field'), request('sort_dir'));
            })
            ->latest()
            ->paginate(10);

        $statusForFrontend = 'Ongoing';

        return view('backend.fdr.index', compact('lists', 'statusForFrontend'));
    }

    public function completed(Request $request)
    {
        $search = $request->search;

        $lists = Fdr::with(['plan', 'user'])
            ->completed()
            ->search($search)
            ->when(in_array($request->sort_field, ['fdr_id', 'created_at', 'amount', 'status']), function ($query) {
                $query->orderBy(request('sort_field'), request('sort_dir'));
            })
            ->latest()
            ->paginate(10);

        $statusForFrontend = 'Completed';

        return view('backend.fdr.index', compact('lists', 'statusForFrontend'));
    }

    public function close(Request $request)
    {
        $search = $request->search;

        $lists = Fdr::with(['plan', 'user'])
            ->closed()
            ->search($search)
            ->when(in_array($request->sort_field, ['fdr_id', 'created_at', 'amount', 'status']), function ($query) {
                $query->orderBy(request('sort_field'), request('sort_dir'));
            })
            ->latest()
            ->paginate(10);

        $statusForFrontend = 'Close';

        return view('backend.fdr.index', compact('lists', 'statusForFrontend'));
    }

    public function all(Request $request)
    {
        $search = $request->search;

        $lists = Fdr::with(['plan', 'user'])
            ->search($search)
            ->when(in_array($request->sort_field, ['fdr_id', 'created_at', 'amount', 'status']), function ($query) {
                $query->orderBy(request('sort_field'), request('sort_dir'));
            })
            ->latest()
            ->paginate(10);

        $statusForFrontend = 'All';

        return view('backend.fdr.index', compact('lists', 'statusForFrontend'));
    }

    public function details($fdrId)
    {
        $fdr = Fdr::with(['user', 'plan', 'transactions'])->find($fdrId);

        return view('backend.fdr.details', compact('fdr'));
    }

    public function createFdrRequest(Request $request)
    {
        $fdrPlans = FdrPlan::query()
            ->where('status', 1)
            ->get();

        $selectFdrPlan = $request->filled('fdr_plan_id') ? FdrPlan::query()
            ->where('id', $request->fdr_plan_id)
            ->where('status', 1)
            ->first() : [];

        return view('backend.fdr.subscribe-fdr', compact('fdrPlans', 'selectFdrPlan'));
    }

    public function subscribeFdrRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id',
            'fdr_plan_id' => 'required|integer|exists:fdr_plans,id',
            'fdr_amount' => 'required',
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

        if (! setting('user_fdr', 'permission') || ! $user->fdr_status) {
            notify()->error(__('FDR currently unavailable!'), 'Error');

            return redirect()->back();
        } elseif (! setting('kyc_fdr') && ! $user->kyc) {
            notify()->error(__('Please verify your KYC.'), 'Error');

            return redirect()->back();
        }

        $plan = FdrPlan::find($request->fdr_plan_id);

        if (! $plan) {
            notify()->error(__('FDR Plan Not found.'), 'Error');

            return redirect()->back();
        }

        $amount = (float) $request->fdr_amount;

        $currency = setting('currency_symbol', 'global');

        $min = (int) $plan->minimum_amount;

        $max = (int) $plan->maximum_amount;

        if ($amount < $min || $amount > $max) {
            $message = __('You can FDR minimum :minimum_amount and maximum :maximum_amount', ['minimum_amount' => $currency.$plan->minimum_amount, 'maximum_amount' => $currency.$plan->maximum_amount]);
            notify()->error($message);

            return redirect()->back();
        }

        if ($user->balance <= $amount) {
            $message = __('Insufficient Balance. User balance must be upper than :amount', ['amount' => $currency.$amount]);
            notify()->error($message);

            return redirect()->back();
        }

        try {
            DB::beginTransaction();

            $fdr = Fdr::create([
                'fdr_id' => 'F'.random_int(10000000, 99999999),
                'user_id' => $user->id,
                'fdr_plan_id' => $plan->id,
                'amount' => $amount,
                'end_date' => Carbon::now()->addDays($plan->locked),
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
                    'given_date' => Carbon::parse($fdr->created_at)->addDays($plan->intervel * $i),
                    'given_amount' => $interest,
                ];
            }

            FDRTransaction::insert($fdrTransactions);

            if (setting('fdr_level')) {
                $level = LevelReferral::where('type', 'fdr')->max('the_order') + 1;
                creditReferralBonus($user, 'fdr', $amount, $level);
            }

            $user->balance -= $amount;
            $user->save();

            Txn::new($amount, 0, $amount, 'System', 'FDR Plan Subscribed #'.$fdr->fdr_id.'', TxnType::Fdr, TxnStatus::Success, '', null, $user->id, null, 'User');

            DB::commit();

            notify()->success(__('FDR has been subscribed successfully!'), 'Success');

            return redirect()->route('admin.fdr.all');
        } catch (\Throwable $e) {
            DB::rollBack();
            notify()->error(__('Sorry! Something went wrong. Please try again'), 'Error');

            return redirect()->back();
        }
    }
}
