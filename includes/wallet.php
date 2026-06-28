<?php
declare(strict_types=1);

/**
 * اطمینان از وجود رکورد کیف پول برای کاربر
 */
function wallet_ensure(int $userId): void
{
    $stmt = db()->prepare("INSERT IGNORE INTO wallets (user_id, balance) VALUES (?, 0)");
    $stmt->execute([$userId]);
}

/**
 * دریافت موجودی کیف پول
 */
function wallet_balance(int $userId): int
{
    wallet_ensure($userId);
    return (int) db()->prepare("SELECT balance FROM wallets WHERE user_id = ?")->execute([$userId])->fetchColumn();
}

/**
 * واریز به کیف پول (افزایش موجودی) + ثبت تراکنش
 */
function wallet_deposit(int $userId, int $amount, string $description = 'شارژ حساب'): void
{
    wallet_ensure($userId);
    db()->prepare("UPDATE wallets SET balance = balance + ? WHERE user_id = ?")->execute([$amount, $userId]);
    db()->prepare("INSERT INTO wallet_transactions (user_id, amount, type, description) VALUES (?, ?, 'deposit', ?)")
        ->execute([$userId, $amount, $description]);
}

/**
 * برداشت از کیف پول (کاهش موجودی) + ثبت تراکنش
 * (برای درخواست برداشت استاد یا خرید دوره)
 */
function wallet_withdraw(int $userId, int $amount, string $description = 'برداشت'): void
{
    $balance = wallet_balance($userId);
    if ($balance < $amount) {
        throw new RuntimeException('موجودی کافی نیست.');
    }
    db()->prepare("UPDATE wallets SET balance = balance - ? WHERE user_id = ?")->execute([$amount, $userId]);
    db()->prepare("INSERT INTO wallet_transactions (user_id, amount, type, description) VALUES (?, ?, 'withdraw', ?)")
        ->execute([$userId, -$amount, $description]);
}

/**
 * خرید دوره با کیف پول
 */
function wallet_purchase(int $userId, int $amount, string $description = 'خرید دوره'): void
{
    wallet_withdraw($userId, $amount, $description);
    // می‌توانید اینجا تراکنش را از نوع purchase هم ثبت کنید، ولی withdraw کافی است.
}

/**
 * بازگشت وجه به کیف پول (ریفاند)
 */
function wallet_refund(int $userId, int $amount, string $description = 'بازگشت وجه'): void
{
    wallet_deposit($userId, $amount, $description);
}

/**
 * واریز دستمزد استاد (۸۰٪ مبلغ دوره)
 */
function wallet_teacher_earning(int $teacherId, int $amount, string $description = 'درآمد تدریس'): void
{
    wallet_deposit($teacherId, $amount, $description);
}