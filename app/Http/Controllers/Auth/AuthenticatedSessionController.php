<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        if ($user->requiresTwoFactor() && $user->hasTwoFactorEnabled()) {
            $intended = redirect()->intended(route($user->dashboardRoute(), absolute: false))->getTargetUrl();

            Auth::logout();
            $request->session()->put('2fa:user:id', $user->id);
            $request->session()->put('2fa:intended', $intended);
            $request->session()->regenerate();

            return redirect()->route('two-factor.challenge');
        }

        return redirect()->intended(route($user->dashboardRoute(), absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
