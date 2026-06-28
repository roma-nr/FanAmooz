<?php

declare(strict_types=1);

$pageTitle = $pageTitle ?? 'پنل مدیریت';
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
	<script src="https://cdn.tiny.cloud/1/2qs136k1557c1jpyp2fial85xmrjnc9e78srprpvwf4dou73/tinymce/8/tinymce.min.js" referrerpolicy="origin" crossorigin="anonymous"></script>






<link rel="stylesheet" href="<?= asset_url('css/jalalidatepicker.min.css') ?>">
<script src="<?= asset_url('js/jalalidatepicker.min.js') ?>"></script>


<style>
    @media (max-width: 991.98px) {
        .admin-sidebar {
            position: fixed;
            top: 0;
            right: -280px;
            width: 280px;
            height: 100vh;
            background: #fff;
            z-index: 1050;
            transition: right 0.3s ease;
            box-shadow: -2px 0 10px rgba(0,0,0,0.1);
            overflow-y: auto;
        }
        .admin-sidebar.open {
            right: 0;
        }
        .admin-sidebar-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1040;
            display: none;
        }
        .admin-sidebar-backdrop.show {
            display: block;
        }
    }
	/* اصلاح سایز فونت در تقویم جلالی */
jdp-container {
    font-size: 12px;               /* فونت کلی کوچک‌تر */
}
jdp-container .jdp-day,
jdp-container .jdp-day-name {
    height: 28px;                  /* ارتفاع سلول‌های روز */
    line-height: 28px;
}
jdp-container .jdp-month select,
jdp-container .jdp-year select,
jdp-container .jdp-time select {
    font-size: 10px;               /* فونت داخل selectهای ماه/سال */
    padding: 2px 4px;
    transition: none !important;   /* حذف افکت پررنگ/کمرنگ */
}
jdp-container .jdp-month:hover,
jdp-container .jdp-year:hover {
    filter: none !important;       /* غیرفعال کردن hover که چشمک می‌زند */
}
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var toggler = document.getElementById('adminSidebarToggler');
        var sidebar = document.getElementById('adminSidebar');
        var backdrop = document.createElement('div');
        backdrop.className = 'admin-sidebar-backdrop';
        document.body.appendChild(backdrop);
        
        function closeSidebar() {
            sidebar.classList.remove('open');
            backdrop.classList.remove('show');
        }
        function openSidebar() {
            sidebar.classList.add('open');
            backdrop.classList.add('show');
        }
        toggler.addEventListener('click', function(e) {
            if (sidebar.classList.contains('open')) closeSidebar();
            else openSidebar();
        });
        backdrop.addEventListener('click', closeSidebar);
    });
</script>
    <?php require __DIR__ . '/head_extras.php'; ?>
</head>
<body>
<script>
  tinymce.init({
    selector: 'textarea',
	 directionality: 'rtl',
    plugins: [
      // Core editing features
      'anchor', 'autolink', 'charmap', 'codesample', 'emoticons', 'link', 'lists', 'media', 'searchreplace', 'table', 'visualblocks', 'wordcount',
      // Premium features
      'checklist', 'mediaembed', 'casechange', 'formatpainter', 'pageembed', 'a11ychecker', 'tinymcespellchecker', 'permanentpen', 'powerpaste', 'advtable', 'advcode', 'advtemplate', 'tinymceai', 'uploadcare', 'mentions', 'tinycomments', 'tableofcontents', 'footnotes', 'mergetags', 'autocorrect', 'typography', 'inlinecss', 'markdown','importword', 'exportword', 'exportpdf'
    ],
    toolbar: 'undo redo | tinymceai-chat tinymceai-quickactions tinymceai-review | blocks fontfamily fontsize | bold italic underline strikethrough | link media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography uploadcare | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
    tinycomments_mode: 'embedded',
    tinycomments_author: 'Author name',
    mergetags_list: [
      { value: 'First.Name', title: 'First Name' },
      { value: 'Email', title: 'Email' },
    ],
    tinymceai_token_provider: async () => {
      await fetch(`https://demo.api.tiny.cloud/1/2qs136k1557c1jpyp2fial85xmrjnc9e78srprpvwf4dou73/auth/random`, { method: "POST", credentials: "include" });
      return { token: await fetch(`https://demo.api.tiny.cloud/1/2qs136k1557c1jpyp2fial85xmrjnc9e78srprpvwf4dou73/jwt/tinymceai`, { credentials: "include" }).then(r => r.text()) };
    },
    uploadcare_public_key: '352cfce4ef26faefa7c7',
  });
</script>
<nav class="navbar navbar-dark bg-primary shadow-sm">
    <div class="container-fluid">
        <!-- دکمه همبرگر برای موبایل -->
        <button class="navbar-toggler d-lg-none" type="button" id="adminSidebarToggler">
            <span class="navbar-toggler-icon"></span>
        </button>
        <a class="navbar-brand" href="<?= e(base_url('admin/index.php')) ?>">
            <i class="bi bi-speedometer2 ms-1"></i> پنل مدیریت <?= e(site_name()) ?>
        </a>
        <div class="d-flex align-items-center gap-3 text-white">
            <span class="small d-none d-md-inline"><?= e(auth_user()['full_name'] ?? '') ?></span>
            <a href="<?= e(base_url()) ?>" class="btn btn-outline-light btn-sm" target="_blank">مشاهده سایت</a>
            <a href="<?= e(base_url('logout.php')) ?>" class="btn btn-light btn-sm text-primary">خروج</a>
        </div>
    </div>
</nav>
<div class="container-fluid">
    <div class="row">
        
		<aside class="col-lg-2 admin-sidebar py-3" id="adminSidebar">
    <nav class="nav flex-column px-2">
        <?php
                $menu = [
    'dashboard'       => ['index.php', 'داشبورد', 'bi-grid'],
    'provinces'       => ['provinces.php', 'استان‌ها', 'bi-map'],
    'institutions'    => ['institutions.php', 'دانشکده‌ها', 'bi-building'],
    'students'        => ['students.php', 'دانشجویان', 'bi-people'],
    'teachers_list'   => ['teachers.php', 'اساتید', 'bi-person-badge'],
    'teachers'        => ['teacher_applications.php', 'درخواست همکاری', 'bi-inbox'],
    'announcements'   => ['announcements.php', 'اطلاعیه‌ها', 'bi-megaphone'],
    'links'           => ['useful_links.php', 'لینک‌های مفید', 'bi-link-45deg'],
    'courses'         => ['courses.php', 'دوره‌ها', 'bi-book'],
    'certificates'    => ['certificates.php', 'گواهی‌ها', 'bi-award'],
    
	'analytical_reports' => ['analytics.php', 'گزارش‌های تحلیلی', 'bi-bar-chart'],
'error_reports'      => ['error_reports.php', 'گزارش‌های خطا', 'bi-exclamation-triangle'],
    'payments_offline'=> ['pending_payments.php', 'تراکنش‌های آفلاین', 'bi-credit-card-2-back'],
    'settings'        => ['settings.php', 'تنظیمات سایت', 'bi-gear'],
];
                foreach ($menu as $key => [$file, $label, $icon]):
                    $active = $activeMenu === $key ? 'active' : '';
                ?>
                <a class="nav-link <?= $active ?>" href="<?= e(base_url('admin/' . $file)) ?>">
                    <i class="bi <?= e($icon) ?> ms-1"></i> <?= e($label) ?>
                </a>
                <?php endforeach; ?>
    </nav>
</aside>
        <main class="col-lg-10 py-4 px-4">
