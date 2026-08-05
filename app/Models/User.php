<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Concerns\Auditable;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property string|null $avatar
 * @property string|null $name
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $position
 * @property string|null $sex
 * @property string|null $address
 * @property string|null $timezone
 * @property string|null $date_format
 * @property int|null $items_per_page
 * @property string|null $language
 * @property string|null $theme
 * @property string|null $font_size
 * @property bool|null $collapse_sidebar
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'phone',
        'position',
        'sex',
        'address',
        'group_id',
        'institution_id',
        'timezone',
        'date_format',
        'items_per_page',
        'language',
        'theme',
        'font_size',
        'collapse_sidebar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'collapse_sidebar' => 'boolean',
            'is_super_admin' => 'boolean',
            'two_factor_secret' => 'encrypted',
            'two_factor_recovery_codes' => 'encrypted:array',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    private ?array $permissionCache = null;

    private function loadPermissions(): array
    {
        if ($this->permissionCache !== null) return $this->permissionCache;
        if (!$this->group_id) return $this->permissionCache = [];

        $group = $this->group()->with('roles.permissions')->first();
        if (!$group) return $this->permissionCache = [];

        return $this->permissionCache = $group->roles
            ->flatMap(fn($r) => $r->permissions)
            ->map(fn($p) => strtolower($p->module) . '|' . $p->action)
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Check if the user has a given permission via their group's roles.
     * Super admins bypass this entirely and have full access.
     */
    public function hasPermission(string $module, string $action = 'view'): bool
    {
        if ($this->is_super_admin) return true;
        return in_array(strtolower($module) . '|' . $action, $this->loadPermissions());
    }

    public function getRoleAttribute($value): string
    {
        return $value ?? 'Administrator';
    }

    /**
     * Get the staff profile (employee) associated with this user.
     */
    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function employee()
    {
        return $this->hasOne(Employee::class, 'system_username', 'email');
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function isInstitutionAdmin(): bool
    {
        return $this->group?->roles->contains('name', 'Institution Admin') ?? false;
    }

    /**
     * MFA is required for Super Admin and Institution Admin accounts
     * only, to limit friction for regular staff.
     */
    public function requiresTwoFactor(): bool
    {
        return $this->is_super_admin || $this->isInstitutionAdmin();
    }

    public function hasTwoFactorEnabled(): bool
    {
        return $this->two_factor_confirmed_at !== null;
    }

    /**
     * Where a successful login (or completed 2FA challenge) should land.
     */
    public function dashboardRoute(): string
    {
        if ($this->is_super_admin) {
            return 'platform.dashboard';
        }

        return match ($this->institution?->type) {
            'ago' => 'attorney-dashboard.index',
            'cid' => 'cid-dashboard.index',
            default => 'dashboard',
        };
    }
}
