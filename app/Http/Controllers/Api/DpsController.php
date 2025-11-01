<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DpsDetailsReosurce;
use App\Http\Resources\DpsHistoryResource;
use App\Http\Resources\DpsPlanResource;
use App\Http\Resources\DpsTransactionResource;
use App\Models\Dps;
use App\Models\DpsPlan;
use App\Services\DpsService;
use Illuminate\Http\Request;

class DpsController extends Controller
{
    public function __construct(
        private DpsService $dpsService
    ) {}

    public function index()
    {
        $plans = DpsPlan::active()->get();

        return response()->json([
            'status' => true,
            'data' => DpsPlanResource::collection($plans),
        ]);
    }

    public function store(Request $request)
    {
        try {
            $user = auth()->user();

            $plan = DpsPlan::find($request->plan_id);

            $this->dpsService->validate($user, $plan);

            $this->dpsService->subscribe($user, $plan);

            return response()->json([
                'status' => true,
                'message' => __('DPS Plan Subscribed Successfully!'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function increment(Request $request)
    {
        // Get Dps data
        $dps = Dps::findOrFail($request->id);

        try {

            $this->dpsService->validateIncrease($dps, $request);
            $this->dpsService->increase($dps, $request);

            return response()->json([
                'status' => true,
                'message' => __('DPS Increased Successfully!'),
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function decrement(Request $request)
    {
        // Get dps data
        $dps = Dps::findOrFail($request->id);

        try {

            $this->dpsService->validateDecrease($dps, $request);
            $this->dpsService->decrease($dps, $request);

            return response()->json([
                'status' => true,
                'message' => __('DPS Decreased Successfully!'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function destroy(string $id)
    {
        try {

            // Get DPS
            $dps = Dps::where('dps_id', $id)->where('user_id', auth()->id())->firstOrFail();

            // Cancel process
            $this->dpsService->checkDpsCancellationAbility($dps);
            $this->dpsService->cancel($dps);

            return response()->json([
                'status' => true,
                'message' => __('DPS Plan Cancelled Successfully!'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function history(Request $request)
    {
        $dpses = Dps::with('plan')->where('user_id', auth()->id())->when($request->has('dps_id'), function ($query) use ($request) {
            $query->where('dps_id', 'LIKE', '%'.$request->dps_id.'%');
        })
            ->when($request->filled(['from_date', 'to_date']), function ($query) use ($request) {
                $query->whereDate('created_at', '>=', $request->from_date)
                    ->whereDate('created_at', '<=', $request->to_date);
            })
            ->latest()->paginate();

        return response()->json([
            'status' => true,
            'data' => DpsHistoryResource::collection($dpses),
            'meta' => [
                'current_page' => $dpses->currentPage(),
                'last_page' => $dpses->lastPage(),
                'per_page' => $dpses->perPage(),
                'total' => $dpses->total(),
            ],
        ]);
    }

    public function details($dpsId)
    {
        // Get history by specific dps
        $dps = Dps::with('plan')->where('dps_id', $dpsId)->where('user_id', auth()->id())->firstOrFail();

        return response()->json([
            'status' => true,
            'data' => new DpsDetailsReosurce($dps),
        ]);
    }

    public function installments($dpsId)
    {
        $dps = Dps::where('dps_id', $dpsId)->where('user_id', auth()->id())->firstOrFail();

        $transactions = $dps->transactions()->get();

        return response()->json([
            'status' => true,
            'data' => DpsTransactionResource::collection($transactions),
        ]);
    }
}
