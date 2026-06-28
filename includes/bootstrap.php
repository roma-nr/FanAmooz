<?php

declare(strict_types=1);

$configApp = require dirname(__DIR__) . '/config/app.php';
$configDb = require dirname(__DIR__) . '/config/database.php';


date_default_timezone_set($configApp['timezone']);

if (session_status() === PHP_SESSION_NONE) {
    session_name($configApp['session_name']);
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
        'use_strict_mode' => true,
    ]);
}

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/jdf.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/student.php';
require_once __DIR__ . '/teacher.php';
require_once __DIR__ . '/course.php';
require_once __DIR__ . '/csv_import.php';
require_once __DIR__ . '/settings.php';
require_once __DIR__ . '/cms.php';
require_once __DIR__ . '/lms.php';
require_once __DIR__ . '/communication.php';
require_once __DIR__ . '/certificates.php';
require_once __DIR__ . '/reports.php';
require_once __DIR__ . '/certificates.php';
require_once __DIR__ . '/reports.php';

