<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_auth('admin');

$pageTitle = 'مدیریت اطلاعیه‌ها';
$activeMenu = 'announcements';
$errors = [];
$edit = null;

if (isset($_GET['edit'])) {
    $stmt = db()->prepare('SELECT * FROM announcements WHERE id = ?');
    $stmt->execute([(int) $_GET['edit']]);
    $edit = $stmt->fetch() ?: null;
}

if (is_post()) {
    if (!verify_csrf()) {
        $errors['general'] = 'درخواست نامعتبر است.';
    } else {
        $action = $_POST['action'] ?? 'save';
        try {
            if ($action === 'delete') {
                $stmt = db()->prepare('SELECT image FROM announcements WHERE id = ?');
                $stmt->execute([(int) $_POST['id']]);
                $row = $stmt->fetch();
                if ($row && $row['image']) delete_upload($row['image']);
                db()->prepare('DELETE FROM announcements WHERE id = ?')->execute([(int) $_POST['id']]);
                flash('success', 'اطلاعیه حذف شد.');
                redirect(base_url('admin/announcements.php'));
            }

            $id = (int) ($_POST['id'] ?? 0);
            $title = trim($_POST['title'] ?? '');
            $summary = trim($_POST['summary'] ?? '');
            $body = trim($_POST['body'] ?? '');
            $linkUrl = trim($_POST['link_url'] ?? '') ?: null;

            if ($title === '') {
                $errors['title'] = 'عنوان الزامی است.';
            }

            $image = $edit['image'] ?? null;
            if (!empty($_FILES['image']['name'])) {
                if ($image) delete_upload($image);
                $image = handle_upload($_FILES['image'], 'announcements');
            }

            if (empty($errors)) {
                $fields = [
                    $title, $summary, $body, $linkUrl, $image,
                    isset($_POST['is_active']) ? 1 : 0,
                    isset($_POST['show_on_home']) ? 1 : 0,
                    (int) ($_POST['sort_order'] ?? 0),
                    $_POST['published_at'] ?: date('Y-m-d H:i:s')
                ];
                if ($id > 0) {
                    $fields[] = $id;
                    db()->prepare("UPDATE announcements SET title=?, summary=?, body=?, link_url=?, image=?, is_active=?, show_on_home=?, sort_order=?, published_at=? WHERE id=?")->execute($fields);
                    flash('success', 'اطلاعیه ویرایش شد.');
                } else {
                    db()->prepare("INSERT INTO announcements (title, summary, body, link_url, image, is_active, show_on_home, sort_order, published_at) VALUES (?,?,?,?,?,?,?,?,?)")->execute($fields);
                    flash('success', 'اطلاعیه ایجاد شد.');
                }
                redirect(base_url('admin/announcements.php'));
            }
        } catch (Throwable $e) {
            $errors['general'] = $e->getMessage();
        }
    }
}

$items = db()->query('SELECT * FROM announcements ORDER BY sort_order ASC, id DESC')->fetchAll();

require dirname(__DIR__) . '/includes/layout/admin_header.php';
?>

<h1 class="h3 mb-4">مدیریت اطلاعیه‌ها</h1>
<?php if (!empty($errors['general'])): ?><div class="alert alert-danger"><?= e($errors['general']) ?></div><?php endif; ?>
<?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h2 class="h6 mb-3"><?= $edit ? 'ویرایش اطلاعیه' : 'افزودن اطلاعیه' ?></h2>
                <form method="post" enctype="multipart/form-data" novalidate>
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= (int) ($edit['id'] ?? 0) ?>">

                    <div class="mb-2">
                        <label class="form-label">عنوان <span class="text-danger">*</span></label>
                        <input name="title" class="form-control <?= isset($errors['title']) ? 'is-invalid' : '' ?>" value="<?= old('title', $edit['title'] ?? '') ?>">
                        <div class="invalid-feedback"><?= $errors['title'] ?? '' ?></div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">خلاصه</label>
                        <textarea name="summary" class="form-control" rows="2"><?= old('summary', $edit['summary'] ?? '') ?></textarea>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">متن کامل</label>
                        <textarea name="body" class="form-control tinymce" rows="4"><?= old('body', $edit['body'] ?? '') ?></textarea>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">لینک (اختیاری)</label>
                        <input name="link_url" class="form-control" value="<?= old('link_url', $edit['link_url'] ?? '') ?>">
                    </div>

                    <div class="mb-2">
                        <label class="form-label">تصویر بنر</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>

                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label">ترتیب</label>
                            <input type="number" name="sort_order" class="form-control" value="<?= old('sort_order', $edit['sort_order'] ?? 0) ?>">
                        </div>
                        <div class="col-6">
                            <label class="form-label">تاریخ انتشار</label>
                            <input type="datetime-local" name="published_at" class="form-control" value="<?= $edit && $edit['published_at'] ? date('Y-m-d\TH:i', strtotime($edit['published_at'])) : '' ?>">
                        </div>
                    </div>

                    <div class="form-check mb-1">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="ia" <?= ((int)($edit['is_active'] ?? 1) === 1) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="ia">فعال</label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="show_on_home" value="1" id="sh" <?= ((int)($edit['show_on_home'] ?? 1) === 1) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="sh">نمایش در کروسل صفحه اصلی</label>
                    </div>

                    <button class="btn btn-primary"><?= $edit ? 'ذخیره' : 'افزودن' ?></button>
                    <?php if ($edit): ?><a href="announcements.php" class="btn btn-link">انصراف</a><?php endif; ?>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card border-0 shadow-sm table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light"><tr><th>عنوان</th><th>فعال</th><th>خانه</th><th></th></tr></thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= e($item['title']) ?></td>
                        <td><?= $item['is_active'] ? '✓' : '---' ?></td>
                        <td><?= $item['show_on_home'] ? '✓' : '---' ?></td>
                        <td class="text-nowrap">
                            <a href="?edit=<?= (int) $item['id'] ?>" class="btn btn-sm btn-outline-primary">ویرایش</a>
                            <form method="post" class="d-inline" onsubmit="return confirm('حذف اطلاعیه؟')">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                                <button class="btn btn-sm btn-outline-danger">حذف</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require dirname(__DIR__) . '/includes/layout/admin_footer.php'; ?>