<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeadProduct extends Model
{
    use HasFactory, SoftDeletes;



    const PAYMENT_MODE_CONFIG = [
        'cash'          => ['label' => 'Cash',          'icon' => '💵', 'color' => '#16a34a'],
        'bank_transfer' => ['label' => 'Bank Transfer', 'icon' => '🏦', 'color' => '#1d4ed8'],
        'cheque'        => ['label' => 'Cheque',        'icon' => '📝', 'color' => '#7c3aed'],
        'upi'           => ['label' => 'UPI',           'icon' => '📱', 'color' => '#be185d'],
        'card'          => ['label' => 'Card',          'icon' => '💳', 'color' => '#0369a1'],
    ];

    // ── Fillable ──────────────────────────────────────────────────────
    protected $fillable = [
        'lead_id', 'product_id', 'deal_name',
        'product_name', 'description',
        'unit_price', 'quantity', 'discount_percent',
        'remarks', 'product_status',
        'amount_paid', 'created_by',
    ];

    protected $casts = [
        'unit_price'       => 'float',
        'quantity'         => 'integer',
        'discount_percent' => 'float',
        'total_price'      => 'float',
        'amount_paid'       => 'float',
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

    // ── Relationships ─────────────────────────────────────────────────
    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'lead_product_id')->latest('payment_date');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Accessors ─────────────────────────────────────────────────────
    public function getAmountPendingAttribute(): float
    {
        return max(0, $this->total_price - $this->amount_paid);
    }

    public function getPaymentProgressAttribute(): int
    {
        if ($this->total_price <= 0) return 100;
        return (int) min(100, round(($this->amount_paid / $this->total_price) * 100));
    }

    public function getPaymentStatusAttribute(): string
    {
        $p = $this->payment_progress;
        if ($p <= 0)   return 'unpaid';
        if ($p < 100)  return 'partial';
        return 'paid';
    }

    public function getPaymentStatusColorAttribute(): array
    {
        return match ($this->payment_status) {
            'paid'    => ['bg' => '#f0fdf4', 'text' => '#15803d', 'border' => '#bbf7d0'],
            'partial' => ['bg' => '#fff7ed', 'text' => '#c2410c', 'border' => '#fed7aa'],
            default   => ['bg' => '#fafafa', 'text' => '#6b7280', 'border' => '#e5e7eb'],
        };
    }

    public function getProductStatusConfigAttribute(): array
    {
        return self::PRODUCT_STATUS_CONFIG[$this->product_status]
            ?? self::PRODUCT_STATUS_CONFIG['new'];
    }

    public function getFormattedTotalAttribute(): string
    {
        return '₹' . number_format($this->total_price, 2);
    }

    public function getFormattedPaidAttribute(): string
    {
        return '₹' . number_format($this->amount_paid, 2);
    }

    public function getFormattedPendingAttribute(): string
    {
        return '₹' . number_format($this->amount_pending, 2);
    }

    // ── Methods ───────────────────────────────────────────────────────
    /**
     * Recalculate total_paid from actual payment records.
     */
    public function recalcPaid(): void
    {
        $this->amount_paid = $this->payments()->sum('amount');
        $this->saveQuietly();
    }

    /**
     * Build a serializable array for JS hydration.
     */
    public function toJsPayload(): array
    {
        return [
            'id'       => $this->id,
            // 'name'     => $this->product_name,
            'name'     => $this->product?->product_name." (Base Price : ".number_format($this->product?->final_price, 2).")",
            'total'    => (float) $this->total_price,
            'paid'     => (float) $this->amount_paid,
            'pending'  => (float) $this->amount_pending,
            'progress' => $this->payment_progress,
            'payUrl'   => route('leads.products.payments.store', [$this->lead_id, $this->id]),
            'payments' => $this->payments->map(fn($p) => $p->toJsPayload())->toArray(),
        ];
    }
}