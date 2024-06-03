<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    protected $fillable = [
        'country',
        'percent',
        'amount',
    ];

    public static function getAfterTax($original, self $tax_id = null) {
        if (is_null($tax = self::find($tax_id))) $tax = session('tax');

        if ($tax->percent > 0) {
            $original *= 1 + $tax->percent / 100;
        } elseif ($tax->amount > 0) {
            $original += $tax->amount;
        }

        return $original;
    }
}
