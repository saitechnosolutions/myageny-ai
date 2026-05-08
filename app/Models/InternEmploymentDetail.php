<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InternEmploymentDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'intern_joining_form_id',
        'organisation',
        'designation',
        'period_from',
        'period_to',
        'annual_ctc',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'period_from' => 'date',
            'period_to' => 'date',
        ];
    }

    public function internJoiningForm(): BelongsTo
    {
        return $this->belongsTo(InternJoiningForm::class);
    }
}
