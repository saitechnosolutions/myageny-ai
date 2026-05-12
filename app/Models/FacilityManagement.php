<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FacilityManagement extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'facility_managements';
    protected $fillable = [
        'title',
        'office_mopping_date',
        'office_cleaning_date',
        'toilet_cleaning_date',
        'remarks',
    ];

    protected $casts = [
        'office_mopping_date' => 'date',
        'office_cleaning_date' => 'date',
        'toilet_cleaning_date' => 'date',
    ];
}
