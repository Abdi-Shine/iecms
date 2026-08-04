<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorSettingsController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user();
        $google2fa = new Google2FA();

        $qrCodeSvg = null;

        if (!$user->hasTwoFactorEnabled()) {
            if (!$user->two_factor_secret) {
                $user->forceFill(['two_factor_secret' => $google2fa->generateSecretKey()])->save();
            }

            $qrCodeUrl = $google2fa->getQRCodeUrl(
                config('app.name'),
                $user->email,
                $user->two_factor_secret,
            );

            $qrCodeSvg = $this->renderQrCodeSvg($qrCodeUrl);
        }

        return view('Platform.two-factor.show', [
            'user' => $user,
            'qrCodeSvg' => $qrCodeSvg,
            'secret' => $user->hasTwoFactorEnabled() ? null : $user->two_factor_secret,
        ]);
    }

    public function confirm(Request $request): RedirectResponse
    {
        $request->validate(['code' => 'required|string']);

        $user = $request->user();
        $google2fa = new Google2FA();

        if (!$google2fa->verifyKey($user->two_factor_secret, trim($request->code))) {
            return back()->withErrors(['code' => 'The provided code is invalid.']);
        }

        $recoveryCodes = collect(range(1, 10))
            ->map(fn () => Str::random(10) . '-' . Str::random(10))
            ->all();

        $user->forceFill([
            'two_factor_confirmed_at' => now(),
            'two_factor_recovery_codes' => array_map(fn ($code) => Hash::make($code), $recoveryCodes),
        ])->save();

        return redirect()->route('two-factor.show')
            ->with('recoveryCodes', $recoveryCodes)
            ->with('success', 'Two-factor authentication enabled. Save your recovery codes now — they will not be shown again.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validate(['password' => 'required|current_password']);

        $request->user()->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        return redirect()->route('two-factor.show')->with('success', 'Two-factor authentication disabled.');
    }

    private function renderQrCodeSvg(string $url): string
    {
        $qrCode = new \Endroid\QrCode\QrCode(data: $url, size: 200);
        $writer = new \Endroid\QrCode\Writer\SvgWriter();

        return $writer->write($qrCode)->getString();
    }
}
