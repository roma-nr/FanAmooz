<?php
declare(strict_types=1);

/**
 * نصب اولیه و به‌روزرسانی دیتابیس
 * یک‌بار اجرا کنید: http://localhost/FanAmooz/install.php
 * پس از نصب موفق، این فایل را حذف کنید
 */

$configDb = require __DIR__ . '/config/database.php';
$messages = [];
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $dsn = sprintf('mysql:host=%s;port=%d;charset=%s', $configDb['host'], $configDb['port'], $configDb['charset']);
        $pdo = new PDO($dsn, $configDb['username'], $configDb['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

        // اجرای schema
        $schema = file_get_contents(__DIR__ . '/sql/schema.sql');
        $seed = file_get_contents(__DIR__ . '/sql/seed.sql');

        foreach (preg_split('/;\s*\n/', $schema) as $sql) {
            $sql = trim($sql);
            if ($sql !== '') {
                $pdo->exec($sql);
            }
        }

        $pdo->exec('USE fanamooz');

        foreach (preg_split('/;\s*\n/', $seed) as $sql) {
            $sql = trim($sql);
            if ($sql !== '' && stripos($sql, 'USE fanamooz') === false) {
                try {
                    $pdo->exec($sql);
                } catch (PDOException) {
                    // ignore duplicate inserts
                }
            }
        }

        // ========== اضافه کردن فیلدهای جدید ==========

        // 1. duration_hours به جدول courses
        try {
            $pdo->exec("ALTER TABLE courses ADD COLUMN duration_hours INT UNSIGNED DEFAULT 0 AFTER session_count");
        } catch (PDOException $e) {
            if (!str_contains($e->getMessage(), 'Duplicate column')) {
                throw $e;
            }
        }

        // 2. president_name به settings
        try {
            $pdo->exec("INSERT INTO settings (setting_key, setting_value) VALUES ('president_name', 'دکتر سید محمد حسینی') ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
        } catch (PDOException $e) {
            // ignore
        }

        // 3. duration_hours به certificate_request_detail نیاز دارد، خودش ستون جدیدی نیست

        // 4. آپدیت رمز مدیر
        $hash = password_hash('Admin@1404', PASSWORD_BCRYPT);
        $stmt = $pdo->prepare('UPDATE users SET password_hash = ? WHERE username = ? AND role = ?');
        $stmt->execute([$hash, 'admin', 'admin']);

        // ایجاد پوشه‌های مورد نیاز
        $dirs = [
            'uploads', 'uploads/logos', 'uploads/announcements', 'uploads/courses',
            'uploads/links', 'uploads/teachers', 'uploads/teachers/resumes',
            'uploads/payments', 'uploads/chat', 'uploads/assignments'
        ];
        foreach ($dirs as $dir) {
            $path = __DIR__ . '/' . $dir;
            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }
        }

        $messages[] = '✅ پایگاه داده و داده‌های اولیه با موفقیت ایجاد شد.';
        $messages[] = '✅ فیلدهای جدید (duration_hours, president_name) اضافه شدند.';
        $messages[] = '✅ نام کاربری مدیر: admin - رمز عبور: Admin@1404';
        $messages[] = '⚠️ لطفاً فایل install.php را حذف کنید.';

    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نصب فن‌آموز</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5" style="max-width: 560px;">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <h1 class="h4 text-primary mb-4">نصب سامانه فن‌آموز</h1>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php foreach ($messages as $msg): ?>
                <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
            <?php endforeach; ?>

            <?php if (!$messages): ?>
                <p class="text-muted">XAMPP را روشن کنید و MySQL را فعال کنید، سپس دکمه نصب را بزنید.</p>
                <form method="post">
                    <button type="submit" class="btn btn-primary w-100">شروع نصب</button>
                </form>
            <?php else: ?>
                <a href="index.php" class="btn btn-primary w-100">ورود به سایت</a>
                <p class="text-danger small mt-2">⚠️ پس از نصب، فایل install.php را حذف کنید.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>