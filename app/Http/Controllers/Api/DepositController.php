<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DepositRequest;
use App\Models\DepositMethod;
use App\Services\DepositService;
use Illuminate\Http\Request;

class DepositController extends Controller
{
    public function __construct(
        private DepositService $depositService
    ) {}

    public function index(Request $request)
    {
        $methods = DepositMethod::when($request->has('currency'), function ($query) use ($request) {
            $query->where('currency', $request->currency);
        })->where('status', 1)->get()->map(function ($method) {
            $method->logo = asset($method->logo ?? $method->gateway->logo);

            return $method;
        });

        return response()->json([
            'status' => true,
            'data' => $methods,
        ]);
    }

    public function store(DepositRequest $request)
    {
        try {
            $user = auth()->user();

            $this->depositService->validate($user, $request);

            $response = $this->depositService->process($user, $request, $request->get('wallet_type', 'default'));

            return response()->json([
                'status' => true,
                'data' => $response,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
