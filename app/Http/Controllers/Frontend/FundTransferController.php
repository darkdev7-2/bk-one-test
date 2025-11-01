<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransferRequest;
use App\Models\Beneficiary;
use App\Models\OthersBank;
use App\Models\Transaction;
use App\Models\WireTransfar;
use App\Services\CurrencyService;
use App\Services\TransferService;
use App\Services\WireTransferService;
use App\Traits\ImageUpload;
use App\Traits\NotifyTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class FundTransferController extends Controller
{
    use ImageUpload, NotifyTrait;

    public function __construct(
        private TransferService $transferService,
        private WireTransferService $wireTransferService
    ) {}

    public function index($code = 'default')
    {
        if (! setting('transfer_status', 'permission') || ! Auth::user()->transfer_status) {
            notify()->error(__('Fund transfer currently unavailable!'), 'Error');

            return to_route('user.dashboard');
        } elseif (! setting('kyc_fund_transfer') && auth()->user()->kyc != 1) {
            notify()->error(__('Please verify your KYC.'), 'Error');

            return to_route('user.dashboard');
        }

        $banks = OthersBank::active()->get();
        $wallets = auth()->user()->wallets->load('currency');

        return view('frontend::fund_transfer.index', compact('banks', 'code', 'wallets'));
    }

    public function getBeneficiary(Request $request, $bankId)
    {
        $currencyCode = $request->get('currency_code', setting('site_currency', 'global'));
        if ($bankId != '0') {
            $beneficiaries = Beneficiary::own()->where('bank_id', $bankId)->get();
            $banksData = OthersBank::find($bankId);
            $minimumTransfer = CurrencyService::convert($banksData->minimum_transfer, setting('site_currency', 'global'), $currencyCode);
            $maximumTransfer = CurrencyService::convert($banksData->maximum_transfer, setting('site_currency', 'global'), $currencyCode);
            $banksData->minimum_transfer = $minimumTransfer;
            $banksData->maximum_transfer = $maximumTransfer;
            $charge = $banksData->charge_type === 'percentage' ? $banksData->charge : CurrencyService::convert($banksData->charge, setting('site_currency', 'global'), $currencyCode);
            $banksData->charge = $charge;
        } else {
            $beneficiaries = Beneficiary::own()->whereNull('bank_id')->get();
            $banksData = [
                'minimum_transfer' => CurrencyService::convert(setting('min_fund_transfer', 'fee'), setting('site_currency', 'global'), $currencyCode),
                'maximum_transfer' => CurrencyService::convert(setting('max_fund_transfer', 'fee'), setting('site_currency', 'global'), $currencyCode),
                'charge_type' => setting('fund_transfer_charge_type', 'fee'),
                'charge' => setting('fund_transfer_charge_type', 'fee') === 'percentage' ? setting('fund_transfer_charge', 'fee') : CurrencyService::convert(setting('fund_transfer_charge', 'fee'), setting('site_currency', 'global'), $currencyCode),
            ];
        }

        return response()->json([
            'beneficiaries' => $beneficiaries,
            'banksData' => $banksData,
        ]);
    }

    public function transfer(TransferRequest $request)
    {

        $data = $request->validated();

        try {
            $user = auth()->user();

            $this->transferService->validate($user, $data, $request->get('wallet_type', 'default'));

            $responseData = $this->transferService->process($user, $data, $request->get('wallet_type', 'default'));

            $message = __('Fund Transfer Successfully!');

            return view('frontend::fund_transfer.success', compact('message', 'responseData'));
        } catch (\Exception $e) {

            notify()->error($e->getMessage());

            return redirect()->back();
        }
    }

    public function log()
    {
        $from_date = trim(@explode('-', request('daterange'))[0]);
        $to_date = trim(@explode('-', request('daterange'))[1]);

        $transactions = Transaction::with('userWallet')->fundTransfar()->where('user_id', auth()->id())
            ->search(request('trx'))
            ->when(request('daterange'), function ($query) use ($from_date, $to_date) {
                $query->whereDate('created_at', '>=', Carbon::parse($from_date)->format('Y-m-d'));
                $query->whereDate('created_at', '<=', Carbon::parse($to_date)->format('Y-m-d'));
            })
            ->latest()
            ->paginate(request('limit', 15));

        return view('frontend::fund_transfer.log', compact('transactions'));
    }

    public function wire()
    {
        if (! setting('transfer_status', 'permission') || ! Auth::user()->transfer_status) {
            notify()->error(__('Fund transfer currently unavailable!'), 'Error');

            return to_route('user.dashboard');
        } elseif (! setting('kyc_fund_transfer') && auth()->user()->kyc != 1) {
            notify()->error(__('Please verify your KYC.'), 'Error');

            return to_route('user.dashboard');
        }

        $data = WireTransfar::first();
        $currency = setting('site_currency', 'global');

        $fields = json_decode($data->field_options, true);

        return view('frontend::fund_transfer.wire_transfer', compact('data', 'currency', 'fields'));
    }

    public function wirePost(Request $request)
    {
        try {
            $user = auth()->user();

            $this->wireTransferService->validate($user, $request);

            $responseData = $this->wireTransferService->process($request);

            $message = __('Wire Transfer Successfully!');

            return view('frontend::fund_transfer.success', compact('responseData', 'message'));
        } catch (\Exception $e) {
            notify()->error($e->getMessage());

            return redirect()->back();
        }
    }
}
