<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use BelongsToCompany;

    protected $fillable = [
        'name',
        'guard_name',
        'display_name',
        'description',
        'department_id',
        'company_id',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function roleMapping(): HasOne
    {
        return $this->hasOne(RoleMapping::class);
    }

    public function roleParentMapping(): HasOne
    {
        return $this->hasOne(RoleHierarchyMapping::class, 'child_role_id');
    }

    public function childRoleMappings(): HasMany
    {
        return $this->hasMany(RoleHierarchyMapping::class, 'parent_role_id');
    }

    public static function tenantRoleName(string $name, ?int $companyId): string
    {
        $slug = str($name)->slug('_')->value();

        if (! $companyId) {
            return $slug;
        }

        return 'company_' . $companyId . '__' . $slug;
    }
}
