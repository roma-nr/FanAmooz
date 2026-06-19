<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_student_auth();

$pageTitle = 'تکالیف من';
$activeMenu = 'assignments';
$userId = (int) auth_id();
$filter = $_GET['filter'] ?? 'all';
$allowedFilters = ['all', 'pending', 'submitted', 'graded', 'overdue'];
if (!in_array($filter, $allowedFilters, true)) {
    $filter = 'all';
}

$items = student_all_assignments($userId, $filter === 'all' ? null : $filter);

if (!lms_tables_ready()) {
    $migrationNeeded = true;
} else {
    $migrationNeeded = false;
}

require dirname(__DIR__) . '/includes/layout/student_header.php';
?>

<h1 class="h3 text-primary mb-4">تکالیف من</h1>

<?php if ($migrationNeeded): ?>
    <div class="alert alert-warning">
        جداول فاز ۵ هنوز نصب نشده‌اند. مدیر سیستم باید
        <a href="<?= e(base_url('migrate_phase5.php')) ?>">migrate_phase5.php</a> را اجرا کند.
    </div>
<?php endif; ?>

<?php if ($msg = flash('success')): ?>
    <div class="alert alert-success alert-dismissible fade show"><?= e($msg) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<ul class="nav nav-pills mb-4 flex-wrap gap-1">
    <?php
    $filters = [
        'all' => 'همه',
        'pending' => 'در انتظار ارسال',
        'submitted' => 'در انتظار نمره',
        'graded' => 'نمره‌دهی شده',
        'overdue' => 'مهلت گذشته',
    ];
    foreach ($filters as $key => $label):
    ?>
    <li class="nav-item">
        <a class="nav-link <?= $filter === $key ? 'active' : '' ?>" href="?filter=<?= e($key) ?>"><?= e($label) ?></a>
    </li>
    <?php endforeach; ?>
</ul>

<?php if (!$items): ?>
    <div class="alert alert-info">تکلیفی در این دسته یافت نشد.</div>
<?php else: ?>
    <div class="row g-3">
        <?php foreach ($items as $row): ?>
            <?php
            $sub = $row['_submission'];
            $assignment = $row;
            $status = submission_status_label($sub, $assignment);
            $badge = submission_status_badge_class($sub, $assignment);
            ?>
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-2">
                            <div>
                                <h2 class="h6 mb-1"><?= e($row['title']) ?></h2>
                                <p class="small text-muted mb-0">
                                    دوره: <a href="<?= e(base_url('student/my_course.php?slug=' . urlencode($row['course_slug']) . '&tab=assignments')) ?>"><?= e($row['course_title']) ?></a>
                                </p>
                            </div>
                            <span class="badge <?= e($badge) ?>"><?= e($status) ?></span>
                        </div>
                        <p class="small mb-2">
                            مهلت: <?= $row['due_at'] ? e(format_datetime($row['due_at'])) : 'بدون مهلت' ?>
                            — حداکثر نمره: <?= e($row['max_score']) ?>
                        </p>
                        <?php if ($sub && $sub['score'] !== null): ?>
                            <p class="mb-2"><strong>نمره:</strong> <?= e($sub['score']) ?> از <?= e($row['max_score']) ?>
                                <?php if ($sub['feedback']): ?> — <span class="text-muted"><?= e($sub['feedback']) ?></span><?php endif; ?>
                            </p>
                        <?php endif; ?>
                        <a href="<?= e(base_url('student/my_course.php?slug=' . urlencode($row['course_slug']) . '&tab=assignments')) ?>" class="btn btn-sm btn-primary">
                            <?= $sub ? 'مشاهده / ویرایش پاسخ' : 'ارسال پاسخ' ?>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require dirname(__DIR__) . '/includes/layout/student_footer.php'; ?>
