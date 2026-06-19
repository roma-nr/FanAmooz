<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_auth('admin');

$pageTitle = 'مدیریت اساتید';
$activeMenu = 'teachers_list';
$errors = [];
$edit = null;

if (isset($_GET['edit'])) {
    $stmt = db()->prepare("SELECT * FROM users WHERE id = ? AND role = 'teacher'");
    $stmt->execute([(int) $_GET['edit']]);
    $edit = $stmt->fetch() ?: null;
}

if (is_post()) {
    if (!verify_csrf()) {
        $errors['general'] = 'درخواست نامعتبر است.';
    } else {
        try {
            if (($_POST['action'] ?? '') === 'delete') {
                db()->prepare("DELETE FROM users WHERE id = ? AND role = 'teacher'")->execute([(int) $_POST['id']]);
                flash('success', 'استاد حذف شد.');
                redirect(base_url('admin/teachers.php'));
            }

            $fullName = trim($_POST['full_name'] ?? '');
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '') ?: null;
            $phone = trim($_POST['phone'] ?? '') ?: null;
            $id = (int) ($_POST['id'] ?? 0);
            $status = $_POST['teacher_status'] ?? 'approved';
            $password = $_POST['password'] ?? '';

            if ($fullName === '') {
                $errors['full_name'] = 'نام کامل الزامی است.';
            }
            if ($username === '') {
                $errors['username'] = 'نام کاربری الزامی است.';
            }

            if (empty($errors)) {
                if ($id > 0) {
                    $dup = db()->prepare("SELECT id FROM users WHERE username = ? AND id != ? LIMIT 1");
                    $dup->execute([$username, $id]);
                    if ($dup->fetch()) {
                        throw new RuntimeException('نام کاربری تکراری است.');
                    }
                    db()->prepare("UPDATE users SET full_name=?, username=?, email=?, phone=?, teacher_status=?, is_active=? WHERE id=? AND role='teacher'")->execute([
                        $fullName, $username, $email, $phone, $status,
                        isset($_POST['is_active']) ? 1 : 0,
                        $id
                    ]);
                    if (!empty($password)) {
                        $hash = password_hash($password, PASSWORD_BCRYPT);
                        db()->prepare("UPDATE users SET password_hash = ? WHERE id = ?")->execute([$hash, $id]);
                    }
                    flash('success', 'استاد ویرایش شد.');
                } else {
                    if ($password === '') {
                        $errors['password'] = 'رمز عبور برای استاد جدید الزامی است.';
                    } else {
                        create_teacher_user($fullName, $username, $password, $email, $phone);
                        flash('success', 'استاد افزوده شد.');
                    }
                }
                if (empty($errors)) redirect(base_url('admin/teachers.php'));
            }
        } catch (Throwable $e) {
            $errors['general'] = $e->getMessage();
        }
    }
}

$q = trim($_GET['q'] ?? '');
$sql = "SELECT u.* FROM users u WHERE u.role = 'teacher'";
$params = [];
if ($q !== '') {
    $sql .= " AND (u.full_name LIKE ? OR u.username LIKE ? OR u.email LIKE ?)";
    $like = "%$q%";
    $params = [$like, $like, $like];
}
$sql .= " ORDER BY u.id DESC LIMIT 200";
$stmt = db()->prepare($sql);
$stmt->execute($params);
$items = $stmt->fetchAll();

require dirname(__DIR__) . '/includes/layout/admin_header.php';
?>

<h1 class="h3 mb-4">مدیریت اساتید</h1>
<p class="text-muted small">افزودن مستقیم استاد تأییدشده یا بررسی <a href="teacher_applications.php">درخواست‌های همکاری</a></p>

<?php if (!empty($errors['general'])): ?><div class="alert alert-danger"><?= e($errors['general']) ?></div><?php endif; ?>
<?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h2 class="h6 mb-3"><?= $edit ? 'ویرایش استاد' : 'افزودن استاد' ?></h2>
                <form method="post" novalidate>
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= (int) ($edit['id'] ?? 0) ?>">

                    <div class="mb-2">
                        <label class="form-label">نام کامل <span class="text-danger">*</span></label>
                        <input name="full_name" class="form-control <?= isset($errors['full_name']) ? 'is-invalid' : '' ?>" value="<?= old('full_name', $edit['full_name'] ?? '') ?>">
                        <div class="invalid-feedback"><?= $errors['full_name'] ?? '' ?></div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">نام کاربری <span class="text-danger">*</span></label>
                        <input name="username" class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>" dir="ltr" value="<?= old('username', $edit['username'] ?? '') ?>">
                        <div class="invalid-feedback"><?= $errors['username'] ?? '' ?></div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">ایمیل</label>
                        <input type="email" name="email" class="form-control" dir="ltr" value="<?= old('email', $edit['email'] ?? '') ?>">
                    </div>

                    <div class="mb-2">
                        <label class="form-label">تلفن</label>
                        <input name="phone" class="form-control" value="<?= old('phone', $edit['phone'] ?? '') ?>" maxlength="11">
                    </div>

                    <div class="mb-2">
                        <label class="form-label">رمز عبور <?= $edit ? '(خالی = بدون تغییر)' : '*' ?></label>
                        <input type="password" name="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" <?= $edit ? '' : 'required' ?>>
                        <div class="invalid-feedback"><?= $errors['password'] ?? '' ?></div>
                    </div>

                    <?php if ($edit): ?>
                    <div class="mb-2">
                        <label class="form-label">وضعیت همکاری</label>
                        <select name="teacher_status" class="form-select searchable-select">
                            <option value="approved" <?= old('teacher_status', $edit['teacher_status'] ?? '') == 'approved' ? 'selected' : '' ?>>تأیید شده</option>
                            <option value="pending" <?= old('teacher_status', $edit['teacher_status'] ?? '') == 'pending' ? 'selected' : '' ?>>در انتظار</option>
                            <option value="rejected" <?= old('teacher_status', $edit['teacher_status'] ?? '') == 'rejected' ? 'selected' : '' ?>>رد شده</option>
                        </select>
                    </div>
                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" name="is_active" value="1" id="ta" <?= ((int)($edit['is_active'] ?? 1) === 1) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="ta">فعال</label>
                    </div>
                    <?php endif; ?>

                    <button class="btn btn-primary"><?= $edit ? 'ذخیره' : 'افزودن' ?></button>
                    <?php if ($edit): ?><a href="teachers.php" class="btn btn-link">انصراف</a><?php endif; ?>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <form method="get" class="mb-3">
            <div class="input-group">
                <input type="search" name="q" class="form-control" placeholder="جستجو نام، نام کاربری، ایمیل" value="<?= e($q) ?>">
                <button class="btn btn-outline-primary">جستجو</button>
            </div>
        </form>

        <div class="card border-0 shadow-sm table-responsive">
            <table class="table table-hover mb-0 small">
                <thead class="table-light"><tr><th>نام</th><th>نام کاربری</th><th>وضعیت</th><th></th></tr></thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= e($item['full_name']) ?></td>
                        <td dir="ltr"><?= e($item['username'] ?? '') ?></td>
                        <td><?= e($item['teacher_status']) ?></td>
                        <td class="text-nowrap">
                            <a href="?edit=<?= (int) $item['id'] ?>" class="btn btn-sm btn-outline-primary">ویرایش</a>
                            <form method="post" class="d-inline" onsubmit="return confirm('حذف استاد؟')">
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