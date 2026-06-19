<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_student_auth();

$id = (int) ($_GET['id'] ?? 0);
$userId = (int) auth_id();

if ($id <= 0 || !student_can_view_certificate($id, $userId)) {
    http_response_code(403);
    exit('دسترسی به این گواهی مجاز نیست.');
}

$cert = certificate_request_detail($id);
require dirname(__DIR__) . '/includes/layout/certificate_print.php';
