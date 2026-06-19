<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_auth('admin');

$pageTitle = 'مدیریت دانشجویان';
$activeMenu = 'students';
$errors = [];
$edit = null;

$institutions = db()->query("SELECT i.id, i.name, p.name AS province_name FROM institutions i JOIN provinces p ON p.id = i.province_id ORDER BY p.name, i.name")->fetchAll();

if (isset($_GET['edit'])) {
    $stmt = db()->prepare("SELECT * FROM users WHERE id = ? AND role = 'student'");
    $stmt->execute([(int) $_GET['edit']]);
    $edit = $stmt->fetch() ?: null;
}

if (is_post()) {
    if (!verify_csrf()) {
        $errors['general'] = 'درخواست نامعتبر است.';
    } else {
        try {
            if (($_POST['action'] ?? '') === 'delete') {
                db()->prepare("DELETE FROM users WHERE id = ? AND role = 'student'")->execute([(int) $_POST['id']]);
                flash('success', 'دانشجو حذف شد.');
                redirect(base_url('admin/students.php'));
            }

            $fullName = trim($_POST['full_name'] ?? '');
            $studentCode = trim($_POST['student_code'] ?? '');
            $nationalId = trim($_POST['national_id'] ?? '');
            $institutionId = (int) ($_POST['institution_id'] ?? 0);
            $phone = trim($_POST['phone'] ?? '') ?: null;
            $id = (int) ($_POST['id'] ?? 0);

            if ($fullName === '') {
                $errors['full_name'] = 'نام کامل الزامی است.';
            }
            if ($studentCode === '') {
                $errors['student_code'] = 'کد دانشجویی الزامی است.';
            }
            if ($nationalId === '') {
                $errors['national_id'] = 'کد ملی الزامی است.';
            } elseif (!validate_national_id($nationalId)) {
                $errors['national_id'] = 'کد ملی نامعتبر است (۱۰ رقم).';
            }
            if ($institutionId <= 0) {
                $errors['institution_id'] = 'انتخاب دانشکده الزامی است.';
            }

            if (empty($errors)) {
                $inst = db()->prepare('SELECT province_id FROM institutions WHERE id = ?');
                $inst->execute([$institutionId]);
                $instRow = $inst->fetch();
                if (!$instRow) {
                    throw new RuntimeException('دانشکده نامعتبر است.');
                }

                if ($id > 0) {
                    db()->prepare("UPDATE users SET full_name=?, phone=?, province_id=?, institution_id=?, student_code=?, national_id=?, is_active=?, first_login_done=? WHERE id=? AND role='student'")->execute([
                        $fullName, $phone, (int)$instRow['province_id'], $institutionId,
                        $studentCode, $nationalId,
                        isset($_POST['is_active']) ? 1 : 0,
                        isset($_POST['first_login_done']) ? 1 : 0,
                        $id
                    ]);
                    flash('success', 'دانشجو ویرایش شد.');
                } else {
                    create_student_user($fullName, $studentCode, $nationalId, $institutionId, $phone);
                    flash('success', 'دانشجو افزوده شد.');
                }
                redirect(base_url('admin/students.php'));
            }
        } catch (Throwable $e) {
            $errors['general'] = $e->getMessage();
        }
    }
}

$q = trim($_GET['q'] ?? '');
$filterInst = (int) ($_GET['institution_id'] ?? 0);
$sql = "SELECT u.*, i.name AS institution_name, p.name AS province_name FROM users u LEFT JOIN institutions i ON i.id = u.institution_id LEFT JOIN provinces p ON p.id = u.province_id WHERE u.role = 'student'";
$params = [];
if ($q !== '') {
    $sql .= " AND (u.full_name LIKE ? OR u.student_code LIKE ? OR u.national_id LIKE ?)";
    $like = "%$q%";
    $params = array_merge($params, [$like, $like, $like]);
}
if ($filterInst > 0) {
    $sql .= " AND u.institution_id = ?";
    $params[] = $filterInst;
}
$sql .= " ORDER BY u.id DESC LIMIT 200";
$stmt = db()->prepare($sql);
$stmt->execute($params);
$items = $stmt->fetchAll();

require dirname(__DIR__) . '/includes/layout/admin_header.php';
?>

<h1 class="h3 mb-4">مدیریت دانشجویان</h1>
<?php if (!empty($errors['general'])): ?><div class="alert alert-danger"><?= e($errors['general']) ?></div><?php endif; ?>
<?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>

