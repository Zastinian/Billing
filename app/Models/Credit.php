<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Credit extends Model
{
    protected $fillable = [
        'client_id',
        'details',
        'change',
        'balance',
    ];
}
