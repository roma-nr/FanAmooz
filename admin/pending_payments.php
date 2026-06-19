<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_auth('admin');

$pageTitle = 'مدیریت فیش‌های پرداخت';
$activeMenu = 'payments';

$statusFilter = $_GET['status'] ?? 'pending'; // pending, approved, rejected, all

// تعریف وضعیت‌های واقعی
$statusMap = [
    'pending'  => ['pending_payment', 'waiting_approval'],
    'approved' => ['active', 'completed'],
    'rejected' => ['cancelled'],
];

$sql = "
    SELECT e.id, e.receipt_path, e.status, e.enrolled_at,
           u.full_name, c.title, c.price
    FROM enrollments e
    JOIN users u ON u.id = e.user_id
    JOIN courses c ON c.id = e.course_id
    WHERE c.is_paid = 1 AND e.receipt_path IS NOT NULL
";
$params = [];
if ($statusFilter !== 'all' && isset($statusMap[$statusFilter])) {
    $placeholders = implode(',', array_fill(0, count($statusMap[$statusFilter]), '?'));
    $sql .= " AND e.status IN ($placeholders)";
    $params = $statusMap[$statusFilter];
}
$sql .= " ORDER BY e.enrolled_at DESC";

$stmt = db()->prepare($sql);
$stmt->execute($params);
$payments = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf()) {
    $enrollmentId = (int) $_POST['enrollment_id'];
    $action = $_POST['action'];
    if ($action === 'approve') {
        db()->prepare("UPDATE enrollments SET status = 'active' WHERE id = ?")->execute([$enrollmentId]);
        flash('success', 'پرداخت تأیید و ثبت‌نام فعال شد.');
    } elseif ($action === 'reject') {
        db()->prepare("UPDATE enrollments SET status = 'cancelled' WHERE id = ?")->execute([$enrollmentId]);
        flash('success', 'درخواست رد شد.');
    }
    redirect(base_url('admin/pending_payments.php?status=' . urlencode($statusFilter)));
}

require dirname(__DIR__) . '/includes/layout/admin_header.php';
?>
<h1 class="h3 mb-4">مدیریت فیش‌های پرداخت (آفلاین)</h1>

<div class="d-flex gap-2 mb-3">
    <a href="?status=pending"  class="btn btn-sm <?= $statusFilter === 'pending'  ? 'btn-warning' : 'btn-outline-warning' ?>">در انتظار تأیید</a>
    <a href="?status=approved" class="btn btn-sm <?= $statusFilter === 'approved' ? 'btn-success' : 'btn-outline-success' ?>">تأییدشده</a>
    <a href="?status=rejected" class="btn btn-sm <?= $statusFilter === 'rejected' ? 'btn-danger' : 'btn-outline-danger' ?>">ردشده</a>
    <a href="?status=all"      class="btn btn-sm <?= $statusFilter === 'all'      ? 'btn-secondary' : 'btn-outline-secondary' ?>">همه</a>
</div>

<?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>

<div class="table-responsive bg-white rounded shadow-sm">
    <table class="table table-bordered align-middle">
        <thead class="table-light">
            <tr>
                <th>دانشجو</th>
                <th>دوره</th>
                <th>مبلغ (تومان)</th>
                <th>فیش</th>
                <th>تاریخ</th>
                <th>وضعیت</th>
                <th>عملیات</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($payments as $row): ?>
            <tr>
                <td><?= e($row['full_name']) ?></td>
                <td><?= e($row['title']) ?></td>
                <td><?= number_format((int)$row['price']) ?></td>
                <td><a href="<?= e(upload_url($row['receipt_path'])) ?>" target="_blank" class="btn btn-sm btn-outline-primary">مشاهده فیش</a></td>
                <td><?= e(format_datetime($row['enrolled_at'])) ?></td>
                <td>
                    <?php if (in_array($row['status'], ['pending_payment', 'waiting_approval'])): ?>
                        <span class="badge bg-warning text-dark">در انتظار تأیید</span>
                    <?php elseif (in_array($row['status'], ['active', 'completed'])): ?>
                        <span class="badge bg-success">تأییدشده</span>
                    <?php elseif ($row['status'] === 'cancelled'): ?>
                        <span class="badge bg-danger">ردشده</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if (in_array($row['status'], ['pending_payment', 'waiting_approval'])): ?>
                    <form method="post" class="d-inline" onsubmit="return confirm('تأیید شود؟')">
                        <?= csrf_field() ?>
                        <input type="hidden" name="enrollment_id" value="<?= (int) $row['id'] ?>">
                        <input type="hidden" name="action" value="approve">
                        <button class="btn btn-sm btn-success">تأیید</button>
                    </form>
                    <form method="post" class="d-inline" onsubmit="return confirm('رد شود؟')">
                        <?= csrf_field() ?>
                        <input type="hidden" name="enrollment_id" value="<?= (int) $row['id'] ?>">
                        <input type="hidden" name="action" value="reject">
                        <button class="btn btn-sm btn-danger">رد</button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (!$payments): ?><tr><td colspan="7" class="text-center text-muted">هیچ فیشی یافت نشد.</td></tr><?php endif; ?>
        </tbody>
    </table>
</div>
<?php require dirname(__DIR__) . '/includes/layout/admin_footer.php'; ?>