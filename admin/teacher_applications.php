<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_auth('admin');

$pageTitle = 'درخواست‌های همکاری اساتید';
$activeMenu = 'teachers';
$error = null;
$success = flash('success');

$filter = $_GET['status'] ?? 'pending';
$allowedFilters = ['all', 'pending', 'approved', 'rejected'];
if (!in_array($filter, $allowedFilters, true)) {
    $filter = 'pending';
}

$detailId = (int) ($_GET['id'] ?? 0);
$detail = null;

if ($detailId > 0) {
    $stmt = db()->prepare(
        "SELECT u.*, ta.education, ta.work_experience, ta.skills_summary, ta.resume_path, ta.admin_note AS app_admin_note,
                ta.submitted_at AS app_submitted_at, ta.reviewed_at AS app_reviewed_at
         FROM users u
         LEFT JOIN teacher_applications ta ON ta.user_id = u.id
         WHERE u.id = ? AND u.role = 'teacher' LIMIT 1"
    );
    $stmt->execute([$detailId]);
    $detail = $stmt->fetch() ?: null;
}

if (is_post()) {
    if (!verify_csrf()) {
        $error = 'درخواست نامعتبر است.';
    } else {
        $action = $_POST['action'] ?? '';
        $userId = (int) ($_POST['user_id'] ?? 0);
        $adminId = (int) auth_id();

        try {
            if ($userId <= 0) {
                throw new RuntimeException('کاربر نامعتبر است.');
            }

            $chk = db()->prepare("SELECT id, teacher_status FROM users WHERE id = ? AND role = 'teacher' LIMIT 1");
            $chk->execute([$userId]);
            $row = $chk->fetch();
            if (!$row) {
                throw new RuntimeException('استاد یافت نشد.');
            }

            if ($action === 'approve') {
                if ($row['teacher_status'] !== 'pending') {
                    throw new RuntimeException('فقط درخواست‌های «در حال بررسی» قابل تأیید هستند.');
                }
                db()->beginTransaction();
                db()->prepare("UPDATE users SET teacher_status = 'approved' WHERE id = ?")->execute([$userId]);
                db()->prepare(
                    'UPDATE teacher_applications SET reviewed_at = NOW(), reviewed_by = ?, admin_note = NULL WHERE user_id = ?'
                )->execute([$adminId, $userId]);
                db()->commit();
                flash('success', 'درخواست استاد تأیید شد.');
                redirect(base_url('admin/teacher_applications.php?id=' . $userId . '&status=all'));
            }

            if ($action === 'reject') {
                if ($row['teacher_status'] !== 'pending') {
                    throw new RuntimeException('فقط درخواست‌های «در حال بررسی» قابل رد هستند.');
                }
                $note = trim($_POST['admin_note'] ?? '');
                if (mb_strlen($note) < 5) {
                    throw new RuntimeException('دلیل رد را حداقل ۵ کاراکتر بنویسید.');
                }
                db()->beginTransaction();
                db()->prepare("UPDATE users SET teacher_status = 'rejected' WHERE id = ?")->execute([$userId]);
                db()->prepare(
                    'UPDATE teacher_applications SET admin_note = ?, reviewed_at = NOW(), reviewed_by = ? WHERE user_id = ?'
                )->execute([$note, $adminId, $userId]);
                db()->commit();
                flash('success', 'درخواست رد شد و استاد می‌تواند فرم را اصلاح کند.');
                redirect(base_url('admin/teacher_applications.php?id=' . $userId . '&status=all'));
            }
        } catch (RuntimeException $e) {
            if (db()->inTransaction()) {
                db()->rollBack();
            }
            $error = $e->getMessage();
        } catch (PDOException $e) {
            if (db()->inTransaction()) {
                db()->rollBack();
            }
            $error = 'خطای پایگاه داده: ' . $e->getMessage();
        }
    }
}

$sql = "SELECT u.id, u.full_name, u.username, u.email, u.national_id, u.teacher_status, u.created_at, ta.submitted_at
        FROM users u
        LEFT JOIN teacher_applications ta ON ta.user_id = u.id
        WHERE u.role = 'teacher'";
$params = [];
if ($filter !== 'all') {
    $sql .= ' AND u.teacher_status = ?';
    $params[] = $filter;
}
$sql .= ' ORDER BY COALESCE(ta.submitted_at, u.created_at) DESC';

$listStmt = db()->prepare($sql);
$listStmt->execute($params);
$rows = $listStmt->fetchAll();

require dirname(__DIR__) . '/includes/layout/admin_header.php';
?>

<?php if ($success): ?>
    <div class="alert alert-success alert-dismissible fade show"><?= e($success) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>

<h1 class="h4 text-primary mb-4">درخواست‌های همکاری اساتید</h1>

