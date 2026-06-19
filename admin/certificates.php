<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_auth('admin');

$pageTitle = 'درخواست‌های گواهی';
$activeMenu = 'certificates';
$filter = $_GET['status'] ?? 'all';

if (is_post() && verify_csrf()) {
    $id = (int) ($_POST['id'] ?? 0);
    $status = $_POST['status'] ?? 'pending';
    $note = trim($_POST['admin_note'] ?? '') ?: null;
    $adminId = (int) auth_id();

    try {
        if ($status === 'approved') {
            approve_certificate_request($id, $adminId, $note);
            flash('success', 'گواهی تأیید و شماره صادر شد.');
        } elseif ($status === 'rejected') {
            reject_certificate_request($id, $adminId, $note);
            flash('success', 'درخواست رد شد.');
        } else {
            db()->prepare(
                'UPDATE certificate_requests SET status=?, admin_note=?, reviewed_at=NOW(), reviewed_by=? WHERE id=?'
            )->execute([$status, $note, $adminId, $id]);
            flash('success', 'وضعیت به‌روزرسانی شد.');
        }
    } catch (Throwable $e) {
        flash('error', $e->getMessage());
    }
    redirect(base_url('admin/certificates.php?status=' . urlencode($filter)));
}

$items = admin_certificates_list($filter === 'all' ? null : $filter);

require dirname(__DIR__) . '/includes/layout/admin_header.php';
?>

<h1 class="h3 mb-4">درخواست‌های گواهی پایان دوره</h1>
<?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
<?php if ($err = flash('error')): ?><div class="alert alert-danger"><?= e($err) ?></div><?php endif; ?>



<ul class="nav nav-pills mb-3 flex-wrap gap-1">
    <?php foreach (['all' => 'همه', 'pending' => 'در انتظار', 'approved' => 'تأییدشده', 'rejected' => 'ردشده'] as $k => $label): ?>
        <li class="nav-item">
            <a class="nav-link <?= $filter === $k ? 'active' : '' ?>" href="?status=<?= e($k) ?>"><?= e($label) ?></a>
        </li>
    <?php endforeach; ?>
</ul>

<div class="card border-0 shadow-sm table-responsive">
    <table class="table table-hover mb-0 small">
        <thead class="table-light">
            <tr>
                <th>دانشجو</th>
                <th>دوره</th>
                <th>استان / دانشکده</th>
                <th>نمره</th>
                <th>وضعیت</th>
                <th>شماره گواهی</th>
                <th>تاریخ</th>
                <th>عملیات</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($items as $item): ?>
            <tr>
                <td><?= e($item['student_name']) ?><br><small class="text-muted"><?= e($item['student_code'] ?? '') ?></small></td>
                <td><?= e($item['course_title']) ?></td>
                <td class="small"><?= e($item['province_name'] ?? '') ?><br><?= e($item['institution_name'] ?? '') ?></td>
                <td><?= e($item['final_grade'] ?? '—') ?></td>
                <td><span class="badge bg-<?= $item['status'] === 'approved' ? 'success' : ($item['status'] === 'rejected' ? 'danger' : 'warning text-dark') ?>"><?= e(certificate_status_label($item['status'])) ?></span></td>
                <td dir="ltr" class="small"><?= e($item['certificate_number'] ?? '—') ?></td>
                <td><?= e(format_date($item['requested_at'])) ?></td>
                
				<td style="min-width:200px">
    <?php if ($item['status'] === 'approved' && $item['certificate_number']): ?>
        <!-- گواهی تأیید شده: فقط مشاهده و چاپ -->
        <a href="<?= e(base_url('admin/certificate_print.php?id=' . (int) $item['id'])) ?>" 
           class="btn btn-sm btn-outline-success mb-1" target="_blank">
            <i class="bi bi-printer"></i> چاپ گواهی
        </a>
        <span class="badge bg-success">تأیید شده</span>
    <?php else: ?>
        <!-- گواهی در انتظار یا رد شده: نمایش فرم تغییر وضعیت -->
        <form method="post" class="d-flex flex-column gap-1">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
            <select name="status" class="form-select form-select-sm searchable-select">
                <option value="pending" <?= $item['status'] === 'pending' ? 'selected' : '' ?>>در انتظار</option>
                <option value="approved" <?= $item['status'] === 'approved' ? 'selected' : '' ?>>تأیید</option>
                <option value="rejected" <?= $item['status'] === 'rejected' ? 'selected' : '' ?>>رد</option>
            </select>
            <input name="admin_note" class="form-control form-control-sm" 
                   placeholder="یادداشت" value="<?= e($item['admin_note'] ?? '') ?>">
            <button class="btn btn-sm btn-primary">ذخیره وضعیت</button>
        </form>
    <?php endif; ?>
</td>

				
            </tr>
        <?php endforeach; ?>
        <?php if (!$items): ?>
            <tr><td colspan="8" class="text-center text-muted py-4">درخواستی یافت نشد.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require dirname(__DIR__) . '/includes/layout/admin_footer.php'; ?>
