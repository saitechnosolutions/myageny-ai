<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttributeValue extends Model
{
    use HasFactory;

    protected $fillable = ['attribute_id', 'value', 'label', 'sort_order'];

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    public function getDisplayLabelAttribute(): string
    {
        return $this->label ?? $this->value;
    }
}
