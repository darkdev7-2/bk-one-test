<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\WireTransferService;
use Illuminate\Http\Request;

class WireTransferController extends Controller
{
    public function __construct(
        private WireTransferService $wireTransferService
    ) {}

    public function __invoke(Request $request)
    {
        try {
            $user = auth()->user();

            $this->wireTransferService->validate($user, $request);

            $responseData = $this->wireTransferService->process($request);

            return response()->json([
                'status' => true,
                'message' => __('Wire transfer successful'),
                'data' => $responseData,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
