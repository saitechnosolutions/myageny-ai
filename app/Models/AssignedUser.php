<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssignedUser extends Model
{
    protected $fillable = [ 'user_id', 'campaign_id', 'user_name' ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(CampaignMaster::class, 'campaign_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
