<?php

declare(strict_types=1);

$pageTitle = $pageTitle ?? 'پنل استاد';
$activeMenu = $activeMenu ?? '';
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<!-- ادیتور TinyMCE -->
<script src="https://cdn.tiny.cloud/1/2qs136k1557c1jpyp2fial85xmrjnc9e78srprpvwf4dou73/tinymce/8/tinymce.min.js" referrerpolicy="origin" crossorigin="anonymous"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    if (typeof tinymce !== 'undefined') {
      tinymce.init({
        selector: 'textarea.tinymce',
        directionality: 'rtl',
        plugins: 'link lists code image',
        toolbar: 'undo redo | bold italic | alignright aligncenter alignleft | bullist numlist | link image | code',
        menubar: false,
        branding: false,
        height: 300,
        language: 'fa'
      });
    }
  });
</script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> | <?= e(site_name()) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= e(asset_url('css/theme.css')) ?>" rel="stylesheet">
    <?php require __DIR__ . '/head_extras.php'; ?>


<link rel="stylesheet" href="<?= asset_url('css/jalalidatepicker.min.css') ?>">
<script src="<?= asset_url('js/jalalidatepicker.min.js') ?>"></script>
             
			 <style>
/* اصلاح سایز فونت در تقویم جلالی */
jdp-container { font-size: 12px; }
jdp-container .jdp-day, jdp-container .jdp-day-name { height: 28px; line-height: 28px; }
jdp-container .jdp-month select, jdp-container .jdp-year select, jdp-container .jdp-time select { font-size: 10px; padding: 2px 4px; transition: none !important; }
jdp-container .jdp-month:hover, jdp-container .jdp-year:hover { filter: none !important; }
/* مخفی کردن اسپینر input عددی سال */
jdp-container .jdp-year input[type="number"]::-webkit-inner-spin-button,
jdp-container .jdp-year input[type="number"]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
</style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark site-header">
    <div class="container">
        <a class="navbar-brand" href="<?= e(base_url('teacher/index.php')) ?>"><?= e(site_name()) ?> — استاد</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#tNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="tNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link <?= $activeMenu === 'dashboard' ? 'active fw-bold' : '' ?>" href="<?= e(base_url('teacher/index.php')) ?>">داشبورد</a></li>
                <?php if (teacher_session_status() === 'approved'): ?>
                    <li class="nav-item"><a class="nav-link <?= $activeMenu === 'courses' ? 'active fw-bold' : '' ?>" href="<?= e(base_url('teacher/courses.php')) ?>">دوره‌های من</a></li>
                    <li class="nav-item">
                        <a class="nav-link <?= $activeMenu === 'messages' ? 'active fw-bold' : '' ?>" href="<?= e(base_url('teacher/messages.php')) ?>">
                            پیام‌ها
                            <?php $tUnread = teacher_total_unread_messages((int) (auth_id() ?? 0)); if ($tUnread > 0): ?>
                                <span class="badge bg-danger"><?= $tUnread ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
            <span class="navbar-text text-white small me-3"><?= e(auth_user()['full_name'] ?? '') ?></span>
            <a href="<?= e(base_url()) ?>" class="btn btn-outline-light btn-sm me-2" target="_blank">سایت</a>
            <a href="<?= e(base_url('logout.php')) ?>" class="btn btn-outline-light btn-sm">خروج</a>
        </div>
    </div>
</nav>

<main class="container py-4">
