<?php

namespace Database\Seeders;

use App\Models\UserNavigation;
use Illuminate\Database\Seeder;

class UserNavigationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserNavigation::truncate();

        $navigations = [
            [
                'type' => 'dashboard',
                'icon' => 'inbox',
                'url' => 'user/dashboard',
                'name' => 'Dashboard',
                'position' => 1,
            ],
            [
                'type' => 'wallets',
                'icon' => 'wallet',
                'url' => 'user/wallets',
                'name' => 'Wallets',
                'position' => 2,
            ],
            [
                'type' => 'cards',
                'icon' => 'credit-card',
                'url' => 'user/cards',
                'name' => 'Virtual Cards',
                'position' => 3,
            ],
            [
                'type' => 'deposit',
                'icon' => 'plus-circle',
                'url' => 'user/deposit',
                'name' => 'Deposit',
                'position' => 4,
            ],
            [
                'type' => 'fund_transfer',
                'icon' => 'send',
                'url' => 'user/fund-transfer',
                'name' => 'Fund Transfer',
                'position' => 5,
            ],
            [
                'type' => 'dps',
                'icon' => 'archive',
                'url' => 'user/dps',
                'name' => 'DPS',
                'position' => 6,
            ],
            [
                'type' => 'fdr',
                'icon' => 'book',
                'url' => 'user/fdr',
                'name' => 'FDR',
                'position' => 7,
            ],
            [
                'type' => 'loan',
                'icon' => 'alert-triangle',
                'url' => 'user/loan',
                'name' => 'Loan',
                'position' => 8,
            ],
            [
                'type' => 'pay_bill',
                'icon' => 'credit-card',
                'url' => 'user/pay-bill/airtime',
                'name' => 'Pay Bill',
                'position' => 9,
            ],
            [
                'type' => 'transactions',
                'icon' => 'alert-circle',
                'url' => 'user/transactions',
                'name' => 'Transactions',
                'position' => 10,
            ],
            [
                'type' => 'withdraw',
                'icon' => 'box',
                'url' => 'user/withdraw',
                'name' => 'Withdraw',
                'position' => 11,
            ],
            [
                'type' => 'referral',
                'icon' => 'users',
                'url' => 'user/referral',
                'name' => 'Referral',
                'position' => 12,
            ],
            [
                'type' => 'portfolio',
                'icon' => 'pie-chart',
                'url' => 'user/portfolio',
                'name' => 'Portfolio',
                'position' => 13,
            ],
            [
                'type' => 'rewards',
                'icon' => 'gift',
                'url' => 'user/rewards',
                'name' => 'Rewards',
                'position' => 14,
            ],
            [
                'type' => 'support',
                'icon' => 'message-circle',
                'url' => 'user/support-ticket/index',
                'name' => 'Support',
                'position' => 15,
            ],
            [
                'type' => 'settings',
                'icon' => 'settings',
                'url' => 'user/settings',
                'name' => 'Settings',
                'position' => 16,
            ],
            [
                'type' => 'logout',
                'icon' => 'log-out',
                'url' => '',
                'name' => 'Logout',
                'position' => 17,
            ],
        ];

        UserNavigation::insert($navigations);
    }
}
