<?php

use App\Models\Extension;

$extension_model = Extension::class;

try {
    $access_token = $extension_model::where([['extension', 'MercadoPago'], ['key', 'access_token']])->value('value');
    $enabled = $extension_model::where([['extension', 'MercadoPago'], ['key', 'enabled']])->value('value');
} catch (\Throwable $err) {
    $access_token = null;
    $enabled = '0';
}

return [
    'access_token' => $access_token,
    'enabled' => $enabled,
    'notification_url' => '/extension/mercadopago/ipn',
];
