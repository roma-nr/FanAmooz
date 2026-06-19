<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_student_auth();

$courseId = (int) ($_GET['course_id'] ?? 0);
$userId = (int) auth_id();

if ($courseId <= 0 || !user_can_access_course_chat($userId, 'student', $courseId)) {
    flash('error', 'دسترسی به گفتگوی این دوره مجاز نیست.');
    redirect(base_url('student/messages.php'));
}

$course = course_by_id($courseId);
if (!$course) {
    redirect(base_url('student/messages.php'));
}

if (is_post() && verify_csrf() && ($_POST['action'] ?? '') === 'send_message') {
    try {
        $body = trim($_POST['message_body'] ?? '');
        $filePath = null;
        if (!empty($_FILES['message_file']['name'])) {
            $filePath = handle_upload($_FILES['message_file'], 'chat', ['pdf', 'doc', 'docx', 'zip', 'jpg', 'jpeg', 'png'], 10 * 1024 * 1024);
        }
        send_course_message($courseId, $userId, $body, $filePath);
        redirect(base_url('student/chat.php?course_id=' . $courseId));
    } catch (Throwable $e) {
        flash('error', $e->getMessage());
        redirect(base_url('student/chat.php?course_id=' . $courseId));
    }
}

$pageTitle = 'گفتگو — ' . $course['title'];
$activeMenu = 'messages';
$messages = course_messages_list($courseId);
$chatPostUrl = base_url('student/chat.php?course_id=' . $courseId);
$chatApiUrl = base_url('api/chat_messages.php?course_id=' . $courseId);
$chatUserId = $userId;
$chatCourseId = $courseId;
$chatMessages = $messages;

require dirname(__DIR__) . '/includes/layout/student_header.php';
?>

<p class="mb-3">
    <a href="<?= e(base_url('student/messages.php')) ?>">&larr; همه پیام‌ها</a>
    · <a href="<?= e(base_url('student/my_course.php?slug=' . urlencode($course['slug']))) ?>">صفحه دوره</a>
</p>

<h1 class="h4 text-primary mb-3">گفتگو با استاد — <?= e($course['title']) ?></h1>
<?php if ($msg = flash('error')): ?><div class="alert alert-danger"><?= e($msg) ?></div><?php endif; ?>

<?php if (!phase6_tables_ready()): ?>
    <div class="alert alert-warning">سیستم چت فعال نیست.</div>
<?php else: ?>
    <?php require dirname(__DIR__) . '/includes/layout/chat_panel.php'; ?>
<?php endif; ?>

<?php require dirname(__DIR__) . '/includes/layout/student_footer.php'; ?>
