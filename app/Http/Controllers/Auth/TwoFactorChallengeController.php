<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorChallengeController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        if (!$request->session()->has('2fa:user:id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor-challenge');
    }

    public function store(Request $request): RedirectResponse
    {
        $userId = $request->session()->get('2fa:user:id');

        if (!$userId) {
            return redirect()->route('login');
        }

        $user = User::findOrFail($userId);

        $request->validate([
            'code' => 'nullable|string',
            'recovery_code' => 'nullable|string',
        ]);

        $verified = false;

        if ($request->filled('code')) {
            $verified = (new Google2FA())->verifyKey($user->two_factor_secret, trim($request->code));
        } elseif ($request->filled('recovery_code')) {
            $verified = $this->consumeRecoveryCode($user, trim($request->recovery_code));
        }

        if (!$verified) {
            return back()->withErrors(['code' => 'The provided code is invalid or has expired.']);
        }

        $intended = $request->session()->pull('2fa:intended');
        $request->session()->forget('2fa:user:id');
        $request->session()->regenerate();

        Auth::login($user);
        AuditLog::recordEvent('login', 'User completed two-factor challenge', $user);

        return $intended ? redirect($intended) : redirect()->route($user->dashboardRoute());
    }

    private function consumeRecoveryCode(User $user, string $code): bool
    {
        $codes = $user->two_factor_recovery_codes ?? [];

        foreach ($codes as $index => $hashedCode) {
            if (Hash::check($code, $hashedCode)) {
                unset($codes[$index]);
                $user->forceFill(['two_factor_recovery_codes' => array_values($codes)])->save();
                return true;
            }
        }

        return false;
    }
}
