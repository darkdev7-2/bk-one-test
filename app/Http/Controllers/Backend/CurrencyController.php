<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function update(Request $request)
    {
        $all_currencies = $request->field_options;

        // create or update the currencies from the list
        foreach ($all_currencies as $single_currency) {
            if ($this->checkCurrencyExists($single_currency) && $this->notExitsDefaultCurrency($single_currency)) {
                $currency = Currency::where('code', $single_currency['current_code'])->first();
                $currency->update([
                    'name' => $single_currency['name'],
                    'symbol' => $single_currency['symbol'],
                    'code' => $single_currency['code'],
                    'rate' => $single_currency['rate'],
                ]);
            } elseif ($this->checkCurrencyNotExists($single_currency) && $this->notExitsDefaultCurrency($single_currency)) {
                $currency = Currency::create([
                    'name' => $single_currency['name'],
                    'symbol' => $single_currency['symbol'],
                    'code' => $single_currency['code'],
                    'rate' => $single_currency['rate'],
                ]);
            }
        }

        // Delete the currencies which are not in the list
        $delete_currency_ids = $request->delete_currencies;
        if ($delete_currency_ids) {
            Currency::doesnthave('userWallet')->whereIn('id', $delete_currency_ids)->delete();
        }

        // Success message and redirect
        notify()->success(__('Currency updated successfully!'), 'Success');

        return back();
    }

    public function destroy($id)
    {
        // Find the currency and delete
        $currency = Currency::withCount('userWallet')->findOrFail($id);

        // Check if currency has wallet balance which is already in use
        if ($currency->user_wallets_count > 0) {
            return [
                'status' => 'error',
                'message' => 'This currency has wallet balance which is already in use!',
            ];
        }

        // Delete the currency
        $currency->delete();

        // Success message
        return [
            'status' => 'success',
            'message' => 'Currency deleted successfully!',
        ];
    }

    protected function checkCurrencyExists($single_currency)
    {
        return isset($single_currency['current_name'], $single_currency['name'], $single_currency['current_symbol']) && ($single_currency['current_name'] != $single_currency['name'] || $single_currency['current_symbol'] != $single_currency['symbol'] || $single_currency['current_code'] != $single_currency['code'] || $single_currency['current_rate'] != $single_currency['rate']);
    }

    protected function checkCurrencyNotExists($single_currency)
    {
        return ! isset($single_currency['current_name'], $single_currency['name'], $single_currency['current_symbol']);
    }

    protected function notExitsDefaultCurrency($single_currency)
    {
        return $single_currency['code'] != setting('site_currency', 'global');
    }
}
