<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsedCoupon extends Model
{
    protected $fillable = [
        'coupon_id',
        'client_id',
        'server_id',
    ];
}
