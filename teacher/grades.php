<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_teacher_approved();

$courseId = (int) ($_GET['course_id'] ?? 0);
$teacherId = (int) auth_id();
$courseStmt = db()->prepare('SELECT * FROM courses WHERE id = ? AND teacher_id = ?');
$courseStmt->execute([$courseId, $teacherId]);
$course = $courseStmt->fetch();

if (!$course) {
    flash('error', 'دوره یافت نشد.');
    redirect(base_url('teacher/courses.php'));
}

if (!lms_tables_ready()) {
    require dirname(__DIR__) . '/includes/layout/teacher_header.php';
    echo '<div class="alert alert-warning">ابتدا migrate_phase5.php را اجرا کنید.</div>';
    require dirname(__DIR__) . '/includes/layout/teacher_footer.php';
    exit;
}

if (is_post() && verify_csrf()) {
    $action = $_POST['action'] ?? 'save_grades';
    if ($action === 'apply_suggested') {
        foreach ($_POST['suggested'] ?? [] as $enrollmentId => $grade) {
            $g = (float) $grade;
            $status = $g >= (float) $course['min_pass_grade'] ? 'completed' : 'active';
            db()->prepare("UPDATE enrollments SET final_grade = ?, status = ? WHERE id = ? AND course_id = ?")->execute([$g, $status, (int) $enrollmentId, $courseId]);
        }
        flash('success', 'نمرات پیشنهادی اعمال شد.');
    } else {
        foreach ($_POST['final_grade'] ?? [] as $enrollmentId => $grade) {
            $grade = trim((string) $grade);
            $g = $grade === '' ? null : (float) $grade;
            $status = ($g !== null && $g >= (float) $course['min_pass_grade']) ? 'completed' : 'active';
            db()->prepare("UPDATE enrollments SET final_grade = ?, status = ? WHERE id = ? AND course_id = ?")->execute([$g, $status, (int) $enrollmentId, $courseId]);
        }
        flash('success', 'نمرات نهایی ذخیره شد.');
    }
    redirect(base_url('teacher/grades.php?course_id=' . $courseId));
}

$list = enrolled_students_for_course($courseId);
$rows = [];
foreach ($list as $row) {
    $uid = (int) $row['id'];
    $avgAssignments = student_assignment_average($uid, $courseId);
    $attendance = student_attendance_percent($uid, $courseId);
    $suggested = null;
    if ($avgAssignments !== null && $attendance !== null) {
        $suggested = round($avgAssignments * 0.7 + $attendance * 0.3, 2);
    } elseif ($avgAssignments !== null) {
        $suggested = $avgAssignments;
    }
    $rows[] = array_merge($row, [
        'assignment_avg' => $avgAssignments,
        'attendance_pct' => $attendance,
        'suggested_grade' => $suggested,
    ]);
}

require dirname(__DIR__) . '/includes/layout/teacher_header.php';
?>

<p><a href="courses.php" class="text-decoration-none">&larr; بازگشت به دوره‌ها</a></p>
<h1 class="h4 mb-3">نمره نهایی - <?= e($course['title']) ?></h1>
<p class="small text-muted">حداقل نمره قبولی: <?= e($course['min_pass_grade']) ?> - پیشنهاد سیستم: ۷۰٪ میانگین تکالیف + ۳۰٪ حضور</p>

<?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>

<form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="save_grades">

    <div class="table-responsive bg-white rounded shadow-sm mb-3">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>دانشجو</th>
                    <th>میانگین تکالیف</th>
                    <th>حضور %</th>
                    <th>پیشنهاد</th>
                    <th>نمره نهایی</th>
                    <th>وضعیت</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?= e($row['full_name']) ?></td>
                    <td><?= $row['assignment_avg'] !== null ? e($row['assignment_avg']) . '%' : '---' ?></td>
                    <td><?= $row['attendance_pct'] !== null ? e($row['attendance_pct']) . '%' : '---' ?></td>
                    <td>
                        <?php if ($row['suggested_grade'] !== null): ?>
                            <span class="badge bg-secondary"><?= e($row['suggested_grade']) ?></span>
                            <input type="hidden" name="suggested[<?= (int) $row['enrollment_id'] ?>]" value="<?= e($row['suggested_grade']) ?>">
                        <?php else: ?>
                            ---
                        <?php endif; ?>
                    </td>
                    <td style="min-width:100px">
                        <input type="number" step="0.01" name="final_grade[<?= (int) $row['enrollment_id'] ?>]" class="form-control form-control-sm" value="<?= e($row['final_grade'] ?? '') ?>" placeholder="<?= $row['suggested_grade'] !== null ? e((string) $row['suggested_grade']) : '' ?>">
                    </td>
                    <td class="small"><?= e(enrollment_status_label($row['enrollment_status'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <button type="submit" class="btn btn-primary">ذخیره نمرات نهایی</button>
</form>

<?php if (array_filter($rows, fn($r) => $r['suggested_grade'] !== null)): ?>
    <form method="post" class="mt-2" onsubmit="return confirm('نمرات پیشنهادی برای همه اعمال شود؟')">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="apply_suggested">
        <?php foreach ($rows as $row): ?>
            <?php if ($row['suggested_grade'] !== null): ?>
                <input type="hidden" name="suggested[<?= (int) $row['enrollment_id'] ?>]" value="<?= e($row['suggested_grade']) ?>">
            <?php endif; ?>
        <?php endforeach; ?>
        <button type="submit" class="btn btn-outline-secondary btn-sm">اعمال خودکار نمرات پیشنهادی</button>
    </form>
<?php endif; ?>

<?php require dirname(__DIR__) . '/includes/layout/teacher_footer.php'; ?>