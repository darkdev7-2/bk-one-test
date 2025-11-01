<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DirectReferralsResource;
use App\Http\Resources\ReferralTreeResource;
use App\Models\Setting;

class ReferralController extends Controller
{
    public function index()
    {
        $currencySymbol = setting('currency_symbol', 'global');

        return response()->json([
            'status' => true,
            'data' => [
                'text' => __('Earn :amount after inviting one members', ['amount' => $currencySymbol.setting('referral_bonus', 'fee')]),
                'link' => url('/register?invite='.auth()->user()->getReferrals()->first()->code),
                'joined_text' => __(':people_count peoples are joined by using this URL', ['people_count' => auth()->user()->getReferrals()->first()->relationships()->count()]),
                'is_shown_referral_rules' => (bool) setting('referral_rules_visibility'),
                'rules' => json_decode(Setting::where('name', 'referral_rules')->first()?->val),
            ],
        ]);
    }

    public function directReferrals()
    {
        $users = auth()->user()->referrals()->get();
        $users = DirectReferralsResource::collection($users);

        return response()->json([
            'status' => true,
            'data' => $users,
        ]);
    }

    public function referralTree()
    {
        $user = auth()->user();
        $users = $user->load('referralTree');

        return response()->json([
            'status' => true,
            'data' => new ReferralTreeResource($users),
        ]);
    }
}