<div class="mb-3">
    <a href="<?= e(base_url('admin/students_import.php')) ?>" class="btn btn-success btn-sm"><i class="bi bi-upload"></i> ورود گروهی CSV</a>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h2 class="h6 mb-3"><?= $edit ? 'ویرایش دانشجو' : 'افزودن دانشجو' ?></h2>
                <form method="post" novalidate>
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= (int) ($edit['id'] ?? 0) ?>">

                    <div class="mb-2">
                        <label class="form-label">نام کامل <span class="text-danger">*</span></label>
                        <input name="full_name" class="form-control <?= isset($errors['full_name']) ? 'is-invalid' : '' ?>" value="<?= old('full_name', $edit['full_name'] ?? '') ?>">
                        <div class="invalid-feedback"><?= $errors['full_name'] ?? '' ?></div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">دانشکده <span class="text-danger">*</span></label>
                        <select name="institution_id" class="form-select searchable-select <?= isset($errors['institution_id']) ? 'is-invalid' : '' ?>">
                            <option value="">انتخاب دانشکده</option>
                            <?php foreach ($institutions as $i): ?>
                                <option value="<?= (int) $i['id'] ?>" <?= old('institution_id', $edit['institution_id'] ?? 0) == $i['id'] ? 'selected' : '' ?>><?= e($i['province_name'] . ' - ' . $i['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback"><?= $errors['institution_id'] ?? '' ?></div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">کد دانشجویی <span class="text-danger">*</span></label>
                        <input name="student_code" class="form-control <?= isset($errors['student_code']) ? 'is-invalid' : '' ?>" value="<?= old('student_code', $edit['student_code'] ?? '') ?>" maxlength="14">
                        <div class="invalid-feedback"><?= $errors['student_code'] ?? '' ?></div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">کد ملی <span class="text-danger">*</span></label>
                        <input name="national_id" class="form-control <?= isset($errors['national_id']) ? 'is-invalid' : '' ?>" maxlength="10" dir="ltr" value="<?= old('national_id', $edit['national_id'] ?? '') ?>">
                        <div class="invalid-feedback"><?= $errors['national_id'] ?? '' ?></div>
                        <small class="text-muted">۱۰ رقم</small>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">تلفن</label>
                        <input name="phone" class="form-control" value="<?= old('phone', $edit['phone'] ?? '') ?>" maxlength="11">
                    </div>

                    <?php if ($edit): ?>
                    <div class="form-check mb-1">
                        <input type="checkbox" class="form-check-input" name="is_active" value="1" id="sa" <?= ((int)($edit['is_active'] ?? 1) === 1) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="sa">فعال</label>
                    </div>
                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" name="first_login_done" value="1" id="sf" <?= ((int)($edit['first_login_done'] ?? 0) === 1) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="sf">تکمیل onboarding</label>
                    </div>
                    <?php endif; ?>

                    <button class="btn btn-primary"><?= $edit ? 'ذخیره' : 'افزودن' ?></button>
                    <?php if ($edit): ?><a href="students.php" class="btn btn-link">انصراف</a><?php endif; ?>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <form method="get" class="row g-2 mb-3">
            <div class="col-md-5"><input type="search" name="q" class="form-control" placeholder="جستجو نام، کد، کد ملی" value="<?= e($q) ?>"></div>
            <div class="col-md-5">
                <select name="institution_id" class="form-select searchable-select">
                    <option value="">همه دانشکده‌ها</option>
                    <?php foreach ($institutions as $i): ?>
                        <option value="<?= (int) $i['id'] ?>" <?= $filterInst == $i['id'] ? 'selected' : '' ?>><?= e($i['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2"><button class="btn btn-outline-primary w-100">فیلتر</button></div>
        </form>

        <div class="card border-0 shadow-sm table-responsive">
            <table class="table table-hover mb-0 small">
                <thead class="table-light"><tr><th>نام</th><th>کد</th><th>کد ملی</th><th>دانشکده</th><th></th></tr></thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= e($item['full_name']) ?></td>
                        <td><?= e($item['student_code']) ?></td>
                        <td dir="ltr"><?= e($item['national_id']) ?></td>
                        <td><?= e($item['institution_name'] ?? '') ?></td>
                        <td class="text-nowrap">
                            <a href="?edit=<?= (int) $item['id'] ?>" class="btn btn-sm btn-outline-primary">ویرایش</a>
                            <form method="post" class="d-inline" onsubmit="return confirm('حذف دانشجو؟')">
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