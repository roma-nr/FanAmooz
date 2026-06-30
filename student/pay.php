<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_auth(['student', 'teacher']);

$courseId = (int)($_GET['course_id'] ?? 0);
$userId   = (int)auth_id();

if ($courseId <= 0) {
    flash('error', 'دوره نامعتبر است.');
    redirect(base_url('student/courses.php'));
}

// اگر قبلاً پرداخت موفق داشته، نیاز به پرداخت نیست
$enrollment = student_enrollment($userId, $courseId);
if ($enrollment && in_array($enrollment['status'], ['active', 'completed'])) {
    flash('success', 'شما قبلاً در این دوره ثبت‌نام کرده‌اید.');
    redirect(base_url('student/my_course.php?slug=' . urlencode(course_by_id($courseId)['slug'] ?? '')));
}

// اگر ثبت‌نام در وضعیت pending نباشد، خطا
if (!$enrollment || $enrollment['status'] !== 'pending') {
    flash('error', 'هیچ درخواست پرداخت معتبری یافت نشد.');
    redirect(base_url('student/courses.php'));
}

try {
    start_course_payment($userId, $courseId);
} catch (RuntimeException $e) {
    flash('error', $e->getMessage());
    redirect(base_url('student/courses.php'));
}