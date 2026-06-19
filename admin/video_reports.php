<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_auth('admin');

$pageTitle = 'گزارش‌های ویدئوی کلاس';
$activeMenu = 'video_reports';
$errors = [];

if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    if ($_GET['action'] === 'resolve') {
        db()->prepare("UPDATE video_reports SET status = 'resolved' WHERE id = ?")->execute([$id]);
        flash('success', 'گزارش بسته شد.');
        redirect(base_url('admin/video_reports.php'));
    }
}

if (is_post() && isset($_POST['extend_deadline'])) {
    if (!verify_csrf()) {
        $errors['general'] = 'درخواست نامعتبر.';
    } else {
        $id = (int) $_POST['report_id'];
        $extraDays = max(1, min(7, (int) ($_POST['extra_days'] ?? 1)));
        $note = trim($_POST['admin_note'] ?? '');
        db()->prepare("
            UPDATE video_reports 
            SET deadline = DATE_ADD(deadline, INTERVAL ? DAY), 
                status = 'extended', 
                extended_by = ?, 
                extended_at = NOW(), 
                admin_note = ? 
            WHERE id = ?
        ")->execute([$extraDays, auth_id(), $note, $id]);
        flash('success', 'مهلت با موفقیت تمدید شد.');
        redirect(base_url('admin/video_reports.php'));
    }
}

$reports = db()->query("
    SELECT vr.*, c.title as course_title, u.full_name as teacher_name, 
           ls.title as live_title, cs.title as session_title
    FROM video_reports vr
    JOIN courses c ON c.id = vr.course_id
    JOIN users u ON u.id = vr.teacher_id
    JOIN live_sessions ls ON ls.id = vr.live_session_id
    JOIN course_sessions cs ON cs.id = vr.session_id
    ORDER BY vr.deadline ASC, vr.status ASC
")->fetchAll();

require dirname(__DIR__) . '/includes/layout/admin_header.php';
?>

<h1 class="h3 mb-4">گزارش‌های ویدئوی کلاس</h1>

<?php if (!empty($errors['general'])): ?>
    <div class="alert alert-danger"><?= e($errors['general']) ?></div>
<?php endif; ?>
<?php if ($msg = flash('success')): ?>
    <div class="alert alert-success"><?= e($msg) ?></div>
<?php endif; ?>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>دوره</th>
                    <th>استاد</th>
                    <th>جلسه آنلاین</th>
                    <th>مهلت آپلود</th>
                    <th>وضعیت</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reports as $report): ?>
                    <?php $isExpired = strtotime($report['deadline']) < time() && $report['status'] !== 'resolved'; ?>
                    <tr class="<?= $isExpired ? 'table-danger' : '' ?>">
                        <td><?= e($report['course_title']) ?></td>
                        <td><?= e($report['teacher_name']) ?></td>
                        <td><?= e($report['live_title']) ?></td>
                        <td>
                            <?= e(format_datetime($report['deadline'])) ?>
                            <?php if ($isExpired): ?>
                                <span class="badge bg-danger">مهلت گذشته</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge bg-<?= $report['status'] === 'resolved' ? 'success' : ($report['status'] === 'extended' ? 'warning' : 'danger') ?>">
                                <?= e($report['status']) ?>
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#extendModal<?= (int) $report['id'] ?>">
                                <i class="bi bi-clock-history"></i> تمدید
                            </button>
                            <a href="?action=resolve&id=<?= (int) $report['id'] ?>" class="btn btn-sm btn-outline-success" onclick="return confirm('آیا ویدئو آپلود شده و مشکل برطرف شده است؟')">
                                <i class="bi bi-check-lg"></i> بستن
                            </a>
                        </td>
                    </tr>

                    <!-- مودال تمدید -->
                    <div class="modal fade" id="extendModal<?= (int) $report['id'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">تمدید مهلت آپلود</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form method="post">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="report_id" value="<?= (int) $report['id'] ?>">
                                    <div class="modal-body">
                                        <p><strong>دوره:</strong> <?= e($report['course_title']) ?></p>
                                        <p><strong>استاد:</strong> <?= e($report['teacher_name']) ?></p>
                                        <p><strong>مهلت فعلی:</strong> <?= e(format_datetime($report['deadline'])) ?></p>
                                        <div class="mb-2">
                                            <label class="form-label">تمدید (روز)</label>
                                            <input type="number" name="extra_days" class="form-control" min="1" max="7" value="2">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">یادداشت برای استاد</label>
                                            <textarea name="admin_note" class="form-control" rows="2">مهلت آپلود ویدئو تمدید شد.</textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">انصراف</button>
                                        <button type="submit" name="extend_deadline" class="btn btn-primary">تمدید</button>
                                    </div>
                                </form>
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