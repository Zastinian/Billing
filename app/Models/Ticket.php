<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'client_id',
        'subject',
        'server_id',
        'department_id',
        'category_id',
        'status',
        'is_locked',
        'priority',
    ];
}
