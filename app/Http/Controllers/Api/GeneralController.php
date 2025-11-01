<?php

namespace App\Http\Controllers\Api;

use App\Enums\TxnType;
use App\Http\Controllers\Controller;
use App\Http\Resources\BankResource;
use App\Http\Resources\NavigationResource;
use App\Http\Resources\NotificationResource;
use App\Models\BillService;
use App\Models\Branch;
use App\Models\Currency;
use App\Models\OthersBank;
use App\Models\PageSetting;
use App\Models\Plugin;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserNavigation;
use App\Models\WireTransfar;
use App\Models\WithdrawMethod;
use Illuminate\Http\Request;

class GeneralController extends Controller
{
    public function getCountries()
    {
        $location = getLocation();

        return response()->json([
            'status' => true,
            'data' => collect(getCountries())->map(function ($country) use ($location) {
                $country['selected'] = $country['code'] == $location->country_code;

                return $country;
            }),
        ]);
    }

    public function getBranches()
    {
        if (! branch_enabled()) {
            return response()->json([
                'status' => false,
                'message' => 'Branch system is disabled',
                'data' => [],
            ]);
        }

        $branches = Branch::where('status', 1)->get();

        return response()->json([
            'status' => true,
            'data' => $branches->toArray(),
        ]);
    }

    public function getBanks()
    {
        $banks = OthersBank::all();

        return response()->json([
            'status' => true,
            'data' => BankResource::collectionWithDefault($banks, auth()->user()),
        ]);
    }

    public function getCurrencies()
    {
        $multiCurrencyEnabled = setting('multiple_currency', 'permission');
        if (! $multiCurrencyEnabled) {
            return response()->json([
                'status' => false,
                'message' => 'Multiple currency is disabled',
                'data' => [],
            ]);
        }

        $currencies = Currency::all();

        return response()->json([
            'status' => true,
            'data' => $currencies->toArray(),
        ]);
    }

    public function getSettings(Request $request)
    {
        $type = $request->get('key', 'all');
        $settings = Setting::select('name', 'val')->get()->map(function ($setting) {
            return [
                'name' => $setting->name,
                'value' => file_exists(base_path('assets/'.$setting->val)) ? asset($setting->val) : $setting->val,
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $type == 'all' ? $settings : data_get(collect($settings)->firstWhere('name', $type), 'value'),
        ]);
    }

    public function getLanguages()
    {
        if (! setting('language_switcher')) {
            return response()->json([
                'status' => false,
                'message' => 'Language switcher is disabled',
                'data' => [],
            ]);
        }
        $languages = \App\Models\Language::where('status', 1)->get();

        return response()->json([
            'status' => true,
            'data' => $languages->toArray(),
        ]);
    }

    public function getRegisterFields()
    {
        $registerFields = PageSetting::select('key', 'value')->whereNotIn('key', ['shape_one', 'shape_two', 'shape_three', 'basic_page_background', 'breadcrumb'])->get();

        $registerFields = $registerFields->map(function ($field) {

            if ($field->key == 'register_custom_fields') {
                $field->value = count(json_decode($field->value, true)) == 0 ? '{}' : $field->value;
            }

            return $field;
        });

        return response()->json([
            'status' => true,
            'data' => $registerFields,
        ]);
    }

    public function getTransactionTypes()
    {
        $transactionTypes = collect(TxnType::cases())->map(function ($txnType) {
            return [
                'name' => ucwords(str_replace('_', ' ', $txnType->value)),
                'value' => $txnType->value,
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $transactionTypes,
        ]);
    }

    public function wireTransferSettings()
    {
        return response()->json([
            'status' => true,
            'data' => WireTransfar::first()->toArray(),
        ]);
    }

    public function getAccounts($account_id)
    {
        $user = User::where('account_number', sanitizeAccountNumber($account_id))->first();

        return response()->json([
            'status' => true,
            'data' => [
                'name' => $user->full_name ?? '',
                'branch_name' => $user->branch?->name ?? '',
            ],
        ]);
    }

    public function getBillCountries($type)
    {
        $countries = BillService::where('type', $type)->pluck('country')->unique()->values();

        return response()->json([
            'status' => true,
            'data' => $countries,
        ]);
    }

    public function getCardProviders()
    {
        $providers = Plugin::active()->type('virtual_card_provider')->select('id', 'name')->get();

        return response()->json([
            'status' => true,
            'data' => $providers,
        ]);
    }

    public function getWithdrawMethods()
    {
        $methods = WithdrawMethod::where('status', 1)->get()->map(function ($method) {
            $method->fields = json_encode((object) json_decode($method->fields, true));

            return $method;
        });

        return response()->json([
            'status' => true,
            'data' => $methods,
        ]);
    }

    public function getOnboardingScreenImages()
    {
        return response()->json([
            'status' => true,
            'data' => [
                asset(getPageSetting('app_splash_one_image')),
                asset(getPageSetting('app_splash_two_image')),
                asset(getPageSetting('app_splash_three_image')),
                asset(getPageSetting('app_splash_four_image')),
            ],
        ]);
    }

    public function getNavigations()
    {
        $user_navigations = UserNavigation::orderBy('position')->get();

        return response()->json([
            'status' => true,
            'data' => NavigationResource::collection($user_navigations),
        ]);
    }

    public function getPlugins()
    {
        $plugins = Plugin::where('status', 1)->get();

        return response()->json([
            'status' => true,
            'data' => $plugins,
        ]);
    }

    public function getNotifications()
    {
        $notifications = auth()->user()->notifications()->latest()->paginate();

        $grouped = $notifications->groupBy(function ($notification) {
            $date = $notification->created_at->startOfDay();
            if ($date->isToday()) {
                return 'Today';
            } elseif ($date->isYesterday()) {
                return 'Yesterday';
            }

            return $date->format('d M Y');

        });

        $result = [];

        foreach ($grouped as $key => $group) {
            $result[$key] = NotificationResource::collection($group);
        }

        return response()->json([
            'status' => true,
            'data' => $result,
            'meta' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'per_page' => $notifications->perPage(),
                'total' => $notifications->total(),
            ],
        ]);
    }
}
