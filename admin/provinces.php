<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_auth('admin');

$pageTitle = 'مدیریت استان‌ها';
$activeMenu = 'provinces';
$error = null;
$edit = null;

if (isset($_GET['edit'])) {
    $stmt = db()->prepare('SELECT * FROM provinces WHERE id = ?');
    $stmt->execute([(int) $_GET['edit']]);
    $edit = $stmt->fetch() ?: null;
}

if (is_post()) {
    if (!verify_csrf()) {
        $error = 'درخواست نامعتبر است.';
    } else {
        try {
            if (($_POST['action'] ?? '') === 'delete') {
                db()->prepare('DELETE FROM provinces WHERE id = ?')->execute([(int) $_POST['id']]);
                flash('success', 'استان حذف شد.');
            } else {
                $name = trim($_POST['name'] ?? '');
                if ($name === '') {
                    throw new RuntimeException('نام استان الزامی است.');
                }
                $slug = trim($_POST['slug'] ?? '') ?: slugify($name);
                $id = (int) ($_POST['id'] ?? 0);
                $fields = [$name, $slug, isset($_POST['is_active']) ? 1 : 0, (int) ($_POST['sort_order'] ?? 0)];
                if ($id > 0) {
                    $fields[] = $id;
                    db()->prepare('UPDATE provinces SET name=?, slug=?, is_active=?, sort_order=? WHERE id=?')->execute($fields);
                    flash('success', 'استان ویرایش شد.');
                } else {
                    db()->prepare('INSERT INTO provinces (name, slug, is_active, sort_order) VALUES (?,?,?,?)')->execute($fields);
                    flash('success', 'استان افزوده شد.');
                }
            }
            redirect(base_url('admin/provinces.php'));
        } catch (Throwable $e) {
            $error = $e->getMessage();
        }
    }
}

$items = db()->query('SELECT * FROM provinces ORDER BY sort_order, name')->fetchAll();

require dirname(__DIR__) . '/includes/layout/admin_header.php';
?>

<h1 class="h3 mb-4">مدیریت استان‌ها</h1>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h2 class="h6 mb-3"><?= $edit ? 'ویرایش' : 'افزودن' ?> استان</h2>
                <form method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= (int) ($edit['id'] ?? 0) ?>">
                    <div class="mb-2"><label class="form-label">نام *</label>
                        <input name="name" class="form-control" required value="<?= e($edit['name'] ?? '') ?>"></div>
                    <div class="mb-2"><label class="form-label">نامک (slug)</label>
                        <input name="slug" class="form-control" dir="ltr" value="<?= e($edit['slug'] ?? '') ?>"></div>
                    <div class="mb-2"><label class="form-label">ترتیب</label>
                        <input type="number" name="sort_order" class="form-control" value="<?= (int)($edit['sort_order'] ?? 0) ?>"></div>
                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" name="is_active" value="1" id="pa" <?= ($edit['is_active'] ?? 1) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="pa">فعال</label>
                    </div>
                    <button class="btn btn-primary"><?= $edit ? 'ذخیره' : 'افزودن' ?></button>
                    <?php if ($edit): ?><a href="provinces.php" class="btn btn-link">انصراف</a><?php endif; ?>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm table-responsive">
            <table class="table mb-0">
                <thead class="table-light"><tr><th>نام</th><th>slug</th><th>فعال</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= e($item['name']) ?></td>
                        <td dir="ltr" class="small"><?= e($item['slug']) ?></td>
                        <td><?= $item['is_active'] ? '✓' : '—' ?></td>
                        <td class="text-nowrap">
                            <a href="?edit=<?= (int)$item['id'] ?>" class="btn btn-sm btn-outline-primary">ویرایش</a>
                            <form method="post" class="d-inline" onsubmit="return confirm('حذف استان؟ دانشکده‌های وابسته هم حذف می‌شوند.')">
                                <?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
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