<div class="d-flex flex-wrap gap-2 mb-3">
    <?php foreach (['pending' => 'در حال بررسی', 'approved' => 'تأیید شده', 'rejected' => 'رد شده', 'all' => 'همه'] as $k => $label): ?>
        <a class="btn btn-sm <?= $filter === $k ? 'btn-primary' : 'btn-outline-primary' ?>"
           href="<?= e(base_url('admin/teacher_applications.php?status=' . $k)) ?>"><?= e($label) ?></a>
    <?php endforeach; ?>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="table-responsive bg-white rounded shadow-sm">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                <tr>
                    <th>نام</th>
                    <th>ایمیل</th>
                    <th>وضعیت</th>
                    <th>ارسال</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($rows as $r): ?>
                    <tr class="<?= $detailId === (int) $r['id'] ? 'table-primary' : '' ?>">
                        <td><?= e($r['full_name']) ?></td>
                        <td dir="ltr" class="small"><?= e($r['username'] ?? $r['email'] ?? '') ?></td>
                        <td><span class="badge bg-secondary"><?= e($r['teacher_status']) ?></span></td>
                        <td class="small"><?= e(format_date($r['submitted_at'] ?? $r['created_at'])) ?></td>
                        <td><a class="btn btn-sm btn-outline-primary" href="<?= e(base_url('admin/teacher_applications.php?id=' . (int) $r['id'] . '&status=' . urlencode($filter))) ?>">جزئیات</a></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$rows): ?>
                    <tr><td colspan="5" class="text-muted text-center py-4">رکوردی یافت نشد.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-lg-5">
        <?php if ($detail): ?>
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <span><?= e($detail['full_name']) ?></span>
                    <span class="badge bg-primary"><?= e($detail['teacher_status']) ?></span>
                </div>
                <div class="card-body small">
                    <p><strong>ایمیل:</strong> <span dir="ltr"><?= e($detail['username'] ?? '') ?></span></p>
                    <p><strong>کد ملی:</strong> <span dir="ltr"><?= e($detail['national_id'] ?? '—') ?></span></p>
                    <p><strong>تلفن:</strong> <?= e($detail['phone'] ?? '—') ?></p>
                    <p><strong>ثبت درخواست:</strong> <?= e(format_date($detail['app_submitted_at'] ?? null)) ?></p>
                    <hr>
                    <p class="fw-bold mb-1">سوابق تحصیلی</p>
                    <div class="text-muted mb-3"><?= nl2br(e($detail['education'] ?? '—')) ?></div>
                    <p class="fw-bold mb-1">سوابق شغلی</p>
                    <div class="text-muted mb-3"><?= nl2br(e($detail['work_experience'] ?? '—')) ?></div>
                    <p class="fw-bold mb-1">توانمندی‌ها</p>
                    <div class="text-muted mb-3"><?= nl2br(e($detail['skills_summary'] ?? '—')) ?></div>
                    <?php if (!empty($detail['resume_path'])): ?>
                        <p><a class="btn btn-sm btn-outline-secondary" target="_blank" href="<?= e(upload_url($detail['resume_path'])) ?>">دانلود رزومه</a></p>
                    <?php endif; ?>
                    <?php if ($detail['teacher_status'] === 'rejected' && !empty($detail['app_admin_note'])): ?>
                        <div class="alert alert-danger small mb-0">آخرین توضیح رد: <?= nl2br(e($detail['app_admin_note'])) ?></div>
                    <?php endif; ?>
                </div>
                <?php if ($detail['teacher_status'] === 'pending'): ?>
                    <div class="card-footer bg-white">
                        <form method="post" class="mb-3" onsubmit="return confirm('تأیید درخواست این استاد؟');">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="approve">
                            <input type="hidden" name="user_id" value="<?= (int) $detail['id'] ?>">
                            <button type="submit" class="btn btn-success w-100">تأیید درخواست</button>
                        </form>
                        <form method="post">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="reject">
                            <input type="hidden" name="user_id" value="<?= (int) $detail['id'] ?>">
                            <label class="form-label">دلیل رد (برای استاد نمایش داده می‌شود)</label>
                            <input name="admin_note" class="form-control mb-2">
                            <button type="submit" class="btn btn-outline-danger w-100">رد درخواست</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        <?php elseif ($detailId > 0): ?>
            <div class="alert alert-warning">استاد با این شناسه یافت نشد.</div>
        <?php else: ?>
            <div class="text-muted">برای مشاهده جزئیات، یک استاد را از جدول انتخاب کنید.</div>
        <?php endif; ?>
    </div>
</div>

<?php require dirname(__DIR__) . '/includes/layout/admin_footer.php'; ?>
