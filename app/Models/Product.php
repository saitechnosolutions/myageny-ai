<?php
// ================================================================
// FILE: app/Models/Product.php
// ================================================================

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_name',
        'product_code',
        'rate',
        'gst_percent',
        'gst_amount',
        'rate_with_gst',
        'description',
        'unit',
        'category',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'rate'          => 'decimal:2',
        'gst_percent'   => 'decimal:2',
        'gst_amount'    => 'decimal:2',
        'rate_with_gst' => 'decimal:2',
        'is_active'     => 'boolean',
    ];

    // ── Constants ──────────────────────────────────────────────────

    const GST_RATES = [0, 3, 5, 12, 18, 28];

    const UNITS = [
        'Nos'   => 'Nos (Numbers)',
        'Kg'    => 'Kg (Kilogram)',
        'Ltr'   => 'Ltr (Litre)',
        'Set'   => 'Set',
        'Hr'    => 'Hr (Hours)',
        'Month' => 'Month',
        'Year'  => 'Year',
        'Sqft'  => 'Sqft',
        'Meter' => 'Meter',
    ];

    // ── Relationships ──────────────────────────────────────────────

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Scopes ─────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('product_name', 'like', "%{$term}%")
              ->orWhere('product_code', 'like', "%{$term}%")
              ->orWhere('category', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%");
        });
    }

    // ── Accessors ─────────────────────────────────────────────────

    public function getFormattedRateAttribute(): string
    {
        return '₹' . number_format($this->rate, 2);
    }

    public function getFormattedRateWithGstAttribute(): string
    {
        return '₹' . number_format($this->rate_with_gst, 2);
    }

    public function getGstLabelAttribute(): string
    {
        return $this->gst_percent > 0
            ? $this->gst_percent . '% GST'
            : 'GST Exempt';
    }

    // ── Auto-compute GST fields + auto-generate product_code ───────

    protected static function booted(): void
    {
        // Compute GST before save
        static::saving(function (Product $p) {
            $p->gst_amount    = round($p->rate * ($p->gst_percent / 100), 2);
            $p->rate_with_gst = round($p->rate + $p->gst_amount, 2);
        });

        // Auto-generate product_code on create
        static::creating(function (Product $p) {
            if (empty($p->product_code)) {
                $last = static::withTrashed()->max('id') ?? 0;
                $p->product_code = 'PRD-' . str_pad($last + 1, 4, '0', STR_PAD_LEFT);
            }
            if (empty($p->created_by) && auth()->check()) {
                $p->created_by = auth()->id();
            }
        });
    }
}
