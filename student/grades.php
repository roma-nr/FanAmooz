<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_student_auth();

$pageTitle = 'نمرات و حضور';
$activeMenu = 'grades';
$userId = (int) auth_id();
$enrolled = student_enrolled_courses($userId);

$details = [];
foreach ($enrolled as $c) {
    if (!in_array($c['enrollment_status'], ['active', 'completed'], true)) {
        continue;
    }
    $courseId = (int) $c['id'];
    $details[] = [
        'course' => $c,
        'assignment_avg' => student_assignment_average($userId, $courseId),
        'attendance_pct' => student_attendance_percent($userId, $courseId),
        'attendance_rows' => student_attendance_by_course($userId, $courseId),
        'assignments' => course_assignments($courseId),
    ];
}

require dirname(__DIR__) . '/includes/layout/student_header.php';
?>

<h1 class="h3 text-primary mb-4">نمرات و حضور و غیاب</h1>

<?php if (!lms_tables_ready()): ?>
    <div class="alert alert-warning">برای نمایش نمرات تکالیف، <a href="<?= e(base_url('migrate_phase5.php')) ?>">migrate_phase5.php</a> را اجرا کنید.</div>
<?php endif; ?>

<?php if (!$details): ?>
    <div class="alert alert-info">در دوره فعالی ثبت‌نام نکرده‌اید.</div>
<?php else: ?>
    <?php foreach ($details as $block): ?>
        <?php $c = $block['course']; ?>
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h2 class="h6 mb-0">
                    <a href="<?= e(base_url('student/my_course.php?slug=' . urlencode($c['slug']))) ?>"><?= e($c['title']) ?></a>
                </h2>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <div class="p-3 rounded bg-light">
                            <div class="small text-muted">نمره نهایی دوره</div>
                            <div class="fs-4 fw-bold text-primary">
                                <?= $c['final_grade'] !== null ? e($c['final_grade']) : '—' ?>
                            </div>
                            <div class="small">حداقل قبولی: <?= e($c['min_pass_grade']) ?></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded bg-light">
                            <div class="small text-muted">میانگین تکالیف (%)</div>
                            <div class="fs-4 fw-bold"><?= $block['assignment_avg'] !== null ? e($block['assignment_avg']) . '%' : '—' ?></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded bg-light">
                            <div class="small text-muted">درصد حضور</div>
                            <div class="fs-4 fw-bold"><?= $block['attendance_pct'] !== null ? e($block['attendance_pct']) . '%' : '—' ?></div>
                        </div>
                    </div>
                </div>

                <?php if ($block['assignments']): ?>
                <h3 class="h6">تکالیف</h3>
                <div class="table-responsive mb-3">
                    <table class="table table-sm table-bordered mb-0">
                        <thead class="table-light"><tr><th>عنوان</th><th>مهلت</th><th>نمره</th><th>وضعیت</th></tr></thead>
                        <tbody>
                        <?php foreach ($block['assignments'] as $a): ?>
                            <?php $sub = assignment_submission((int) $a['id'], $userId); ?>
                            <tr>
                                <td><?= e($a['title']) ?></td>
                                <td class="small"><?= $a['due_at'] ? e(format_datetime($a['due_at'])) : '—' ?></td>
                                <td><?= $sub && $sub['score'] !== null ? e($sub['score']) . ' / ' . e($a['max_score']) : '—' ?></td>
                                <td><span class="badge <?= submission_status_badge_class($sub, $a) ?>"><?= e(submission_status_label($sub, $a)) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>

                <?php if ($block['attendance_rows']): ?>
                <h3 class="h6">حضور در جلسات</h3>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered mb-0">
                        <thead class="table-light"><tr><th>جلسه</th><th>عنوان</th><th>وضعیت</th></tr></thead>
                        <tbody>
                        <?php foreach ($block['attendance_rows'] as $ar): ?>
                            <tr>
                                <td><?= (int) $ar['session_number'] ?></td>
                                <td><?= e($ar['title']) ?></td>
                                <td>
                                    <?php if ($ar['present'] === null): ?>
                                        <span class="text-muted">ثبت نشده</span>
                                    <?php elseif ((int) $ar['present'] === 1): ?>
                                        <span class="text-success">حاضر</span>
                                    <?php else: ?>
                                        <span class="text-danger">غایب</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php require dirname(__DIR__) . '/includes/layout/student_footer.php'; ?>
