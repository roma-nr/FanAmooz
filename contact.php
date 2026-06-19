<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$stmt = db()->prepare('SELECT title, body FROM cms_pages WHERE slug = ?');
$stmt->execute(['contact']);
$page = $stmt->fetch() ?: ['title' => 'تماس با ما', 'body' => ''];
$pageTitle = $page['title'];

require __DIR__ . '/includes/layout/header.php';
?>

<div class="container py-5">
    <h1 class="section-title h3 text-primary"><?= e($page['title']) ?></h1>
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="bg-white p-4 rounded shadow-sm"><?= $page['body'] ?></div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h2 class="h6">اطلاعات تماس</h2>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="bi bi-telephone text-primary ms-2"></i><?= e(setting('contact_phone')) ?></li>
                        <li class="mb-2"><i class="bi bi-envelope text-primary ms-2"></i><?= e(setting('contact_email')) ?></li>
                        <li><i class="bi bi-geo-alt text-primary ms-2"></i><?= e(setting('contact_address')) ?></li>
                    </ul>
					
                </div>
            </div>
			<!-- بخش گزارش خطا -->
<div class="card border-0 shadow-sm mt-4">
    <div class="card-body">
        <h3 class="h5">گزارش خطا یا مشکل</h3>
        <p class="text-muted small">اگر در حین استفاده از سامانه با خطا یا مشکلی مواجه شدید، از این بخش گزارش دهید.</p>
        <form method="post" action="<?= e(base_url('submit_report.php')) ?>">
            <?= csrf_field() ?>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">نقش<span class="text-danger">*</span></label>
                    <select name="user_role" class="form-select searchable-select" required>
                        <option value="">انتخاب کنید</option>
                        <option value="student">دانشجو</option>
                        <option value="teacher">استاد</option>
                        <option value="guest">کاربر مهمان</option>
                    </select>
                </div>
                <div class="col-md-8">
                    <label class="form-label">موضوع <span class="text-danger">*</span></label>
                    <input name="title" class="form-control" required>
                </div>
                <div class="col-12">
                    <label class="form-label">شرح مشکل <span class="text-danger">*</span></label>
                    <textarea name="description" class="form-control" rows="4" required></textarea>
                </div>
                <div class="col-12">
                    <button class="btn btn-warning">ارسال گزارش</button>
                </div>
            </div>
        </form>
    </div>
</div>
			<!-- بخش گزارش خطا -->

        </div>
		
    </div>
</div>

<?php require __DIR__ . '/includes/layout/footer.php'; ?>
