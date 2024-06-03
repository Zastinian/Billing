<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Contact extends Model
{
    use Notifiable;

    protected $fillable = [
        'email',
        'name',
        'subject',
        'message',
    ];
}
