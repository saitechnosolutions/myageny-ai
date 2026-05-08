<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InternFamilyDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'intern_joining_form_id',
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

    public function internJoiningForm(): BelongsTo
    {
        return $this->belongsTo(InternJoiningForm::class);
    }
}
