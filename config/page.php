<?php

use App\Models\Page;

$page_model = Page::class;

try {
    $home = $page_model::where('name', 'home')->value('content');
    $contact = $page_model::where('name', 'contact')->value('content');
    $status = $page_model::where('name', 'status')->value('content');
    $terms = $page_model::where('name', 'terms')->value('content');
    $privacy = $page_model::where('name', 'privacy')->value('content');
} catch (Throwable $err) {
    $home = "<h1>Welcome to your new store.</h1>\n<p>This is the home page. You may edit this page in the admin area.</p>";
    $contact = config('mail.from.address', 'hello@example.com');
    $status = "<h1>Welcome to your System Status page.</h1>\n<p>You may edit this page in the admin area.</p>";
    $terms = "<h1>Welcome to your Terms of Service page.</h1>\n<p>You may edit this page in the admin area.</p>";
    $privacy = "<h1>Welcome to your Privacy Policy page.</h1>\n<p>You may edit this page in the admin area.</p>";
}

return [
    'home' => $home,
    'contact' => $contact,
    'status' => $status,
    'terms' => $terms,
    'privacy' => $privacy,
];
