<?php

namespace App\Providers;

use App\Models\AuditLog;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(Login::class, function (Login $event) {
            AuditLog::recordEvent('login', 'User logged in', $event->user);
        });

        Event::listen(Logout::class, function (Logout $event) {
            if ($event->user) {
                AuditLog::recordEvent('logout', 'User logged out', $event->user);
            }
        });

        Event::listen(Failed::class, function (Failed $event) {
            $email = $event->credentials['email'] ?? 'unknown';
            AuditLog::recordEvent('login_failed', "Failed login attempt for {$email}", null);
        });
    }
}
