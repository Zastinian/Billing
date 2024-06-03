<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = [
        'name',
        'symbol',
        'rate',
        'precision',
        'default',
    ];

    const SYMBOL_VALUE_NAME = 30;
    const SYMBOL_VALUE = 20;
    const VALUE_ONLY = 10;
    const VALUE_NO_CONVERSION = 0;
}
