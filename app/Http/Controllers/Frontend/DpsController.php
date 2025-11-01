<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Dps;
use App\Models\DpsPlan;
use App\Services\DpsService;
use App\Traits\NotifyTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DpsController extends Controller
{
    use NotifyTrait;

    public function __construct(
        private DpsService $dpsService
    ) {}

    public function index()
    {
        if (! setting('user_dps', 'permission') || ! Auth::user()->dps_status) {
            notify()->error(__('DPS currently unavailable!'), 'Error');

            return to_route('user.dashboard');
        } elseif (! setting('kyc_dps') && auth()->user()->kyc != 1) {
            notify()->error(__('Please verify your KYC.'), 'Error');

            return to_route('user.dashboard');
        }

        $plans = DpsPlan::active()->get();

        return view('frontend::dps.index', compact('plans'));
    }

    public function history()
    {
        // Get all dps transaction history
        $from_date = trim(@explode('-', request('daterange'))[0]);
        $to_date = trim(@explode('-', request('daterange'))[1]);

        $dpses = Dps::with(['user', 'plan', 'transactions'])
            ->where('user_id', auth()->id())
            ->when(request('dps_id'), function ($query) {
                $query->where('dps_id', 'LIKE', '%'.request('dps_id').'%');
            })
            ->when(request('daterange'), function ($query) use ($from_date, $to_date) {
                $query->whereDate('created_at', '>=', Carbon::parse($from_date)->format('Y-m-d'));
                $query->whereDate('created_at', '<=', Carbon::parse($to_date)->format('Y-m-d'));
            })
            ->latest()
            ->paginate(request('limit', 15))
            ->withQueryString();

        return view('frontend::dps.history', compact('dpses'));
    }

    public function subscribe($id)
    {
        try {
            $user = auth()->user();

            $plan = DpsPlan::find($id);

            $this->dpsService->validate($user, $plan);

            $this->dpsService->subscribe($user, $plan);

            notify()->success(__('DPS Plan Subscribed Successfully!'), 'Success');

            return redirect()->route('user.dps.history');
        } catch (\Exception $e) {
            notify()->error($e->getMessage(), 'Error');

            return redirect()->back();
        }
    }

    public function details($dpsId)
    {
        // Get history by specific dps
        $dps = Dps::with('transactions')->where('dps_id', $dpsId)->where('user_id', auth()->id())->firstOrFail();

        return view('frontend::dps.details', compact('dps'));
    }

    public function cancel($dpsId)
    {
        try {

            // Get DPS
            $dps = Dps::where('dps_id', $dpsId)->where('user_id', auth()->id())->firstOrFail();

            // Cancel process
            $this->dpsService->checkDpsCancellationAbility($dps);
            $this->dpsService->cancel($dps);

            notify()->success(__('DPS Plan Cancelled Successfully!'), 'Success');
        } catch (\Exception $e) {
            notify()->error($e->getMessage());
        }

        return redirect()->back();
    }

    public function increment(Request $request, $id)
    {
        // Get Dps data
        $dps = Dps::findOrFail(decrypt($id));

        try {

            $this->dpsService->validateIncrease($dps, $request);
            $this->dpsService->increase($dps, $request);

            notify()->success(__('DPS Increased Successfully!'), 'Success');
        } catch (\Exception $e) {
            notify()->error($e->getMessage(), 'Error');
        }

        return back();
    }

    public function decrement(Request $request, $id)
    {
        // Get dps data
        $dps = Dps::findOrFail(decrypt($id));

        try {

            $this->dpsService->validateDecrease($dps, $request);
            $this->dpsService->decrease($dps, $request);

            notify()->success(__('DPS Decreased Successfully!'), 'Success');
        } catch (\Exception $e) {
            notify()->error($e->getMessage(), 'Error');
        }

        return back();
    }
}
