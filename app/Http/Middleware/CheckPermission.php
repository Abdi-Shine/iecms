<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $module, string $action = 'view'): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Supports "ModuleA|ModuleB" to grant access if the user has the
        // permission under any one of the listed modules.
        $modules = explode('|', $module);
        $allowed = false;
        foreach ($modules as $m) {
            if (auth()->user()->hasPermission(trim($m), $action)) {
                $allowed = true;
                break;
            }
        }

        if (!$allowed) {
            abort(403);
        }

        return $next($request);
    }
}
