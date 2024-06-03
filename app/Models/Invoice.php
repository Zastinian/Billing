<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'id',
        'client_id',
        'server_id',
        'total',
        'credit',
        'late_fee',
        'payment_method',
        'payment_link',
        'due_date',
        'paid',
    ];
}
