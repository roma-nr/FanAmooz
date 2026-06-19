<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_student_auth();

$enrollmentId = (int) ($_GET['enrollment_id'] ?? 0);
$cardNumber = setting('bank_card_number');
$accountName = setting('bank_account_name');
$bankName = setting('bank_name');
$userId = auth_id();

$stmt = db()->prepare("SELECT e.*, c.title FROM enrollments e JOIN courses c ON c.id = e.course_id WHERE e.id = ? AND e.user_id = ?");
$stmt->execute([$enrollmentId, $userId]);
$enrollment = $stmt->fetch();
if (!$enrollment || $enrollment['status'] !== 'pending_payment') {
    flash('error', 'درخواست نامعتبر.');
    redirect(base_url('student/index.php'));
}

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf()) {
    try {
        $file = handle_upload($_FILES['receipt'], 'payments', ['jpg','jpeg','png','pdf'], 5*1024*1024);
        db()->prepare("UPDATE enrollments SET receipt_path = ? WHERE id = ?")->execute([$file, $enrollmentId]);
        flash('success', 'فیش شما با موفقیت ارسال شد. پس از تأیید مدیر، دوره فعال می‌شود.');
        redirect(base_url('student/index.php'));
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

$pageTitle = 'آپلود فیش پرداخت';
require dirname(__DIR__) . '/includes/layout/student_header.php';
?>
<div class="container py-5" style="max-width:600px">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h4 mb-3">آپلود فیش پرداخت برای دوره <?= e($enrollment['title']) ?></h1>
            <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
            <form method="post" enctype="multipart/form-data">
			<div class="alert alert-info">
    <strong>اطلاعات واریز:</strong><br>
    بانک: <?= e($bankName) ?><br>
    شماره کارت: <span dir="ltr"><?= e($cardNumber) ?></span><br>
    به نام: <?= e($accountName) ?>
</div>
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label">تصویر یا PDF فیش (حداکثر ۵ مگابایت)</label>
                    <input type="file" name="receipt" class="form-control" accept="image/*,.pdf" required>
                </div>
                <button class="btn btn-primary">ارسال فیش</button>
                <a href="<?= e(base_url('student/index.php')) ?>" class="btn btn-link">انصراف</a>
            </form>
        </div>
    </div>
</div>
<?php require dirname(__DIR__) . '/includes/layout/student_footer.php'; ?>