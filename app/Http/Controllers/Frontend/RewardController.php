<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\TxnType;
use App\Http\Controllers\Controller;
use App\Models\RewardPointEarning;
use App\Models\RewardPointRedeem;
use App\Models\Transaction;
use App\Services\RewardsService;

class RewardController extends Controller
{
    public function __construct(
        private RewardsService $rewardsService
    ) {}

    public function index()
    {
        $redeems = RewardPointRedeem::with('portfolio')->get();
        $earnings = RewardPointEarning::with('portfolio')->get();

        $myPortfolio = RewardPointRedeem::where('portfolio_id', auth()->user()->portfolio_id)->first();

        $transactions = Transaction::where('user_id', auth()->id())
            ->latest()
            ->where('type', TxnType::RewardRedeem)
            ->paginate(5);

        return view('frontend::rewards.index', compact('redeems', 'earnings', 'myPortfolio', 'transactions'));
    }

    public function redeemNow()
    {
        try {
            // Get user
            $user = auth()->user();
            $this->rewardsService->redeem($user);

            notify()->success(__('Rewards redeem successfully!'));

            return back();
        } catch (\Exception $e) {
            notify()->error($e->getMessage());

            return back();
        }
    }
}
