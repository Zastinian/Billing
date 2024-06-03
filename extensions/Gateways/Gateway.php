<?php

namespace Extensions\Gateways;

use App\Models\Invoice;
use Illuminate\Http\Request;

interface Gateway {
    public static function config();

    public static function seeder();

    public static function routes();
    
    /**
     * Redirect the client to an external payment website
     */
    public static function checkout(Invoice $invoice, string $url);
    
    /**
     * Verify if the payment is valid
     */
    public static function payment(Request $request, Invoice $invoice, string $url);
    
    /**
     * Show the extension settings page in admin area
     */
    public static function show();

    /**
     * Update extension settings to the database
     */
    public static function store(Request $request);
}
