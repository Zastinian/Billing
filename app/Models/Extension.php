<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Extension extends Model
{
    protected $fillable = [
        'extension',
        'key',
        'value',
    ];

    protected $hidden = [
        'value',
    ];
}
