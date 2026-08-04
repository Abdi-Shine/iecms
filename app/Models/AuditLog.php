<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $guarded = [];
    const UPDATED_AT = null;

    private const REDACTED_KEYS = ['password', 'remember_token', 'declaration_signature'];

    protected function casts(): array
    {
        return [
            'changes' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function auditable()
    {
        return $this->morphTo();
    }

    public static function record(string $action, Model $model, array $changes = []): void
    {
        $user = auth()->user();

        static::create([
            'user_id'         => $user?->id,
            'institution_id'  => $user?->institution_id,
            'action'          => $action,
            'auditable_type'  => get_class($model),
            'auditable_id'    => $model->getKey(),
            'description'     => class_basename($model) . ' ' . $action,
            'changes'         => $changes ? static::redact($changes) : null,
            'ip_address'      => request()->ip(),
        ]);
    }

    /**
     * $user is deliberately not defaulted from auth()->user() here — a
     * login_failed event must never be attributed to whatever unrelated
     * session happens to be authenticated elsewhere, so callers must
     * pass the actor explicitly (or null, e.g. for failed attempts).
     */
    public static function recordEvent(string $action, string $description, ?User $user): void
    {
        static::create([
            'user_id'        => $user?->id,
            'institution_id' => $user?->institution_id,
            'action'         => $action,
            'description'    => $description,
            'ip_address'     => request()->ip(),
        ]);
    }

    private static function redact(array $data): array
    {
        foreach (self::REDACTED_KEYS as $key) {
            if (array_key_exists($key, $data)) {
                $data[$key] = '[REDACTED]';
            }
        }

        return $data;
    }
}
