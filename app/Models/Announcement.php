<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = [
        'enabled',
        'subject',
        'content',
        'theme',
    ];
}
