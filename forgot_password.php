<?php
require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'فراموشی رمز عبور';
$errors = [];
$success = false;

if (is_post()) {
    if (!verify_csrf()) {
        $errors['general'] = 'درخواست نامعتبر.';
    } else {
        $email = trim($_POST['email'] ?? '');
        if ($email === '') {
            $errors['email'] = 'ایمیل خود را وارد کنید.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'ایمیل معتبر نیست.';
        } else {
            $user = db()->prepare("SELECT id, full_name FROM users WHERE username = ? OR email = ? LIMIT 1");
            $user->execute([$email, $email]);
            $userRow = $user->fetch();
            if (!$userRow) {
                $errors['email'] = 'کاربری با این ایمیل یافت نشد.';
            } else {
                $token = bin2hex(random_bytes(32));
                $expiredAt = date('Y-m-d H:i:s', strtotime('+1 hour'));
                db()->prepare("INSERT INTO password_resets (email, token, expired_at) VALUES (?, ?, ?)")
                   ->execute([$email, $token, $expiredAt]);

                $resetLink = base_url('reset_password.php?token=' . $token);
                // برای ارسال ایمیل واقعی، از PHPMailer استفاده کن
                // فعلاً با mail() ساده
                mail($email, 'بازنشانی رمز عبور فن‌آموز', 
                    "سلام {$userRow['full_name']}\n\nبرای بازنشانی رمز، روی لینک زیر کلیک کنید:\n$resetLink\n\nاین لینک تا ۱ ساعت معتبر است.", 
                    "From: noreply@fanamooz.ir");
                
                $success = true;
                flash('success', 'لینک بازنشانی به ایمیل شما ارسال شد.');
                redirect(base_url('login.php'));
            }
        }
    }
}

require __DIR__ . '/includes/layout/header.php';
?>

<div class="container py-5" style="max-width:440px">
    <div class="card shadow">
        <div class="card-body p-4">
            <h1 class="h4 text-center mb-4">فراموشی رمز عبور</h1>
            <p class="text-muted small text-center">ایمیل خود را وارد کنید تا لینک بازنشانی ارسال شود.</p>
            <?php if (!empty($errors['general'])): ?>
                <div class="alert alert-danger"><?= e($errors['general']) ?></div>
            <?php endif; ?>
            <form method="post" novalidate>
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label">ایمیل <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" value="<?= old('email') ?>" required>
                    <div class="invalid-feedback"><?= $errors['email'] ?? '' ?></div>
                </div>
                <button type="submit" class="btn btn-primary w-100">ارسال لینک</button>
            </form>
            <p class="text-center small mt-3">
                <a href="<?= e(base_url('login.php')) ?>">بازگشت به ورود</a>
            </p>
        </div>
    </div>
</div>

<?php require __DIR__ . '/includes/layout/footer.php'; ?>