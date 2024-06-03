<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AffiliateEarning extends Model
{
    protected $fillable = [
        'client_id',
        'buyer_id',
        'product',
        'commission',
        'conversion',
        'status',
    ];
}
