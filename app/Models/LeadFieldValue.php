<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadFieldValue extends Model
{
    protected $fillable = ['lead_id', 'lead_form_field_id', 'value'];

    public function field()
    {
        return $this->belongsTo(LeadFormField::class, 'lead_form_field_id');
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}