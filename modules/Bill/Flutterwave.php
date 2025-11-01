<?php

namespace Bill;

use App\Models\BillService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class Flutterwave
{
    protected $client;

    public function __construct()
    {
        $secretKey = config('flutterwave.connections.secret_key');

        $this->client = Http::baseUrl('https://api.flutterwave.com/v3')->withToken($secretKey);
    }

    public function getAllCategories($type = null)
    {
        $response = $this->client->withUrlParameters([
            'type' => $type,
        ])->get('/top-bill-categories');

        if ($response->json('status')) {

            return [
                'status' => true,
                'data' => $response->json('data'),
            ];
        }

        return [
            'status' => false,
            'data' => [],
            'message' => $response->json('message'),
        ];
    }

    public function getAllCategoriesDropDown($type = null)
    {
        $categories = $this->getAllCategories($type) ?? [];

        $supportedTypes = [
            'AIRTIME',
            'MOBILEDATA',
            'CABLEBILLS',
            'INTSERVICE',
            'UTILITYBILLS',
        ];

        $validCategory = collect($categories['data'])->filter(function ($category) use ($supportedTypes) {
            return in_array(strtoupper($category['code']), $supportedTypes);
        });

        $html = '<option value="" selected disabled>Select Category</option>';

        foreach ($validCategory as $key => $cate) {
            $html .= "<option data-country='{$cate['country_code']}' data-code='{$cate['code']}' value='{$cate['code']}:{$cate['country_code']}'>{$cate['name']} - {$cate['country_code']}</option>";
        }

        return $html;
    }

    public function getBiller($data)
    {
        $data = explode(':', $data);

        $category = $data[0];
        $country = $data[1];

        $response = $this->client->withUrlParameters([
            'category' => $category,
        ])->get('/bills/{category}/billers', [
            'country' => $country,
        ]);

        $res_data = $response->json('data');

        foreach ($res_data as $key => $value) {
            $res_data[$key]['id'] = $value['biller_code'];
        }

        if ($response->json('status')) {

            return [
                'status' => true,
                'data' => $res_data,
            ];
        }

        return [
            'status' => false,
            'data' => [],
            'message' => $response->json('message'),
        ];
    }

    public function getBillerProducts($category, $operator)
    {
        $response = $this->client->withUrlParameters([
            'biller_code' => $operator,
        ])->get('/billers/{biller_code}/items');

        if ($response->json('status')) {

            return [
                'status' => true,
                'data' => $response->json('data'),
            ];
        }

        return [
            'status' => false,
            'data' => [],
            'message' => $response->json('message'),
        ];
    }

    public function validatePay($customer, $item_code, $biller_code)
    {
        $response = $this->client->withUrlParameters([
            'item_code' => $item_code,
        ])->get('/bill-items/{item_code}/validate', [
            'code' => $biller_code,
            'customer' => $customer,
        ]);

        if ($response->json('status') == 'success') {
            return [
                'status' => 'success',
            ];
        }

        return [
            'status' => false,
            'message' => $response->json('message') ?? 'Validation failed',
        ];
    }

    public function payBill($request, $service)
    {
        $service_id = json_decode($request->service_id);
        $service = BillService::find($service_id);
        $item_code = data_get(json_decode($service->data, true), 'item_code');
        $customer = Arr::first($request->data);

        if (strtoupper($service->type) == 'AIRTIME') {
            $validate = $this->validatePay($customer, $item_code, $service->code);
            if ($validate['status'] == 'success') {
                return $this->createPayment($request);
            }

            return $validate;
        }

        return $this->createPayment($request);
    }

    public function createPayment($request = null)
    {
        $service_id = json_decode($request->service_id);
        $service = BillService::find($service_id);
        $item_code = data_get(json_decode($service->data, true), 'item_code');
        $customer = Arr::first($request->data);

        // dynamic params
        $params = [
            'amount' => $request->amount,
            'customer_id' => $customer,
            'country' => $service['country_code'],
        ];

        $response = $this->client->withUrlParameters([
            'biller_code' => $service['provider_code'],
            'item_code' => $item_code,
        ])->post('billers/{biller_code}/items/{item_code}/payment', $params);

        if ($response->json('status') == 'success') {

            return [
                'status' => true,
                'message' => $response->json('message'),
                'data' => $response->json('data'),
            ];
        }

        return [
            'status' => false,
            'data' => [],
            'message' => $response->json('message') ?? 'Something went wrong',
        ];
    }
}
