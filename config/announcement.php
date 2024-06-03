<?php

use App\Models\Announcement;

$announcement_model = Announcement::class;

try {
    $announcements = $announcement_model::all();
} catch (\Throwable $th) {
    $announcements = [];
}

$config = [];
foreach ($announcements as $announcement) {
    $config[$announcement->id] = [
        'enabled' => $announcement->enabled,
        'subject' => $announcement->subject,
        'content' => $announcement->content,
        'theme' => $announcement->theme,
    ];
}

return ['items' => $config];
