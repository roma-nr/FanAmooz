<?php

declare(strict_types=1);

return [
    'name' => 'فن‌آموز',
    'url' => 'http://localhost/FanAmooz',
    'timezone' => 'Asia/Tehran',
    'upload_path' => dirname(__DIR__) . '/uploads',
    'upload_url' => '/uploads',
    'session_name' => 'fanamooz_session',
    'roles' => [
        'admin' => 'مدیر',
        'teacher' => 'استاد',
        'student' => 'دانشجو',
    ],
];
