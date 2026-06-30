<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_auth(['student','teacher']);

if (!is_post() || !verify_csrf()) {
    flash('error', 'درخواست نامعتبر.');
    redirect(base_url('student/wallet.php'));
}

$amount = (int)($_POST['amount'] ?? 0);
if ($amount < 1000) {
    flash('error', 'حداقل مبلغ ۱,۰۰۰ تومان است.');
    redirect(base_url('student/wallet.php'));
}

$userId = (int)auth_id();
$merchant = setting('zarinpal_merchant_id', '');
if ($merchant === '') {
    flash('error', 'درگاه پرداخت فعال نیست.');
    redirect(base_url('student/wallet.php'));
}

require_once __DIR__ . '/../includes/payment/zarinpal.php';

$amountRial = $amount * 10; // تبدیل به ریال
$callback = base_url('student/wallet_callback.php');
$user = db()->prepare('SELECT email, phone FROM users WHERE id = ?');
$user->execute([$userId]);
$u = $user->fetch();

try {
    $result = zarinpal_request($amountRial, 'شارژ کیف پول', $callback, $u['email'] ?? null, $u['phone'] ?? null);
} catch (RuntimeException $e) {
    flash('error', $e->getMessage());
    redirect(base_url('student/wallet.php'));
}

// ذخیره اطلاعات موقت در session تا در callback پردازش شود
$_SESSION['wallet_charge'] = [
    'user_id' => $userId,
    'amount'  => $amount,
    'authority'=> $result['authority'],
    'time'    => time()
];

redirect($result['pay_url']);