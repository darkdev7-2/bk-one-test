<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LoanDetailsResource;
use App\Http\Resources\LoanHistoryResource;
use App\Http\Resources\LoanPlanResource;
use App\Http\Resources\LoanTransactionResource;
use App\Models\Loan;
use App\Models\LoanPlan;
use App\Services\LoanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    public function __construct(
        private LoanService $loanService
    ) {}

    public function plans()
    {
        $plans = LoanPlan::active()->get();

        return response()->json([
            'status' => true,
            'data' => LoanPlanResource::collection($plans),
        ]);
    }

    public function subscribe(Request $request)
    {
        try {
            $user = auth()->user();

            $plan = LoanPlan::find($request->plan_id);

            $this->loanService->validate($user, $plan, $request->amount, $request);

            $this->loanService->subscribe($user, $plan, $request->amount, $request);

            return response()->json([
                'status' => true,
                'message' => __('Loan applied successfully!'),
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
        $loans = Loan::with('transactions', 'plan', 'user')
            ->where('user_id', auth()->id())
            ->when($request->has('loan_id'), function ($query) use ($request) {
                $query->where('loan_no', 'LIKE', '%'.$request->loan_id.'%');
            })
            ->when($request->filled(['from_date', 'to_date']), function ($query) use ($request) {
                $query->whereDate('created_at', '>=', $request->from_date)
                    ->whereDate('created_at', '<=', $request->to_date);
            })
            ->latest()->paginate();

        return response()->json([
            'status' => true,
            'data' => LoanHistoryResource::collection($loans),
            'meta' => [
                'current_page' => $loans->currentPage(),
                'last_page' => $loans->lastPage(),
                'per_page' => $loans->perPage(),
                'total' => $loans->total(),
            ],
        ]);
    }

    public function details($loanId)
    {
        $loan = Loan::with('transactions', 'plan', 'user')->where('loan_no', $loanId)->where('user_id', auth()->id())->firstOrFail();

        return response()->json([
            'status' => true,
            'data' => new LoanDetailsResource($loan),
        ]);
    }

    public function cancel(Request $request)
    {
        try {
            $loan = Loan::where('loan_no', $request->loan_id)->where('user_id', auth()->id())->firstOrFail();

            $this->loanService->cancel(auth()->user(), $loan);

            return response()->json([
                'status' => true,
                'message' => __('Loan request cancelled successfully!'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function installments($loan_id)
    {
        try {
            $user = auth()->user();

            $loan = Loan::where('loan_no', $loan_id)->where('user_id', $user->id)->firstOrFail();

            $transactions = $loan->transactions()->get();

            return response()->json([
                'status' => true,
                'data' => LoanTransactionResource::collection($transactions),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function payInstallment(Request $request)
    {
        $loan_id = $request->loan_id;
        $trans_id = $request->trans_id;

        try {
            DB::beginTransaction();

            $user = Auth::user();

            $loan = Loan::query()
                ->with('transactions')
                ->where('user_id', $user->id)
                ->where('loan_no', $loan_id)
                ->first();

            foreach ($loan->transactions as $loanTransaction) {

                if ($trans_id && $loanTransaction->id != $trans_id) {
                    continue;
                }

                $this->loanService->payInstallment($user, $loan, $loanTransaction);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => __('User Loan Installment Successfully Done'),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
