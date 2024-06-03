<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'percent_off',
        'one_time',
        'global_limit',
        'per_client_limit',
        'is_global',
        'end_date',
    ];

    public static function verifyCoupon(self $coupon, Client $client = null)
    {
        return (is_null($coupon->end_date) || Carbon::parse($coupon->end_date)->timestamp > Carbon::now()->timestamp)
            && (is_null($coupon->global_limit) || UsedCoupon::where('coupon_id', $coupon->id)->count() < $coupon->global_limit)
            && (is_null($coupon->per_client_limit) || is_null($client)
            || UsedCoupon::where('coupon_id', $coupon->id)->where('client_id', $client->id)->count() < $coupon->per_client_limit)
            ? $coupon : false;
    }

    public static function getValidCoupons()
    {
        $coupons = [];
        foreach (Coupon::all() as $coupon) if (Coupon::verifyCoupon($coupon)) array_push($coupons, $coupon);
        return $coupons;
    }
}
