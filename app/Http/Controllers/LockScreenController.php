<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class LockScreenController extends Controller
{
    public function lock(Request $request)
    {
        $userId = Auth::id();
        $userName = Auth::user()->name;
        Auth::logout();
        $request->session()->regenerate();
        Session::put('locked', true);
        Session::put('locked_user_id', $userId);
        Session::put('locked_user_name', $userName);
        return redirect()->route('lock-screen.show');
    }

    public function show()
    {
        if (!Session::has('locked')) {
            return redirect()->route('login');
        }
        return view('auth.lock_screen');
    }

    public function unlock(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $userId = Session::get('locked_user_id');
        $user = \App\Models\User::findOrFail($userId);

        if (!Hash::check($request->input('password'), $user->password)) {
            return back()->withErrors(['password' => 'Incorrect password.']);
        }

        Session::forget('locked');
        Session::forget('locked_user_id');
        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
