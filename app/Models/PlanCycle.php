<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanCycle extends Model
{
    protected $fillable = [
        'plan_id',
        'cycle_length',
        'cycle_type',
        'init_price',
        'renew_price',
        'setup_fee',
        'late_fee',
        'trial_length',
        'trial_type',
    ];

    public static function type_name(int $length, int $type) {
        switch ($type) {
            case 0:
                return 'One-time';
            case 1:
                return 'Hourly';
            case 2:
                switch ($length) {
                    case 1:
                        return 'Daily';
                    case 7:
                        return 'Weekly';
                    case 14:
                        return 'Biweekly';
                    case 21:
                        return 'Triweekly';
                    case 28:
                    case 30:
                        return 'Monthly';
                    default:
                        return $length.' Daily';
                }
            case 3:
                switch ($length) {
                    case 1:
                        return 'Monthly';
                    case 2:
                        return 'Bimonthly';
                    case 3:
                        return 'Quarterly';
                    case 6:
                        return 'Biannually';
                    case 12:
                        return 'Annually';
                    default:
                        return $length.' Monthly';
                }
            case 4:
                switch ($length) {
                    case 1:
                        return 'Annually';
                    case 2:
                        return 'Biennially';
                    case 3:
                        return 'Triennially';
                    default:
                        return $length.' Yearly';
                }
        }
    }

    public static function type_sec(int $length, int $type) {
        switch ($type) {
            case 0:
                return INF;
            case 1:
                return $length * 60*60;
            case 2:
                return $length * 60*60*24;
            case 3:
                return $length * 60*60*24*30;
            case 4:
                return $length * 60*60*24*30*12;
        }
    }
}
