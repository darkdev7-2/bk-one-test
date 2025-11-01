<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\LoginActivities;
use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $email = $request->get('email');
        $type = ! $this->isEmail($email) ? 'username' : 'email';

        // Get the user by email or username
        $column = $type === 'email' ? 'email' : 'username';
        $user = User::where($column, $email)->first();

        // Check if user exists and password is correct
        if (! $user || ! Hash::check($request->password, $user->password)) {
            RateLimiter::hit($this->throttleKey($request->email));

            throw ValidationException::withMessages([
                $type => trans('auth.failed'),
            ]);
        }

        $this->ensureIsNotRateLimited($type, $request);

        RateLimiter::clear($this->throttleKey($request->email));

        $token = $user->createToken('auth_token')->plainTextToken;

        LoginActivities::add($user->id);

        return response()->json([
            'status' => true,
            'token' => $token,
            'token_type' => 'Bearer',
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()?->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully',
        ]);
    }

    private function isEmail($param)
    {
        return filter_var($param, FILTER_VALIDATE_EMAIL);
    }

    public function throttleKey($email)
    {
        return Str::transliterate(Str::lower($email).'|'.request()->ip());
    }

    public function ensureIsNotRateLimited($type, Request $request)
    {
        $throttleKey = $this->throttleKey($request->email);
        if (! RateLimiter::tooManyAttempts($throttleKey, 5)) {
            return;
        }

        event(new Lockout($request));

        $seconds = RateLimiter::availableIn($throttleKey);

        throw ValidationException::withMessages([
            $type => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }
}
