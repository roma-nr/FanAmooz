<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/bootstrap.php';

$authority = $_GET['Authority'] ?? '';
$status    = $_GET['Status']    ?? '';

if ($authority === '' || $status === '') {
    flash('error', 'اطلاعات بازگشتی نامعتبر است.');
    redirect(base_url('student/courses.php'));
}

try {
    verify_course_payment($authority, $status);
} catch (RuntimeException $e) {
    flash('error', $e->getMessage());
    redirect(base_url('student/courses.php'));
}