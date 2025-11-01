<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FdrDetailsResource;
use App\Http\Resources\FdrHistoryResource;
use App\Http\Resources\FdrPlanResource;
use App\Http\Resources\FdrTransactionResource;
use App\Models\Fdr;
use App\Models\FdrPlan;
use App\Services\FdrService;
use Illuminate\Http\Request;

class FdrController extends Controller
{
    public function __construct(
        private FdrService $fdrService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $fdrs = FdrPlan::active()->get();

        return response()->json([
            'status' => true,
            'data' => FdrPlanResource::collection($fdrs),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Get user
            $user = auth()->user();
            // Get FDR Plan
            $plan = FdrPlan::find($request->plan_id);

            // Validate
            $this->fdrService->validate($user, $plan);
            // Subscribe
            $this->fdrService->subscribe($plan, $user, $request);

            return response()->json([
                'status' => true,
                'message' => __('FDR Plan Subscribed Successfully!'),
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            // Get FDR
            $fdr = Fdr::where('fdr_id', $id)->where('user_id', auth()->id())->firstOrFail();

            // Cancel process
            $this->fdrService->checkFdrCancellationAbility($fdr);
            $this->fdrService->cancel($fdr);

            return response()->json([
                'status' => true,
                'message' => __('FDR Plan Cancelled Successfully!'),
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
        $dpses = Fdr::with('plan')->where('user_id', auth()->id())->when($request->has('fdr_id'), function ($query) use ($request) {
            $query->where('fdr_id', 'LIKE', '%'.$request->fdr_id.'%');
        })
            ->when($request->filled(['from_date', 'to_date']), function ($query) use ($request) {
                $query->whereDate('created_at', '>=', $request->from_date)
                    ->whereDate('created_at', '<=', $request->to_date);
            })
            ->latest()->paginate();

        return response()->json([
            'status' => true,
            'data' => FdrHistoryResource::collection($dpses),
            'meta' => [
                'current_page' => $dpses->currentPage(),
                'last_page' => $dpses->lastPage(),
                'per_page' => $dpses->perPage(),
                'total' => $dpses->total(),
            ],
        ]);
    }

    public function increment(Request $request)
    {
        try {
            // Get FDR data
            $fdr = Fdr::findOrFail($request->id);

            // Validate
            $this->fdrService->valdiateIncrement($request, $fdr);

            // Increment
            $this->fdrService->increment($request, $fdr);

            return response()->json([
                'status' => true,
                'message' => __('FDR Increased Successfully!'),
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
        try {
            // Get FDR data
            $fdr = Fdr::findOrFail($request->id);

            // Validate
            $this->fdrService->valdiateDecrement($request, $fdr);

            // decrement
            $this->fdrService->decrement($request, $fdr);

            return response()->json([
                'status' => true,
                'message' => __('FDR decreased Successfully!'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function details($fdrId)
    {
        // Get history by specific dps
        $fdr = Fdr::with('plan')->where('fdr_id', $fdrId)->where('user_id', auth()->id())->firstOrFail();

        return response()->json([
            'status' => true,
            'data' => new FdrDetailsResource($fdr),
        ]);
    }

    public function installments($fdrId)
    {
        $dps = Fdr::where('fdr_id', $fdrId)->where('user_id', auth()->id())->firstOrFail();

        $transactions = $dps->transactions()->get();

        return response()->json([
            'status' => true,
            'data' => FdrTransactionResource::collection($transactions),
        ]);
    }
}
