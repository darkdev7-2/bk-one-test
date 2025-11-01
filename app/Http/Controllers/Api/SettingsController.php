<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ImageUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use PragmaRX\Google2FALaravel\Support\Authenticator;

class SettingsController extends Controller
{
    use ImageUpload;

    public function profileUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'username' => 'required|unique:users,username,'.auth()->id(),
            'gender' => 'required',
            'date_of_birth' => 'date',
            'phone' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $user = auth()->user();
        $input = $request->all();

        if ($request->hasFile('avatar')) {
            $input['avatar'] = self::imageUploadTrait($request->avatar, $user->avatar);
        }

        $user->update($input);

        return response()->json([
            'status' => true,
            'message' => __('Profile updated successfully'),
        ]);
    }

    public function twoFa(Request $request, $type)
    {
        $validator = Validator::make($request->all(), [
            'one_time_password' => [
                Rule::requiredIf($type === 'enable'),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $user = auth()->user();
        if ($type == 'enable') {
            session([
                config('google2fa.session_var') => [
                    'auth_passed' => false,
                ],
            ]);

            $authenticator = app(Authenticator::class)->boot($request);
            if ($authenticator->isAuthenticated()) {

                $user->update([
                    'two_fa' => 1,
                ]);

                return response()->json([
                    'status' => true,
                    'message' => __('2FA enabled successfully'),
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => __('One time key is wrong!'),
            ], 422);
        } elseif ($type == 'disable') {

            if (Hash::check(request('one_time_password'), $user->password)) {
                $user->update([
                    'two_fa' => 0,
                ]);

                return response()->json([
                    'status' => true,
                    'message' => __('2FA disabled successfully'),
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => __('Your password is wrong!'),
            ], 422);
        } elseif ($type == 'generate') {
            $google2fa = app('pragmarx.google2fa');
            $secret = $google2fa->generateSecretKey();

            $user->update([
                'google2fa_secret' => $secret,
            ]);

            return response()->json([
                'status' => true,
                'message' => __('QR Code and Secret Key generate successfully'),
                'data' => [
                    'qr_code' => $google2fa->getQRCodeInline(setting('site_title', 'global'), $user->email, $secret),
                    'secret' => $secret,
                ],
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => __('Invalid request'),
        ], 422);
    }

    public function passcode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'passcode' => 'required|integer',
            'passcode_confirmation' => 'required|integer|same:passcode',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $user = auth()->user();
        $user->passcode = bcrypt($request->passcode);
        $user->save();

        return response()->json([
            'status' => true,
            'message' => __('Passcode turned on'),
        ]);
    }

    public function changePasscode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_passcode' => 'required',
            'passcode' => 'required|integer',
            'passcode_confirmation' => 'required|integer|same:passcode',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        if (! Hash::check($request->old_passcode, auth()->user()->passcode)) {
            return response()->json([
                'status' => false,
                'message' => __('Old Passcode is wrong!'),
            ], 422);
        }

        $user = auth()->user();
        $user->passcode = bcrypt($request->passcode);
        $user->save();

        return response()->json([
            'status' => true,
            'message' => __('Passcode changed successfully'),
        ]);
    }

    public function disablePasscode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        if (! Hash::check($request->password, auth()->user()->password)) {
            return response()->json([
                'status' => false,
                'message' => __('Password is wrong!'),
            ], 422);
        }

        $user = auth()->user();
        $user->passcode = null;
        $user->save();

        return response()->json([
            'status' => true,
            'message' => __('Passcode turned off'),
        ]);
    }

    public function accountClose(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $user = auth()->user();
        $user->update([
            'status' => 2,
            'close_reason' => $request->reason,
        ]);

        return response()->json([
            'status' => true,
            'message' => __('Your account is closed successfully'),
        ]);
    }

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => 'required|confirmed',
            'password_confirmation' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        if (! Hash::check($request->current_password, auth()->user()->password)) {
            return response()->json([
                'status' => false,
                'message' => __('Current password is wrong!'),
            ], 422);
        }

        $user = auth()->user();
        $user->password = bcrypt($request->password);
        $user->save();

        return response()->json([
            'status' => true,
            'message' => __('Password changed successfully'),
        ]);
    }

    public function verifyPasscode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'passcode' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        if (! Hash::check($request->passcode, auth()->user()->passcode)) {
            return response()->json([
                'status' => false,
                'message' => __('Passcode is wrong!'),
            ], 422);
        }

        return response()->json([
            'status' => true,
            'message' => __('Passcode verified successfully'),
        ]);
    }
}
