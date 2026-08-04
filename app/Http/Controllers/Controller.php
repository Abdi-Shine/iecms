<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

abstract class Controller
{
    /**
     * Resolve the pagination page size: an explicit ?per_page= query param wins
     * when it's one of the allowed sizes, otherwise fall back to the signed-in
     * user's saved "Items Per Page" preference, otherwise $default.
     */
    protected function resolvePerPage(Request $request, array $allowed = [10, 25, 50, 100], ?int $default = null): int
    {
        $requested = (int) $request->get('per_page');
        if (in_array($requested, $allowed, true)) {
            return $requested;
        }

        $preferred = (int) ($request->user()?->items_per_page ?? 0);
        if (in_array($preferred, $allowed, true)) {
            return $preferred;
        }

        return $default ?? 10;
    }
}
