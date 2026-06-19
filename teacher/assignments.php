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

$pageTitle = 'تکالیف - ' . $course['title'];
$activeMenu = 'courses';
$errors = [];
$editId = (int) ($_GET['edit'] ?? 0);
$editAssignment = $editId > 0 ? assignment_by_id($editId, $courseId) : null;

if (!lms_tables_ready()) {
    require dirname(__DIR__) . '/includes/layout/teacher_header.php';
    echo '<div class="alert alert-warning">ابتدا migrate_phase5.php را اجرا کنید.</div>';
    require dirname(__DIR__) . '/includes/layout/teacher_footer.php';
    exit;
}

if (is_post() && verify_csrf()) {
    $action = $_POST['action'] ?? '';
    try {
        if ($action === 'delete') {
            db()->prepare('DELETE FROM assignments WHERE id = ? AND course_id = ?')->execute([(int) $_POST['id'], $courseId]);
            flash('success', 'تکلیف حذف شد.');
            redirect(base_url('teacher/assignments.php?course_id=' . $courseId));
        }

        if ($action === 'grade_submission') {
            $subId = (int) ($_POST['submission_id'] ?? 0);
            $score = trim($_POST['score'] ?? '');
            $scoreVal = $score === '' ? null : (float) $score;
            db()->prepare("UPDATE assignment_submissions s INNER JOIN assignments a ON a.id = s.assignment_id AND a.course_id = ? SET s.score=?, s.feedback=?, s.graded_at=NOW(), s.graded_by=? WHERE s.id=?")->execute([
                $courseId, $scoreVal, trim($_POST['feedback'] ?? ''), $teacherId, $subId
            ]);
            flash('success', 'نمره ثبت شد.');
            redirect(base_url('teacher/assignments.php?course_id=' . $courseId));
        }

        $title = trim($_POST['title'] ?? '');
        $dueAt = trim($_POST['due_at'] ?? '') ?: null;
        $description = trim($_POST['description'] ?? '');
        $maxScore = (float) ($_POST['max_score'] ?? 100);
        $id = (int) ($_POST['id'] ?? 0);

        if ($title === '') {
            $errors['title'] = 'عنوان الزامی است.';
        }

        if (empty($errors)) {
            if ($id > 0) {
                db()->prepare("UPDATE assignments SET title=?, description=?, due_at=?, max_score=? WHERE id=? AND course_id=?")->execute([$title, $description, $dueAt, $maxScore, $id, $courseId]);
                flash('success', 'تکلیف ویرایش شد.');
            } else {
                db()->prepare("INSERT INTO assignments (course_id, title, description, due_at, max_score, created_by) VALUES (?,?,?,?,?,?)")->execute([$courseId, $title, $description, $dueAt, $maxScore, $teacherId]);
                flash('success', 'تکلیف ایجاد شد.');
            }
            redirect(base_url('teacher/assignments.php?course_id=' . $courseId));
        }
    } catch (Throwable $e) {
        $errors['general'] = $e->getMessage();
    }
}

$assignments = course_assignments($courseId);
$enrolledStudents = enrolled_students_for_course($courseId);

require dirname(__DIR__) . '/includes/layout/teacher_header.php';
?>

<p><a href="courses.php" class="text-decoration-none">&larr; بازگشت به دوره‌ها</a></p>
<h1 class="h4 mb-3">مدیریت تکالیف - <?= e($course['title']) ?></h1>

