<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/bootstrap.php';

$authority = $_GET['Authority'] ?? '';
$status    = $_GET['Status'] ?? '';

if ($authority === '' || $status === '') {
    flash('error', 'اطلاعات بازگشتی نامعتبر.');
    redirect(base_url('student/wallet.php'));
}

$sessionData = $_SESSION['wallet_charge'] ?? null;
if (!$sessionData || $sessionData['authority'] !== $authority) {
    flash('error', 'جلسهٔ پرداخت منقضی شده است.');
    redirect(base_url('student/wallet.php'));
}
unset($_SESSION['wallet_charge']);

if ($status !== 'OK') {
    flash('error', 'پرداخت لغو شد.');
    redirect(base_url('student/wallet.php'));
}

require_once __DIR__ . '/../includes/payment/zarinpal.php';

$amountRial = $sessionData['amount'] * 10;
try {
    $verified = zarinpal_verify($authority, $amountRial);
} catch (RuntimeException $e) {
    flash('error', $e->getMessage());
    redirect(base_url('student/wallet.php'));
}

// واریز به کیف پول
wallet_deposit((int)$sessionData['user_id'], (int)$sessionData['amount'], 'شارژ کیف پول – کد پیگیری: ' . $verified['ref_id']);

flash('success', 'کیف پول شما با موفقیت شارژ شد.');
redirect(base_url('student/wallet.php'));