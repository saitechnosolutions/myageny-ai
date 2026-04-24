<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'email',
        'mobile_number',
        'address',
        'number_of_accounts',
        'company_status',
        'facebook_client_id',
        'facebook_client_secret',
    ];

    protected function casts(): array
    {
        return [
            'number_of_accounts' => 'integer',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->company_status === 'active' ? 'Activate' : 'Deactivate';
    }
}
