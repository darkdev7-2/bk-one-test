<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TwoFactorController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:6',
        ]);

        $user = $request->user();
        $google2fa = app('pragmarx.google2fa');

        if (! $user->two_fa) {
            return response()->json([
                'status' => false,
                'message' => '2FA is not enabled',
            ], 422);
        }

        // Check code is valid
        if (! $google2fa->verifyKey($user->google2fa_secret, $request->code)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid code',
                'errors' => [
                    'code' => [
                        'Invalid code',
                    ],
                ],
            ], 422);
        }

        return response()->json([
            'status' => true,
            'message' => 'Two factor verified successfully',
        ]);
    }
}
