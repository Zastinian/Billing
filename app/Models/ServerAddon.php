<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServerAddon extends Model
{
    protected $fillable = [
        'addon_id',
        'cycle_id',
        'server_id',
        'client_id',
        'value',
    ];
}
