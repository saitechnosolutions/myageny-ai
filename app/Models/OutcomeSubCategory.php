<?php
// app/Models/OutcomeSubCategory.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OutcomeSubCategory extends Model
{
    use HasFactory;

    protected $fillable = ['category_id', 'name', 'company_id'];

    /**
     * Sub Category belongs to one Category.
     */
    public function category()
    {
        return $this->belongsTo(OutcomeCategory::class, 'category_id');
    }

    public function scopeForCompany($query)
    {
        return $query->where('company_id', auth()->user()->company_id);
    }
}
