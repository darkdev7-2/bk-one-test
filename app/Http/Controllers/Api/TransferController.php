<?php

namespace App\Http\Controllers\Api;

use App\Enums\TxnType;
use App\Http\Controllers\Controller;
use App\Http\Requests\TransferRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Services\TransferService;
use App\Traits\NotifyTrait;

class TransferController extends Controller
{
    use NotifyTrait;

    public function __construct(
        private TransferService $transferService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transactions = Transaction::with('userWallet')->where('user_id', auth()->id())->where('type', TxnType::FundTransfer)->when(request()->has('transaction_id'), function ($query) {
            $query->where('tnx', request('transaction_id'));
        })->when(request(['from_date', 'to_date']), function ($query) {
            $query->whereDate('created_at', '>=', request('from_date'))
                ->whereDate('created_at', '<=', request('to_date'));
        })->latest()->paginate();

        return TransactionResource::collection($transactions);
    }

    public function store(TransferRequest $request)
    {
        $data = $request->validated();

        try {
            $user = auth()->user();

            $this->transferService->validate($user, $data, $request->get('wallet_type', 'default'));

            $data = $this->transferService->process($user, $data, $request->get('wallet_type', 'default'));

            return response()->json([
                'status' => true,
                'data' => $data,
                'message' => __('Transfer successful'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
