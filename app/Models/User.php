<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasRoles, HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'password',
        'branch_id',
        'phone',
        'avatar',
        'is_active',
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

    // ── Relationships ──────────────────────────────────────────

    public function branch()
    {
        return $this->belongsTo(Branch::class);
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
        return $this->hasRole('super_admin');
    }
}
