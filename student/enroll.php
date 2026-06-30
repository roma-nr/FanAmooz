<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';

// فقط دانشجویان و اساتید مجاز به ثبت‌نام هستند (ادمین نه)
if (!in_array(auth_role(), ['student', 'teacher'])) {
    flash('error', 'شما مجاز به ثبت‌نام نیستید.');
    redirect(base_url('courses.php'));
}

if (!is_post() || !verify_csrf()) {
    flash('error', 'درخواست نامعتبر است.');
    redirect(base_url('courses.php'));
}

$courseId = (int) ($_POST['course_id'] ?? 0);
if ($courseId <= 0) {
    flash('error', 'دوره نامعتبر است.');
    redirect(base_url('courses.php'));
}

$userId = (int) auth_id();

// اگر کاربر استادِ خود دوره باشد، جلوگیری
$isTeacher = db()->prepare("SELECT 1 FROM courses WHERE id = ? AND teacher_id = ?")->execute([$courseId, $userId])->fetchColumn();
if ($isTeacher) {
    flash('error', 'شما مدرس این دوره هستید و نمی‌توانید در آن ثبت‌نام کنید.');
    redirect(base_url('courses.php'));
}

try {
    enroll_student_in_course($userId, $courseId);
} catch (RuntimeException $e) {
    flash('error', $e->getMessage());
    redirect(base_url('courses.php'));
}