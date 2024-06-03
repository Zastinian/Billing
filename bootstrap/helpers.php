<?php

use App\Models\Currency;

function to_page($route, $parameters = [])
{
    $url = route($route, $parameters);
    return "href=\"$url\" onclick=\"return false;\" data-href-no-refresh=\"true\"";
}

function price(float $value, int $level = Currency::SYMBOL_VALUE_NAME, Currency $currency = null)
{
    if (is_null($currency))
        $currency = session()->has('currency') ? session('currency') : Currency::where('default', true)->first();
    
    $output = round($value * ($level >= Currency::VALUE_ONLY ? $currency->rate : 1), $currency->precision);
    if ($level >= Currency::SYMBOL_VALUE) $output = $currency->symbol.$output;
    if ($level >= Currency::SYMBOL_VALUE_NAME) $output = $output.' '.$currency->name;
    
    return $output;
}
