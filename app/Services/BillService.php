<?php

namespace App\Services;

use App\Enums\BillStatus;
use App\Enums\TxnStatus;
use App\Enums\TxnType;
use App\Facades\Txn\Txn;
use App\Models\Bill;
use App\Models\BillService as BillServiceModel;
use App\Traits\NotifyTrait;
use Bill\Flutterwave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class BillService
{
    use NotifyTrait;

    public function getServices($country, $type)
    {
        return BillServiceModel::where('country', $country)->type($type)->get();
    }

    public function validate(Request $request)
    {
        if (! setting('kyc_pay_bill') && ! auth()->user()->kyc) {
            throw ValidationException::withMessages(['error' => __('Please verify your KYC.')]);
        }

        $validator = Validator::make($request->all(), [
            'service_id' => 'required|exists:bill_services,id',
            'amount' => 'required|integer',
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages(['error' => $validator->errors()->first()]);
        }
    }

    public function pay(Request $request, BillServiceModel $service)
    {
        $charge = $service->charge_type == 'fixed' ? ($service->charge ?? 0) : ($request->amount / 100) * $service->charge;

        $rates = json_decode(plugin_active(ucfirst($service->method))->data, true)['currencies'];
        $rate = data_get($rates, $service->currency, 0);
        $amount = ($request->amount / $rate) + $charge;

        if (auth()->user()->balance < $amount) {
            throw new \Exception(__('Insufficient Balance!'));
        }

        $response = match ($service->method) {
            'flutterwave' => (new Flutterwave)->payBill($request, $service),
        };

        if ($response['status'] == 'success') {

            $bill = Bill::create([
                'bill_service_id' => $request->get('service_id'),
                'user_id' => auth()->id(),
                'data' => $request->get('data'),
                'amount' => $amount,
                'charge' => $charge ?? 0,
                'response_data' => json_encode($response),
                'status' => BillStatus::Completed,
            ]);

            auth()->user()->decrement('balance', $amount);

            (new Txn)->new($amount, $charge, $amount + $charge, 'System', 'Pay Bill "'.$service->name.'"', TxnType::PayBill, TxnStatus::Success, '', null, $bill->user_id, null, 'User');

            $shortcodes = [
                '[[user_name]]' => auth()->user()->full_name,
                '[[service_name]]' => $service->name,
                '[[amount]]' => $amount,
                '[[charge]]' => $charge,
            ];

            $this->pushNotify('bill_pay', $shortcodes, route('admin.bill.history.complete'), auth()->id(), 'Admin');
        } else {
            throw new \Exception($response['message']);
        }
    }
}
