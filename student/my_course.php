<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_student_auth();

$slug = trim($_GET['slug'] ?? '');
$course = $slug !== '' ? course_by_slug($slug, false) : null;
$tab = $_GET['tab'] ?? 'content';

if (!$course) {
    flash('error', 'دوره یافت نشد.');
    redirect(base_url('student/index.php'));
}

$userId = (int) auth_id();
$courseId = (int) $course['id'];
$enrollment = student_enrollment($userId, $courseId);

if ($enrollment === null || !in_array($enrollment['status'], ['active', 'completed'], true)) {
    flash('error', 'برای مشاهده محتوا باید در دوره ثبت‌نام کنید.');
    redirect(base_url('course.php?slug=' . urlencode($slug)));
}

$error = null;

if (is_post() && verify_csrf()) {
    $action = $_POST['action'] ?? '';
    try {
        if ($action === 'submit_assignment') {
            $assignmentId = (int) ($_POST['assignment_id'] ?? 0);
            $aStmt = db()->prepare('SELECT * FROM assignments WHERE id = ? AND course_id = ?');
            $aStmt->execute([$assignmentId, $courseId]);
            $assignment = $aStmt->fetch();
            if (!$assignment) {
                throw new RuntimeException('تکلیف یافت نشد.');
            }
            $existing = assignment_submission($assignmentId, $userId);
            if (!student_can_submit_assignment($assignment, $existing)) {
                throw new RuntimeException('مهلت ارسال این تکلیف به پایان رسیده است.');
            }
            $body = trim($_POST['body_text'] ?? '');
            $filePath = null;
            if (!empty($_FILES['submission_file']['name'])) {
                $filePath = handle_upload($_FILES['submission_file'], 'assignments', ['pdf', 'doc', 'docx', 'zip', 'jpg', 'jpeg', 'png'], 15 * 1024 * 1024);
            }
            if ($body === '' && !$filePath) {
                throw new RuntimeException('متن یا فایل ارسال کنید.');
            }
            if ($existing) {
                delete_upload($existing['file_path']);
                db()->prepare(
                    'UPDATE assignment_submissions SET body_text=?, file_path=?, submitted_at=NOW(), score=NULL, feedback=NULL, graded_at=NULL WHERE id=?'
                )->execute([$body ?: null, $filePath, (int) $existing['id']]);
            } else {
                db()->prepare(
                    'INSERT INTO assignment_submissions (assignment_id, user_id, body_text, file_path) VALUES (?,?,?,?)'
                )->execute([$assignmentId, $userId, $body ?: null, $filePath]);
            }
            flash('success', 'پاسخ تکلیف ارسال شد.');
            redirect(base_url('student/my_course.php?slug=' . urlencode($slug) . '&tab=assignments'));
        }

        if ($action === 'send_message') {
            $body = trim($_POST['message_body'] ?? '');
            $filePath = null;
            if (!empty($_FILES['message_file']['name'])) {
                $filePath = handle_upload($_FILES['message_file'], 'chat', ['pdf', 'doc', 'docx', 'zip', 'jpg', 'jpeg', 'png'], 10 * 1024 * 1024);
            }
            send_course_message($courseId, $userId, $body, $filePath);
            redirect(base_url('student/my_course.php?slug=' . urlencode($slug) . '&tab=chat'));
        }

        if ($action === 'request_certificate') {
            request_certificate_for_enrollment((int) $enrollment['id'], $userId);
            flash('success', 'درخواست گواهی ثبت شد و در انتظار تأیید مدیر است.');
            redirect(base_url('student/my_course.php?slug=' . urlencode($slug) . '&tab=certificate'));
        }
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}

$pageTitle = $course['title'];
$activeMenu = 'courses';
$sessions = course_sessions_list($courseId);
$assignments = course_assignments($courseId);
$liveSessions = course_live_sessions($courseId);
$messages = course_messages_list($courseId);
$certRequest = certificate_request_for_enrollment((int) $enrollment['id']);
$attendanceRows = student_attendance_by_course($userId, $courseId);
$assignmentAvg = student_assignment_average($userId, $courseId);
$attendancePct = student_attendance_percent($userId, $courseId);

require dirname(__DIR__) . '/includes/layout/student_header.php';
?>

<p class="mb-3"><a href="<?= e(base_url('student/index.php')) ?>">&larr; داشبورد</a></p>

<h1 class="h3 text-primary mb-2"><?= e($course['title']) ?></h1>
<p class="text-muted small mb-3">
    استاد: <?= e($course['teacher_name'] ?? '—') ?> |
    <?= e(format_schedule_course($course)) ?>
</p>

<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>

<ul class="nav nav-tabs mb-4">
    <li class="nav-item"><a class="nav-link <?= $tab === 'content' ? 'active' : '' ?>" href="?slug=<?= e(urlencode($slug)) ?>&tab=content">محتوا</a></li>
    <li class="nav-item"><a class="nav-link <?= $tab === 'assignments' ? 'active' : '' ?>" href="?slug=<?= e(urlencode($slug)) ?>&tab=assignments">تکالیف</a></li>
    <li class="nav-item"><a class="nav-link <?= $tab === 'attendance' ? 'active' : '' ?>" href="?slug=<?= e(urlencode($slug)) ?>&tab=attendance">حضورغیاب</a></li>
    <li class="nav-item"><a class="nav-link <?= $tab === 'grades' ? 'active' : '' ?>" href="?slug=<?= e(urlencode($slug)) ?>&tab=grades">نمرات</a></li>
    <li class="nav-item"><a class="nav-link <?= $tab === 'live' ? 'active' : '' ?>" href="?slug=<?= e(urlencode($slug)) ?>&tab=live">کلاس آنلاین</a></li>
    <li class="nav-item"><a class="nav-link <?= $tab === 'chat' ? 'active' : '' ?>" href="?slug=<?= e(urlencode($slug)) ?>&tab=chat">گفتگو با استاد</a></li>
    <li class="nav-item"><a class="nav-link <?= $tab === 'certificate' ? 'active' : '' ?>" href="?slug=<?= e(urlencode($slug)) ?>&tab=certificate">گواهی</a></li>
</ul>

<?php if ($tab === 'content'): ?>
    <?php if (!$sessions): ?>
        <div class="alert alert-info">هنوز جلسه‌ای بارگذاری نشده است.</div>
    <?php else: ?>
        <div class="accordion" id="sessionsAcc">
            <?php foreach ($sessions as $i => $s): ?>
                <div class="accordion-item border-0 shadow-sm mb-2">
                    <h2 class="accordion-header">
                        <button class="accordion-button <?= $i > 0 ? 'collapsed' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#sess<?= (int) $s['id'] ?>">
                            جلسه <?= (int) $s['session_number'] ?> — <?= e($s['title']) ?>
                        </button>
                    </h2>
                    <div id="sess<?= (int) $s['id'] ?>" class="accordion-collapse collapse <?= $i === 0 ? 'show' : '' ?>">
                        <div class="accordion-body">
                            <?php if ($s['description']): ?><p class="text-muted"><?= nl2br(e($s['description'])) ?></p><?php endif; ?>
                            <?php if ($s['video_path']): ?><video class="w-100 rounded mb-2" controls src="<?= e(upload_url($s['video_path'])) ?>"></video><?php endif; ?>
                            <?php if ($s['audio_path']): ?><audio class="w-100 mb-2" controls src="<?= e(upload_url($s['audio_path'])) ?>"></audio><?php endif; ?>
                            <?php if ($s['pdf_path']): ?>
                                <a href="<?= e(upload_url($s['pdf_path'])) ?>" class="btn btn-outline-primary btn-sm" target="_blank">دانلود PDF</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

<?php elseif ($tab === 'assignments'): ?>
    <?php if (!$assignments): ?>
        <div class="alert alert-info">تکلیفی تعریف نشده است.</div>
    <?php else: ?>
        <?php foreach ($assignments as $a): ?>
            <?php
            $sub = assignment_submission((int) $a['id'], $userId);
            $canSubmit = student_can_submit_assignment($a, $sub);
            ?>
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h3 class="h6 mb-0"><?= e($a['title']) ?></h3>
                        <span class="badge <?= submission_status_badge_class($sub, $a) ?>"><?= e(submission_status_label($sub, $a)) ?></span>
                    </div>
                    <?php if ($a['description']): ?><p class="small text-muted"><?= nl2br(e($a['description'])) ?></p><?php endif; ?>
                    <p class="small">مهلت: <?= $a['due_at'] ? e(format_datetime($a['due_at'])) : 'بدون مهلت' ?> — از <?= e($a['max_score']) ?> نمره</p>
                    <?php if ($sub): ?>
                        <div class="alert alert-secondary small mb-2">
                            <?php if ($sub['score'] !== null): ?>
                                نمره: <strong><?= e($sub['score']) ?></strong>
                            <?php else: ?>
                                ارسال شده — در انتظار تصحیح استاد
                            <?php endif; ?>
                            <?php if ($sub['feedback']): ?><br>بازخورد: <?= e($sub['feedback']) ?><?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($canSubmit): ?>
                    <form method="post" enctype="multipart/form-data" class="mt-2">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="submit_assignment">
                        <input type="hidden" name="assignment_id" value="<?= (int) $a['id'] ?>">
                        <textarea name="body_text" class="form-control mb-2" rows="3" placeholder="پاسخ متنی"><?= e($sub['body_text'] ?? '') ?></textarea>
                        <input type="file" name="submission_file" class="form-control mb-2">
                        <button class="btn btn-primary btn-sm"><?= $sub ? 'ارسال مجدد' : 'ارسال پاسخ' ?></button>
                    </form>
                    <?php elseif (!$sub): ?>
                        <p class="small text-danger mb-0">مهلت ارسال به پایان رسیده است.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

<?php elseif ($tab === 'attendance'): ?>
    <?php if (!$attendanceRows): ?>
        <div class="alert alert-info">جلسه‌ای برای ثبت حضور تعریف نشده یا هنوز حضورغیابی ثبت نشده است.</div>
    <?php else: ?>
        <p class="mb-3">درصد حضور شما: <strong><?= $attendancePct !== null ? e($attendancePct) . '%' : '—' ?></strong></p>
        <div class="table-responsive bg-white rounded shadow-sm">
            <table class="table mb-0">
                <thead class="table-light"><tr><th>جلسه</th><th>عنوان</th><th>وضعیت</th></tr></thead>
                <tbody>
                <?php foreach ($attendanceRows as $ar): ?>
                    <tr>
                        <td><?= (int) $ar['session_number'] ?></td>
                        <td><?= e($ar['title']) ?></td>
                        <td>
                            <?php if ($ar['present'] === null): ?><span class="text-muted">ثبت نشده</span>
                            <?php elseif ((int)$ar['present'] === 1): ?><span class="text-success">حاضر</span>
                            <?php else: ?><span class="text-danger">غایب</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

<?php elseif ($tab === 'grades'): ?>
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3">
                <div class="small text-muted">نمره نهایی</div>
                <div class="fs-4 fw-bold text-primary"><?= $enrollment['final_grade'] !== null ? e($enrollment['final_grade']) : '—' ?></div>
                <div class="small">حداقل قبولی: <?= e($course['min_pass_grade']) ?></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3">
                <div class="small text-muted">میانگین تکالیف</div>
                <div class="fs-4 fw-bold"><?= $assignmentAvg !== null ? e($assignmentAvg) . '%' : '—' ?></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3">
                <div class="small text-muted">درصد حضور</div>
                <div class="fs-4 fw-bold"><?= $attendancePct !== null ? e($attendancePct) . '%' : '—' ?></div>
            </div>
        </div>
    </div>
    <a href="<?= e(base_url('student/grades.php')) ?>" class="btn btn-outline-primary btn-sm">مشاهده جزئیات همه دوره‌ها</a>

<?php elseif ($tab === 'live'): ?>
    <p class="mb-3"><a href="<?= e(base_url('student/live_classes.php')) ?>" class="btn btn-sm btn-outline-primary">مشاهده همه کلاس‌های آنلاین</a></p>
    <?php if (!$liveSessions): ?>
        <div class="alert alert-info">جلسه آنلاینی برنامه‌ریزی نشده است.</div>
    <?php else: ?>
        <div class="row g-3">
            <?php foreach ($liveSessions as $ls): ?>
                <?php $lsStatus = live_session_status($ls); ?>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm live-session-card <?= $lsStatus === 'live' ? 'live-now' : ($lsStatus === 'upcoming' ? 'upcoming-soon' : '') ?>">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <h3 class="h6 mb-0"><?= e($ls['title']) ?></h3>
                                <span class="badge <?= live_session_status_badge($lsStatus) ?>"><?= e(live_session_status_label($lsStatus)) ?></span>
                            </div>
                            <p class="small text-muted mb-2"><?= e(format_datetime($ls['scheduled_at'])) ?> — <?= (int) $ls['duration_minutes'] ?> دقیقه</p>
                            <?php if (!empty($ls['notes'])): ?><p class="small"><?= nl2br(e($ls['notes'])) ?></p><?php endif; ?>
                            <?php if ($lsStatus === 'live' || $lsStatus === 'upcoming'): ?>
                                <a href="<?= e($ls['adobe_connect_url']) ?>" class="btn btn-primary btn-sm" target="_blank" rel="noopener">
                                    <i class="bi bi-camera-video"></i> ورود به Adobe Connect
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

<?php elseif ($tab === 'chat'): ?>
    <p class="mb-2"><a href="<?= e(base_url('student/chat.php?course_id=' . $courseId)) ?>" class="small">باز کردن در صفحه کامل</a></p>
    <?php
    // پنل چت مستقیماً نمایش داده شود (بدون شرط migrate)
    $chatPostUrl = base_url('student/my_course.php?slug=' . urlencode($slug) . '&tab=chat');
    $chatApiUrl = base_url('api/chat_messages.php?course_id=' . $courseId);
    $chatUserId = $userId;
    $chatCourseId = $courseId;
    $chatMessages = $messages;
    require dirname(__DIR__) . '/includes/layout/chat_panel.php';
    ?>

<?php elseif ($tab === 'certificate'): ?>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <p>حداقل نمره قبولی: <strong><?= e($course['min_pass_grade']) ?></strong></p>
            <p>نمره نهایی شما: <strong><?= $enrollment['final_grade'] !== null ? e($enrollment['final_grade']) : 'ثبت نشده' ?></strong></p>
            <?php if ($certRequest): ?>
                <div class="alert alert-<?= $certRequest['status'] === 'approved' ? 'success' : ($certRequest['status'] === 'rejected' ? 'danger' : 'info') ?>">
                    وضعیت: <?= e(certificate_status_label($certRequest['status'])) ?>
                    <?php if ($certRequest['status'] === 'approved' && !empty($certRequest['certificate_number'])): ?>
                        <br>شماره گواهی: <code dir="ltr"><?= e($certRequest['certificate_number']) ?></code>
                    <?php endif; ?>
                    <?php if ($certRequest['admin_note']): ?>
                        <br><small><?= e($certRequest['admin_note']) ?></small>
                    <?php endif; ?>
                </div>
                <?php if ($certRequest['status'] === 'approved'): ?>
                    <a href="<?= e(base_url('student/certificate_print.php?id=' . (int) $certRequest['id'])) ?>" class="btn btn-success" target="_blank">
                        <i class="bi bi-printer"></i> مشاهده و چاپ گواهی
                    </a>
                <?php endif; ?>
            <?php elseif (student_can_request_certificate($enrollment, $course)): ?>
                <form method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="request_certificate">
                    <button class="btn btn-success">درخواست صدور گواهی</button>
                </form>
                <p class="small text-muted mt-2 mb-0">پس از تأیید مدیر، گواهی قابل چاپ خواهد بود.</p>
            <?php else: ?>
                <p class="text-muted small">پس از ثبت نمره نهایی توسط استاد و رسیدن به حد نصاب (<?= e($course['min_pass_grade']) ?>) می‌توانید درخواست دهید.</p>
            <?php endif; ?>
            <p class="mt-3 mb-0"><a href="<?= e(base_url('student/certificates.php')) ?>">همه گواهی‌های من</a></p>
        </div>
    </div>
<?php endif; ?>

<?php require dirname(__DIR__) . '/includes/layout/student_footer.php'; ?>