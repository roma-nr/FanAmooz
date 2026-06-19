<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_auth('admin');

$pageTitle = 'لینک‌های مفید';
$activeMenu = 'links';
$error = null;
$edit = null;

if (isset($_GET['edit'])) {
    $stmt = db()->prepare('SELECT * FROM useful_links WHERE id = ?');
    $stmt->execute([(int) $_GET['edit']]);
    $edit = $stmt->fetch() ?: null;
}

if (is_post()) {
    if (!verify_csrf()) {
        $error = 'درخواست نامعتبر است.';
    } else {
        $action = $_POST['action'] ?? 'save';
        try {
            if ($action === 'delete') {
                $stmt = db()->prepare('SELECT icon FROM useful_links WHERE id = ?');
                $stmt->execute([(int) $_POST['id']]);
                $row = $stmt->fetch();
                if ($row && $row['icon']) {
                    delete_upload($row['icon']);
                }
                db()->prepare('DELETE FROM useful_links WHERE id = ?')->execute([(int) $_POST['id']]);
                flash('success', 'لینک حذف شد.');
            } else {
                $id = (int) ($_POST['id'] ?? 0);
                $title = trim($_POST['title'] ?? '');
                $url = trim($_POST['url'] ?? '');
                if ($title === '' || $url === '') {
                    throw new RuntimeException('عنوان و آدرس لینک الزامی است.');
                }
                $icon = $edit['icon'] ?? null;
                if (!empty($_FILES['icon']['name'])) {
                    delete_upload($icon);
                    $icon = handle_upload($_FILES['icon'], 'links');
                }
                $fields = [
                    $title,
                    trim($_POST['description'] ?? ''),
                    $url,
                    $icon,
                    isset($_POST['is_active']) ? 1 : 0,
                    isset($_POST['show_on_home']) ? 1 : 0,
                    (int) ($_POST['sort_order'] ?? 0),
                ];
                if ($id > 0) {
                    $fields[] = $id;
                    db()->prepare(
                        'UPDATE useful_links SET title=?, description=?, url=?, icon=?, is_active=?, show_on_home=?, sort_order=? WHERE id=?'
                    )->execute($fields);
                    flash('success', 'لینک ویرایش شد.');
                } else {
                    db()->prepare(
                        'INSERT INTO useful_links (title, description, url, icon, is_active, show_on_home, sort_order) VALUES (?,?,?,?,?,?,?)'
                    )->execute($fields);
                    flash('success', 'لینک افزوده شد.');
                }
            }
            redirect(base_url('admin/useful_links.php'));
        } catch (Throwable $e) {
            $error = $e->getMessage();
        }
    }
}

$items = db()->query('SELECT * FROM useful_links ORDER BY sort_order ASC, id DESC')->fetchAll();

require dirname(__DIR__) . '/includes/layout/admin_header.php';
?>

<h1 class="h3 mb-4">مدیریت لینک‌های مفید</h1>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h2 class="h6 mb-3"><?= $edit ? 'ویرایش' : 'افزودن' ?> لینک</h2>
                <form method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= (int) ($edit['id'] ?? 0) ?>">
                    <div class="mb-2">
                        <label class="form-label">عنوان *</label>
                        <input name="title" class="form-control" required value="<?= e($edit['title'] ?? '') ?>">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">توضیح</label>
                        <input name="description" class="form-control" required value="<?= e($edit['description'] ?? '') ?>">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">آدرس URL *</label>
                        <input name="url" type="url" class="form-control" required value="<?= e($edit['url'] ?? '') ?>">
                    </div>
                    
                    <div class="mb-2">
                        <label class="form-label">ترتیب</label>
                        <input type="number" name="sort_order" class="form-control" value="<?= (int) ($edit['sort_order'] ?? 0) ?>">
                    </div>
                    <div class="form-check mb-1">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="la" <?= ($edit['is_active'] ?? 1) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="la">فعال</label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="show_on_home" value="1" id="lh" <?= ($edit['show_on_home'] ?? 1) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="lh">نمایش در کروسل صفحه اصلی</label>
                    </div>
                    <button class="btn btn-primary"><?= $edit ? 'ذخیره' : 'افزودن' ?></button>
                    <?php if ($edit): ?><a href="useful_links.php" class="btn btn-link">انصراف</a><?php endif; ?>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light"><tr><th>عنوان</th><th>URL</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= e($item['title']) ?></td>
                        <td class="small text-truncate" style="max-width:180px"><?= e($item['url']) ?></td>
                        <td class="text-nowrap">
                            <a href="?edit=<?= (int) $item['id'] ?>" class="btn btn-sm btn-outline-primary">ویرایش</a>
                            <form method="post" class="d-inline" onsubmit="return confirm('حذف؟')">
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
