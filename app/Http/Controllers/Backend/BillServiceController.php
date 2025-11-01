<?php

namespace App\Http\Controllers\Backend;

use App\Enums\BillType;
use App\Http\Controllers\Controller;
use App\Models\BillService;
use App\Models\Plugin;
use Bill\Flutterwave;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class BillServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:bill-service-import', ['only' => ['store', 'import', 'bulkStore']]);
        $this->middleware('permission:bill-service-list', ['only' => ['index']]);
        $this->middleware('permission:bill-service-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:bill-convert-rate', ['only' => ['convertRate', 'saveRate']]);
    }

    public function index()
    {
        $services = BillService::latest()->paginate();

        return view('backend.bill.service.index', compact('services'));
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'method' => 'required',
            'category' => 'required',
            'data' => 'required',
        ]);

        if ($validator->fails()) {

            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $data = $request->data;

        $service = $this->insertService($data, $request);

        return response()->json([
            'success' => $service,
            'message' => __($service ? 'Service has been added.' : 'Sorry, something went wrong'),
        ]);
    }

    public function bulkStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'method' => 'required',
            'category' => 'required',
            'operator' => 'nullable',
            'services' => 'required|array|min:1',
        ]);

        if ($validator->fails()) {

            notify()->error($validator->errors()->first(), 'Error');

            return back();
        }

        foreach ($request->services as $item) {

            $this->insertService(collect(json_decode($item))->toArray(), $request);
        }

        notify()->success(__('Bulk service has been added.'), 'Success');

        return to_route('admin.bill.service.index');
    }

    private function insertService($data, $request)
    {
        $countries = getCountries();

        if ($request->method == 'flutterwave') {
            $country = collect($countries)->where('code', $data['country'])->value('name', $data['country']);

            $response = BillService::updateOrCreate([
                'api_id' => $data['id'],
            ], [
                'name' => $data['biller_name'],
                'code' => $data['biller_code'],
                'provider_code' => $data['biller_code'],
                'country' => $country,
                'country_code' => $data['country'],
                'data' => json_encode($data),
                'currency' => getCurrency($country),
                'amount' => $data['amount'],
                'method' => $request->method,
                'type' => $this->findCategory($request->category),
                'charge' => $data['default_commission'] ?? 0,
                'label' => json_encode([$data['label_name']]),
                'status' => true,
            ]);
        }

        return $response ?? false;
    }

    protected function findCategory($type)
    {
        return match (Arr::first(explode(':', $type))) {
            'UTILITYBILLS' => BillType::Electricity,
            'INTSERVICE' => BillType::Internet,
            'CABLEBILLS', 'CableBills' => BillType::Cables,
            'AIRTIME', 'Airtime' => BillType::Airtime,
            'MOBILEDATA' => BillType::DataBundle,
            'POWER' => BillType::Toll,
            default => 'Unknown',
        };
    }

    public function edit($id)
    {
        $service = BillService::find($id);

        return view('backend.bill.service.include.__edit_form', compact('service'))->render();
    }

    public function update(Request $request, $id)
    {
        $service = BillService::findOrFail($id);

        $service->update([
            'status' => $request->boolean('status'),
            'min_amount' => $request->integer('min_amount'),
            'max_amount' => $request->integer('max_amount'),
            'charge' => $request->integer('charge'),
            'charge_type' => $request->charge_type,
        ]);

        notify()->success('Bill service updated successfully');

        return back();
    }

    public function import(Request $request)
    {
        $methods = Plugin::whereIn('name', ['Flutterwave', 'Reloadly', 'Bloc', 'Tpaga'])->where('status', 1)->get();
        $category = $request->category;
        $operator = $request->operator;

        if (request('type') === 'get_service') {

            $response = match (request('method')) {
                'flutterwave' => (new Flutterwave)->getBillerProducts($category, $operator),
                default => [
                    'status' => false,
                    'message' => __('Sorry, something went wrong!'),
                    'data' => [],
                ]
            };

            if ($response['status']) {

                $services = $response['data'];
                $operators = [];
                if (isset($services['operators'])) {
                    $operators = $services['operators'];
                    unset($services['operators']);
                }

                $servicesIds = BillService::where('method', request('method'))->pluck('api_id')->toArray();

                return view('backend.bill.service.import', compact('methods', 'services', 'operators', 'servicesIds'));
            }

            notify()->error(data_get($response, 'message', __('Unknown Error Occurred')), 'Error');

            return back();
        }

        return view('backend.bill.service.import', compact('methods'));
    }

    public function getOperators(Request $request, $method, $category)
    {
        $response = match ($method) {
            'flutterwave' => (new Flutterwave)->getBiller($category),
            default => [
                'status' => false,
                'message' => __('Sorry, something went wrong!'),
                'data' => [],
            ]
        };

        $html = '<option value="" selected disabled>'.__('Select Operator').'</option>';

        foreach ($response['data'] ?? [] as $key => $product) {
            $html .= "<option value='{$product['id']}'>{$product['name']}</option>";
        }

        return $html;
    }

    public function getCategories($method)
    {
        if ($method == 'flutterwave') {
            $categories = app(Flutterwave::class)->getAllCategoriesDropDown();

            return $categories;
        }
    }

    public function convertRate()
    {
        $methods = Plugin::whereIn('name', ['Flutterwave'])->get();
        $currencies = null;
        $rates = [];

        if (request('type') == 'get_currencies') {
            $currencies = match (request('method')) {
                'flutterwave' => BillService::where('method', 'flutterwave')->pluck('currency')->unique()->toArray(),
            };

            $plugin = Plugin::where('name', ucfirst(request('method')))->first();
            $rates = data_get(json_decode($plugin?->data, true), 'currencies');
        }

        return view('backend.bill.convert-rate', compact('methods', 'currencies', 'rates'));
    }

    public function saveRate(Request $request)
    {
        $request->validate([
            'method' => 'required',
            'rate' => 'required|array|min:1',
        ]);

        $plugin = Plugin::where('name', ucfirst($request->method))->firstOrFail();

        $data = json_decode($plugin->data, true);
        $data['currencies'] = $request->rate;
        $plugin->data = json_encode($data);
        $plugin->save();

        notify()->success(__('Conversion Rate added successfully!'), 'Success');

        return back();
    }
}
