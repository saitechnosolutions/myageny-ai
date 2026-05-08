<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes, BelongsToCompany;

    protected $fillable = [
        'product_category_id', 'package_name', 'sku',
        'base_price', 'tax_type', 'tax_value', 'discount_type', 'discount_value',
        'final_price', 'description', 'status', 'sort_order', 'product_name',
        'company_id',
    ];

    protected $casts = [
        'base_price'     => 'float',
        'tax_value'      => 'float',
        'discount_value' => 'float',
        'final_price'    => 'float',
    ];

    // ── Boot ──────────────────────────────────────────────────────────
    protected static function boot(): void
    {
        parent::boot();

        $callback = function (self $model) {
            $model->final_price = $model->computeFinalPrice();
            if (empty($model->sku)) {
                $model->sku = 'PRD-' . strtoupper(uniqid());
            }
        };

        static::creating($callback);
        static::updating($callback);
    }

    // ── Relationships ─────────────────────────────────────────────────
    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function attributeValues()
    {
        return $this->hasMany(ProductAttributeValue::class)->with('attribute')->orderBy('sort_order');
    }

    // ── Price Logic ───────────────────────────────────────────────────
    public function computeFinalPrice(): float
    {
        $base = (float) $this->base_price;

        // Apply discount first
        if ($this->discount_type === 'percentage') {
            $discounted = $base - ($base * $this->discount_value / 100);
        } else {
            $discounted = $base - $this->discount_value;
        }

        // Apply tax
        if ($this->tax_type === 'percentage') {
            $final = $discounted + ($discounted * $this->tax_value / 100);
        } else {
            $final = $discounted + $this->tax_value;
        }

        return round(max(0, $final), 2);
    }

    // ── Accessors ─────────────────────────────────────────────────────
    public function getFormattedFinalPriceAttribute(): string
    {
        return '₹' . number_format($this->final_price, 2);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'active'   => '<span class="pm-badge pm-badge--active">Active</span>',
            'inactive' => '<span class="pm-badge pm-badge--inactive">Inactive</span>',
            'draft'    => '<span class="pm-badge pm-badge--draft">Draft</span>',
            default    => '',
        };
    }

    // ── Scopes ────────────────────────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByCategory($query, int $categoryId)
    {
        return $query->where('product_category_id', $categoryId);
    }
}
