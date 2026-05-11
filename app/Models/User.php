<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasRoles, HasApiTokens, BelongsToCompany;

    protected $fillable = [
        'name',
        'email',
        'password',
        'company_id',
        'branch_id',
        'phone',
        'avatar',
        'photo',
        'designation',
        'is_active',
        'user_status',
        'last_login_at',
        'last_login_ip',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at'     => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    public function setPhoneAttribute($value): void
    {
        $this->attributes['designation'] = $value;
    }

    public function getPhoneAttribute(): ?string
    {
        return $this->attributes['designation'] ?? null;
    }

    public function setAvatarAttribute($value): void
    {
        $this->attributes['photo'] = $value;
    }

    public function getAvatarAttribute(): ?string
    {
        return $this->attributes['photo'] ?? null;
    }

    public function setIsActiveAttribute($value): void
    {
        $isActive = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        $isActive = $isActive ?? (bool) $value;

        $this->attributes['is_active'] = $isActive;
        $this->attributes['user_status'] = $isActive ? 'active' : 'inactive';
    }

    public function getIsActiveAttribute($value): bool
    {
        if ($value !== null) {
            return (bool) $value;
        }

        return ($this->attributes['user_status'] ?? null) === 'active';
    }

    public function setUserStatusAttribute($value): void
    {
        $status = $value === 'active' ? 'active' : 'inactive';

        $this->attributes['user_status'] = $status;
        $this->attributes['is_active'] = $status === 'active';
    }

    // ── Relationships ──────────────────────────────────────────

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function managedUserMappings(): HasMany
    {
        return $this->hasMany(UserMapping::class, 'manager_id');
    }

    public function managerMappings(): HasMany
    {
        return $this->hasMany(UserMapping::class, 'user_id');
    }

    public function managedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_mappings', 'manager_id', 'user_id')
            ->withTimestamps();
    }

    public function mappedManagers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_mappings', 'user_id', 'manager_id')
            ->withTimestamps();
    }

    // ── Convenience Helpers (wrap Spatie) ──────────────────────

    /**
     * Get the user's primary role name.
     */
    public function getRoleNameAttribute(): string
    {
        return $this->roles->first()?->name ?? 'No Role';
    }

    /**
     * Get the user's primary role display name.
     */
    public function getRoleDisplayNameAttribute(): string
    {
        $role = $this->roles->first();
        return $role ? ($role->display_name ?? ucfirst(str_replace('_', ' ', $role->name))) : 'No Role';
    }

    /**
     * Get the user's branch name.
     */
    public function getBranchNameAttribute(): string
    {
        return $this->branch?->name ?? 'No Branch';
    }

    /**
     * Check if user is super admin (bypasses permission checks).
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasSystemRole();
    }

    public function isSystemAdmin(): bool
    {
        if ($this->company_id !== null) {
            return false;
        }

        return $this->hasSystemRole();
    }

    public function isCompanyAdmin(): bool
    {
        if ($this->company_id === null) {
            return false;
        }

        return $this->hasExactRoleName(Role::tenantRoleName('company_admin', $this->company_id));
    }

    private function hasExactRoleName(string $roleName): bool
    {
        if ($this->relationLoaded('roles')) {
            return $this->roles->contains('name', $roleName);
        }

        return DB::table('model_has_roles')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('model_has_roles.model_type', self::class)
            ->where('model_has_roles.model_id', $this->getKey())
            ->where('roles.name', $roleName)
            ->exists();
    }

    public function hasCrmPermission(string $permission): bool
    {
        $allPermissions = $this->getAllPermissions();

        if ($allPermissions->contains('name', $permission)) {
            return true;
        }

        if ($this->company_id === null) {
            return false;
        }

        return $allPermissions->contains('name', Permission::tenantPermissionKey($permission, $this->company_id));
    }

    private function hasSystemRole(): bool
    {
        if ($this->relationLoaded('roles')) {
            return $this->roles->contains(fn ($role) => $this->isSystemRoleName($role->name));
        }

        return DB::table('model_has_roles')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('model_has_roles.model_type', self::class)
            ->where('model_has_roles.model_id', $this->getKey())
            ->get(['roles.name'])
            ->contains(fn ($role) => $this->isSystemRoleName($role->name));
    }

    private function isSystemRoleName(string $roleName): bool
    {
        return in_array(Str::of($roleName)->lower()->replace(' ', '_')->value(), ['super_admin', 'admin'], true);
    }
}
