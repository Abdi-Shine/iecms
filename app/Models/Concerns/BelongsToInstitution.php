<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

/**
 * Scopes a root model's queries to the authenticated user's institution.
 * Super admins and non-web contexts (console, queue) bypass the scope
 * entirely — the isolation only applies during an authenticated request.
 */
trait BelongsToInstitution
{
    protected static function bootBelongsToInstitution(): void
    {
        static::addGlobalScope(function (Builder $builder) {
            $user = auth()->user();

            if (!$user || $user->is_super_admin) {
                return;
            }

            $builder->where($builder->getModel()->getTable() . '.institution_id', $user->institution_id);
        });

        static::creating(function ($model) {
            if ($model->institution_id === null && auth()->check()) {
                $model->institution_id = auth()->user()->institution_id;
            }
        });
    }
}
