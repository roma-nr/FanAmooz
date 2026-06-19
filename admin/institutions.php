<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_auth('admin');

$pageTitle = 'مدیریت دانشکده‌ها';
$activeMenu = 'institutions';
$error = null;
$edit = null;
$provinces = db()->query('SELECT id, name FROM provinces ORDER BY sort_order, name')->fetchAll();

if (isset($_GET['edit'])) {
    $stmt = db()->prepare('SELECT * FROM institutions WHERE id = ?');
    $stmt->execute([(int) $_GET['edit']]);
    $edit = $stmt->fetch() ?: null;
}

if (is_post()) {
    if (!verify_csrf()) {
        $error = 'درخواست نامعتبر است.';
    } else {
        try {
            if (($_POST['action'] ?? '') === 'delete') {
                db()->prepare('DELETE FROM institutions WHERE id = ?')->execute([(int) $_POST['id']]);
                flash('success', 'دانشکده حذف شد.');
            } else {
                $name = trim($_POST['name'] ?? '');
                $provinceId = (int) ($_POST['province_id'] ?? 0);
                if ($name === '' || $provinceId <= 0) {
                    throw new RuntimeException('نام و استان الزامی است.');
                }
                $slug = trim($_POST['slug'] ?? '') ?: slugify($name);
                $id = (int) ($_POST['id'] ?? 0);
                $fields = [$provinceId, $name, $slug, isset($_POST['is_active']) ? 1 : 0, (int) ($_POST['sort_order'] ?? 0)];
                if ($id > 0) {
                    $fields[] = $id;
                    db()->prepare('UPDATE institutions SET province_id=?, name=?, slug=?, is_active=?, sort_order=? WHERE id=?')->execute($fields);
                    flash('success', 'دانشکده ویرایش شد.');
                } else {
                    db()->prepare('INSERT INTO institutions (province_id, name, slug, is_active, sort_order) VALUES (?,?,?,?,?)')->execute($fields);
                    flash('success', 'دانشکده افزوده شد.');
                }
            }
            redirect(base_url('admin/institutions.php'));
        } catch (Throwable $e) {
            $error = $e->getMessage();
        }
    }
}

$filterProvince = (int) ($_GET['province_id'] ?? 0);
$sql = 'SELECT i.*, p.name AS province_name FROM institutions i JOIN provinces p ON p.id = i.province_id';
$params = [];
if ($filterProvince > 0) {
    $sql .= ' WHERE i.province_id = ?';
    $params[] = $filterProvince;
}
$sql .= ' ORDER BY p.sort_order, i.sort_order, i.name';
$stmt = db()->prepare($sql);
$stmt->execute($params);
$items = $stmt->fetchAll();

require dirname(__DIR__) . '/includes/layout/admin_header.php';
?>

<h1 class="h3 mb-4">مدیریت دانشکده / دانشگاه</h1>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>

<form method="get" class="row g-2 mb-3 align-items-end">
    <div class="col-auto">
        <label class="form-label">فیلتر استان</label>
        <select name="province_id" class="form-select" onchange="this.form.submit()">
            <option value="">همه</option>
            <?php foreach ($provinces as $p): ?>
                <option value="<?= (int)$p['id'] ?>" <?= $filterProvince === (int)$p['id'] ? 'selected' : '' ?>><?= e($p['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</form>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h2 class="h6 mb-3"><?= $edit ? 'ویرایش' : 'افزودن' ?> دانشکده</h2>
                <form method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= (int) ($edit['id'] ?? 0) ?>">
                    <div class="mb-2">
                        <label class="form-label">استان *</label>
                        <select name="province_id" class="form-select searchable-select" required>
                            <?php foreach ($provinces as $p): ?>
                                <option value="<?= (int)$p['id'] ?>" <?= (int)($edit['province_id'] ?? $filterProvince) === (int)$p['id'] ? 'selected' : '' ?>><?= e($p['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-2"><label class="form-label">نام *</label>
                        <input name="name" class="form-control" required value="<?= e($edit['name'] ?? '') ?>"></div>
                    <div class="mb-2"><label class="form-label">نامک</label>
                        <input name="slug" class="form-control" dir="ltr" value="<?= e($edit['slug'] ?? '') ?>"></div>
                    <div class="mb-2"><label class="form-label">ترتیب</label>
                        <input type="number" name="sort_order" class="form-control" value="<?= (int)($edit['sort_order'] ?? 0) ?>"></div>
                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" name="is_active" value="1" id="ia" <?= ($edit['is_active'] ?? 1) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="ia">فعال</label>
                    </div>
                    <button class="btn btn-primary"><?= $edit ? 'ذخیره' : 'افزودن' ?></button>
                    <?php if ($edit): ?><a href="institutions.php" class="btn btn-link">انصراف</a><?php endif; ?>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm table-responsive">
            <table class="table mb-0">
                <thead class="table-light"><tr><th>نام</th><th>استان</th><th>ID</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= e($item['name']) ?></td>
                        <td><?= e($item['province_name']) ?></td>
                        <td><code><?= (int)$item['id'] ?></code></td>
                        <td class="text-nowrap">
                            <a href="?edit=<?= (int)$item['id'] ?>" class="btn btn-sm btn-outline-primary">ویرایش</a>
                            <form method="post" class="d-inline" onsubmit="return confirm('حذف؟')">
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
