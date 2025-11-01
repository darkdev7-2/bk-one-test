<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\WalletResource;
use App\Models\Transaction;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $wallets = auth()->user()->wallets->load('currency');

        return response()->json([
            'status' => true,
            'data' => WalletResource::collectionWithDefault($wallets, auth()->user()),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'currency_id' => 'required|exists:currencies,id',
        ]);

        $user = auth()->user();

        // Check if wallet already exists for this currency
        if ($user->wallets()->where('currency_id', $request->currency_id)->exists()) {
            return response()->json([
                'status' => false,
                'message' => __('Wallet for this currency already exists'),
            ], 422);
        }

        $user->wallets()->create([
            'currency_id' => $request->currency_id,
        ]);

        return response()->json([
            'status' => true,
            'message' => __('Wallet created successfully'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = auth()->user();
        $wallet = $user->wallets()->findOrFail($id);

        if ($wallet->balance > 0) {
            return response()->json([
                'status' => false,
                'message' => __('You can not delete wallet with balance'),
            ], 422);
        }

        Transaction::where('user_id', $user->id)->where('wallet_type', (string) $wallet->id)->delete();

        $wallet->delete();

        return response()->json([
            'status' => true,
            'message' => __('Wallet deleted successfully'),
        ]);
    }
}
