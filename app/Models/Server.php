<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Server extends Model
{
    protected $fillable = [
        'server_id',
        'identifier',
        'client_id',
        'plan_id',
        'plan_cycle',
        'due_date',
        'payment_method',
        'server_name',
        'nest_id',
        'egg_id',
        'location_id',
        'node_id',
        'ip_address',
        'status',
    ];

    public static function genOrder(int $plan_id, int $cycle_id, ?array $addons_id = [], string $coupon_code = null)
    {
        $plan = Plan::find($plan_id);
        $plan_cycle = PlanCycle::find($cycle_id);

        $subtotal = $plan_cycle->init_price + $plan_cycle->setup_fee;
        $next_subtotal = $plan_cycle->renew_price;

        $addons = [];
        foreach (Addon::orderBy('order', 'asc')->get() as $addon) {
            if (
                in_array($plan->category_id, json_decode($addon->categories, true)) &&
                ($addon_cycle = AddonCycle::where('addon_id', $addon->id)->where('cycle_length', $plan_cycle->cycle_length)
                ->where('cycle_type', $plan_cycle->cycle_type)->first()) && Addon::verifyAddon($addon, auth()->user())
            ) {
                $count = 0;
                if ($addons_id && in_array($addon->id, array_keys($addons_id))) {
                    $count = $addons_id[$addon->id];
                    $subtotal += ($addon_cycle->init_price + $addon_cycle->setup_fee) * $count;
                    $next_subtotal += $addon_cycle->renew_price * $count;
                }

                array_push($addons, [$addon, $addon_cycle, $count]);
            }
        }

        $discount = $coupon = null;
        $due_today_off = $due_next_off = 1;
        if ($coupon_code) {
            if ($coupon = Coupon::verifyCoupon(Coupon::where('code', $coupon_code)->first(), auth()->user())) {
                $due_today_off = (1 - $coupon->percent_off / 100);
                $due_next_off = $coupon->one_time ? 1 : $due_today_off;
            } else {
                return 'The coupon code is invalid, unavailable, or expired!';
            }
        } else {
            foreach (Discount::getValidDiscounts() as $valid_discount) {
                if ($plan->discount === $valid_discount->id || $valid_discount->is_global) {
                    $discount = $valid_discount;
                    $due_today_off = 1 - ($valid_discount->percent_off / 100);
                    break;
                }
            }
        }

        $promotion_off = $subtotal * (1 - $due_today_off);
        $due_today = $subtotal * $due_today_off;
        $due_next = $next_subtotal * $due_next_off;

        $due_today = Tax::getAfterTax($due_today);
        $due_next = Tax::getAfterTax($due_next);

        if (($credit = auth()->user()->credit) > 0)
            if ($due_today - $credit < 0) {
                $credit = $due_today;
                $due_today = 0;
            } else {
                $due_today -= $credit;
            }

        return [
            'cycle' => [
                'name' => PlanCycle::type_name($plan_cycle->cycle_length, $plan_cycle->cycle_type),
                'data' => $plan_cycle,
            ],
            'addons' => $addons,
            'discount' => $discount,
            'coupon' => $coupon,
            'promotion_off' => round($promotion_off, 6),
            'subtotal' => price($subtotal, Currency::VALUE_NO_CONVERSION),
            'credit' => price($credit, Currency::VALUE_NO_CONVERSION),
            'summary' => [
                'due_today' => price($due_today, Currency::VALUE_NO_CONVERSION),
                'due_next' => price($due_next, Currency::VALUE_NO_CONVERSION),
            ]
        ];
    }

    public static function getTotalCost(self $server)
    {
        $addons_id = [];
        $coupon_code = null;

        foreach (ServerAddon::where('server_id', $server->id)->get() as $server_addon)
            $addons_id[$server_addon->id] = ctype_digit($server_addon->value) ? $server_addon->value : 1;

        if ($used_coupon = UsedCoupon::where('server_id', $server->id)->first())
            $coupon_code = Coupon::find($used_coupon->coupon_id)->code;

        return self::genOrder($server->plan_id, $server->plan_cycle, $addons_id, $coupon_code)['summary']
            [(Invoice::where('server_id', $server->id)->where('paid', true)->count() == 0 ? 'due_today' : 'due_next')];
    }
}
