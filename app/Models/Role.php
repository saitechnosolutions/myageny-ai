<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

    public static function tenantRoleName(string $name, ?int $companyId): string
    {
        $slug = str($name)->slug('_')->value();

        if (! $companyId) {
            return $slug;
        }

        return 'company_' . $companyId . '__' . $slug;
    }
}
