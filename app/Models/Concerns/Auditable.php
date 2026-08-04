<?php

namespace App\Models\Concerns;

use App\Models\AuditLog;

/**
 * Writes a row to audit_logs for every create/update/delete on the
 * using model. Kept separate from per-feature activity tables
 * (attorney_case_activities, etc.) — this is a lower-level "who
 * changed what, when" trail for security/compliance review, not
 * business-facing activity history.
 */
trait Auditable
{
    protected static function bootAuditable(): void
    {
        static::created(function ($model) {
            AuditLog::record('created', $model);
        });

        static::updated(function ($model) {
            $changes = $model->getChanges();
            unset($changes['updated_at']);

            if ($changes) {
                AuditLog::record('updated', $model, $changes);
            }
        });

        static::deleted(function ($model) {
            AuditLog::record('deleted', $model, $model->getAttributes());
        });
    }
}
