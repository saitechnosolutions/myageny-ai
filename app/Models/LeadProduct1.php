<?php
// ================================================================
// FILE: app/Models/LeadProduct.php  (UPDATED)
// ================================================================
namespace App\Models;

use App\Models\LeadProductPayment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadProduct extends Model
{
    protected $fillable = [
        'lead_id',
        'product_name',
        'product_status',       // new, hot, warm, cold, converted
        'description',
        'unit_price',
        'quantity',
        'discount_percent',
        'total_price',
        'payment_status',       // pending, partial, paid — auto-computed
        'payment_notes',
    ];

    protected $casts = [
        'unit_price'       => 'decimal:2',
        'total_price'      => 'decimal:2',
        'discount_percent' => 'decimal:2',
    ];

    // ── Product Status Constants ───────────────────────────────────
    const PRODUCT_STATUSES = [
        'new'       => 'New',
        'hot'       => 'Hot',
        'warm'      => 'Warm',
        'cold'      => 'Cold',
        'converted' => 'Converted',
    ];

    const PRODUCT_STATUS_CONFIG = [
        'new'       => [
            'bg'     => '#eff6ff',
            'text'   => '#2563eb',
            'border' => '#bfdbfe',
            'icon'   => '✨',
            'dot'    => '#2563eb',
        ],
        'hot'       => [
            'bg'     => '#fef2f2',
            'text'   => '#dc2626',
            'border' => '#fecaca',
            'icon'   => '🔥',
            'dot'    => '#dc2626',
        ],
        'warm'      => [
            'bg'     => '#fff7ed',
            'text'   => '#ea580c',
            'border' => '#fed7aa',
            'icon'   => '☀️',
            'dot'    => '#ea580c',
        ],
        'cold'      => [
            'bg'     => '#f0f9ff',
            'text'   => '#0284c7',
            'border' => '#bae6fd',
            'icon'   => '🧊',
            'dot'    => '#0284c7',
        ],
        'converted' => [
            'bg'     => '#f0fdf4',
            'text'   => '#16a34a',
            'border' => '#bbf7d0',
            'icon'   => '✅',
            'dot'    => '#16a34a',
        ],
    ];

    const PAYMENT_MODES = [
        'cash'          => 'Cash',
        'bank_transfer' => 'Bank Transfer',
        'cheque'        => 'Cheque',
        'upi'           => 'UPI',
        'card'          => 'Card',
    ];

    const PAYMENT_STATUS_CONFIG = [
        'pending' => ['bg'=>'#fef2f2','text'=>'#dc2626','border'=>'#fecaca'],
        'partial' => ['bg'=>'#fffbeb','text'=>'#b45309','border'=>'#fde68a'],
        'paid'    => ['bg'=>'#f0fdf4','text'=>'#16a34a','border'=>'#bbf7d0'],
    ];

    // ── Relationships ──────────────────────────────────────────────
    public function lead()     { return $this->belongsTo(Lead::class); }
    public function payments() { return $this->hasMany(LeadProductPayment::class)->latest('payment_date'); }

    // ── Accessors ─────────────────────────────────────────────────

    public function getProductStatusConfigAttribute(): array
    {
        return self::PRODUCT_STATUS_CONFIG[$this->product_status] ?? self::PRODUCT_STATUS_CONFIG['new'];
    }

    public function getProductStatusLabelAttribute(): string
    {
        return self::PRODUCT_STATUSES[$this->product_status] ?? ucfirst($this->product_status);
    }

    public function getTotalPaidAttribute(): float
    {
        return (float) $this->payments->sum('amount');
    }

    public function getAmountPendingAttribute(): float
    {
        return max(0, $this->total_price - $this->total_paid);
    }

    public function getPaymentStatusColorAttribute(): array
    {
        return self::PAYMENT_STATUS_CONFIG[$this->payment_status] ?? self::PAYMENT_STATUS_CONFIG['pending'];
    }

    public function getFormattedTotalAttribute(): string
    {
        return '₹' . number_format($this->total_price, 2);
    }

    public function getFormattedPaidAttribute(): string
    {
        return '₹' . number_format($this->total_paid, 2);
    }

    public function getFormattedPendingAttribute(): string
    {
        return '₹' . number_format($this->amount_pending, 2);
    }

    public function getPaymentProgressAttribute(): int
    {
        if ($this->total_price <= 0) return 0;
        return (int) min(100, ($this->total_paid / $this->total_price) * 100);
    }

    // ── Auto-compute total_price and payment_status ───────────────
    protected static function booted(): void
    {
        static::saving(function (LeadProduct $lp) {
            $gross       = $lp->unit_price * $lp->quantity;
            $discount    = $gross * ($lp->discount_percent / 100);
            $lp->total_price = $gross - $discount;
        });

        static::saved(function (LeadProduct $lp) {
            $lp->syncPaymentStatus();
        });
    }

    /**
     * Recompute payment_status from actual payments sum.
     */
    public function syncPaymentStatus(): void
    {
        $paid = $this->payments()->sum('amount');
        if ($paid <= 0) {
            $status = 'pending';
        } elseif ($paid >= $this->total_price) {
            $status = 'paid';
        } else {
            $status = 'partial';
        }
        $this->updateQuietly(['payment_status' => $status]);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

}
