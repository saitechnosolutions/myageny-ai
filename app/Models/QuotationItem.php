<?php
// ================================================================
// FILE: app/Models/QuotationItem.php
// ================================================================
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationItem extends Model
{
    protected $fillable = [
        'quotation_id','product_name','description',
        'quantity','unit','unit_price','discount_percent',
        'total','sort_order',
    ];

    protected $casts = [
        'unit_price'       => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'total'            => 'decimal:2',
    ];

    public function quotation() { return $this->belongsTo(Quotation::class); }

    protected static function booted(): void
    {
        static::saving(function (QuotationItem $item) {
            $gross   = $item->unit_price * $item->quantity;
            $disc    = $gross * ($item->discount_percent / 100);
            $item->total = $gross - $disc;
        });
    }
}
