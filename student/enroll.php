<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_student_auth();

if (!is_post() || !verify_csrf()) {
    flash('error', 'درخواست نامعتبر است.');
    redirect(base_url('student/courses.php'));
}

$courseId = (int) ($_POST['course_id'] ?? 0);
if ($courseId <= 0) {
    flash('error', 'دوره نامعتبر است.');
    redirect(base_url('student/courses.php'));
}

try {
    enroll_student_in_course((int) auth_id(), $courseId);
} catch (RuntimeException $e) {
    flash('error', $e->getMessage());
    redirect(base_url('student/courses.php'));
}



