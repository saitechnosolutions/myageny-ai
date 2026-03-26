<?php
// ================================================================
// FILE: app/Models/Quotation.php
// ================================================================
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quotation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'quotation_number','lead_id','created_by','quotation_date',
        'valid_until','status','subtotal','discount_amount',
        'tax_percent','tax_amount','grand_total',
        'terms_conditions','notes',
    ];

    protected $casts = [
        'quotation_date' => 'date',
        'valid_until'    => 'date',
        'subtotal'       => 'decimal:2',
        'discount_amount'=> 'decimal:2',
        'tax_percent'    => 'decimal:2',
        'tax_amount'     => 'decimal:2',
        'grand_total'    => 'decimal:2',
    ];

    const STATUSES = [
        'draft'    => 'Draft',
        'sent'     => 'Sent',
        'accepted' => 'Accepted',
        'rejected' => 'Rejected',
        'expired'  => 'Expired',
    ];

    const STATUS_COLORS = [
        'draft'    => ['bg'=>'#f5f4f6','text'=>'#7c7c7c','border'=>'#e1dee3'],
        'sent'     => ['bg'=>'#eff6ff','text'=>'#2563eb','border'=>'#bfdbfe'],
        'accepted' => ['bg'=>'#f0fdf4','text'=>'#16a34a','border'=>'#bbf7d0'],
        'rejected' => ['bg'=>'#fef2f2','text'=>'#dc2626','border'=>'#fecaca'],
        'expired'  => ['bg'=>'#fffbeb','text'=>'#b45309','border'=>'#fde68a'],
    ];

    public function lead()      { return $this->belongsTo(Lead::class); }
    public function createdBy() { return $this->belongsTo(User::class, 'created_by'); }
    public function items()     { return $this->hasMany(QuotationItem::class)->orderBy('sort_order'); }

    public function getStatusColorAttribute(): array
    {
        return self::STATUS_COLORS[$this->status] ?? self::STATUS_COLORS['draft'];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }

    public function getFormattedGrandTotalAttribute(): string
    {
        return '₹' . number_format($this->grand_total, 2);
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->valid_until && $this->valid_until->isPast() && $this->status === 'sent';
    }

    // Auto-generate quotation number
    protected static function booted(): void
    {
        static::creating(function (Quotation $q) {
            if (empty($q->quotation_number)) {
                $lastId = static::max('id') ?? 0;
                $q->quotation_number = 'QT-' . now()->format('Y') . '-' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    // Recompute totals from items
    public function recalculateTotals(): void
    {
        $subtotal = $this->items->sum('total');
        $discount = $this->discount_amount ?? 0;
        $taxable  = $subtotal - $discount;
        $taxAmt   = $taxable * ($this->tax_percent / 100);
        $this->update([
            'subtotal'   => $subtotal,
            'tax_amount' => $taxAmt,
            'grand_total'=> $taxable + $taxAmt,
        ]);
    }
}
