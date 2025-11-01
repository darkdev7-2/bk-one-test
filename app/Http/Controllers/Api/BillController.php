<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BillHistoryResource;
use App\Http\Resources\BillServiceResource;
use App\Models\Bill;
use App\Models\BillService as BillServiceModel;
use App\Services\BillService;
use Illuminate\Http\Request;

class BillController extends Controller
{
    public function history()
    {
        $bills = Bill::with('service')->latest()->paginate();

        return response()->json([
            'status' => true,
            'data' => BillHistoryResource::collection($bills),
            'meta' => [
                'current_page' => $bills->currentPage(),
                'last_page' => $bills->lastPage(),
                'per_page' => $bills->perPage(),
                'total' => $bills->total(),
            ],
        ]);
    }

    public function getServices($country, $type)
    {
        $services = BillServiceModel::where('country', $country)->type($type)->get();

        return response()->json([
            'status' => true,
            'data' => BillServiceResource::collection($services),
        ]);
    }

    public function payNow(Request $request)
    {
        try {
            $service = BillServiceModel::find($request->service_id);

            (new BillService)->validate($request);
            (new BillService)->pay($request, $service);

            return response()->json([
                'status' => true,
                'message' => __('Bill payment successful!'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
