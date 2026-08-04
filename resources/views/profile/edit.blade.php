@extends('admin.admin_master')
@section('page_title', 'My Profile')
@section('admin_main_content')

@php
    $initialTab = 'profile';
    if ($errors->has('password') || $errors->has('password_confirmation')) {
        $initialTab = 'security';
    } elseif ($errors->hasAny(['timezone', 'date_format', 'items_per_page', 'language'])) {
        $initialTab = 'system';
    } elseif ($errors->hasAny(['theme', 'font_size', 'collapse_sidebar'])) {
        $initialTab = 'appearance';
    }
@endphp

<div class="p-4 sm:p-6 w-full" x-data="{ tab: '{{ $initialTab }}' }">

    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Edit Profile</h1>
            <p class="text-sm text-gray-500 mt-1">Update your personal information for the district court system.</p>
        </div>
        <a href="{{ route('dashboard') }}"
            class="flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-medium transition-all">
            <i class="bi bi-arrow-left"></i> Back to Profile
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-5">

        <!-- Tab Nav -->
        <div class="flex items-center gap-1 px-4 pt-3 border-b border-gray-100 overflow-x-auto">
            <button type="button" @click="tab = 'profile'"
                class="flex items-center gap-2 px-4 py-3 text-sm font-semibold border-b-2 transition-all whitespace-nowrap"
                :class="tab === 'profile' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700'">
                <i class="bi bi-person"></i> Profile
            </button>
            <button type="button" @click="tab = 'security'"
                class="flex items-center gap-2 px-4 py-3 text-sm font-semibold border-b-2 transition-all whitespace-nowrap"
                :class="tab === 'security' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700'">
                <i class="bi bi-lock"></i> Security
            </button>
            <button type="button" @click="tab = 'appearance'"
                class="flex items-center gap-2 px-4 py-3 text-sm font-semibold border-b-2 transition-all whitespace-nowrap"
                :class="tab === 'appearance' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700'">
                <i class="bi bi-palette"></i> Appearance
            </button>
            <button type="button" @click="tab = 'system'"
                class="flex items-center gap-2 px-4 py-3 text-sm font-semibold border-b-2 transition-all whitespace-nowrap"
                :class="tab === 'system' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700'">
                <i class="bi bi-sliders"></i> System
            </button>
        </div>

        <form id="profile-form" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf

            <!-- Profile Tab -->
            <div x-show="tab === 'profile'" x-cloak>

                <!-- Avatar Section -->
                <div class="bg-primary px-6 py-8 flex flex-col sm:flex-row items-center gap-6">
                    <div class="relative flex-shrink-0">
                        <div id="avatar-preview"
                            class="w-24 h-24 rounded-full overflow-hidden bg-accent flex items-center justify-center shadow-lg border-4 border-white/30">
                            @if($user->avatar)
                                <img src="{{ Storage::url($user->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                            @else
                                <span class="text-primary text-3xl font-bold">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}{{ strtoupper(substr(strstr($user->name, ' '), 1, 1) ?: substr($user->name, 1, 1)) }}
                                </span>
                            @endif
                        </div>
                        <label for="avatar-input"
                            class="absolute bottom-0 right-0 w-7 h-7 bg-accent rounded-full flex items-center justify-center cursor-pointer shadow border-2 border-white hover:bg-accent/80 transition-all">
                            <i class="bi bi-camera text-primary text-xs"></i>
                        </label>
                        <input id="avatar-input" type="file" name="avatar" accept="image/*" class="hidden" onchange="previewAvatar(this)">
                    </div>
                    <div>
                        <h3 class="text-white font-bold text-lg">{{ $user->name }}</h3>
                        <p class="text-white/60 text-sm">{{ $user->position ?? 'Administrator' }}</p>
                        <p class="text-white/40 text-xs mt-1">Click the camera icon to change photo</p>
                    </div>
                </div>

                <!-- Personal Information -->
                <div class="px-6 py-6">
                    <h3 class="flex items-center gap-2 text-base font-semibold text-primary mb-5">
                        <i class="bi bi-person-circle text-lg"></i> Personal Information
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Full Name -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Full Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-700 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all @error('name') border-red-400 @enderror">
                            @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Email Address <span class="text-red-500">*</span></label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-700 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all @error('email') border-red-400 @enderror">
                            @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <!-- Sex -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Sex</label>
                            <select name="sex"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-700 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all bg-white">
                                <option value="">-- Select --</option>
                                <option value="Male"   {{ old('sex', $user->sex) === 'Male'   ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('sex', $user->sex) === 'Female' ? 'selected' : '' }}>Female</option>
                                <option value="Other"  {{ old('sex', $user->sex) === 'Other'  ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>

                        <!-- Phone -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Phone Number</label>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" inputmode="numeric"
                                oninput="this.value=this.value.replace(/\D/g,'')"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-700 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all">
                        </div>

                        <!-- Address -->
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Address</label>
                            <textarea name="address" rows="3"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-700 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all resize-none">{{ old('address', $user->address) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Security Tab -->
            <div x-show="tab === 'security'" x-cloak class="px-6 py-6">
                <h3 class="flex items-center gap-2 text-base font-semibold text-primary mb-1">
                    <i class="bi bi-shield-lock text-lg"></i> Security
                </h3>
                <p class="text-xs text-gray-400 mb-5">Leave the password fields blank to keep your current password.</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">New Password</label>
                        <input type="password" name="password"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-700 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all @error('password') border-red-400 @enderror">
                        @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Confirm New Password</label>
                        <input type="password" name="password_confirmation"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-700 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all">
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div x-show="tab === 'profile' || tab === 'security'" x-cloak class="border-t border-gray-100 px-6 py-5 flex items-center justify-end gap-3">
                <a href="{{ route('dashboard') }}"
                    class="px-6 py-2.5 border border-gray-200 text-gray-600 rounded-xl text-sm font-medium hover:bg-gray-50 transition-all">
                    Cancel
                </a>
                <button type="button" onclick="confirmSave()"
                    class="flex items-center gap-2 px-6 py-2.5 bg-primary text-white rounded-xl text-sm font-bold hover:bg-primary/90 transition-all shadow">
                    <i class="bi bi-floppy"></i> Save Changes
                </button>
            </div>
        </form>

        <!-- Appearance Tab -->
        <form id="appearance-form" method="POST" action="{{ route('profile.appearance.update') }}"
            x-show="tab === 'appearance'" x-cloak>
            @csrf

            <div class="px-6 py-6">
                <h3 class="flex items-center gap-2 text-base font-semibold text-primary mb-5">
                    <i class="bi bi-palette text-lg"></i> Theme
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <label class="relative block cursor-pointer">
                        <input type="radio" name="theme" value="light" class="peer sr-only" onchange="previewTheme(this.value)"
                            {{ old('theme', $user->theme ?? 'light') === 'light' ? 'checked' : '' }}>
                        <div class="rounded-xl border-2 border-gray-200 peer-checked:border-primary p-4 transition-all">
                            <div class="h-20 rounded-lg bg-gradient-to-br from-gray-100 to-gray-200 mb-3"></div>
                            <p class="text-sm font-bold text-gray-800 text-center">Light Theme</p>
                            <p class="text-xs text-gray-400 text-center">Classic bright interface</p>
                        </div>
                    </label>

                    <label class="relative block cursor-pointer">
                        <input type="radio" name="theme" value="dark" class="peer sr-only" onchange="previewTheme(this.value)"
                            {{ old('theme', $user->theme ?? 'light') === 'dark' ? 'checked' : '' }}>
                        <div class="rounded-xl border-2 border-gray-200 peer-checked:border-primary p-4 transition-all">
                            <div class="h-20 rounded-lg bg-gradient-to-br from-slate-800 to-slate-900 mb-3"></div>
                            <p class="text-sm font-bold text-gray-800 text-center">Dark Theme</p>
                            <p class="text-xs text-gray-400 text-center">Easy on the eyes</p>
                        </div>
                    </label>

                    <label class="relative block cursor-pointer">
                        <input type="radio" name="theme" value="system" class="peer sr-only" onchange="previewTheme(this.value)"
                            {{ old('theme', $user->theme ?? 'light') === 'system' ? 'checked' : '' }}>
                        <div class="rounded-xl border-2 border-gray-200 peer-checked:border-primary p-4 transition-all">
                            <div class="h-20 rounded-lg mb-3" style="background:linear-gradient(90deg, #E5E7EB 50%, #1E293B 50%)"></div>
                            <p class="text-sm font-bold text-gray-800 text-center">System Theme</p>
                            <p class="text-xs text-gray-400 text-center">Follows OS preference</p>
                        </div>
                    </label>

                    <label class="relative block cursor-pointer">
                        <input type="radio" name="theme" value="blue" class="peer sr-only" onchange="previewTheme(this.value)"
                            {{ old('theme', $user->theme ?? 'light') === 'blue' ? 'checked' : '' }}>
                        <div class="rounded-xl border-2 border-gray-200 peer-checked:border-primary p-4 transition-all">
                            <div class="h-20 rounded-lg bg-gradient-to-br from-ago-600 to-ago-800 mb-3"></div>
                            <p class="text-sm font-bold text-gray-800 text-center">Blue Theme</p>
                            <p class="text-xs text-gray-400 text-center">Government & enterprise style</p>
                        </div>
                    </label>
                </div>
                @error('theme')<p class="text-red-500 text-xs mb-6 -mt-4">{{ $message }}</p>@enderror

                <div class="grid grid-cols-1 gap-4 max-w-md mb-6">
                    <!-- Font Size -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Font Size</label>
                        <select name="font_size"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-700 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all bg-white @error('font_size') border-red-400 @enderror">
                            @php $fontSizeOptions = ['sm' => 'Small', 'md' => 'Medium', 'lg' => 'Large']; @endphp
                            @foreach($fontSizeOptions as $value => $label)
                                <option value="{{ $value }}" {{ old('font_size', $user->font_size ?? 'md') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('font_size')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <!-- Collapse Sidebar -->
                <div class="flex items-center justify-between max-w-md pt-2">
                    <div>
                        <p class="text-sm font-bold text-gray-800">Collapse Sidebar by Default</p>
                        <p class="text-xs text-gray-400">Start with a collapsed sidebar for more screen space</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer flex-shrink-0 ml-4">
                        <input type="checkbox" name="collapse_sidebar" value="1" class="peer sr-only"
                            {{ old('collapse_sidebar', $user->collapse_sidebar ?? false) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-primary transition-all
                                    after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full
                                    after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-5"></div>
                    </label>
                </div>
            </div>

            <div class="border-t border-gray-100 px-6 py-5 flex items-center justify-end">
                <button type="submit"
                    class="flex items-center gap-2 px-6 py-2.5 bg-primary text-white rounded-xl text-sm font-bold hover:bg-primary/90 transition-all shadow">
                    <i class="bi bi-floppy"></i> Save Appearance
                </button>
            </div>
        </form>

        <!-- System Tab -->
        <form id="system-form" method="POST" action="{{ route('profile.system-preferences.update') }}"
            x-show="tab === 'system'" x-cloak>
            @csrf

            <div class="px-6 py-6">
                <h3 class="flex items-center gap-2 text-base font-semibold text-primary mb-5">
                    <i class="bi bi-sliders text-lg"></i> System Preferences
                </h3>

                <div class="grid grid-cols-1 gap-4 max-w-md">
                    <!-- Timezone -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Timezone</label>
                        <select name="timezone"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-700 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all bg-white @error('timezone') border-red-400 @enderror">
                            @php
                                $timezoneOptions = [
                                    'Africa/Mogadishu' => 'Africa/Mogadishu (EAT)',
                                    'Africa/Nairobi'   => 'Africa/Nairobi (EAT)',
                                    'Africa/Cairo'     => 'Africa/Cairo (EET)',
                                    'Africa/Lagos'     => 'Africa/Lagos (WAT)',
                                    'Europe/London'    => 'Europe/London (GMT/BST)',
                                    'UTC'              => 'UTC',
                                ];
                            @endphp
                            @foreach($timezoneOptions as $value => $label)
                                <option value="{{ $value }}" {{ old('timezone', $user->timezone) === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('timezone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- Date Format -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Date Format</label>
                        <select name="date_format"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-700 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all bg-white @error('date_format') border-red-400 @enderror">
                            @php
                                $dateFormatOptions = [
                                    'd/m/Y' => 'DD/MM/YYYY (' . now()->format('d/m/Y') . ')',
                                    'm/d/Y' => 'MM/DD/YYYY (' . now()->format('m/d/Y') . ')',
                                    'Y-m-d' => 'YYYY-MM-DD (' . now()->format('Y-m-d') . ')',
                                ];
                            @endphp
                            @foreach($dateFormatOptions as $value => $label)
                                <option value="{{ $value }}" {{ old('date_format', $user->date_format) === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('date_format')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- Items Per Page -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Items Per Page</label>
                        <select name="items_per_page"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-700 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all bg-white @error('items_per_page') border-red-400 @enderror">
                            @foreach([10, 20, 25, 50, 100] as $n)
                                <option value="{{ $n }}" {{ (int) old('items_per_page', $user->items_per_page) === $n ? 'selected' : '' }}>{{ $n }}</option>
                            @endforeach
                        </select>
                        @error('items_per_page')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- Language -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Language</label>
                        <select name="language"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-700 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all bg-white @error('language') border-red-400 @enderror">
                            @php $languageOptions = ['en' => 'English', 'so' => 'Somali']; @endphp
                            @foreach($languageOptions as $value => $label)
                                <option value="{{ $value }}" {{ old('language', $user->language) === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('language')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-100 px-6 py-5 flex items-center justify-end">
                <button type="submit"
                    class="flex items-center gap-2 px-6 py-2.5 bg-primary text-white rounded-xl text-sm font-bold hover:bg-primary/90 transition-all shadow">
                    <i class="bi bi-floppy"></i> Save System Preferences
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function previewTheme(theme) {
        var html = document.documentElement;
        html.setAttribute('data-theme', theme);
        var resolved = theme === 'dark' ? 'dark'
            : theme === 'system' && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark'
            : 'light';
        html.setAttribute('data-resolved-theme', resolved);
    }

    function previewAvatar(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('avatar-preview').innerHTML =
                    '<img src="' + e.target.result + '" class="w-full h-full object-cover">';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function confirmSave() {
        Swal.fire({
            title: 'Save Changes?',
            text: 'Are you sure you want to update your profile?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#528CBE',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Yes, Save',
            cancelButtonText: 'Cancel',
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('profile-form').submit();
            }
        });
    }

    @if(session('status') === 'profile-updated')
    Swal.fire({
        title: 'Profile Updated!',
        text: 'Your profile has been updated successfully.',
        icon: 'success',
        confirmButtonColor: '#528CBE',
        confirmButtonText: 'OK',
        timer: 3000,
        timerProgressBar: true,
    });
    @endif

    @if(session('status') === 'system-preferences-updated')
    Swal.fire({
        title: 'Preferences Saved!',
        text: 'Your system preferences have been updated.',
        icon: 'success',
        confirmButtonColor: '#528CBE',
        confirmButtonText: 'OK',
        timer: 3000,
        timerProgressBar: true,
    });
    @endif

    @if(session('status') === 'appearance-updated')
    Swal.fire({
        title: 'Appearance Saved!',
        text: 'Your appearance preferences have been updated.',
        icon: 'success',
        confirmButtonColor: '#528CBE',
        confirmButtonText: 'OK',
        timer: 3000,
        timerProgressBar: true,
    });
    @endif
</script>

@endsection
