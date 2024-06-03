<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    protected $fillable = [
        'name',
        'percent_off',
        'is_global',
        'end_date',
    ];

    public static function verifyDiscount(self $discount)
    {
        return Carbon::parse($discount->end_date)->timestamp > Carbon::now()->timestamp || is_null($discount->end_date) ? $discount : false;
    }

    public static function getValidDiscounts()
    {
        $discounts = [];
        foreach (Discount::all() as $discount) if (Discount::verifyDiscount($discount)) array_push($discounts, $discount);
        return $discounts;
    }
}
