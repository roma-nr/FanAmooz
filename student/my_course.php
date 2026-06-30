<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_auth(['student','teacher']); // اگر استاد هم دوره‌ای خریده باشد

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

// پردازش فرم‌ها
if (is_post() && verify_csrf()) {
    $action = $_POST['action'] ?? '';
    try {
        if ($action === 'submit_assignment') {
            // ... (بدون تغییر)
        } elseif ($action === 'send_message') {
            // ... (بدون تغییر)
        } elseif ($action === 'request_certificate') {
            // ... (بدون تغییر)
        } elseif ($action === 'cancel_enrollment') {
            cancel_enrollment($userId, $courseId);
            redirect(base_url('student/my_course.php?slug=' . urlencode($slug)));
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

// اطلاعات انصراف
$cancelInfo = can_cancel_enrollment($userId, $courseId);
$canCancel = $cancelInfo['can'] ?? false;

require dirname(__DIR__) . '/includes/layout/student_header.php';
?>

<!-- ... (هدر صفحه بدون تغییر) ... -->

<ul class="nav nav-tabs mb-4">
    <li class="nav-item"><a class="nav-link <?= $tab === 'content' ? 'active' : '' ?>" href="?slug=<?= e(urlencode($slug)) ?>&tab=content">محتوا</a></li>
    <li class="nav-item"><a class="nav-link <?= $tab === 'assignments' ? 'active' : '' ?>" href="?slug=<?= e(urlencode($slug)) ?>&tab=assignments">تکالیف</a></li>
    <li class="nav-item"><a class="nav-link <?= $tab === 'attendance' ? 'active' : '' ?>" href="?slug=<?= e(urlencode($slug)) ?>&tab=attendance">حضورغیاب</a></li>
    <li class="nav-item"><a class="nav-link <?= $tab === 'grades' ? 'active' : '' ?>" href="?slug=<?= e(urlencode($slug)) ?>&tab=grades">نمرات</a></li>
    <li class="nav-item"><a class="nav-link <?= $tab === 'live' ? 'active' : '' ?>" href="?slug=<?= e(urlencode($slug)) ?>&tab=live">کلاس آنلاین</a></li>
    <li class="nav-item"><a class="nav-link <?= $tab === 'chat' ? 'active' : '' ?>" href="?slug=<?= e(urlencode($slug)) ?>&tab=chat">گفتگو با استاد</a></li>
    <li class="nav-item"><a class="nav-link <?= $tab === 'certificate' ? 'active' : '' ?>" href="?slug=<?= e(urlencode($slug)) ?>&tab=certificate">گواهی</a></li>
    <li class="nav-item"><a class="nav-link <?= $tab === 'cancel' ? 'active' : '' ?>" href="?slug=<?= e(urlencode($slug)) ?>&tab=cancel">انصراف</a></li>
</ul>

<?php if ($tab === 'content'): ?>
    <!-- ... (بدون تغییر) ... -->

<?php elseif ($tab === 'cancel'): ?>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h3 class="h5 mb-3">انصراف از دوره</h3>
            <?php $progress = $cancelInfo['progress'] ?? 0; ?>
            <div class="progress mb-3" style="height: 25px;">
                <div class="progress-bar bg-<?= $canCancel ? 'success' : 'warning' ?>" role="progressbar" style="width: <?= $progress ?>%;" aria-valuenow="<?= $progress ?>" aria-valuemin="0" aria-valuemax="100"><?= $progress ?>٪</div>
            </div>
            <p><?= $cancelInfo['message'] ?></p>
            <?php if ($canCancel): ?>
                <form method="post" onsubmit="return confirm('آیا از انصراف خود اطمینان دارید؟ مبلغ پرداختی به کیف پول شما بازگردانده خواهد شد.')">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="cancel_enrollment">
                    <button class="btn btn-danger">انصراف از دوره و بازگشت وجه</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<!-- سایر تب‌ها بدون تغییر -->
<?php require dirname(__DIR__) . '/includes/layout/student_footer.php'; ?>