<?php

use App\Models\AffiliateProgram;

$affiliate_program_model = AffiliateProgram::class;

sleep(3);

try {
    $enabled = $affiliate_program_model::find(1)->value;
    $conversion = $affiliate_program_model::find(2)->value;
} catch (Throwable $err) {
    $enabled = 'true';
    $conversion = '50';
}

return [
    'enabled' => $enabled,
    'conversion' => $conversion,
];
