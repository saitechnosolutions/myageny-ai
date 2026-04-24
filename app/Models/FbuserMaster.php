<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FbuserMaster extends Model
{
    protected $fillable = [
        "fbuser_id",
        "name",
        "email"
    ];
}