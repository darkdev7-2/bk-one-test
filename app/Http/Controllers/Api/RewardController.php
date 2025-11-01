<?php

namespace App\Http\Controllers\Api;

use App\Enums\TxnType;
use App\Http\Controllers\Controller;
use App\Http\Resources\RewardEarningsResource;
use App\Http\Resources\RewardRedeemResource;
use App\Models\RewardPointEarning;
use App\Models\RewardPointRedeem;
use App\Services\RewardsService;
use Illuminate\Http\Request;

class RewardController extends Controller
{
    public function __construct(
        private RewardsService $rewardsService
    ) {}

    public function index()
    {
        $user = auth()->user();
        $myPortfolio = RewardPointRedeem::where('portfolio_id', $user->portfolio_id)->first();
        $currencySymbol = setting('currency_symbol', 'global');
        $earnings = RewardPointEarning::with('portfolio')->get();
        $redeems = RewardPointRedeem::with('portfolio')->get();

        return response()->json([
            'status' => true,
            'data' => [
                'points' => $user->points,
                'is_portfolio' => $myPortfolio ? true : false,
                'portfolio' => $myPortfolio->portfolio->portfolio_name ?? null,
                'portfolio_icon' => asset($user->portfolio?->icon),
                'text' => __('Every :points reward points are :amount', ['points' => $myPortfolio?->point, 'amount' => $currencySymbol.$myPortfolio?->amount]),
                'earnings' => RewardEarningsResource::collection($earnings),
                'redeems' => RewardRedeemResource::collection($redeems),
            ],
        ]);
    }

    public function transactions()
    {
        $transactions = auth()->user()->transactions()->where('type', TxnType::RewardRedeem)->latest()->paginate(10);

        return response()->json([
            'status' => true,
            'data' => $transactions,
        ]);
    }

    public function redeem(Request $request)
    {
        try {
            // Get user
            $user = auth()->user();
            // Redeem
            $this->rewardsService->redeem($user);

            return response()->json([
                'status' => true,
                'message' => __('Rewards redeem successfully!'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
