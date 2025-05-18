<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
{
    $credentials = $request->only('email', 'password');


    if (Auth::attempt($credentials)) {
        $request->session()->regenerate(); 

        $user = Auth::user(); 


        if ($user && $user->two_factor_secret) {
         
            Auth::logout();
            session()->invalidate();
            session()->regenerateToken();

            session(['login.id' => $user->id]);

            Log::info('User passed credentials but requires 2FA', [
                'user_id' => $user->id,
                'ip' => $request->ip(),
            ]);

            return redirect()->route('two-factor.login');
        }

        Log::info('User logged in', [
            'user_id' => $user->id,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->intended(route('dashboard'));
    }

    Log::warning('Failed login attempt', [
        'email' => Str::limit($request->input('email'), 5, '...'),
        'ip' => $request->ip(),
        'user_agent' => $request->userAgent(),
    ]);

    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ]);
}


    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->intended('/');
    }
}
