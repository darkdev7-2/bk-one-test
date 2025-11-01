<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\WithdrawAccountResource;
use App\Models\WithdrawAccount;
use App\Traits\ImageUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WithdrawAccountController extends Controller
{
    use ImageUpload;

    public function index()
    {
        $accounts = WithdrawAccount::with('method')
            ->where('user_id', auth()->id())
            ->get()
            ->map(function ($account) {
                $credentials = json_decode($account->credentials ?? '[]', true) ?? [];

                $account->fields = collect($credentials)->map(function ($field) {
                    if ($field['type'] === 'file' && file_exists(base_path('assets/'.$field['value']))) {
                        $field['value'] = asset($field['value']);
                    }

                    return $field;
                })->toArray();

                return $account;
            });

        return response()->json([
            'status' => true,
            'data' => WithdrawAccountResource::collection($accounts),
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'withdraw_method_id' => 'required',
            'method_name' => 'required',
            'fields' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $input = $request->all();
        $credentials = $input['fields'];
        $input['user_id'] = auth()->id();

        foreach ($request->fields as $key => $formData) {
            if (isset($formData['value']) && $formData['type'] == 'file' && $formData['value'] instanceof \Illuminate\Http\UploadedFile) {
                $credentials[$key]['value'] = self::imageUploadTrait($formData['value']);
            }
        }

        WithdrawAccount::create([
            'user_id' => auth()->id(),
            'withdraw_method_id' => $input['withdraw_method_id'],
            'method_name' => $input['method_name'],
            'credentials' => json_encode($credentials),
        ]);

        return response()->json([
            'status' => true,
            'message' => __('Withdraw account created successfully'),
        ]);
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'method_name' => 'required',
            'fields' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $input = $request->all();
        $credentials = $input['fields'];
        $input['user_id'] = auth()->id();

        foreach ($request->fields as $key => $formData) {
            if (isset($formData['value']) && $formData['type'] == 'file' && $formData['value'] instanceof \Illuminate\Http\UploadedFile) {
                $credentials[$key]['value'] = self::imageUploadTrait($formData['value']);
            }
        }

        WithdrawAccount::find($id)->update([
            'method_name' => $input['method_name'],
            'credentials' => json_encode($credentials),
        ]);

        return response()->json([
            'status' => true,
            'message' => __('Withdraw account updated successfully'),
        ]);
    }

    public function destroy(string $id)
    {
        WithdrawAccount::where('user_id', auth()->id())->findOrFail($id)->delete();

        return response()->json([
            'status' => true,
            'message' => __('Withdraw account deleted successfully'),
        ]);
    }
}
