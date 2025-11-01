<?php

namespace App\Http\Controllers\Api\Auth;

use App\Events\UserReferred;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;

class EmailVerificatinController extends Controller
{
    public function sendVerifyEmail(Request $request)
    {
        if (! setting('email_verification', 'permission')) {
            return response()->json([
                'status' => false,
                'message' => 'Email verification is disabled',
            ], 404);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json([
            'status' => true,
            'message' => 'Email verification mail sent successfully',
        ]);
    }

    public function verify(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json([
                'status' => true,
                'message' => 'Email already verified',
            ]);
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));

            // referral code
            event(new UserReferred($request->cookie('invite'), $request->user()));
        }

        return response()->json([
            'status' => true,
            'message' => 'Email verified successfully',
        ]);
    }
}
