<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KbArticle extends Model
{
    protected $fillable = [
        'category_id',
        'subject',
        'content',
        'order',
    ];
}
