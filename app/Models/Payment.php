<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'lead_product_id', 'lead_id',
        'amount', 'payment_mode', 'payment_date',
        'reference_number', 'notes', 'recorded_by',
    ];

    protected $casts = [
        'amount'       => 'float',
        'payment_date' => 'date',
    ];

    // ── Relationships ─────────────────────────────────────────────────
    public function leadProduct()
    {
        return $this->belongsTo(LeadProduct::class, 'lead_product_id');
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    // ── Accessors ─────────────────────────────────────────────────────
    public function getModeLabelAttribute(): string
    {
        return LeadProduct::PAYMENT_MODE_CONFIG[$this->payment_mode]['label'] ?? ucfirst($this->payment_mode);
    }

    public function getModeIconAttribute(): string
    {
        return LeadProduct::PAYMENT_MODE_CONFIG[$this->payment_mode]['icon'] ?? '💰';
    }

    public function getModeColorAttribute(): string
    {
        return LeadProduct::PAYMENT_MODE_CONFIG[$this->payment_mode]['color'] ?? '#6b7280';
    }

    // ── Methods ───────────────────────────────────────────────────────
    public function toJsPayload(): array
    {
        return [
            'id'        => $this->id,
            'amount'    => (float) $this->amount,
            'mode'      => $this->payment_mode,
            'modeLabel' => $this->mode_label,
            'modeIcon'  => $this->mode_icon,
            'modeColor' => $this->mode_color,
            'date'      => $this->payment_date->format('d M Y'),
            'ref'       => $this->reference_number ?? '',
            'notes'     => $this->notes ?? '',
            'by'        => $this->recordedBy?->name ?? 'System',
            'delUrl'    => route('leads.products.payments.destroy', [
                $this->lead_id, $this->lead_product_id, $this->id
            ]),
        ];
    }
}