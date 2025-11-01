<?php

namespace App\Http\Controllers\Backend;

use App\Enums\TxnStatus;
use App\Enums\TxnType;
use App\Http\Controllers\Controller;
use App\Models\Dps;
use App\Models\DpsPlan;
use App\Models\DpsTransaction;
use App\Models\LevelReferral;
use App\Models\User;
use App\Traits\NotifyTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Txn;

class DpsController extends Controller
{
    use NotifyTrait;

    public function __construct()
    {
        $this->middleware('permission:ongoing-dps', ['only' => ['ongoing']]);
        $this->middleware('permission:payable-dps', ['only' => ['payable']]);
        $this->middleware('permission:complete-dps', ['only' => ['complete']]);
        $this->middleware('permission:closed-dps', ['only' => ['close']]);
        $this->middleware('permission:all-dps', ['only' => ['all']]);
        $this->middleware('permission:view-dps-details', ['only' => ['details']]);
        $this->middleware('permission:subscribe-user-dps', ['only' => ['createDpsRequest', 'subscribeDpsRequest']]);
    }

    public function ongoing(Request $request)
    {
        $search = $request->search;

        $dpses = Dps::with(['plan', 'user'])
            ->ongoing()
            ->search($search)
            ->when(in_array($request->sort_field, ['dps_id', 'created_at', 'given_installment', 'status']), function ($query) {
                $query->orderBy(request('sort_field'), request('sort_dir'));
            })
            ->latest()
            ->paginate(10);

        $statusForFrontend = 'Ongoing';

        return view('backend.dps.index', compact('dpses', 'statusForFrontend'));
    }

    public function payable(Request $request)
    {
        $search = $request->search;

        $dpses = Dps::with(['plan', 'user'])
            ->payable()
            ->search($search)
            ->when(in_array($request->sort_field, ['dps_id', 'created_at', 'given_installment', 'status']), function ($query) {
                $query->orderBy(request('sort_field'), request('sort_dir'));
            })
            ->latest()
            ->paginate(10);

        $statusForFrontend = 'Payable';

        return view('backend.dps.index', compact('dpses', 'statusForFrontend'));
    }

    public function complete(Request $request)
    {
        $search = $request->search;
        $dpses = Dps::with(['plan', 'user'])
            ->complete()
            ->search($search)
            ->when(in_array($request->sort_field, ['dps_id', 'created_at', 'given_installment', 'status']), function ($query) {
                $query->orderBy(request('sort_field'), request('sort_dir'));
            })
            ->latest()
            ->paginate(10);

        $statusForFrontend = 'Complete';

        return view('backend.dps.index', compact('dpses', 'statusForFrontend'));
    }

    public function close(Request $request)
    {
        $search = $request->search;
        $dpses = Dps::with(['plan', 'user'])
            ->closed()
            ->search($search)
            ->when(in_array($request->sort_field, ['dps_id', 'created_at', 'given_installment', 'status']), function ($query) {
                $query->orderBy(request('sort_field'), request('sort_dir'));
            })
            ->latest()
            ->paginate(10);

        $statusForFrontend = 'Close';

        return view('backend.dps.index', compact('dpses', 'statusForFrontend'));
    }

    public function all(Request $request)
    {
        $search = $request->search;

        $dpses = Dps::with(['plan', 'user'])
            ->when(in_array($request->sort_field, ['dps_id', 'created_at', 'given_installment', 'status']), function ($query) {
                $query->orderBy(request('sort_field'), request('sort_dir'));
            })
            ->search($search)
            ->latest()
            ->paginate(10);

        $statusForFrontend = 'All';

        return view('backend.dps.index', compact('dpses', 'statusForFrontend'));
    }

    public function details($id)
    {
        $dps = Dps::find($id);

        return view('backend.dps.details', compact('dps'))->render();
    }

    public function createDpsRequest(Request $request)
    {
        $dpsPlans = DpsPlan::query()
            ->where('status', 1)
            ->get();

        $selectDpsPlan = [];

        if ($request->dps_plan_id) {
            $selectDpsPlan = DpsPlan::query()
                ->where('id', $request->dps_plan_id)
                ->where('status', 1)
                ->first();
        }

        return view('backend.dps.subscribe-dps', compact('dpsPlans', 'selectDpsPlan'));
    }

    public function subscribeDpsRequest(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id',
            'dps_plan_id' => 'required|integer|exists:dps_plans,id',
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

        if (! setting('user_dps', 'permission') || ! $user->dps_status) {
            notify()->error(__('DPS currently unavailable!'), 'Error');

            return redirect()->back();
        } elseif (! setting('kyc_dps') && ! $user->kyc) {
            notify()->error(__('Please verify user KYC.'), 'Error');

            return redirect()->back();
        }

        $plan = DpsPlan::find($request->dps_plan_id);

        if (! $plan) {
            notify()->error(__('Dps Plan Not found.'), 'Error');

            return redirect()->back();
        }

        $amount = $plan->per_installment;

        $currency = setting('currency_symbol', 'global');

        if ($user->balance <= $amount) {
            $message = __('Insufficient Balance. User balance must be upper than '.$currency.$amount);
            notify()->error($message, 'Error');

            return redirect()->back();
        }

        try {

            DB::beginTransaction();

            $dps = Dps::create([
                'dps_id' => mt_rand(10000000, 99999999),
                'plan_id' => $plan->id,
                'user_id' => $user->id,
                'per_installment' => $plan->per_installment,
            ]);

            $installments = [];
            for ($i = 0; $i < $plan->total_installment; $i++) {
                $installments[] = [
                    'dps_id' => $dps->id,
                    'paid_amount' => $dps->per_installment,
                    'installment_date' => Carbon::parse($dps->created_at)->addDays($plan->interval * $i),
                ];
            }

            DpsTransaction::insert($installments);

            $transaction = DpsTransaction::where('dps_id', $dps->id)->first();
            $transaction->given_date = today();
            $transaction->paid_amount = $amount;
            $transaction->charge = 0;
            $transaction->final_amount = $amount;
            $transaction->save();

            $user->decrement('balance', $amount);

            Txn::new($amount, 0, $amount, 'System', 'DPS Plan Subscribed #'.$dps->dps_id.'', TxnType::DpsInstallment, TxnStatus::Success, '', null, $user->id, null, 'User');

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

            notify()->success(__('DPS Plan Subscribed Successfully!'), 'Success');

            DB::commit();

            return redirect()->route('admin.dps.all');
        } catch (\Throwable $e) {

            DB::rollBack();
            notify()->error(__('Sorry! Something went wrong. Please try again'), 'Error');

            return redirect()->back();
        }
    }
}
