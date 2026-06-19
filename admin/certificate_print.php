<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_auth('admin');

$id = (int) ($_GET['id'] ?? 0);
$cert = $id > 0 ? certificate_request_detail($id) : null;

if (!$cert || $cert['status'] !== 'approved') {
    http_response_code(404);
    exit('گواهی یافت نشد یا هنوز تأیید نشده است.');
}

$pageTitle = 'گواهی — ' . $cert['student_name'];
require dirname(__DIR__) . '/includes/layout/certificate_print.php';
