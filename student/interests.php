<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';

if (!auth_check() || auth_role() !== 'student') {
    flash('error', 'لطفاً وارد شوید.');
    redirect(base_url('student/login.php'));
}

if (student_first_login_done()) {
    redirect(base_url('student/index.php'));
}

$pageTitle = 'علاقه‌مندی‌ها';
$error = null;
$interests = db()->query(
    'SELECT id, name FROM interests WHERE is_active = 1 ORDER BY sort_order, name'
)->fetchAll();

if (is_post()) {
    if (!verify_csrf()) {
        $error = 'درخواست نامعتبر است.';
    } else {
        $selected = array_map('intval', $_POST['interests'] ?? []);
        if ($selected === []) {
            $error = 'حداقل یک علاقه‌مندی انتخاب کنید.';
        } else {
            $validIds = array_column($interests, 'id');
            $selected = array_values(array_intersect($selected, $validIds));
            if ($selected === []) {
                $error = 'انتخاب نامعتبر است.';
            } else {
                $uid = auth_id();
                db()->prepare('DELETE FROM student_interests WHERE user_id = ?')->execute([$uid]);
                $ins = db()->prepare('INSERT INTO student_interests (user_id, interest_id) VALUES (?, ?)');
                foreach ($selected as $iid) {
                    $ins->execute([$uid, $iid]);
                }
                db()->prepare('UPDATE users SET first_login_done = 1 WHERE id = ?')->execute([$uid]);
                $_SESSION['user']['first_login_done'] = 1;
                flash('success', 'علاقه‌مندی‌های شما ذخیره شد.');
                redirect(base_url('student/index.php'));
            }
        }
    }
}

require dirname(__DIR__) . '/includes/layout/header.php';
?>

<div class="container py-5" style="max-width:640px">
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <h1 class="h4 text-primary mb-2">خوش آمدید، <?= e(auth_user()['full_name'] ?? '') ?></h1>
            <p class="text-muted mb-4">حوزه‌های مورد علاقه خود را انتخاب کنید تا دوره‌های مناسب به شما پیشنهاد شود.</p>
            <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
            <form method="post">
                <?= csrf_field() ?>
                <div class="row g-2 mb-4">
                    <?php foreach ($interests as $interest): ?>
                    <div class="col-md-6">
                        <div class="form-check border rounded p-3 h-100">
                            <input class="form-check-input" type="checkbox" name="interests[]"
                                value="<?= (int) $interest['id'] ?>" id="int<?= (int) $interest['id'] ?>">
                            <label class="form-check-label fw-semibold" for="int<?= (int) $interest['id'] ?>">
                                <?= e($interest['name']) ?>
                            </label>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php if (!$interests): ?>
                    <p class="text-warning">هنوز علاقه‌مندی در سیستم تعریف نشده. با مدیر تماس بگیرید.</p>
                <?php else: ?>
                    <button type="submit" class="btn btn-primary w-100">ذخیره و ادامه</button>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>

<?php require dirname(__DIR__) . '/includes/layout/footer.php'; ?>
