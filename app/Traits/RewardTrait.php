<?php

namespace App\Traits;

use App\Models\RewardPointEarning;
use App\Models\Transaction;
use App\Models\User;

trait RewardTrait
{
    use NotifyTrait;

    public function rewardToUser($user_id, $trasaction_id)
    {
        $user = User::find($user_id);
        $userPortfolio = RewardPointEarning::where('portfolio_id', $user->portfolio_id)->first();

        if (! $user || ! $userPortfolio) {
            return false;
        }

        $transaction = Transaction::find($trasaction_id);
        $points = floor(($transaction->final_amount / $userPortfolio->amount_of_transactions) * $userPortfolio->point);

        $transaction->update([
            'points' => $points,
        ]);

        $user->increment('points', $points);

        if ($points > 0) {
            $shortcodes = [
                '[[points]]' => $points,
            ];

            $this->pushNotify('get_rewards', $shortcodes, route('user.rewards.index'), $user->id);
        }
    }
}
