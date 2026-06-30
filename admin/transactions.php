<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_auth('admin');

$pageTitle = 'مدیریت تراکنش‌ها';
$activeMenu = 'transactions';

$typeFilter = $_GET['type'] ?? 'all';
$statusFilter = $_GET['status'] ?? 'all';

// ساخت کوئری ترکیبی
$paymentSql = "SELECT p.id, p.amount, p.status, p.ref_id, p.paid_at, p.created_at, u.full_name, c.title AS course_title, 'payment' AS source FROM payments p JOIN users u ON u.id = p.user_id JOIN enrollments e ON e.id = p.enrollment_id JOIN courses c ON c.id = e.course_id WHERE 1=1";
$walletSql = "SELECT wt.id, wt.amount, wt.type AS status, '' AS ref_id, NULL AS paid_at, wt.created_at, u.full_name, wt.description AS course_title, 'wallet' AS source FROM wallet_transactions wt JOIN users u ON u.id = wt.user_id WHERE 1=1";

$paymentParams = [];
$walletParams = [];

if ($typeFilter === 'payment') {
    $walletSql .= " AND 1=0";
} elseif ($typeFilter === 'wallet') {
    $paymentSql .= " AND 1=0";
}

if ($statusFilter !== 'all') {
    if ($typeFilter !== 'wallet') {
        $paymentSql .= " AND p.status = ?";
        $paymentParams[] = $statusFilter;
    }
    if ($typeFilter !== 'payment') {
        $walletSql .= " AND wt.type = ?";
        $walletParams[] = $statusFilter;
    }
}

$finalSql = "($paymentSql) UNION ALL ($walletSql) ORDER BY created_at DESC LIMIT 100";
$stmt = db()->prepare($finalSql);
$stmt->execute(array_merge($paymentParams, $walletParams));
$transactions = $stmt->fetchAll();

require dirname(__DIR__) . '/includes/layout/admin_header.php';
?>

<h1 class="h3 mb-4">مدیریت تراکنش‌ها</h1>

<form method="get" class="row g-2 mb-4">
    <div class="col-auto">
        <select name="type" class="form-select form-select-sm">
            <option value="all" <?= $typeFilter === 'all' ? 'selected' : '' ?>>همه</option>
            <option value="payment" <?= $typeFilter === 'payment' ? 'selected' : '' ?>>پرداخت‌ها</option>
            <option value="wallet" <?= $typeFilter === 'wallet' ? 'selected' : '' ?>>کیف پول</option>
        </select>
    </div>
    <div class="col-auto">
        <select name="status" class="form-select form-select-sm">
            <option value="all" <?= $statusFilter === 'all' ? 'selected' : '' ?>>همهٔ وضعیت‌ها</option>
            <option value="paid" <?= $statusFilter === 'paid' ? 'selected' : '' ?>>موفق</option>
            <option value="pending" <?= $statusFilter === 'pending' ? 'selected' : '' ?>>در انتظار</option>
            <option value="failed" <?= $statusFilter === 'failed' ? 'selected' : '' ?>>ناموفق</option>
        </select>
    </div>
    <div class="col-auto">
        <button class="btn btn-primary btn-sm">فیلتر</button>
    </div>
</form>

<div class="table-responsive bg-white rounded shadow-sm">
    <table class="table table-hover mb-0">
        <thead class="table-light">
            <tr><th>کاربر</th><th>نوع</th><th>مبلغ (تومان)</th><th>وضعیت</th><th>تاریخ</th><th>جزئیات</th></tr>
        </thead>
        <tbody>
            <?php foreach ($transactions as $t): ?>
            <tr>
                <td><?= e($t['full_name']) ?></td>
                <td>
                    <?php if ($t['source'] === 'payment'): ?>
                        <span class="badge bg-info">پرداخت</span>
                    <?php else: ?>
                        <span class="badge bg-secondary">کیف پول</span>
                    <?php endif; ?>
                </td>
                <td><?= number_format(abs((int)$t['amount'])) ?></td>
                <td>
                    <?php
                    $status = $t['status'];
                    $class = match($status) {
                        'paid', 'deposit' => 'success',
                        'withdraw' => 'warning',
                        'refund' => 'info',
                        'pending' => 'secondary',
                        'failed' => 'danger',
                        default => 'light'
                    };
                    ?>
                    <span class="badge bg-<?= $class ?>"><?= e($status) ?></span>
                </td>
                <td><?= e(format_datetime($t['created_at'])) ?></td>
                <td><?= e($t['course_title'] ?? '') ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (!$transactions): ?><tr><td colspan="6" class="text-center text-muted">تراکنشی یافت نشد.</td></tr><?php endif; ?>
        </tbody>
    </table>
</div>

<?php require dirname(__DIR__) . '/includes/layout/admin_footer.php'; ?>