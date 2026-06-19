<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_auth('admin');

$pageTitle = 'گزارش‌های سیستم';
$activeMenu = 'reports';
$errors = [];

// تغییر وضعیت
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $action = $_GET['action'];
    if (in_array($action, ['read', 'resolved'], true)) {
        db()->prepare("UPDATE reports SET status = ? WHERE id = ?")->execute([$action, $id]);
        flash('success', 'وضعیت گزارش به‌روزرسانی شد.');
        redirect(base_url('admin/reports.php'));
    }
}

// ارسال پاسخ
if (is_post() && isset($_POST['submit_response'])) {
    if (!verify_csrf()) {
        $errors['general'] = 'درخواست نامعتبر.';
    } else {
        $id = (int) $_POST['report_id'];
        $response = trim($_POST['admin_response'] ?? '');
        if ($response === '') {
            $errors['response'] = 'متن پاسخ را وارد کنید.';
        } else {
            db()->prepare("UPDATE reports SET admin_response = ?, status = 'resolved', resolved_at = NOW() WHERE id = ?")->execute([$response, $id]);
            flash('success', 'پاسخ ثبت شد و گزارش بسته شد.');
            redirect(base_url('admin/reports.php'));
        }
    }
}

// فیلترها
$statusFilter = $_GET['status'] ?? 'all';
$typeFilter = $_GET['type'] ?? 'all';

$sql = "SELECT r.*, u.full_name 
        FROM reports r
        LEFT JOIN users u ON u.id = r.user_id
        WHERE 1=1";
$params = [];

if ($statusFilter !== 'all') {
    $sql .= " AND r.status = ?";
    $params[] = $statusFilter;
}
if ($typeFilter !== 'all') {
    $sql .= " AND r.type = ?";
    $params[] = $typeFilter;
}

$sql .= " ORDER BY r.created_at DESC";
$stmt = db()->prepare($sql);
$stmt->execute($params);
$reports = $stmt->fetchAll();

// آمار
$stats = db()->query("SELECT status, COUNT(*) as count FROM reports GROUP BY status")->fetchAll(PDO::FETCH_KEY_PAIR);
$stats['new'] = $stats['new'] ?? 0;
$stats['read'] = $stats['read'] ?? 0;
$stats['resolved'] = $stats['resolved'] ?? 0;

require dirname(__DIR__) . '/includes/layout/admin_header.php';
?>

<h1 class="h3 mb-4">گزارش‌های سیستم</h1>

<?php if (!empty($errors['general'])): ?>
    <div class="alert alert-danger"><?= e($errors['general']) ?></div>
<?php endif; ?>
<?php if ($msg = flash('success')): ?>
    <div class="alert alert-success"><?= e($msg) ?></div>
<?php endif; ?>

<!-- آمار -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <a href="?status=new" class="text-decoration-none text-reset">
            <div class="card border-0 shadow-sm p-3 <?= $statusFilter === 'new' ? 'bg-danger text-white' : '' ?>">
                <div class="small">جدید</div>
                <div class="fs-4 fw-bold"><?= $stats['new'] ?></div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="?status=read" class="text-decoration-none text-reset">
            <div class="card border-0 shadow-sm p-3 <?= $statusFilter === 'read' ? 'bg-warning text-dark' : '' ?>">
                <div class="small">خوانده شده</div>
                <div class="fs-4 fw-bold"><?= $stats['read'] ?></div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="?status=resolved" class="text-decoration-none text-reset">
            <div class="card border-0 shadow-sm p-3 <?= $statusFilter === 'resolved' ? 'bg-success text-white' : '' ?>">
                <div class="small">برطرف شده</div>
                <div class="fs-4 fw-bold"><?= $stats['resolved'] ?></div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="?status=all" class="text-decoration-none text-reset">
            <div class="card border-0 shadow-sm p-3 <?= $statusFilter === 'all' ? 'bg-primary text-white' : '' ?>">
                <div class="small">مجموع</div>
                <div class="fs-4 fw-bold"><?= array_sum($stats) ?></div>
            </div>
        </a>
    </div>
</div>

