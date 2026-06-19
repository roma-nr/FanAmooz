<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_auth('admin');

$pageTitle = 'تنظیمات سایت';
$activeMenu = 'settings';
$error = null;

$aboutPage = cms_page('about') ?? ['title' => 'درباره ما', 'body' => ''];
$contactPage = cms_page('contact') ?? ['title' => 'تماس با ما', 'body' => ''];

if (is_post()) {
    if (!verify_csrf()) {
        $error = 'درخواست نامعتبر است.';
    } else {
        try {
            setting_set_many([
                'site_name' => trim($_POST['site_name'] ?? 'فن‌آموز'),
                'contact_phone' => trim($_POST['contact_phone'] ?? ''),
                'contact_email' => trim($_POST['contact_email'] ?? ''),
                'contact_address' => trim($_POST['contact_address'] ?? ''),
            ]);

            cms_page_save(
                'about',
                trim($_POST['about_title'] ?? 'درباره ما'),
                trim($_POST['about_body'] ?? '')
            );
            cms_page_save(
                'contact',
                trim($_POST['contact_title'] ?? 'تماس با ما'),
                trim($_POST['contact_body'] ?? '')
            );

            for ($i = 1; $i <= 3; $i++) {
                setting_set('logo_' . $i . '_alt', trim($_POST['logo_' . $i . '_alt'] ?? ''));
                setting_set('logo_' . $i . '_url', trim($_POST['logo_' . $i . '_url'] ?? ''));

                $fileKey = 'logo_' . $i . '_file';
                if (!empty($_FILES[$fileKey]['name'])) {
                    $old = setting('logo_' . $i);
                    $path = handle_upload($_FILES[$fileKey], 'logos', ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
                    if ($path) {
                        delete_upload($old);
                        setting_set('logo_' . $i, $path);
                    }
                }
            }

            flash('success', 'تنظیمات با موفقیت ذخیره شد.');
            redirect(base_url('admin/settings.php'));
        } catch (Throwable $e) {
            $error = $e->getMessage();
        }
    }
}

$aboutPage = cms_page('about') ?? $aboutPage;
$contactPage = cms_page('contact') ?? $contactPage;

require dirname(__DIR__) . '/includes/layout/admin_header.php';
?>

<h1 class="h3 mb-4">تنظیمات سایت، لوگو و صفحات</h1>

<?php if ($error): ?>
    <div class="alert alert-danger"><?= e($error) ?></div>
<?php endif; ?>
<?php if ($msg = flash('success')): ?>
    <div class="alert alert-success"><?= e($msg) ?></div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h2 class="h5 mb-3">اطلاعات عمومی</h2>
            <div class="mb-3">
                <label class="form-label">نام سایت</label>
                <input type="text" name="site_name" class="form-control" value="<?= e(setting('site_name', 'فن‌آموز')) ?>">
            </div>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">تلفن تماس</label>
                    <input type="text" name="contact_phone" class="form-control" value="<?= e(setting('contact_phone')) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">ایمیل</label>
                    <input type="email" name="contact_email" class="form-control" value="<?= e(setting('contact_email')) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">آدرس</label>
                    <input type="text" name="contact_address" class="form-control" value="<?= e(setting('contact_address')) ?>">
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h2 class="h5 mb-3">صفحه درباره ما</h2>
            <div class="mb-2">
                <label class="form-label">عنوان</label>
                <input type="text" name="about_title" class="form-control" value="<?= e($aboutPage['title'] ?? '') ?>">
            </div>
            <div class="mb-0">
                <label class="form-label">متن (HTML مجاز)</label>
				
				<textarea  name="about_body" class="form-control" rows="6"><?= e($aboutPage['body'] ?? '') ?></textarea>
                           </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h2 class="h5 mb-3">صفحه تماس با ما</h2>
            <div class="mb-2">
                <label class="form-label">عنوان</label>
                <input type="text" name="contact_title" class="form-control" value="<?= e($contactPage['title'] ?? '') ?>">
            </div>
            <div class="mb-0">
                <label class="form-label">متن تکمیلی (HTML مجاز)</label>
                <textarea name="contact_body" class="form-control" rows="4"><?= e($contactPage['body'] ?? '') ?></textarea>
                <p class="small text-muted mt-1">اطلاعات تماس از فیلدهای بالا نیز در صفحه نمایش داده می‌شود.</p>
            </div>
        </div>
    </div>

    <?php for ($i = 1; $i <= 3; $i++): ?>
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <h2 class="h6 mb-3">لوگو <?= $i ?></h2>
            <div class="row g-3 align-items-center">
                <div class="col-md-3 text-center">
                    <img src="<?= e(logo_url($i)) ?>" alt="" class="img-fluid border rounded p-2" style="max-height:80px">
                </div>
                <div class="col-md-9">
                    <div class="mb-2">
                        <label class="form-label">آپلود تصویر جدید</label>
                        <input type="file" name="logo_<?= $i ?>_file" class="form-control" accept="image/*">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">متن جایگزین (alt)</label>
                        <input type="text" name="logo_<?= $i ?>_alt" class="form-control" value="<?= e(setting('logo_' . $i . '_alt')) ?>">
                    </div>
                    <div class="mb-0">
                        <label class="form-label">لینک (اختیاری)</label>
                        <input type="url" name="logo_<?= $i ?>_url" class="form-control" value="<?= e(setting('logo_' . $i . '_url')) ?>" placeholder="https://">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endfor; ?>

    <button type="submit" class="btn btn-primary">ذخیره تنظیمات</button>
</form>

<?php require dirname(__DIR__) . '/includes/layout/admin_footer.php'; ?>
