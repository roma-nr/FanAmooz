<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_teacher_approved();

$courseId = (int) ($_GET['course_id'] ?? 0);
$sessionId = (int) ($_GET['session_id'] ?? 0);
$view = $_GET['view'] ?? 'session';
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

$sessions = course_sessions_list($courseId);
$students = enrolled_students_for_course($courseId);

if ($sessionId <= 0 && $sessions && $view === 'session') {
    $sessionId = (int) $sessions[0]['id'];
}

$session = null;
foreach ($sessions as $s) {
    if ((int) $s['id'] === $sessionId) {
        $session = $s;
        break;
    }
}

$attendanceMatrix = [];
if ($view === 'overview' && $sessions && $students) {
    foreach ($students as $st) {
        $uid = (int) $st['id'];
        $attendanceMatrix[$uid] = ['name' => $st['full_name'], 'sessions' => []];
        foreach ($sessions as $sess) {
            $sid = (int) $sess['id'];
            $q = db()->prepare('SELECT present FROM session_attendance WHERE course_session_id = ? AND user_id = ? LIMIT 1');
            $q->execute([$sid, $uid]);
            $present = $q->fetchColumn();
            $attendanceMatrix[$uid]['sessions'][$sid] = $present === false ? null : (int) $present;
        }
    }
}

if ($view === 'session' && $session) {
    $attRows = db()->prepare(
        "SELECT u.id, u.full_name,
                (SELECT present FROM session_attendance WHERE course_session_id = ? AND user_id = u.id LIMIT 1) AS present
         FROM enrollments e
         JOIN users u ON u.id = e.user_id
         WHERE e.course_id = ? AND e.status IN ('active','completed')
         ORDER BY u.full_name"
    );
    $attRows->execute([$sessionId, $courseId]);
    $sessionStudents = $attRows->fetchAll();
} else {
    $sessionStudents = [];
}

if (is_post() && verify_csrf()) {
    $action = $_POST['action'] ?? 'save';
    if ($action === 'mark_all' && $session) {
        $present = ($_POST['mark'] ?? '') === 'present' ? 1 : 0;
        foreach ($sessionStudents as $st) {
            db()->prepare(
                'INSERT INTO session_attendance (course_session_id, user_id, present, recorded_by)
                 VALUES (?,?,?,?)
                 ON DUPLICATE KEY UPDATE present = VALUES(present), recorded_by = VALUES(recorded_by), recorded_at = NOW()'
            )->execute([$sessionId, (int) $st['id'], $present, $teacherId]);
        }
        flash('success', $present ? 'همه حاضر ثبت شدند.' : 'همه غایب ثبت شدند.');
        redirect(base_url('teacher/attendance.php?course_id=' . $courseId . '&session_id=' . $sessionId));
    }

    if ($action === 'save' && $session && $sessionStudents) {
        foreach ($sessionStudents as $st) {
            $uid = (int) $st['id'];
            $present = isset($_POST['present'][$uid]) ? 1 : 0;
            db()->prepare(
                'INSERT INTO session_attendance (course_session_id, user_id, present, recorded_by)
                 VALUES (?,?,?,?)
                 ON DUPLICATE KEY UPDATE present = VALUES(present), recorded_by = VALUES(recorded_by), recorded_at = NOW()'
            )->execute([$sessionId, $uid, $present, $teacherId]);
        }
        flash('success', 'حضور و غیاب ذخیره شد.');
        redirect(base_url('teacher/attendance.php?course_id=' . $courseId . '&session_id=' . $sessionId));
    }
}

require dirname(__DIR__) . '/includes/layout/teacher_header.php';
?>

<p><a href="courses.php">&larr; دوره‌ها</a></p>
<h1 class="h4 mb-3">حضور و غیاب — <?= e($course['title']) ?></h1>
<?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>

<ul class="nav nav-tabs mb-3">
    <li class="nav-item">
        <a class="nav-link <?= $view === 'session' ? 'active' : '' ?>" href="?course_id=<?= $courseId ?>&view=session">ثبت جلسه</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $view === 'overview' ? 'active' : '' ?>" href="?course_id=<?= $courseId ?>&view=overview">نمای کلی</a>
    </li>
</ul>

<?php if (!$sessions): ?>
    <div class="alert alert-warning">ابتدا در بخش <a href="sessions.php?course_id=<?= $courseId ?>">جلسات</a> محتوا تعریف کنید.</div>
<?php elseif ($view === 'overview'): ?>
    <div class="table-responsive bg-white rounded shadow-sm">
        <table class="table table-sm table-bordered mb-0 text-center">
            <thead class="table-light">
                <tr>
                    <th class="text-start">دانشجو</th>
                    <?php foreach ($sessions as $sess): ?>
                        <th title="<?= e($sess['title']) ?>">ج<?= (int) $sess['session_number'] ?></th>
                    <?php endforeach; ?>
                    <th>حضور %</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($attendanceMatrix as $uid => $data): ?>
                <?php $pct = student_attendance_percent($uid, $courseId); ?>
                <tr>
                    <td class="text-start"><?= e($data['name']) ?></td>
                    <?php foreach ($sessions as $sess): ?>
                        <?php $p = $data['sessions'][(int) $sess['id']] ?? null; ?>
                        <td>
                            <?php if ($p === null): ?><span class="text-muted">—</span>
                            <?php elseif ($p === 1): ?><span class="text-success">✓</span>
                            <?php else: ?><span class="text-danger">✗</span>
                            <?php endif; ?>
                        </td>
                    <?php endforeach; ?>
                    <td><?= $pct !== null ? e($pct) . '%' : '—' ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <form method="get" class="row g-2 mb-3 align-items-end">
        <input type="hidden" name="course_id" value="<?= $courseId ?>">
        <input type="hidden" name="view" value="session">
        <div class="col-md-6">
            <label class="form-label small">انتخاب جلسه</label>
            <select name="session_id" class="form-select searchable-select" onchange="this.form.submit()">
                <?php foreach ($sessions as $s): ?>
                    <option value="<?= (int) $s['id'] ?>" <?= $sessionId === (int)$s['id'] ? 'selected' : '' ?>>
                        جلسه <?= (int) $s['session_number'] ?> — <?= e($s['title']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>

    <?php if ($session && $sessionStudents): ?>
        <div class="mb-2 d-flex gap-2 flex-wrap">
            <form method="post" class="d-inline">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="mark_all">
                <input type="hidden" name="mark" value="present">
                <button class="btn btn-sm btn-outline-success">همه حاضر</button>
            </form>
            <form method="post" class="d-inline">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="mark_all">
                <input type="hidden" name="mark" value="absent">
                <button class="btn btn-sm btn-outline-danger">همه غایب</button>
            </form>
        </div>
        <form method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="save">
            <div class="table-responsive bg-white rounded shadow-sm">
                <table class="table mb-0">
                    <thead class="table-light"><tr><th>دانشجو</th><th>حاضر</th></tr></thead>
                    <tbody>
                    <?php foreach ($sessionStudents as $st): ?>
                        <tr>
                            <td><?= e($st['full_name']) ?></td>
                            <td>
                                <input type="checkbox" name="present[<?= (int) $st['id'] ?>]" value="1"
                                    <?= ($st['present'] === null || (int)$st['present'] === 1) ? 'checked' : '' ?>>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <button class="btn btn-primary mt-3">ذخیره حضورغیاب</button>
        </form>
    <?php endif; ?>
<?php endif; ?>

<?php require dirname(__DIR__) . '/includes/layout/teacher_footer.php'; ?>
