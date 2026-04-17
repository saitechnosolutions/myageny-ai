<?php
// app/Models/OutcomeCategory.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OutcomeCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'company_id'];

    /**
     * One Category has many Sub Categories.
     */
    public function subCategories()
    {
        return $this->hasMany(OutcomeSubCategory::class, 'category_id');
    }

    public function scopeForCompany($query)
    {
        return $query->where('company_id', auth()->user()->company_id);
    }
}
