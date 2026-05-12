<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VisitorEntry extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_CHECKED_IN = 'checked_in';
    public const STATUS_CHECKED_OUT = 'checked_out';

    protected $fillable = [
        'visitor_name',
        'mobile_number',
        'visit_date',
        'in_time',
        'out_time',
        'person_to_meet',
        'status',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'visit_date' => 'date',
        ];
    }

    public static function statusFor(?string $outTime): string
    {
        return $outTime ? self::STATUS_CHECKED_OUT : self::STATUS_CHECKED_IN;
    }
}
