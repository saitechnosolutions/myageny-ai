<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InternEducationalDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'intern_joining_form_id',
        'qualification',
        'institution_name',
        'year_of_passing',
        'percentage',
        'specialization',
        'sort_order',
    ];

    public function internJoiningForm(): BelongsTo
    {
        return $this->belongsTo(InternJoiningForm::class);
    }
}
