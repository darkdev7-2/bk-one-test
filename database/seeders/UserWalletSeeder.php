<?php

namespace Database\Seeders;

use App\Models\Currency;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserWalletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $currency = Currency::all();

        foreach ($users as $user) {
            foreach ($currency->random(3) as $item) {
                $user->wallets()->create([
                    'currency_id' => $item->id,
                    'balance' => 0,
                ]);
            }
        }
    }
}
