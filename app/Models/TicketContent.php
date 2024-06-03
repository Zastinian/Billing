<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketContent extends Model
{
    protected $fillable = [
        'ticket_id',
        'replier_id',
        'message',
        'attachment',
    ];
}
