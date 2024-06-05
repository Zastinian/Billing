<?php

use App\Models\Extension;

$extension_model = Extension::class;

try {
    $mode = $extension_model::where([['extension', 'PayPal'], ['key', 'mode']])->value('value');
    $username = $extension_model::where([['extension', 'PayPal'], ['key', 'username']])->value('value');
    $password = $extension_model::where([['extension', 'PayPal'], ['key', 'password']])->value('value');
    $secret = $extension_model::where([['extension', 'PayPal'], ['key', 'secret']])->value('value');
    $certificate = $extension_model::where([['extension', 'PayPal'], ['key', 'certificate']])->value('value');
    $app_id = $extension_model::where([['extension', 'PayPal'], ['key', 'app_id']])->value('value');
    $enabled = $extension_model::where([['extension', 'PayPal'], ['key', 'enabled']])->value('value');
} catch (\Throwable $err) {
    $mode = 'sandbox';
    $username = null;
    $password = null;
    $secret = null;
    $certificate = null;
    $app_id = null;
    $enabled = '0';
}

return [
    'mode' => $mode,
    'sandbox' => [
        'username' => $username,
        'password' => $password,
        'secret' => $secret,
        'certificate' => $certificate,
        'app_id' => $app_id,
    ],
    'live' => [
        'username' => $username,
        'password' => $password,
        'secret' => $secret,
        'certificate' => $certificate,
        'app_id' => $app_id,
    ],

    'payment_action' => 'Sale',
    'currency'       => 'USD',
    'notify_url'     => '/extension/paypal/ipn',
    'locale'         => '',
    'validate_ssl'   => false,
    'invoice_prefix' => '',
];
