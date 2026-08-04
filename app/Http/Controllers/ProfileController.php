<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', ['user' => $request->user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email', 'max:255', 'unique:users,email,' . $request->user()->id],
            'phone'                 => ['nullable', 'string', 'max:30'],
            'position'              => ['nullable', 'string', 'max:100'],
            'sex'                   => ['nullable', 'in:Male,Female,Other'],
            'address'               => ['nullable', 'string', 'max:500'],
            'avatar'                => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'password'              => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user = $request->user();
        $user->name     = $request->input('name');
        $user->email    = $request->input('email');
        $user->phone    = $request->input('phone');
        $user->position = $request->input('position');
        $user->sex      = $request->input('sex');
        $user->address  = $request->input('address');

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->avatar = $request->file('avatar')->store('avatars', 'public');
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function updateSystemPreferences(Request $request): RedirectResponse
    {
        $request->validate([
            'timezone'       => ['required', 'string', 'timezone:all'],
            'date_format'    => ['required', 'string', 'in:d/m/Y,m/d/Y,Y-m-d'],
            'items_per_page' => ['required', 'integer', 'in:10,20,25,50,100'],
            'language'       => ['required', 'string', 'in:en,so'],
        ]);

        $user = $request->user();
        $user->timezone       = $request->input('timezone');
        $user->date_format    = $request->input('date_format');
        $user->items_per_page = $request->input('items_per_page');
        $user->language       = $request->input('language');
        $user->save();

        return Redirect::route('profile.edit')->with('status', 'system-preferences-updated');
    }

    public function updateAppearance(Request $request): RedirectResponse
    {
        $request->validate([
            'theme'            => ['required', 'string', 'in:light,dark,system,blue'],
            'font_size'        => ['required', 'string', 'in:sm,md,lg'],
            'collapse_sidebar' => ['nullable', 'boolean'],
        ]);

        $user = $request->user();
        $user->theme            = $request->input('theme');
        $user->font_size        = $request->input('font_size');
        $user->collapse_sidebar = $request->boolean('collapse_sidebar');
        $user->save();

        return Redirect::route('profile.edit')->with('status', 'appearance-updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        Auth::logout();
        $user->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
