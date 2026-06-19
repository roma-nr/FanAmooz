<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_teacher_approved();

$pageTitle = 'پیام‌های دوره‌ها';
$activeMenu = 'messages';
$teacherId = (int) auth_id();
$courses = user_chat_courses($teacherId, 'teacher');
$totalUnread = teacher_total_unread_messages($teacherId);

require dirname(__DIR__) . '/includes/layout/teacher_header.php';
?>

<h1 class="h3 text-primary mb-4">
    پیام‌های دوره‌ها
    <?php if ($totalUnread > 0): ?>
        <span class="badge bg-danger"><?= $totalUnread ?> جدید</span>
    <?php endif; ?>
</h1>

<?php if (!phase6_tables_ready()): ?>
    <div class="alert alert-warning">ابتدا migrate_phase6.php را اجرا کنید.</div>
<?php elseif (!$courses): ?>
    <div class="alert alert-info">هنوز دوره‌ای ندارید یا پیامی ثبت نشده است.</div>
<?php else: ?>
    <div class="list-group shadow-sm">
        <?php foreach ($courses as $c): ?>
            <a href="<?= e(base_url('teacher/chat.php?course_id=' . (int) $c['id'])) ?>"
               class="list-group-item list-group-item-action d-flex justify-content-between align-items-start">
                <div>
                    <div class="fw-bold"><?= e($c['title']) ?></div>
                    <?php if ($c['last_body']): ?>
                        <div class="small text-muted text-truncate" style="max-width:400px"><?= e(mb_substr((string) $c['last_body'], 0, 80)) ?></div>
                        <div class="small text-muted"><?= $c['last_at'] ? e(format_datetime($c['last_at'])) : '' ?></div>
                    <?php endif; ?>
                </div>
                <?php if ((int) $c['unread_count'] > 0): ?>
                    <span class="badge bg-danger"><?= (int) $c['unread_count'] ?></span>
                <?php endif; ?>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require dirname(__DIR__) . '/includes/layout/teacher_footer.php'; ?>
