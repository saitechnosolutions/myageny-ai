<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadProductPriceRequest extends Model
{
    use HasFactory;

    public const STATUSES = [
        'pending' => 'Pending',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
    ];

    protected $fillable = [
        'lead_id',
        'product_id',
        'lead_product_id',
        'deal_name',
        'product_name',
        'product_description',
        'original_unit_price',
        'requested_unit_price',
        'quantity',
        'discount_percent',
        'remarks',
        'status',
        'requested_by',
        'approved_by',
        'approved_at',
        'rejection_reason',
    ];

    protected $casts = [
        'original_unit_price' => 'float',
        'requested_unit_price' => 'float',
        'quantity' => 'integer',
        'discount_percent' => 'float',
        'approved_at' => 'datetime',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function approvedLeadProduct()
    {
        return $this->belongsTo(LeadProduct::class, 'lead_product_id');
    }

    public function getRequestedTotalAttribute(): float
    {
        return round($this->requested_unit_price * $this->quantity * (1 - ($this->discount_percent / 100)), 2);
    }

    public function getPriceDifferenceAttribute(): float
    {
        return round($this->requested_unit_price - $this->original_unit_price, 2);
    }
}
