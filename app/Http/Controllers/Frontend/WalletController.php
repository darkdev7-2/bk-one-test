<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Transaction;
use App\Models\UserWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WalletController extends Controller
{
    public function index()
    {
        abort_if(! setting('multiple_currency', 'permission'), 404);

        $user_wallets = auth()->user()->wallets->load('currency');
        $currencies = Currency::all();

        // Filter out the currencies that the user already has
        $currencies = $currencies->filter(function ($currency) use ($user_wallets) {
            return ! in_array($currency->id, $user_wallets->pluck('currency_id')->toArray());
        });

        return view('frontend::wallet.index', compact('user_wallets', 'currencies'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'currency' => 'required|exists:currencies,id',
        ]);
        if ($validator->fails()) {
            notify()->error($validator->errors()->first(), 'Error');

            return redirect()->back()->with('error', __('Invalid currency'));
        }

        // add new wallet
        auth()->user()->wallets()->create([
            'currency_id' => $request->currency,
        ]);

        notify()->success(__('New wallet added successfully'));

        return back();
    }

    public function destroy(UserWallet $wallet)
    {
        // Check if the wallet has balance
        if ($wallet->balance > 0) {
            notify()->error(__('You can not delete wallet with balance'), 'Error');

            return back();
        }

        try {
            Transaction::where('user_id', auth()->id())->where('wallet_type', (string) $wallet->id)->delete();
        } catch (\Exception $e) {
            notify()->error(__('Failed to delete wallet'), 'Error');

            return back();
        }

        // Delete the wallet
        $wallet->delete();

        notify()->success(__('Wallet deleted successfully'));

        return back();
    }
}
