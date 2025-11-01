<?php

namespace Bill;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class Bloc
{
    protected $client;

    public function __construct()
    {
        $token = config('bloc.connections.api_key');
        $this->client = Http::baseUrl('https://api.blochq.io/v1/bills')->withToken($token);
    }

    public function getOperators($bill)
    {
        $response = $this->client->get('operators', [
            'bill' => $bill,
        ]);

        if ($response->json('success')) {

            return [
                'status' => true,
                'data' => $response->json('data'),
                'message' => $response->json('message'),
            ];
        }

        return [
            'status' => false,
            'data' => [],
            'message' => $response->json('message'),
        ];
    }

    public function getOperatorProducts($bill, $operatorId, $withOperators = false)
    {
        $response = $this->client->withUrlParameters([
            'operatorID' => $operatorId,
            'bill' => $bill,
        ])->get('operators/{operatorID}/products?bill={bill}');

        if ($response->successful() && $response->json('success')) {
            $data = $response->json('data');

            if ($withOperators) {
                $operators = $this->getOperators($bill);
                $data = array_merge([
                    'operators' => $operators['data'],
                ], $data);
            }

            return [
                'status' => true,
                'data' => $data,
                'message' => $response->json('message'),
            ];
        }

        return [
            'status' => false,
            'data' => [],
            'message' => $response->json('message'),
        ];
    }

    public function payBill($billService, Request $request)
    {

        $amount = $request->input('amount');
        $country = $request->input('country');
        $serviceId = $request->input('service_id');
        $beneficiaryMsisdn = $request->input('data')['beneficiary_msisdn'];

        $billServiceData = json_decode($billService->data);
        $data = [
            'amount' => $amount,
            'product_id' => $billServiceData->id,
            'operator_id' => $billServiceData->operator,
        ];

        $data['device_details']['beneficiary_msisdn'] = $beneficiaryMsisdn;
        if ($billService->type == 'electricity') {
            $data['device_details']['meter_type'] = $request->input('data')['meter_type'];
        }

        if (in_array($billService->type, ['cable', 'electricity'])) {
            $data['device_details']['device_number'] = $request->input('data')['device_number'];
        }

        if ($billService->type == 'cable') {
            $bill_type = 'television';
        } elseif (in_array($billService->type, ['airtime', 'data-bundle'])) {
            $bill_type = 'telco';
        }

        $data['bill'] = $bill_type;
        $response = $this->client->acceptJson()
            ->withHeaders([
                'content-type' => 'application/json',
            ])->post('payment', $data);
        dd($response->json(), $data);

        return [
            'status' => false,
            'data' => [],
            'message' => __('Sorry,something went wrong!'),
        ];
    }
}
