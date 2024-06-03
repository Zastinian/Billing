<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AddonCycle extends Model
{
    protected $fillable = [
        'addon_id',
        'cycle_length',
        'cycle_type',
        'init_price',
        'renew_price',
        'setup_fee',
    ];
}
