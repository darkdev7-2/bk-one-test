<?php

namespace App\Services;

use App\Enums\TxnStatus;
use App\Enums\TxnType;
use App\Facades\Txn\Txn;
use App\Models\RewardPointRedeem;
use App\Models\Transaction;
use App\Models\User;
use Exception;

class RewardsService
{
    public function redeem(User $user)
    {
        $portfolio = RewardPointRedeem::where('portfolio_id', $user->portfolio_id)->first();

        if (! $portfolio) {
            throw new Exception(__('Reward not available for your portfolio.'));
        }

        if ($user->points <= 0) {
            throw new Exception(__('You don\'t have enough points to redeem.'));
        }

        // Calculate redeem amount by portfolio wise redeem point and amount
        $redeemAmount = ($portfolio->amount / $portfolio->point) * $user->points;

        // Create transaction
        (new Txn)->new($redeemAmount, 0, $redeemAmount, 'System', $user->points.' Points Reward Redeem', TxnType::RewardRedeem, TxnStatus::Success, '', null, $user->id, null, 'User');

        // Deduct user point
        $user->points = 0;

        // Add amount to user balance
        $user->balance += $redeemAmount;
        $user->save();
    }
}
