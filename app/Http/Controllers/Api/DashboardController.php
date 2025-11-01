<?php

namespace App\Http\Controllers\Api;

use App\Enums\DpsStatus;
use App\Enums\FdrStatus;
use App\Enums\LoanStatus;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Export\CSV\TransactionExport;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\WalletResource;
use App\Models\Transaction;
use App\Models\UserWallet;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $wallets = UserWallet::with('currency')->where('user_id', auth()->id())->get();
        $currency_symbol = setting('currency_symbol');
        $user = auth()->user();

        return response()->json([
            'status' => true,
            'data' => [
                'greeting' => grettings(),
                'user_name' => $user->full_name,
                'earn_text' => __('Earn :amount', ['amount' => $currency_symbol.setting('referral_bonus', 'fee')]),
                'wallets' => WalletResource::collectionWithDefault($wallets, $user),
                'dps_data' => [
                    'total_running_dps_amount' => $currency_symbol.$user->dps->whereIn('status', [DpsStatus::Running, DpsStatus::Due])->sum('total_mature_amount'),
                    'running_dps_summary' => $user->dps->whereIn('status', [DpsStatus::Running, DpsStatus::Due])->map(function ($dps) {
                        return [
                            'name' => $dps->plan?->name,
                            'end_date' => $dps->last_date,
                        ];
                    })->values(),
                ],
                'fdr_data' => [
                    'total_running_fdr_amount' => $currency_symbol.$user->fdr->where('status', FdrStatus::Running)->sum('total_mature_amount'),
                    'running_fdr_summary' => $user->fdr->where('status', FdrStatus::Running)->map(function ($fdr) {
                        return [
                            'name' => $fdr->plan?->name,
                            'end_date' => now()->parse($fdr->last_date)->format('d M Y'),
                        ];
                    })->values(),
                ],
                'loan_data' => [
                    'total_running_loan_amount' => $currency_symbol.$user->loan->whereIn('status', [LoanStatus::Running, LoanStatus::Due])->sum('total_loan_amount'),
                    'running_loan_summary' => $user->loan->whereIn('status', [LoanStatus::Running, LoanStatus::Due])->map(function ($loan) {
                        return [
                            'name' => $loan->plan?->name,
                            'end_date' => now()->parse($loan->last_date)->format('d M Y'),
                        ];
                    })->values(),
                ],
                'statistics' => [
                    'total_transactions' => $user->transaction()->count(),
                    'total_deposit' => $currency_symbol.$user->total_deposit,
                    'total_transfer' => $currency_symbol.$user->totalTransfer(),
                    'total_pay_bill' => $user->bill->count(),
                    'total_referral_profit' => $currency_symbol.$user->totalReferralProfit(),
                ],
                'transactions' => TransactionResource::collection($user->transaction()->latest()->take(5)->get()),
            ],
        ]);
    }

    public function transactions()
    {
        $transactions = Transaction::with('userWallet')->where('user_id', auth()->id())->when(request()->has('type'), function ($query) {
            $types = collect(explode(',', request('type')))->map(function ($type) {
                return str()->of($type)->snake()->lower()->value();
            })->toArray();
            $query->whereIn('type', $types);
        })->when(request()->has('transaction_id'), function ($query) {
            $query->where('tnx', 'LIKE', '%'.request('transaction_id').'%');
        })->when(request()->has(['from_date', 'to_date']), function ($query) {
            $query->whereDate('created_at', '>=', request('from_date'))
                ->whereDate('created_at', '<=', request('to_date'));
        })->latest();

        if (request()->has('export')) {
            return $this->exportTransactions($transactions->get());
        }

        $transactions = $transactions->paginate()->withQueryString();

        return TransactionResource::collection($transactions);
    }

    public function statistics()
    {
        $user = auth()->user();
        $currency_symbol = setting('currency_symbol');

        return response()->json([
            'status' => true,
            'data' => [
                'all_transactions' => $user->transaction()->count(),
                'total_deposit' => $currency_symbol.$user->total_deposit,
                'total_withdraw' => $currency_symbol.$user->totalWithdraw(),
                'total_transfer' => $currency_symbol.$user->totalTransfer(),
                'total_dps' => $currency_symbol.$user->dps->sum('total_dps_amount'),
                'total_fdr' => $currency_symbol.$user->fdr->sum('amount'),
                'total_loan' => $currency_symbol.$user->loan->sum('amount'),
                'total_bill' => $currency_symbol.$user->bill->sum('amount'),
                'total_referral_profit' => $currency_symbol.$user->totalReferralProfit(),
                'total_referral' => $user->referrals()->count(),
                'deposit_bonus' => $currency_symbol.$user->totalDepositBonus(),
                'portfolio_achieved' => $user->portfolioAchieved(),
                'total_tickets' => $user->ticket->count(),
                'points' => $user->points,
            ],
        ]);
    }

    public function markNotification()
    {
        auth()->user()->notifications()->update(['read' => true]);

        return response()->json([
            'status' => true,
            'message' => __('All Notifications marked as read'),
        ]);
    }

    public function exportTransactions($transactions)
    {
        return (new TransactionExport($transactions))->download('transactions.csv', \Maatwebsite\Excel\Excel::CSV);
    }
}
