<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_student_auth();

$pageTitle = 'کلاس‌های آنلاین';
$activeMenu = 'live';
$userId = (int) auth_id();
$filter = $_GET['filter'] ?? 'upcoming';
$allowed = ['upcoming', 'today', 'all', 'past'];
if (!in_array($filter, $allowed, true)) {
    $filter = 'upcoming';
}

$sessions = student_live_sessions_all($userId, $filter === 'all' ? null : $filter);

require dirname(__DIR__) . '/includes/layout/student_header.php';
?>

<h1 class="h3 text-primary mb-4">کلاس‌های آنلاین (Adobe Connect)</h1>

<?php if (!phase6_tables_ready()): ?>
    <div class="alert alert-warning">ابتدا <a href="<?= e(base_url('migrate_phase6.php')) ?>">migrate_phase6.php</a> را اجرا کنید.</div>
<?php endif; ?>

<ul class="nav nav-pills mb-4 flex-wrap gap-1">
    <?php foreach (['upcoming' => 'پیش‌رو', 'today' => 'امروز', 'all' => 'همه', 'past' => 'گذشته'] as $k => $label): ?>
        <li class="nav-item">
            <a class="nav-link <?= $filter === $k ? 'active' : '' ?>" href="?filter=<?= e($k) ?>"><?= e($label) ?></a>
        </li>
    <?php endforeach; ?>
</ul>

<?php if (!$sessions): ?>
    <div class="alert alert-info">جلسه آنلاینی در این بازه یافت نشد.</div>
<?php else: ?>
    <div class="row g-3">
        <?php foreach ($sessions as $ls): ?>
            <?php
            $status = live_session_status($ls);
            $cardClass = $status === 'live' ? 'live-now' : ($status === 'upcoming' ? 'upcoming-soon' : '');
            ?>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm live-session-card <?= e($cardClass) ?> h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h2 class="h6 mb-0"><?= e($ls['title']) ?></h2>
                            <span class="badge <?= live_session_status_badge($status) ?>"><?= e(live_session_status_label($status)) ?></span>
                        </div>
                        <p class="small text-muted mb-1">دوره: <?= e($ls['course_title']) ?></p>
                        <p class="small mb-2">
                            <i class="bi bi-calendar-event"></i> <?= e(format_datetime($ls['scheduled_at'])) ?>
                            — <?= (int) $ls['duration_minutes'] ?> دقیقه
                        </p>
                        <?php if (!empty($ls['notes'])): ?>
                            <p class="small"><?= nl2br(e($ls['notes'])) ?></p>
                        <?php endif; ?>
                        <div class="d-flex gap-2 flex-wrap">
                            <?php if ($status === 'live' || $status === 'upcoming'): ?>
                                <a href="<?= e($ls['adobe_connect_url']) ?>" class="btn btn-primary btn-sm" target="_blank" rel="noopener">
                                    <i class="bi bi-camera-video"></i> ورود به کلاس
                                </a>
                            <?php endif; ?>
                            <a href="<?= e(base_url('student/my_course.php?slug=' . urlencode($ls['course_slug']) . '&tab=live')) ?>" class="btn btn-outline-secondary btn-sm">صفحه دوره</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require dirname(__DIR__) . '/includes/layout/student_footer.php'; ?>