<!-- فیلترها -->
<form method="get" class="row g-2 mb-3 align-items-end">
    <div class="col-auto">
        <label class="form-label small">وضعیت</label>
        <select name="status" class="form-select form-select-sm searchable-select">
            <option value="all" <?= $statusFilter === 'all' ? 'selected' : '' ?>>همه</option>
            <option value="new" <?= $statusFilter === 'new' ? 'selected' : '' ?>>جدید</option>
            <option value="read" <?= $statusFilter === 'read' ? 'selected' : '' ?>>خوانده شده</option>
            <option value="resolved" <?= $statusFilter === 'resolved' ? 'selected' : '' ?>>برطرف شده</option>
        </select>
    </div>
    <div class="col-auto">
        <label class="form-label small">نوع</label>
        <select name="type" class="form-select form-select-sm searchable-select">
            <option value="all" <?= $typeFilter === 'all' ? 'selected' : '' ?>>همه</option>
            <option value="video_missing" <?= $typeFilter === 'video_missing' ? 'selected' : '' ?>>ویدئو آپلود نشده</option>
            <option value="user" <?= $typeFilter === 'user' ? 'selected' : '' ?>>گزارش کاربر</option>
            <option value="system" <?= $typeFilter === 'system' ? 'selected' : '' ?>>سیستم</option>
            <option value="general" <?= $typeFilter === 'general' ? 'selected' : '' ?>>عمومی</option>
        </select>
    </div>
    <div class="col-auto">
        <button class="btn btn-primary btn-sm">فیلتر</button>
        <a href="reports.php" class="btn btn-outline-secondary btn-sm">حذف فیلتر</a>
    </div>
</form>

<!-- لیست گزارش‌ها -->
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>فرستنده</th>
                    <th>نوع</th>
                    <th>موضوع</th>
                    <th>وضعیت</th>
                    <th>تاریخ</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reports as $report): ?>
                    <tr class="<?= $report['status'] === 'new' ? 'table-warning' : '' ?>">
                        <td>
                            <?php if ($report['user_role'] === 'system'): ?>
                                <span class="badge bg-secondary">سیستم</span>
                            <?php else: ?>
                                <?= e($report['full_name'] ?? $report['user_role']) ?>
                            <?php endif; ?>
                            <br><small class="text-muted"><?= e($report['user_role']) ?></small>
                        </td>
                        <td>
                            <span class="badge bg-<?= $report['type'] === 'video_missing' ? 'danger' : 'secondary' ?>">
                                <?= $report['type'] === 'video_missing' ? 'ویدئو' : e($report['type']) ?>
                            </span>
                        </td>
                        <td>
                            <strong><?= e($report['title']) ?></strong>
                            <br><small class="text-muted"><?= e(mb_substr($report['description'], 0, 60)) ?>...</small>
                        </td>
                        <td>
                            <span class="badge bg-<?= $report['status'] === 'new' ? 'danger' : ($report['status'] === 'read' ? 'warning text-dark' : 'success') ?>">
                                <?= e($report['status']) ?>
                            </span>
                        </td>
                        <td><?= e(format_datetime($report['created_at'])) ?></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#reportModal<?= (int) $report['id'] ?>">
                                مشاهده
                            </button>
                        </td>
                    </tr>

                    <!-- مودال -->
                    <div class="modal fade" id="reportModal<?= (int) $report['id'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">گزارش: <?= e($report['title']) ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <p><strong>فرستنده:</strong> <?= $report['user_role'] === 'system' ? 'سیستم' : e($report['full_name'] ?? $report['user_role']) ?></p>
                                    <p><strong>نوع:</strong> <?= e($report['type']) ?></p>
                                    <p><strong>وضعیت:</strong> <span class="badge bg-<?= $report['status'] === 'new' ? 'danger' : ($report['status'] === 'read' ? 'warning' : 'success') ?>"><?= e($report['status']) ?></span></p>
                                    <p><strong>توضیحات:</strong></p>
                                    <div class="bg-light p-3 rounded mb-3"><?= nl2br(e($report['description'])) ?></div>
                                    
                                    <?php if (!empty($report['admin_response'])): ?>
                                        <p><strong>پاسخ ادمین:</strong></p>
                                        <div class="bg-success bg-opacity-10 p-3 rounded mb-3"><?= nl2br(e($report['admin_response'])) ?></div>
                                    <?php endif; ?>

                                    <?php if ($report['status'] !== 'resolved'): ?>
                                        <form method="post">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="report_id" value="<?= (int) $report['id'] ?>">
                                            <div class="mb-2">
                                                <label class="form-label">پاسخ ادمین</label>
                                                <textarea name="admin_response" class="form-control <?= isset($errors['response']) ? 'is-invalid' : '' ?>" rows="3" required></textarea>
                                                <div class="invalid-feedback"><?= $errors['response'] ?? '' ?></div>
                                            </div>
                                            <button type="submit" name="submit_response" class="btn btn-primary">ارسال پاسخ و بستن</button>
                                        </form>
                                    <?php endif; ?>

                                    <div class="mt-3">
                                        <a href="?action=read&id=<?= (int) $report['id'] ?>" class="btn btn-sm btn-outline-secondary">خوانده شد</a>
                                        <a href="?action=resolved&id=<?= (int) $report['id'] ?>" class="btn btn-sm btn-outline-success">بستن (بدون پاسخ)</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (!$reports): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">هیچ گزارشی یافت نشد.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require dirname(__DIR__) . '/includes/layout/admin_footer.php'; ?>