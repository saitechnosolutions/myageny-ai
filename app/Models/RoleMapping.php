<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoleMapping extends Model
{
    use HasFactory, BelongsToCompany;

    public const ACCESS_COMPANY = 'company';
    public const ACCESS_TEAM = 'team';
    public const ACCESS_SELF = 'self';

    public const ACCESS_LEVELS = [
        self::ACCESS_COMPANY => 'Company All Data',
        self::ACCESS_TEAM => 'Mapped Team Data',
        self::ACCESS_SELF => 'Assigned Self Data',
    ];

    protected $fillable = [
        'company_id',
        'role_id',
        'access_level',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public static function labelFor(?string $accessLevel): string
    {
        return self::ACCESS_LEVELS[$accessLevel] ?? self::ACCESS_LEVELS[self::ACCESS_SELF];
    }
}
