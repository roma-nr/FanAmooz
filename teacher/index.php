<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_teacher_auth();
teacher_refresh_session_status();

$pageTitle = 'داشبورد استاد';
$activeMenu = 'dashboard';
$status = teacher_session_status();
$app = teacher_application_for_user((int) auth_id());
$userId = auth_id();

// پردازش فرم ویرایش پروفایل
$profileError = null;
$profileSuccess = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile']) && verify_csrf()) {
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    try {
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('ایمیل معتبر نیست.');
        }
        $updates = [];
        $params = [];
        if (!empty($email)) {
            $updates[] = "email = ?";
            $params[] = $email;
        }
        $updates[] = "phone = ?";
        $params[] = $phone;
        if (!empty($password)) {
            $updates[] = "password_hash = ?";
            $params[] = password_hash($password, PASSWORD_BCRYPT);
        }
        if (!empty($updates)) {
            $params[] = $userId;
            db()->prepare("UPDATE users SET " . implode(', ', $updates) . " WHERE id = ?")->execute($params);
            $profileSuccess = 'اطلاعات با موفقیت به‌روزرسانی شد.';
            $_SESSION['user']['full_name'] = db()->query("SELECT full_name FROM users WHERE id = $userId")->fetchColumn();
        }
    } catch (Exception $e) {
        $profileError = $e->getMessage();
    }
}

$userStmt = db()->prepare("SELECT full_name, email, phone FROM users WHERE id = ?");
$userStmt->execute([$userId]);
$userInfo = $userStmt->fetch();

// جلسات آنلاین پیش‌رو — اصلاح‌شده
$upcomingStmt = db()->prepare("
    SELECT cs.*, c.title as course_title
    FROM course_sessions cs
    JOIN courses c ON c.id = cs.course_id
    WHERE c.teacher_id = ? 
      AND cs.adobe_connect_url IS NOT NULL 
      AND cs.adobe_connect_url != ''
      AND cs.scheduled_at > NOW()
    ORDER BY cs.scheduled_at ASC 
    LIMIT 5
");
$upcomingStmt->execute([$userId]);
$upcomingSessions = $upcomingStmt->fetchAll();

require dirname(__DIR__) . '/includes/layout/teacher_header.php';
?>

<?php if ($msg = flash('success')): ?>
    <div class="alert alert-success alert-dismissible fade show"><?= e($msg) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<h1 class="h3 text-primary mb-4">پنل استاد</h1>

<?php if ($status === 'pending'): ?>
    <div class="alert alert-info">
        <strong>وضعیت:</strong> درخواست همکاری شما در حال بررسی است. پس از تصمیم مدیر، از همین صفحه مطلع می‌شوید.
    </div>
    <p class="text-muted">تا زمان تأیید، فقط همین داشبورد و وضعیت درخواست در دسترس است.</p>

<?php elseif ($status === 'rejected'): ?>
    <div class="alert alert-danger">
        <strong>وضعیت:</strong> درخواست شما رد شده است.
        <?php if (!empty($app['admin_note'])): ?>
            <div class="mt-2 small"><strong>توضیح مدیر:</strong> <?= nl2br(e($app['admin_note'])) ?></div>
        <?php endif; ?>
    </div>
    <p>می‌توانید اطلاعات را اصلاح کرده و دوباره ارسال کنید.</p>
    <a href="<?= e(base_url('teacher/apply.php')) ?>" class="btn btn-primary">ویرایش و ارسال مجدد درخواست</a>

<?php elseif ($status === 'approved'): ?>
    <?php if (empty($_SESSION['user']['first_login_done'])): ?>
        <div class="alert alert-success">
            <strong>تبریک:</strong> درخواست شما تأیید شد. از این پس می‌توانید دوره‌ها و محتوا را مدیریت کنید.
        </div>
        <?php
        db()->prepare("UPDATE users SET first_login_done = 1 WHERE id = ?")->execute([$userId]);
        $_SESSION['user']['first_login_done'] = 1;
        ?>
    <?php endif; ?>

    <div class="row g-4">
        <!-- ستون راست: ویرایش اطلاعات -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">ویرایش اطلاعات شخصی</div>
                <div class="card-body">
                    <?php if ($profileError): ?>
                        <div class="alert alert-danger"><?= e($profileError) ?></div>
                    <?php endif; ?>
                    <?php if ($profileSuccess): ?>
                        <div class="alert alert-success"><?= e($profileSuccess) ?></div>
                    <?php endif; ?>
                    <form method="post">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label">نام کامل</label>
                            <input type="text" class="form-control" value="<?= e($userInfo['full_name'] ?? '') ?>" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ایمیل</label>
                            <input type="email" name="email" class="form-control" value="<?= e($userInfo['email'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">تلفن</label>
                            <input type="text" name="phone" class="form-control" value="<?= e($userInfo['phone'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">رمز عبور جدید (در صورت تمایل)</label>
                            <input type="password" name="password" class="form-control" autocomplete="new-password">
                        </div>
                        <button type="submit" name="update_profile" class="btn btn-primary">ذخیره تغییرات</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- ستون چپ: جلسات پیش‌رو -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">جلسات آنلاین پیش‌رو</div>
                <div class="card-body">
                    <?php if (empty($upcomingSessions)): ?>
                        <p class="text-muted">هیچ جلسه آنلاینی در آینده نزدیک ندارید.</p>
                    <?php else: ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($upcomingSessions as $cs): ?>
                                <li class="list-group-item">
                                    <strong><?= e($cs['course_title']) ?></strong><br>
                                    <?= e($cs['title']) ?> — <?= e(format_datetime($cs['scheduled_at'])) ?>
                                    <?php if (!empty($cs['adobe_connect_url'])): ?>
                                        <a href="<?= e($cs['adobe_connect_url']) ?>" target="_blank" class="btn btn-sm btn-outline-primary float-start">ورود به کلاس</a>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                    <div class="mt-3">
                        <a href="<?= e(base_url('teacher/sessions.php')) ?>" class="btn btn-secondary btn-sm">مدیریت جلسات</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php else: ?>
    <div class="alert alert-warning">وضعیت حساب استاد نامشخص است. با پشتیبانی تماس بگیرید.</div>
<?php endif; ?>

<?php if ($app && $status !== 'approved'): ?>
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-light">خلاصه آخرین ارسال</div>
        <div class="card-body small">
            <p class="mb-1"><strong>زمان ارسال:</strong> <?= e(format_date($app['submitted_at'] ?? null)) ?></p>
            <?php if (!empty($app['resume_path'])): ?>
                <p class="mb-0"><a href="<?= e(upload_url($app['resume_path'])) ?>" target="_blank">دانلود رزومه ارسالی</a></p>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<?php require dirname(__DIR__) . '/includes/layout/teacher_footer.php'; ?>