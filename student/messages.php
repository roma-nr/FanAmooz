<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_student_auth();

$pageTitle = 'پیام‌ها';
$activeMenu = 'messages';
$userId = (int) auth_id();
$courses = user_chat_courses($userId, 'student');
$totalUnread = student_total_unread_messages($userId);

require dirname(__DIR__) . '/includes/layout/student_header.php';
?>

<h1 class="h3 text-primary mb-4">
    پیام‌ها
    <?php if ($totalUnread > 0): ?>
        <span class="badge bg-danger"><?= $totalUnread ?> جدید</span>
    <?php endif; ?>
</h1>

<?php if (!phase6_tables_ready()): ?>
    <div class="alert alert-warning">ابتدا <a href="<?= e(base_url('migrate_phase6.php')) ?>">migrate_phase6.php</a> را اجرا کنید.</div>
<?php elseif (!$courses): ?>
    <div class="alert alert-info">در دوره فعالی ثبت‌نام نکرده‌اید یا هنوز پیامی رد و بدل نشده است.</div>
<?php else: ?>
    <div class="list-group shadow-sm">
        <?php foreach ($courses as $c): ?>
            <a href="<?= e(base_url('student/chat.php?course_id=' . (int) $c['id'])) ?>"
               class="list-group-item list-group-item-action d-flex justify-content-between align-items-start gap-3">
                <div class="flex-grow-1">
                    <div class="fw-bold"><?= e($c['title']) ?></div>
                    <div class="small text-muted">استاد: <?= e($c['teacher_name'] ?? '—') ?></div>
                    <?php if ($c['last_body']): ?>
                        <div class="small text-truncate mt-1" style="max-width:100%"><?= e(mb_substr(strip_tags((string) $c['last_body']), 0, 80)) ?></div>
                        <div class="small text-muted"><?= $c['last_at'] ? e(format_datetime($c['last_at'])) : '' ?></div>
                    <?php else: ?>
                        <div class="small text-muted mt-1">شروع گفتگو با استاد</div>
                    <?php endif; ?>
                </div>
                <?php if ((int) $c['unread_count'] > 0): ?>
                    <span class="badge bg-danger rounded-pill"><?= (int) $c['unread_count'] ?></span>
                <?php endif; ?>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require dirname(__DIR__) . '/includes/layout/student_footer.php'; ?>
