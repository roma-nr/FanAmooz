<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';

if (auth_check()) {
    redirect_after_login();
}

$pageTitle = 'ورود';
$errors = [];

if (is_post()) {
    if (!verify_csrf()) {
        $errors['general'] = 'درخواست نامعتبر است.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '') {
            $errors['username'] = 'نام کاربری الزامی است.';
        }
        if ($password === '') {
            $errors['password'] = 'رمز عبور الزامی است.';
        }

        if (empty($errors)) {
            if (attempt_login($username, $password)) {
                redirect_after_login();
            } else {
                $errors['general'] = 'نام کاربری یا رمز عبور نادرست است، یا حساب غیرفعال است.';
            }
        }
    }
}

$flashError = flash('error');

require __DIR__ . '/includes/layout/header.php';
?>

<div class="container py-5" style="max-width:440px">
    <div class="card login-card border-0 shadow">
        <div class="card-body p-4">
            <h1 class="h4 text-center text-primary mb-4">ورود به <?= e(site_name()) ?></h1>

            <?php if (!empty($errors['general']) || $flashError): ?>
                <div class="alert alert-danger">
                    <?= e($errors['general'] ?? $flashError) ?>
                </div>
            <?php endif; ?>

            <form method="post" novalidate>
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label class="form-label" for="username">نام کاربری <span class="text-danger">*</span></label>
                    <input type="text" name="username" id="username" 
                           class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>" 
                           value="<?= e($_POST['username'] ?? '') ?>" 
                           required maxlength="100" autocomplete="username" dir="ltr">
                    <div class="invalid-feedback">
                        <?= e($errors['username'] ?? '') ?>
                    </div>
                    <small class="text-muted">نام کاربری خود را وارد کنید.</small>
                </div>

                <div class="mb-4">
                    <label class="form-label" for="password">رمز عبور <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password" name="password" id="password" 
                               class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" 
                               required autocomplete="current-password" maxlength="255">
                        <span class="btn-eye-wrapper input-group-text" style="cursor:pointer; background:#fff; border-left:0;">
                            <video id="eyeVideo" class="eye-video" muted playsinline
                                   data-close-video="<?= e(asset_url('videos/eyeclosing.mp4')) ?>"
                                   data-open-video="<?= e(asset_url('videos/eyeopening.mp4')) ?>"
                                   style="width:28px; height:28px; object-fit:cover; pointer-events:none;">
                                <source src="<?= e(asset_url('videos/eyeopening.mp4')) ?>" type="video/mp4">
                            </video>
                        </span>
                    </div>
                    <div class="invalid-feedback">
                        <?= e($errors['password'] ?? '') ?>
                    </div>
                    <small class="text-muted">رمز عبور خود را وارد کنید.</small>
                </div>

                <button type="submit" class="button-52 w-100">ورود</button>
            </form>

            <p class="text-center small text-muted mt-3 mb-0">
                استاد هستید؟ <a href="<?= e(base_url('teacher/apply.php')) ?>">درخواست همکاری</a>
            </p>
			<p class="text-center small mt-3">
    <a href="<?= e(base_url('forgot_password.php')) ?>" class="text-muted">فراموشی رمز عبور؟</a>
</p>
        </div>
    </div>
</div>

<?php require __DIR__ . '/includes/layout/footer.php'; ?>