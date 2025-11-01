<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\DpsStatus;
use App\Enums\FdrStatus;
use App\Enums\LoanStatus;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = auth()->user()->load([
            'dps.plan',
            'fdr.plan',
            'loan.plan',
        ]);

        $transactions = Transaction::where('user_id', $user->id);

        $recentTransactions = $transactions->latest()->take(5)->get();

        $referral = $user->getReferrals()->first();

        $dataCount = [
            'total_transaction' => $transactions->count(),
            'total_deposit' => $user->totalDeposit(),
            'total_profit' => $user->totalProfit(),
            'profit_last_7_days' => $user->totalProfit(7),
            'total_withdraw' => $user->totalWithdraw(),
            'total_transfer' => $user->totalTransfer(),
            'total_dps' => $user->dps->count(),
            'total_bill' => $user->bill->count(),
            'total_fdr' => $user->fdr->count(),
            'total_running_dps' => $user->dps->whereIn('status', [DpsStatus::Running, DpsStatus::Due])->count(),
            'total_running_loan' => $user->loan->whereIn('status', [LoanStatus::Running, LoanStatus::Due])->count(),
            'total_running_fdr' => $user->fdr->where('status', FdrStatus::Running)->count(),
            'total_loan' => $user->loan->count(),
            'total_referral_profit' => $user->totalReferralProfit(),
            'total_referral' => $referral?->relationships()->count() ?? 0,
            'deposit_bonus' => $user->totalDepositBonus(),
            'portfolio_achieved' => $user->portfolioAchieved(),
            'total_tickets' => $user->ticket->count(),
            'recentTransactions' => $recentTransactions,
            'user' => $user,
            'dps_mature_amount' => $user->dps->whereIn('status', [DpsStatus::Running, DpsStatus::Due])->sum('total_mature_amount'),
            'fdr_mature_amount' => $user->fdr->where('status', FdrStatus::Running)->sum('total_mature_amount'),
            'total_loan_amount' => $user->loan->whereIn('status', [LoanStatus::Running, LoanStatus::Due])->sum('total_loan_amount'),
        ];

        return view('frontend::user.dashboard', $dataCount);
    }
}
