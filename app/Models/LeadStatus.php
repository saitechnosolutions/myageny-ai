<?php
// app/Models/LeadStatus.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeadStatus extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'company_id'];

    /**
     * Scope to filter by the authenticated user's company.
     */
    public function scopeForCompany($query)
    {
        return $query->where('company_id', auth()->user()->company_id);
    }
}
