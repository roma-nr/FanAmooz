<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_teacher_approved();

$courseId = (int) ($_GET['course_id'] ?? 0);
$teacherId = (int) auth_id();

if ($courseId <= 0 || !user_can_access_course_chat($teacherId, 'teacher', $courseId)) {
    flash('error', 'دوره یافت نشد.');
    redirect(base_url('teacher/messages.php'));
}

$courseStmt = db()->prepare('SELECT * FROM courses WHERE id = ? AND teacher_id = ?');
$courseStmt->execute([$courseId, $teacherId]);
$course = $courseStmt->fetch();

if (!$course) {
    redirect(base_url('teacher/messages.php'));
}

if (is_post() && verify_csrf() && ($_POST['action'] ?? '') === 'send_message') {
    try {
        $body = trim($_POST['message_body'] ?? '');
        $filePath = null;
        if (!empty($_FILES['message_file']['name'])) {
            $filePath = handle_upload($_FILES['message_file'], 'chat', ['pdf', 'doc', 'docx', 'zip', 'jpg', 'jpeg', 'png'], 10 * 1024 * 1024);
        }
        send_course_message($courseId, $teacherId, $body, $filePath);
        redirect(base_url('teacher/chat.php?course_id=' . $courseId));
    } catch (Throwable $e) {
        flash('error', $e->getMessage());
        redirect(base_url('teacher/chat.php?course_id=' . $courseId));
    }
}

$pageTitle = 'گفتگو — ' . $course['title'];
$activeMenu = 'messages';
$messages = course_messages_list($courseId);
$chatPostUrl = base_url('teacher/chat.php?course_id=' . $courseId);
$chatApiUrl = base_url('api/chat_messages.php?course_id=' . $courseId);
$chatUserId = $teacherId;
$chatCourseId = $courseId;
$chatMessages = $messages;

require dirname(__DIR__) . '/includes/layout/teacher_header.php';
?>

<p class="mb-3"><a href="<?= e(base_url('teacher/messages.php')) ?>">&larr; همه پیام‌ها</a></p>
<h1 class="h4 mb-3">گفتگو با دانشجویان — <?= e($course['title']) ?></h1>
<?php if ($msg = flash('error')): ?><div class="alert alert-danger"><?= e($msg) ?></div><?php endif; ?>

<p class="small text-muted">پیام‌ها برای دانشجویان ثبت‌نام‌شده در این دوره نمایش داده می‌شود.</p>
<?php require dirname(__DIR__) . '/includes/layout/chat_panel.php'; ?>

<?php require dirname(__DIR__) . '/includes/layout/teacher_footer.php'; ?>