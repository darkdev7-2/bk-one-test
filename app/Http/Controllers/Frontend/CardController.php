<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\TxnStatus;
use App\Enums\TxnType;
use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\CardHolder;
use App\Models\DepositMethod;
use App\Models\Plugin;
use App\Models\UserWallet;
use App\Services\CardService;
use App\Traits\Payment;
use App\Traits\VirtualCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Txn;

class CardController extends Controller
{
    use Payment, VirtualCard;

    public function __construct(
        private CardService $cardService
    ) {}

    public function index()
    {
        // Load user cards
        $cards = Card::currentUser()->with('cardHolder')->latest()->get();
        $countries_list = File::json(resource_path('json/CountryCodes.json'));
        $card_holders = CardHolder::currentUser()->get();

        $card_providers = Plugin::active()->type('virtual_card_provider')->get();

        // Return view with cards
        return view('frontend::user.virtual_card.card.index', compact('cards', 'countries_list', 'card_holders', 'card_providers'));
    }

    public function store(Request $request)
    {
        try {

            $this->cardService->validate($request);

            $this->cardService->process($request);

            notify()->success(__('Card created successfully'));

            return to_route('user.card.index');
        } catch (\Exception $e) {
            notify()->error($e->getMessage());

            return back();
        }
    }

    public function details($card_id)
    {
        // Get Card
        $card = Card::with('cardHolder')->currentUser()->where('card_id', $card_id)->firstOrFail();
        $user = auth()->user();

        // Check cache first
        $cacheKey = 'card_transactions_'.$card->id;

        if (Cache::has($cacheKey)) {
            $transactions = Cache::get($cacheKey);
        } else {
            // Provider instance
            $provider = $this->cardProviderMap($card->provider);

            // Load card transactions from API
            $provider_transaction = $provider->getCardTransactions($card->card_id);

            // Store in cache for 1 hour if transactions exist
            if ($provider_transaction && count($provider_transaction)) {
                $transactions = Cache::remember($cacheKey, now()->addHour(), function () use ($provider_transaction) {
                    return $provider_transaction;
                });
            } else {
                $transactions = [];
            }
        }

        $wallets = UserWallet::where('user_id', $user->id)->whereRelation('currency', 'code', $card->currency)->get();
        $gateways = DepositMethod::where('status', 1)->get();

        // Return view with card details
        return view('frontend::user.virtual_card.card.details', [
            'card' => $card,
            'wallets' => $wallets,
            'user' => $user,
            'transactions' => $transactions,
            'gateways' => $gateways,
        ]);
    }

    public function syncCardTransaction($card_id)
    {
        $card = Card::with('cardHolder')->currentUser()->findOrFail($card_id);

        // Forget card transactions cache
        Cache::forget('card_transactions_'.$card->id);

        // Notify user and redirect back
        notify()->success(__('Card transactions synced successfully'));

        return back();
    }

    public function updateStatus($card_id)
    {
        try {
            // Start transaction
            DB::beginTransaction();

            $card = Card::with('cardHolder')->currentUser()->where('card_id', $card_id)->firstOrFail();

            // update stripe card balance
            $this->cardProviderMap($card->provider)->updateCardStatus($card);

            // Commit transaction
            DB::commit();

            // Notify user and redirect back
            notify()->success(__('Card status updated successfully'));

            return back();
        } catch (\Throwable $th) {
            // Rollback transaction
            DB::rollBack();

            // Notify user and redirect back
            notify()->error($th->getMessage());

            return back();
        }
    }

    public function updateCardBalance(Request $request, $card_id)
    {
        $cardTopupEnabled = setting('card_topup', 'permission');

        if (! $cardTopupEnabled) {
            notify()->error(__('Card Topup is unavailable.'));

            return back();
        }

        // Validate request
        $validation = [
            'type' => ['required', 'in:my_wallet,auto_payment'],
            'amount' => ['required', 'regex:/^[0-9]+(\.[0-9][0-9]?)?$/'],
        ];

        if ($request->type == 'auto_payment') {
            $validation['gateway_code'] = 'required';
        }

        $validator = Validator::make($request->all(), $validation);
        if ($validator->fails()) {
            notify()->error($validator->errors()->first(), 'Error');

            return back();
        }

        $min_topup = setting('min_card_topup', 'virtual_card');
        $max_topup = setting('max_card_topup', 'virtual_card');
        $amount = $request->get('amount');

        if ($amount < $min_topup || $amount > $max_topup) {
            $currencySymbol = setting('currency_symbol', 'global');
            $message = 'Please topup the amount within the range '.$currencySymbol.$min_topup.' to '.$currencySymbol.$max_topup;
            notify()->error($message, 'Error');

            return redirect()->back();
        }

        // Get Card
        $card = Card::with('cardHolder')->currentUser()->findOrFail($card_id);

        // if ($request->type == 'auto_payment') {
        //     // Get user and amount
        //     [$gatewayInfo, $txnInfo] = (new CreatePaymentLink)->execute($request);

        //     return self::depositAutoGateway($gatewayInfo->gateway_code, $txnInfo);
        // }

        // Get user and amount
        $user = auth()->user();
        $amount = $request->amount;
        $balance = $user->balance;

        if ($balance < $amount) {
            notify()->error(__('Insufficient balance'));

            return back();
        }

        // charge amount deduction
        $charge = setting('card_topup_charge_type', 'virtual_card') == 'percentage' ? ((setting('card_topup_charge', 'virtual_card') / 100) * $amount) : setting('card_topup_charge', 'virtual_card');
        $charge_amount = $amount + $charge;
        $balance_amount = $card->amount + $amount;

        // add card balance
        $this->cardProviderMap($card->provider)->addCardBalance($card, $balance_amount);

        // Deduct amount from user wallet
        $user->decrement('balance', $charge_amount);

        // create transaction for card topup
        Txn::new($amount, $charge, $balance_amount, 'System', 'Card Topup Charge', TxnType::CardLoad, TxnStatus::Success, 'usd', $charge, auth()->id(), null, 'User', $manualData ?? [], 'default');

        // Notify user and redirect back
        notify()->success(__('Card balance updated successfully'));

        return back();
    }
}
