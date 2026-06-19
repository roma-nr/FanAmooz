<?php

declare(strict_types=1);

$pageTitle = $pageTitle ?? 'پنل دانشجو';
$activeMenu = $activeMenu ?? '';
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> | <?= e(site_name()) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= e(asset_url('css/theme.css')) ?>" rel="stylesheet">
    <?php require __DIR__ . '/head_extras.php'; ?>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark site-header">
    <div class="container">
        <a class="navbar-brand" href="<?= e(base_url('index.php')) ?>"><?= e(site_name()) ?></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#stuNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="stuNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link <?= $activeMenu === 'dashboard' ? 'active fw-bold' : '' ?>" href="<?= e(base_url('student/index.php')) ?>">داشبورد</a></li>
                
                <li class="nav-item"><a class="nav-link <?= $activeMenu === 'assignments' ? 'active fw-bold' : '' ?>" href="<?= e(base_url('student/assignments.php')) ?>">تکالیف</a></li>
                <li class="nav-item"><a class="nav-link <?= $activeMenu === 'grades' ? 'active fw-bold' : '' ?>" href="<?= e(base_url('student/grades.php')) ?>">نمرات</a></li>
                <li class="nav-item"><a class="nav-link <?= $activeMenu === 'live' ? 'active fw-bold' : '' ?>" href="<?= e(base_url('student/live_classes.php')) ?>">کلاس آنلاین</a></li>
                <li class="nav-item"><a class="nav-link <?= $activeMenu === 'certificates' ? 'active fw-bold' : '' ?>" href="<?= e(base_url('student/certificates.php')) ?>">گواهی‌ها</a></li>
                <li class="nav-item">
                    <a class="nav-link <?= $activeMenu === 'messages' ? 'active fw-bold' : '' ?>" href="<?= e(base_url('student/messages.php')) ?>">
                        پیام‌ها
                        <?php $unread = student_total_unread_messages((int) (auth_id() ?? 0)); if ($unread > 0): ?>
                            <span class="badge bg-danger"><?= $unread ?></span>
                        <?php endif; ?>
                    </a>
                </li>
            </ul>
            <span class="navbar-text text-white small me-3"><?= e(auth_user()['full_name'] ?? '') ?></span>
            <a href="<?= e(base_url('logout.php')) ?>" class="btn btn-outline-light btn-sm">خروج</a>
        </div>
    </div>
</nav>
<main class="container py-4">