<?php if (!empty($errors['general'])): ?><div class="alert alert-danger"><?= e($errors['general']) ?></div><?php endif; ?>
<?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h2 class="h6 mb-3"><?= $editAssignment ? 'ویرایش تکلیف' : 'تکلیف جدید' ?></h2>
                <form method="post" novalidate>
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= (int) ($editAssignment['id'] ?? 0) ?>">

                    <div class="mb-2">
                        <label class="form-label">عنوان <span class="text-danger">*</span></label>
                        <input name="title" class="form-control <?= isset($errors['title']) ? 'is-invalid' : '' ?>" value="<?= old('title', $editAssignment['title'] ?? '') ?>">
                        <div class="invalid-feedback"><?= $errors['title'] ?? '' ?></div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">توضیحات</label>
                        <textarea name="description" class="form-control tinymce" rows="3"><?= old('description', $editAssignment['description'] ?? '') ?></textarea>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">مهلت ارسال</label>
                        <input type="datetime-local" name="due_at" class="form-control" value="<?= $editAssignment && $editAssignment['due_at'] ? date('Y-m-d\TH:i', strtotime($editAssignment['due_at'])) : '' ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">حداکثر نمره</label>
                        <input type="number" step="0.01" name="max_score" class="form-control" value="<?= old('max_score', $editAssignment['max_score'] ?? 100) ?>">
                    </div>

                    <button class="btn btn-primary btn-sm"><?= $editAssignment ? 'ذخیره' : 'افزودن' ?></button>
                    <?php if ($editAssignment): ?>
                        <a href="assignments.php?course_id=<?= $courseId ?>" class="btn btn-link btn-sm">انصراف</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <?php foreach ($assignments as $a): ?>
            <?php $submissions = assignment_submissions_for_assignment((int) $a['id']); ?>
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <strong><?= e($a['title']) ?></strong>
                        <span class="small text-muted d-block">
                            مهلت: <?= $a['due_at'] ? e(format_datetime($a['due_at'])) : 'بدون مهلت' ?>
                            - نمره از <?= e($a['max_score']) ?>
                        </span>
                    </div>
                    <div class="btn-group btn-group-sm">
                        <a href="?course_id=<?= $courseId ?>&edit=<?= (int) $a['id'] ?>" class="btn btn-outline-secondary">ویرایش</a>
                        <form method="post" class="d-inline" onsubmit="return confirm('حذف تکلیف؟')">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= (int) $a['id'] ?>">
                            <button class="btn btn-outline-danger">حذف</button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <?php if ($a['description']): ?>
                        <p class="small text-muted"><?= nl2br(e($a['description'])) ?></p>
                    <?php endif; ?>

                    <h4 class="h6">ارسال‌های دانشجو (<?= count($submissions) ?>)</h4>
                    <?php if ($submissions): ?>
                        <?php foreach ($submissions as $s): ?>
                            <div class="border rounded p-2 mb-2 small">
                                <strong><?= e($s['full_name']) ?></strong>
                                <span class="text-muted"> - <?= e(format_datetime($s['submitted_at'])) ?></span>
                                <?php if ($s['body_text']): ?>
                                    <p class="mb-1 mt-1"><?= nl2br(e($s['body_text'])) ?></p>
                                <?php endif; ?>
                                <?php if ($s['file_path']): ?>
                                    <a href="<?= e(upload_url($s['file_path'])) ?>" target="_blank" class="btn btn-outline-primary btn-sm">دانلود فایل</a>
                                <?php endif; ?>
                                <form method="post" class="row g-2 mt-2 align-items-end">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="grade_submission">
                                    <input type="hidden" name="submission_id" value="<?= (int) $s['id'] ?>">
                                    <div class="col-md-3">
                                        <label class="form-label small">نمره</label>
                                        <input name="score" type="number" step="0.01" max="<?= e($a['max_score']) ?>" class="form-control form-control-sm" value="<?= e($s['score'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small">بازخورد</label>
                                        <input name="feedback" class="form-control form-control-sm" value="<?= e($s['feedback'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <button class="btn btn-primary btn-sm w-100">ثبت نمره</button>
                                    </div>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="small text-muted">هنوز ارسالی نیست.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if (!$assignments): ?>
            <div class="alert alert-info">هنوز تکلیفی تعریف نکرده‌اید.</div>
        <?php endif; ?>
    </div>
</div>

<?php require dirname(__DIR__) . '/includes/layout/teacher_footer.php'; ?>