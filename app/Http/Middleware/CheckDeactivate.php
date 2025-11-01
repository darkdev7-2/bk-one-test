<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class CheckDeactivate
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response|RedirectResponse)  $next
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->status == 0) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            $message = __('Your account is disabled, please contact our support at :contact_email', [
                'contact_email' => setting('support_email', 'global'),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => $message,
                ], 401);
            }

            return redirect()->route('login')->withErrors(['msg' => $message]);
        } elseif (auth()->check() && auth()->user()->status == 2) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            $message = __('Your account is closed, please contact our support at :contact_email', [
                'contact_email' => setting('support_email', 'global'),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => $message,
                ], 401);
            }

            return redirect()->route('login')->withErrors(['msg' => $message]);
        }

        return $next($request);
    }
}
