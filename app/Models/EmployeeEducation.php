<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeEducation extends Model
{
    use HasFactory;

    protected $table = 'employee_educations';

    protected $fillable = [
        'employee_onboarding_id',
        'qualification',
        'institution_name',
        'year_of_passing',
        'percentage',
        'specialization',
        'sort_order',
    ];

    public function employeeOnboarding(): BelongsTo
    {
        return $this->belongsTo(EmployeeOnboarding::class);
    }
}