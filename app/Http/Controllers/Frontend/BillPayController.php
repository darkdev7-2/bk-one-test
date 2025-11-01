<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\BillType;
use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\BillService;
use App\Services\BillService as BillServiceClass;
use App\Traits\NotifyTrait;
use Illuminate\Http\Request;

class BillPayController extends Controller
{
    use NotifyTrait;

    public function airtime()
    {
        $countries = BillService::where('type', BillType::Airtime)->where('status', true)->pluck('country')->unique();

        return view('frontend::pay_bill.airtime', compact('countries'));
    }

    public function electricity()
    {
        $countries = BillService::where('type', BillType::Electricity)->where('status', true)->pluck('country')->unique();

        return view('frontend::pay_bill.electricity', compact('countries'));
    }

    public function internet()
    {
        $countries = BillService::where('type', BillType::Internet)->where('status', true)->pluck('country')->unique();

        return view('frontend::pay_bill.internet', compact('countries'));
    }

    public function cables()
    {
        $countries = BillService::where('type', BillType::Cables)->where('status', true)->pluck('country')->unique();

        return view('frontend::pay_bill.cables', compact('countries'));
    }

    public function dataBundle()
    {
        $countries = BillService::where('type', BillType::DataBundle)->where('status', true)->pluck('country')->unique();

        return view('frontend::pay_bill.data-bundle', compact('countries'));
    }

    public function toll()
    {
        $countries = BillService::where('type', BillType::Toll)->where('status', true)->pluck('country')->unique();

        return view('frontend::pay_bill.toll', compact('countries'));
    }

    public function history()
    {
        $bills = Bill::with('service')->latest()->paginate();

        return view('frontend::pay_bill.history', compact('bills'));
    }

    public function store(Request $request)
    {
        $service = BillService::findOrFail($request->service_id);

        if (! setting('kyc_pay_bill') && auth()->user()->kyc != 1) {
            notify()->error(__('Please verify your KYC.'), 'Error');

            return to_route('user.dashboard');
        }

        try {
            (new BillServiceClass)->pay($request, $service);
            notify()->success(__('Bill payment successful!'), 'Success');
        } catch (\Exception $e) {
            notify()->error($e->getMessage(), 'Error');
        }

        return back();
    }

    public function getServices($country, $type)
    {
        $services = BillService::where('country', $country)->type($type)->get();

        $html = "<option value='' selected disabled>".__('Select Service').'</option>';

        foreach ($services as $service) {
            $html .= sprintf("<option value='%s' data-currency='%s' data-label='%s' data-amount='%s'>%s</option>", $service->id, $service->currency, json_encode($service->label), $service->amount, $service->name);
        }

        return response()->json([
            'html' => $html,
        ]);
    }

    public function getPaymentDetails(Request $request)
    {
        $service = BillService::findOrFail($request->service_id);

        $request->amount = empty($request->amount) ? 0 : $request->amount;

        $charge = $service->charge_type == 'fixed' ? $service->charge : ($request->amount / 100) * $service->charge;

        $currency = setting('site_currency', 'global');
        $currency_rates = json_decode(plugin_active(ucfirst($service->method))->data, true);

        $rate = data_get($currency_rates, 'currencies.'.$service->currency, 0);
        $payable_amount = $rate > 0 ? (($request->amount / $rate) + $charge) : 0;

        return response()->json([
            'charge' => $charge.' '.$currency,
            'amount' => $request->amount.' '.$service->currency,
            'rate' => "1 {$currency} = {$rate} {$service->currency}",
            'payable_amount' => $payable_amount.' '.$currency,
        ]);
    }
}
