<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'quotation_no',
        'quotation_date',
        'valid_until',
        'tax',
        'subtotal',
        'tax_amount',
        'total_amount',
        'lead_id',
        'is_approved',
        'approved_by',
        'approved_at',
        'notes',
        'created_by',
        'bill_to_address',
        'ship_to_address'
    ];

    protected $casts = [
        'quotation_date' => 'date',
        'valid_until'    => 'date',
        'is_approved'    => 'boolean',
        'approved_at'    => 'datetime',
        'tax'            => 'decimal:2',
        'subtotal'       => 'decimal:2',
        'tax_amount'     => 'decimal:2',
        'total_amount'   => 'decimal:2',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuotationItem::class);
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    /**
     * Generate the next sequential quotation number.
     * Format: QT-YY-0001
     */
    public static function generateQuotationNo(): string
    {
        $year   = now()->format('y');
        $prefix = "QT-{$year}-";

        $last = static::where('quotation_no', 'like', "{$prefix}%")
            ->orderByDesc('id')
            ->value('quotation_no');

        $seq = $last ? ((int) substr($last, -4)) + 1 : 1;

        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Human-readable approval status.
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->is_approved ? 'Approved' : 'Pending';
    }
}