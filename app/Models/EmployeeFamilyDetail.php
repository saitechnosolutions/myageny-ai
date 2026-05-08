<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeFamilyDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_onboarding_id',
        'name',
        'relation',
        'occupation',
        'date_of_birth',
        'mobile_no',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
        ];
    }

    public function employeeOnboarding(): BelongsTo
    {
        return $this->belongsTo(EmployeeOnboarding::class);
    }
}
