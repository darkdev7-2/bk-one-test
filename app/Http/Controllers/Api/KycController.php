<?php

namespace App\Http\Controllers\Api;

use App\Enums\KYCStatus;
use App\Http\Controllers\Controller;
use App\Models\Kyc;
use App\Models\UserKyc;
use App\Traits\ImageUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KycController extends Controller
{
    use ImageUpload;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userKycIds = UserKyc::whereIn('status', ['pending', 'approved'])->where('user_id', auth()->id())->where('is_valid', true)->pluck('kyc_id');

        $kycs = Kyc::where('status', true)->whereNotIn('id', $userKycIds)->get();

        return response()->json([
            'status' => true,
            'data' => $kycs,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kyc_id' => 'required',
            'fields' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $kyc = Kyc::find($request->kyc_id);

        $user = auth()->user();

        $newKycs = $request->fields;

        foreach ($newKycs as $key => $value) {
            if (is_file($value)) {
                $newKycs[$key] = self::imageUploadTrait($value);
            }
        }

        UserKyc::create([
            'user_id' => $user->id,
            'kyc_id' => $kyc->id,
            'type' => $kyc->name,
            'data' => $newKycs,
            'is_valid' => true,
            'status' => 'pending',
        ]);

        $pendingCount = UserKyc::where('user_id', $user->id)->whereIn('status', ['pending', 'approved'])->where('is_valid', true)->count();
        $isPending = Kyc::where('status', true)->count() == $pendingCount ? true : false;

        $user->update([
            'kyc' => $isPending ? KYCStatus::Pending : KYCStatus::NOT_SUBMITTED,
        ]);

        return response()->json([
            'status' => true,
            'message' => __('KYC submitted successfully'),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $kyc = UserKyc::find($id);

        $kyc->data = collect($kyc->data)->map(function ($value) {
            if (file_exists(base_path('assets/'.$value))) {
                return asset($value);
            }

            return $value;
        });

        return response()->json([
            'status' => true,
            'data' => $kyc,
        ]);
    }

    public function histories()
    {
        $histories = UserKyc::where('user_id', auth()->id())->latest()->get()->map(function ($history) {
            $history->data = collect($history->data)->map(function ($value) {
                if (file_exists(base_path('assets/'.$value))) {
                    return asset($value);
                }

                return $value;
            });

            return $history;
        });

        return response()->json([
            'status' => true,
            'data' => $histories,
        ]);
    }
}
