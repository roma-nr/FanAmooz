<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_auth('admin');

$pageTitle = 'ورود گروهی دانشجو';
$activeMenu = 'students';
$error = null;
$result = null;

if (is_post()) {
    if (!verify_csrf()) {
        $error = 'درخواست نامعتبر است.';
    } elseif (empty($_FILES['csv_file']['name'])) {
        $error = 'فایل CSV را انتخاب کنید.';
    } else {
        try {
            $tmp = $_FILES['csv_file']['tmp_name'];
            $ext = strtolower(pathinfo($_FILES['csv_file']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ['csv', 'txt'], true)) {
                throw new RuntimeException('فقط فایل CSV مجاز است.');
            }
            $result = import_students_csv($tmp);
            flash('success', sprintf('%d دانشجو import شد، %d رد شد.', $result['imported'], $result['skipped']));
        } catch (Throwable $e) {
            $error = $e->getMessage();
        }
    }
}

require dirname(__DIR__) . '/includes/layout/admin_header.php';
?>

<h1 class="h3 mb-4">ورود گروهی دانشجویان (CSV)</h1>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">فایل CSV</label>
                        <input type="file" name="csv_file" class="form-control" accept=".csv,text/csv" required>
                    </div>
                    <button type="submit" class="btn btn-primary">آپلود و import</button>
                    <a href="<?= e(base_url('admin/students.php')) ?>" class="btn btn-link">بازگشت</a>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h2 class="h6">ستون‌های فایل</h2>
                <p class="small text-muted mb-2">سطر اول = عنوان ستون‌ها (UTF-8)</p>
                <ul class="small mb-0">
                    <li><code>full_name</code> — نام کامل (الزامی)</li>
                    <li><code>student_code</code> — کد دانشجویی (الزامی)</li>
                    <li><code>national_id</code> — کد ملی ۱۰ رقمی (الزامی)</li>
                    <li><code>institution_id</code> — شناسه دانشکده (از لیست دانشکده‌ها)</li>
                    <li class="text-muted">یا: <code>province_name</code> + <code>institution_name</code></li>
                    <li><code>phone</code> — اختیاری</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php if ($result && $result['errors'] !== []): ?>
<div class="card border-0 shadow-sm mt-4">
    <div class="card-body">
        <h2 class="h6 text-danger">خطاهای import</h2>
        <ul class="small mb-0">
            <?php foreach ($result['errors'] as $line => $msg): ?>
                <li>سطر <?= (int)$line ?>: <?= e($msg) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
<?php endif; ?>

<?php require dirname(__DIR__) . '/includes/layout/admin_footer.php'; ?>
