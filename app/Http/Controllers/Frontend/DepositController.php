<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\TxnType;
use App\Http\Requests\DepositRequest;
use App\Models\DepositMethod;
use App\Models\Transaction;
use App\Services\DepositService;
use App\Traits\ImageUpload;
use App\Traits\NotifyTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DepositController extends GatewayController
{
    use ImageUpload, NotifyTrait;

    public function __construct(protected DepositService $depositService) {}

    public function deposit($code = 'default')
    {
        if (! setting('user_deposit', 'permission') || ! Auth::user()->deposit_status) {
            notify()->error(__('Deposit currently unavailable'), 'Error');

            return to_route('user.dashboard');
        } elseif (! setting('kyc_deposit') && auth()->user()->kyc != 1) {
            notify()->error(__('Please verify your KYC.'), 'Error');

            return to_route('user.dashboard');
        }

        $isStepOne = 'current';
        $isStepTwo = '';
        $gateways = DepositMethod::where('status', 1)->get();
        $wallets = auth()->user()->wallets->load('currency');

        return view('frontend::deposit.now', compact('isStepOne', 'code', 'isStepTwo', 'gateways', 'wallets'));
    }

    public function depositNow(DepositRequest $request)
    {
        try {
            $user = auth()->user();

            $this->depositService->validate($user, $request);

            $response = $this->depositService->process($user, $request, $request->get('wallet_type', 'default'));

            return $response;
        } catch (\Exception $e) {
            notify()->error($e->getMessage());

            return redirect()->back();
        }
    }

    public function depositSuccess()
    {
        return view('frontend::deposit.success');
    }

    public function depositLog()
    {
        $from_date = trim(@explode('-', request('daterange'))[0]);
        $to_date = trim(@explode('-', request('daterange'))[1]);

        $deposits = Transaction::where('user_id', auth()->user()->id)
            ->search(request('trx'))
            ->whereIn('type', [TxnType::Deposit, TxnType::ManualDeposit])
            ->when(request('daterange'), function ($query) use ($from_date, $to_date) {
                $query->whereDate('created_at', '>=', Carbon::parse($from_date)->format('Y-m-d'));
                $query->whereDate('created_at', '<=', Carbon::parse($to_date)->format('Y-m-d'));
            })->latest()->paginate(request('limit', 15))->withQueryString();

        return view('frontend::deposit.log', compact('deposits'));
    }
}
