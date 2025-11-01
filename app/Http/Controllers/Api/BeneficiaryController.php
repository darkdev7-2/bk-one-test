<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Beneficiary;
use App\Models\User;
use App\Services\BeneficiaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BeneficiaryController extends Controller
{
    public function __construct(
        private BeneficiaryService $beneficiaryService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $beneficiaries = Beneficiary::own()
            ->when($request->has('bank_id'), function ($query) use ($request) {
                if ($request->bank_id == '0') {
                    $query->whereNull('bank_id');
                } else {
                    $query->where('bank_id', $request->bank_id);
                }
            })->latest()->get()->map(function ($beneficiary) {
                if ($beneficiary->bank_id === null) {
                    $beneficiary->bank_id = 0;
                }

                return $beneficiary;
            });

        return response()->json([
            'status' => true,
            'data' => $beneficiaries,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bank_id' => 'required',
            'account_name' => 'required',
            'account_number' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        if ($request->bank_id == 0 && ! User::where('account_number', sanitizeAccountNumber($request->account_number))->first()) {
            return response()->json([
                'status' => false,
                'message' => __('Receiver account not found!'),
            ], 422);
        }

        $input = $request->all();
        $input['user_id'] = auth()->id();
        $input['bank_id'] = $request->bank_id == 0 ? null : $request->bank_id;

        $this->beneficiaryService->store($input);

        return response()->json([
            'status' => true,
            'message' => __('Beneficiary added successfully'),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $beneficiary = Beneficiary::with('bank')->own()->findOrFail($id)->toArray();

        if ($beneficiary['bank_id'] === null) {
            $beneficiary['bank_id'] = 0;
            $beneficiary['bank'] = [
                'minimum_transfer' => (float) setting('min_fund_transfer', 'fee'),
                'maximum_transfer' => (float) setting('max_fund_transfer', 'fee'),
                'charge_type' => 'percentage',
                'charge' => (float) setting('fund_transfer_charge', 'fee'),
            ];
        }

        return response()->json([
            'status' => true,
            'data' => $beneficiary,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'bank_id' => 'required_if:bank_id,null',
            'account_name' => 'required',
            'account_number' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        if (! $request->has('bank_id') && ! User::where('account_number', sanitizeAccountNumber($request->account_number))->first()) {
            return response()->json([
                'status' => false,
                'message' => __('Receiver account not found!'),
            ], 422);
        }

        $input = $request->all();
        $input['bank_id'] = $request->bank_id == 0 ? null : $request->bank_id;

        $this->beneficiaryService->update($id, $input);

        return response()->json([
            'status' => true,
            'message' => __('Beneficiary updated successfully'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->beneficiaryService->delete($id);

        return response()->json([
            'status' => true,
            'message' => __('Beneficiary deleted successfully'),
        ]);
    }
}
