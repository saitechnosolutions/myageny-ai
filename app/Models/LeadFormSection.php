<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadFormSection extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'description', 'sort_order', 'is_active', 'branch_id',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
    ];

    public function fields()
    {
        return $this->belongsToMany(
            LeadFormField::class,
            'lead_form_field_section',
            'lead_form_section_id',
            'lead_form_field_id'
        )->withPivot('sort_order')->orderByPivot('sort_order');
    }
}