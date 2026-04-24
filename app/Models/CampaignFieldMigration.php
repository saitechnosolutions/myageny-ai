<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignFieldMigration extends Model
{
    protected $fillable = [ 'campaign_id', 'campaign_field_id', 'lead_field_id', 'campaign_field_name', 'crm_field_name' ];
}