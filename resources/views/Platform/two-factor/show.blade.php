@extends('admin.admin_master')
@section('page_title', 'Two-Factor Authentication')
@section('admin_main_content')

<div class="p-4 sm:p-6 w-full" style="max-width:640px">

    <div class="mb-6 mt-2">
        <h1 class="text-2xl font-bold text-neutral-800 tracking-tight">Two-Factor Authentication</h1>
        <p class="text-sm text-neutral-500 mt-0.5">Add an extra layer of security to your account</p>
    </div>

    @if(session('success'))
        <div class="mb-4 flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-white" style="background:#10b981">
            <i class="bi bi-check-circle-fill"></i> <span>{{ session('success') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 px-4 py-3 rounded-xl text-sm font-medium text-white" style="background:#ef4444">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    @if(session('recoveryCodes'))
        <div class="bg-white rounded-2xl shadow-sm border-2 border-amber-300 p-6 mb-6">
            <h2 class="text-sm font-bold text-amber-700 uppercase tracking-wider mb-2">
                <i class="bi bi-exclamation-triangle-fill"></i> Save your recovery codes
            </h2>
            <p class="text-sm text-neutral-500 mb-4">
                Store these somewhere safe. Each can be used once to sign in if you lose access to your authenticator app. They will not be shown again.
            </p>
            <div class="grid grid-cols-2 gap-2 font-mono text-sm bg-neutral-50 rounded-xl p-4">
                @foreach(session('recoveryCodes') as $code)
                    <div>{{ $code }}</div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-neutral-100 p-6">

        @if($user->hasTwoFactorEnabled())
            <div class="flex items-center gap-3 mb-4">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-semibold" style="background:rgba(16,185,129,0.1);color:#059669">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span> Enabled
                </span>
            </div>
            <p class="text-sm text-neutral-500 mb-4">
                Two-factor authentication is active on your account. Enabled since {{ $user->two_factor_confirmed_at->format('Y-m-d H:i') }}.
            </p>

            <form action="{{ route('two-factor.destroy') }}" method="POST" onsubmit="return confirm('Disable two-factor authentication? This makes your account less secure.')">
                @csrf @method('DELETE')
                <label class="block text-xs font-bold text-neutral-600 uppercase tracking-wider mb-1">Confirm your password to disable</label>
                <input type="password" name="password" required
                       style="width:100%;padding:.65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;margin-bottom:1rem">
                <button type="submit" style="padding:.6rem 1.5rem;font-size:.85rem;font-weight:700;color:white;background:#ef4444;border:none;border-radius:.625rem;cursor:pointer">
                    Disable Two-Factor Authentication
                </button>
            </form>
        @else
            <p class="text-sm text-neutral-500 mb-4">
                Scan this QR code with an authenticator app (Google Authenticator, Authy, etc.), then enter the 6-digit code it generates to confirm.
            </p>

            <div class="flex justify-center mb-4" style="max-width:220px;margin-left:auto;margin-right:auto">
                {!! $qrCodeSvg !!}
            </div>

            <p class="text-xs text-neutral-400 text-center mb-4">
                Can't scan? Enter this code manually: <code class="font-mono">{{ $secret }}</code>
            </p>

            <form action="{{ route('two-factor.confirm') }}" method="POST">
                @csrf
                <label class="block text-xs font-bold text-neutral-600 uppercase tracking-wider mb-1">Confirmation code</label>
                <input type="text" name="code" required inputmode="numeric" autocomplete="one-time-code" placeholder="123456"
                       style="width:100%;padding:.65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;margin-bottom:1rem">
                <button type="submit" style="padding:.6rem 1.75rem;font-size:.85rem;font-weight:700;color:white;background:#528CBE;border:none;border-radius:.625rem;cursor:pointer">
                    Enable Two-Factor Authentication
                </button>
            </form>
        @endif
    </div>

</div>
@endsection
