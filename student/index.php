<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_student_auth();

$pageTitle = 'داشبورد';
$activeMenu = 'dashboard';
$userId = auth_id();

$recommended = student_recommended_courses($userId, 6);
$enrolled = student_enrolled_courses($userId);
$nextLive = student_next_live_session($userId);
$unreadMessages = student_total_unread_messages($userId);

require dirname(__DIR__) . '/includes/layout/student_header.php';
?>

<?php if ($msg = flash('success')): ?>
    <div class="alert alert-success alert-dismissible fade show"><?= e($msg) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<h1 class="h3 text-primary mb-4">داشبورد دانشجو</h1>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card stat-card p-3 border-0">
            <div class="text-muted small">دوره‌های ثبت‌نام‌شده</div>
            <div class="stat-value"><?= count($enrolled) ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <a href="<?= e(base_url('student/assignments.php?filter=pending')) ?>" class="text-decoration-none text-reset">
            <div class="card stat-card p-3 border-0 h-100">
                <div class="text-muted small">تکالیف در انتظار</div>
                <div class="stat-value"><?= student_pending_assignments_count($userId) ?></div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="<?= e(base_url('student/messages.php')) ?>" class="text-decoration-none text-reset">
            <div class="card stat-card p-3 border-0 h-100">
                <div class="text-muted small">پیام‌های خوانده‌نشده</div>
                <div class="stat-value"><?= $unreadMessages ?></div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="<?= e(base_url('student/live_classes.php')) ?>" class="text-decoration-none text-reset">
            <div class="card stat-card p-3 border-0 h-100">
                <div class="text-muted small">کلاس آنلاین بعدی</div>
                <div class="stat-value" style="font-size:1rem"><?= $nextLive ? e(format_datetime($nextLive['scheduled_at'])) : '—' ?></div>
            </div>
        </a>
    </div>
</div>

<?php if ($nextLive): ?>
    <?php $nlStatus = live_session_status($nextLive); ?>
    <div class="alert alert-<?= $nlStatus === 'live' ? 'success' : 'primary' ?> d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <strong><?= $nlStatus === 'live' ? 'کلاس در حال برگزاری است!' : 'کلاس آنلاین پیش‌رو' ?></strong>
            — <?= e($nextLive['title']) ?> (<?= e($nextLive['course_title']) ?>)
            <span class="small d-block"><?= e(format_datetime($nextLive['scheduled_at'])) ?></span>
        </div>
        <?php if ($nlStatus === 'live' || $nlStatus === 'upcoming'): ?>
            <a href="<?= e($nextLive['adobe_connect_url']) ?>" class="btn btn-<?= $nlStatus === 'live' ? 'success' : 'primary' ?> btn-sm" target="_blank" rel="noopener">ورود به کلاس</a>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php
$pendingAssignments = student_all_assignments($userId, 'pending');
if ($pendingAssignments):
?>
<section class="mb-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h5 section-title mb-0">تکالیف فوری</h2>
        <a href="<?= e(base_url('student/assignments.php')) ?>" class="btn btn-sm btn-outline-primary">همه تکالیف</a>
    </div>
    <div class="list-group">
        <?php foreach (array_slice($pendingAssignments, 0, 5) as $pa): ?>
            <a href="<?= e(base_url('student/my_course.php?slug=' . urlencode($pa['course_slug']) . '&tab=assignments')) ?>"
               class="list-group-item list-group-item-action">
                <div class="d-flex justify-content-between">
                    <strong class="small"><?= e($pa['title']) ?></strong>
                    <span class="badge bg-warning text-dark">ارسال نشده</span>
                </div>
                <span class="small text-muted"><?= e($pa['course_title']) ?> — مهلت: <?= $pa['due_at'] ? e(format_datetime($pa['due_at'])) : 'بدون مهلت' ?></span>
            </a>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<section class="mb-5">
    <h2 class="h5 section-title">دوره‌های پیشنهادی برای شما</h2>
    <div class="row g-3">
        <?php foreach ($recommended as $c): ?>
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <h3 class="h6 card-title"><?= e($c['title']) ?></h3>
                    <p class="small text-muted mb-2"><?= e($c['category_name'] ?? '') ?></p>
                    <p class="mb-2">
                        <?php if ((int)$c['is_paid']): ?>
                            <span class="badge badge-paid"><?= e(format_price($c['price'])) ?></span>
                        <?php else: ?>
                            <span class="badge badge-free">رایگان</span>
                        <?php endif; ?>
                    </p>
                    <a href="<?= e(base_url('course.php?slug=' . urlencode($c['slug']))) ?>" class="btn btn-sm btn-outline-primary">مشاهده</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php if (!$recommended): ?>
            <p class="text-muted">دوره پیشنهادی یافت نشد.</p>
        <?php endif; ?>
    </div>
</section>

<section>
    <h2 class="h5 section-title">دوره‌های من</h2>
    <?php if ($enrolled): ?>
    <div class="table-responsive bg-white rounded shadow-sm">
        <table class="table table-hover mb-0">
            <thead class="table-light"><tr><th>دوره</th><th>وضعیت</th><th>نمره</th></tr></thead>
            <tbody>
            <?php foreach ($enrolled as $c): ?>
                <tr>
                    <td><a href="<?= e(base_url('student/my_course.php?slug=' . urlencode($c['slug']))) ?>"><?= e($c['title']) ?></a></td>
                    <td><?= e(enrollment_status_label($c['enrollment_status'])) ?></td>
                    <td><?= $c['final_grade'] !== null ? e($c['final_grade']) : '—' ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
        <p class="text-muted">هنوز در دوره‌ای ثبت‌نام نکرده‌اید. از بخش <a href="<?= e(base_url('student/courses.php')) ?>">دوره‌ها</a> اقدام کنید.</p>
    <?php endif; ?>
</section>

<?php require dirname(__DIR__) . '/includes/layout/student_footer.php'; ?>
