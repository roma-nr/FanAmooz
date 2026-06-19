<?php
require_once __DIR__ . '/includes/bootstrap.php';

$token = $_GET['token'] ?? '';
$pageTitle = 'بازنشانی رمز عبور';
$errors = [];
$validToken = false;
$email = '';

if ($token !== '') {
    $stmt = db()->prepare("SELECT email, expired_at FROM password_resets WHERE token = ? AND used = 0 LIMIT 1");
    $stmt->execute([$token]);
    $row = $stmt->fetch();
    if ($row && strtotime($row['expired_at']) > time()) {
        $validToken = true;
        $email = $row['email'];
    }
}

if (is_post() && $validToken) {
    if (!verify_csrf()) {
        $errors['general'] = 'درخواست نامعتبر.';
    } else {
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['password_confirm'] ?? '';
        if (strlen($password) < 8) {
            $errors['password'] = 'حداقل ۸ کاراکتر.';
        } elseif ($password !== $confirm) {
            $errors['password_confirm'] = 'تکرار یکسان نیست.';
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            db()->prepare("UPDATE users SET password_hash = ? WHERE username = ? OR email = ?")->execute([$hash, $email, $email]);
            db()->prepare("UPDATE password_resets SET used = 1 WHERE token = ?")->execute([$token]);
            flash('success', 'رمز عبور تغییر کرد. اکنون وارد شوید.');
            redirect(base_url('login.php'));
        }
    }
}

require __DIR__ . '/includes/layout/header.php';
?>

<div class="container py-5" style="max-width:440px">
    <div class="card shadow">
        <div class="card-body p-4">
            <h1 class="h4 text-center mb-4">بازنشانی رمز عبور</h1>
            <?php if (!$validToken): ?>
                <div class="alert alert-danger">لینک نامعتبر است. <a href="<?= e(base_url('forgot_password.php')) ?>">درخواست مجدد</a></div>
            <?php else: ?>
                <form method="post" novalidate>
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">رمز جدید <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" minlength="8" required>
                        <div class="invalid-feedback"><?= $errors['password'] ?? '' ?></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">تکرار رمز <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirm" class="form-control <?= isset($errors['password_confirm']) ? 'is-invalid' : '' ?>" minlength="8" required>
                        <div class="invalid-feedback"><?= $errors['password_confirm'] ?? '' ?></div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">تغییر رمز</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require __DIR__ . '/includes/layout/footer.php'; ?>