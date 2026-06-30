<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_teacher_approved();

$teacherId = (int)auth_id();
$balance = wallet_balance($teacherId);
$pageTitle = 'کیف پول و درآمدها';
$activeMenu = 'wallet';

// پردازش برداشت مستقیم
if (is_post() && verify_csrf() && ($_POST['action'] ?? '') === 'withdraw') {
    $amount = (int)($_POST['amount'] ?? 0);
    if ($amount < 5000) {
        $error = 'حداقل مبلغ برداشت ۵,۰۰۰ تومان است.';
    } elseif ($amount > $balance) {
        $error = 'موجودی کافی نیست.';
    } else {
        try {
            wallet_withdraw($teacherId, $amount, 'برداشت از کیف پول');
            flash('success', 'برداشت با موفقیت انجام شد.');
            redirect(base_url('teacher/wallet.php'));
        } catch (RuntimeException $e) {
            $error = $e->getMessage();
        }
    }
}

// تاریخچه تراکنش‌ها
$txns = db()->prepare("SELECT * FROM wallet_transactions WHERE user_id = ? ORDER BY created_at DESC LIMIT 30");
$txns->execute([$teacherId]);
$transactions = $txns->fetchAll();

require dirname(__DIR__) . '/includes/layout/teacher_header.php';
?>

<h1 class="h3 text-primary mb-4">کیف پول و درآمدها</h1>
<?php if (isset($error)): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center p-4">
            <div class="display-4 text-success mb-2"><?= number_format($balance) ?></div>
            <div class="text-muted small">تومان موجودی</div>
            <form method="post" action="<?= e(base_url('teacher/wallet.php')) ?>" class="mt-3">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="withdraw">
                <div class="mb-2">
                    <input type="number" name="amount" class="form-control" placeholder="مبلغ برداشت (تومان)" required min="5000" step="1000">
                </div>
                <button class="btn btn-primary w-100">برداشت وجه</button>
            </form>
            <p class="small text-muted mt-2">حداقل ۵,۰۰۰ تومان</p>
        </div>
    </div>
    <div class="col-md-8">
        <!-- تاریخچه تراکنش‌ها (بدون تغییر) -->
    </div>
</div>

<?php require dirname(__DIR__) . '/includes/layout/teacher_footer.php'; ?>