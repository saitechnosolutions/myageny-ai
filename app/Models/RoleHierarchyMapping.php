<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoleHierarchyMapping extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'parent_role_id',
        'child_role_id',
    ];

    public function parentRole(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'parent_role_id');
    }

    public function childRole(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'child_role_id');
    }
}
