<?php
declare(strict_types=1);

$pageTitle = $pageTitle ?? site_name();
$bodyClass = $bodyClass ?? '';
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> | <?= e(site_name()) ?></title>

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- Bootstrap RTL + Core CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    
    <!-- تمام فایل‌های CSS قالب Tezu -->
    <link href="<?= e(asset_url('css/bootstrap.min.css')) ?>" rel="stylesheet">
    <link href="<?= e(asset_url('css/flaticon.css')) ?>" rel="stylesheet">
    <link href="<?= e(asset_url('css/remixicon.css')) ?>" rel="stylesheet">
    <!-- Owl Carousel CSS (CDN) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
    <link href="<?= e(asset_url('css/magnific-popup.css')) ?>" rel="stylesheet">
    <link href="<?= e(asset_url('css/fancybox.css')) ?>" rel="stylesheet">
    <link href="<?= e(asset_url('css/odometer.css')) ?>" rel="stylesheet">
    <link href="<?= e(asset_url('css/aos.css')) ?>" rel="stylesheet">
    <link href="<?= e(asset_url('css/style.css')) ?>" rel="stylesheet">
    <link href="<?= e(asset_url('css/responsive.css')) ?>" rel="stylesheet">
    <link href="<?= e(asset_url('css/theme.css')) ?>" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">

    <!-- Hammer.js (اختیاری) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/hammer.js/2.0.8/hammer.min.js"></script>

    <style>
        /* استایل‌های همستر (همان کدی که فرستادید) */
        .wheel-and-hamster {
            --dur: 1s;
            position: relative;
            width: 12em;
            height: 12em;
            font-size: 14px;
        }
        .wheel,
        .hamster,
        .hamster div,
        .spoke {
            position: absolute;
        }
        .wheel,
        .spoke {
            border-radius: 50%;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
        .wheel {
            background: radial-gradient(100% 100% at center,hsla(0,0%,60%,0) 47.8%,hsl(0,0%,60%) 48%);
            z-index: 2;
        }
        .hamster {
            animation: hamster var(--dur) ease-in-out infinite;
            top: 50%;
            left: calc(50% - 3.5em);
            width: 7em;
            height: 3.75em;
            transform: rotate(4deg) translate(-0.8em,1.85em);
            transform-origin: 50% 0;
            z-index: 1;
        }
        .hamster__head {
            animation: hamsterHead var(--dur) ease-in-out infinite;
            background: hsl(30,90%,55%);
            border-radius: 70% 30% 0 100% / 40% 25% 25% 60%;
            box-shadow: 0 -0.25em 0 hsl(30,90%,80%) inset, 0.75em -1.55em 0 hsl(30,90%,90%) inset;
            top: 0;
            left: -2em;
            width: 2.75em;
            height: 2.5em;
            transform-origin: 100% 50%;
        }
        .hamster__ear {
            animation: hamsterEar var(--dur) ease-in-out infinite;
            background: hsl(0,90%,85%);
            border-radius: 50%;
            box-shadow: -0.25em 0 hsl(30,90%,55%) inset;
            top: -0.25em;
            right: -0.25em;
            width: 0.75em;
            height: 0.75em;
            transform-origin: 50% 75%;
        }
        .hamster__eye {
            animation: hamsterEye var(--dur) linear infinite;
            background-color: hsl(0,0%,0%);
            border-radius: 50%;
            top: 0.375em;
            left: 1.25em;
            width: 0.5em;
            height: 0.5em;
        }
        .hamster__nose {
            background: hsl(0,90%,75%);
            border-radius: 35% 65% 85% 15% / 70% 50% 50% 30%;
            top: 0.75em;
            left: 0;
            width: 0.2em;
            height: 0.25em;
        }
        .hamster__body {
            animation: hamsterBody var(--dur) ease-in-out infinite;
            background: hsl(30,90%,90%);
            border-radius: 50% 30% 50% 30% / 15% 60% 40% 40%;
            box-shadow: 0.1em 0.75em 0 hsl(30,90%,55%) inset, 0.15em -0.5em 0 hsl(30,90%,80%) inset;
            top: 0.25em;
            left: 2em;
            width: 4.5em;
            height: 3em;
            transform-origin: 17% 50%;
            transform-style: preserve-3d;
        }
        .hamster__limb--fr,
        .hamster__limb--fl {
            clip-path: polygon(0 0,100% 0,70% 80%,60% 100%,0% 100%,40% 80%);
            top: 2em;
            left: 0.5em;
            width: 1em;
            height: 1.5em;
            transform-origin: 50% 0;
        }
        .hamster__limb--fr {
            animation: hamsterFRLimb var(--dur) linear infinite;
            background: linear-gradient(hsl(30,90%,80%) 80%,hsl(0,90%,75%) 80%);
            transform: rotate(15deg) translateZ(-1px);
        }
        .hamster__limb--fl {
            animation: hamsterFLLimb var(--dur) linear infinite;
            background: linear-gradient(hsl(30,90%,90%) 80%,hsl(0,90%,85%) 80%);
            transform: rotate(15deg);
        }
        .hamster__limb--br,
        .hamster__limb--bl {
            border-radius: 0.75em 0.75em 0 0;
            clip-path: polygon(0 0,100% 0,100% 30%,70% 90%,70% 100%,30% 100%,40% 90%,0% 30%);
            top: 1em;
            left: 2.8em;
            width: 1.5em;
            height: 2.5em;
            transform-origin: 50% 30%;
        }
        .hamster__limb--br {
            animation: hamsterBRLimb var(--dur) linear infinite;
            background: linear-gradient(hsl(30,90%,80%) 90%,hsl(0,90%,75%) 90%);
            transform: rotate(-25deg) translateZ(-1px);
        }
        .hamster__limb--bl {
            animation: hamsterBLLimb var(--dur) linear infinite;
            background: linear-gradient(hsl(30,90%,90%) 90%,hsl(0,90%,85%) 90%);
            transform: rotate(-25deg);
        }
        .hamster__tail {
            animation: hamsterTail var(--dur) linear infinite;
            background: hsl(0,90%,85%);
            border-radius: 0.25em 50% 50% 0.25em;
            box-shadow: 0 -0.2em 0 hsl(0,90%,75%) inset;
            top: 1.5em;
            right: -0.5em;
            width: 1em;
            height: 0.5em;
            transform: rotate(30deg) translateZ(-1px);
            transform-origin: 0.25em 0.25em;
        }
        .spoke {
            animation: spoke var(--dur) linear infinite;
            background: radial-gradient(100% 100% at center,hsl(0,0%,60%) 4.8%,hsla(0,0%,60%,0) 5%), linear-gradient(hsla(0,0%,55%,0) 46.9%,hsl(0,0%,65%) 47% 52.9%,hsla(0,0%,65%,0) 53%) 50% 50% / 99% 99% no-repeat;
        }
        @keyframes hamster {
            from, to { transform: rotate(4deg) translate(-0.8em,1.85em); }
            50% { transform: rotate(0) translate(-0.8em,1.85em); }
        }
        @keyframes hamsterHead {
            from, 25%, 50%, 75%, to { transform: rotate(0); }
            12.5%, 37.5%, 62.5%, 87.5% { transform: rotate(8deg); }
        }
        @keyframes hamsterEye {
            from, 90%, to { transform: scaleY(1); }
            95% { transform: scaleY(0); }
        }
        @keyframes hamsterEar {
            from, 25%, 50%, 75%, to { transform: rotate(0); }
            12.5%, 37.5%, 62.5%, 87.5% { transform: rotate(12deg); }
        }
        @keyframes hamsterBody {
            from, 25%, 50%, 75%, to { transform: rotate(0); }
            12.5%, 37.5%, 62.5%, 87.5% { transform: rotate(-2deg); }
        }
        @keyframes hamsterFRLimb {
            from, 25%, 50%, 75%, to { transform: rotate(50deg) translateZ(-1px); }
            12.5%, 37.5%, 62.5%, 87.5% { transform: rotate(-30deg) translateZ(-1px); }
        }
        @keyframes hamsterFLLimb {
            from, 25%, 50%, 75%, to { transform: rotate(-30deg); }
            12.5%, 37.5%, 62.5%, 87.5% { transform: rotate(50deg); }
        }
        @keyframes hamsterBRLimb {
            from, 25%, 50%, 75%, to { transform: rotate(-60deg) translateZ(-1px); }
            12.5%, 37.5%, 62.5%, 87.5% { transform: rotate(20deg) translateZ(-1px); }
        }
        @keyframes hamsterBLLimb {
            from, 25%, 50%, 75%, to { transform: rotate(20deg); }
            12.5%, 37.5%, 62.5%, 87.5% { transform: rotate(-60deg); }
        }
        @keyframes hamsterTail {
            from, 25%, 50%, 75%, to { transform: rotate(30deg) translateZ(-1px); }
            12.5%, 37.5%, 62.5%, 87.5% { transform: rotate(10deg) translateZ(-1px); }
        }
        @keyframes spoke {
            from { transform: rotate(0); }
            to { transform: rotate(-1turn); }
        }

        /* مخفی کردن لودر بعد از بارگذاری */
        #loader-wrapper.hidden {
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.8s ease;
        }

        /* ناوبار همیشه چسبان با رنگ دلخواه */
        .site-header {
            position: sticky;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1020;
            transition: background 0.3s ease;
        }
        .site-header .navbar {
            background: rgba(13, 110, 253, 0.95) !important;
            backdrop-filter: blur(10px);
        }

        /* سایدبار چسبان */
        .sidebar-sticky {
            position: sticky;
            top: 90px;
        }

        /* ========== جعبه جستجوی ناوبار ========== */
        .search-wrapper {
            position: relative;
            margin-left: 1rem;
        }
        .search-wrapper .form-group {
            position: relative;
            margin: 0;
            width: 220px;
        }
        .search-wrapper .form-group input {
            padding-right: 30px;
            padding-left: 40px;
            border-radius: 8px;
            width: 100%;
            border: 1px solid rgba(0,0,0,0.15);
            background: #fff;
            color: #333;
            font-size: 14px;
            height: 38px;
        }
        .search-wrapper .form-group input::placeholder {
            color: #888;
        }
        .search-wrapper .form-group .search-btn {
            position: absolute;
            left: 8px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #555;
            font-size: 18px;
            padding: 0;
            line-height: 1;
            cursor: pointer;
        }
        .search-wrapper .form-group .clear-btn {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #000;
            font-size: 18px;
            padding: 0;
            line-height: 1;
            cursor: pointer;
            display: none;
        }
        .search-wrapper .form-group .clear-btn.visible {
            display: block;
        }

        /* دراپ‌داون نتایج جستجو */
        .search-dropdown {
            position: absolute;
            top: calc(100% + 5px);
            left: auto;
            right: 0;
            width: 320px;
            background: #fff;
            border: 1px solid rgba(0,0,0,0.1);
            border-radius: 0 0 8px 8px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
            z-index: 1030;
            max-height: 350px;
            overflow-y: auto;
            display: none;
            font-size: 13px;
        }
        .search-dropdown.show {
            display: block;
        }
        .search-dropdown .dropdown-item {
            padding: 0.6rem 1rem;
            border-bottom: 1px solid #f0f0f0;
            cursor: pointer;
            white-space: normal;
            color: #333;
            transition: background 0.15s;
        }
        .search-dropdown .dropdown-item:hover {
            background: #f1f3ff;
        }
        .search-dropdown .dropdown-item .badge {
            background-color: #ffe54c;
            color: #1565c0;
            font-weight: 600;
            margin-left: 0.5rem;
        }
        .search-dropdown .dropdown-item .desc {
            font-size: 0.8rem;
            color: #666;
            margin-top: 2px;
        }
        .search-dropdown .dropdown-item mark {
            background: #ffeb3b;
            padding: 0 2px;
            border-radius: 2px;
        }
        .search-dropdown .all-results {
            text-align: center;
            padding: 0.5rem;
            background: #f8f9fa;
            border-top: 1px solid #eee;
            font-weight: 500;
            cursor: pointer;
        }
        .search-dropdown .all-results:hover {
            background: #e9ecef;
        }

        /* حذف کادر سفید لوگوی سوم (اختیاری) */
        .logo-slot-3 img {
            background: none !important;
            padding: 0 !important;
            box-shadow: none !important;
        }
    </style>

    <?php require __DIR__ . '/head_extras.php'; ?>

    <!-- Tom Select -->
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

    <!-- Owl Carousel -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
</head>
<body class="<?= e($bodyClass) ?>">

<!-- Preloader همستر -->
<div id="loader-wrapper" style="position:fixed; top:0; left:0; width:100%; height:100%; background:#ffffff; display:flex; align-items:center; justify-content:center; z-index:9999; flex-direction:column;">
    <div aria-label="Orange and tan hamster running in a metal wheel" role="img" class="wheel-and-hamster">
        <div class="wheel"></div>
        <div class="hamster">
            <div class="hamster__body">
                <div class="hamster__head">
                    <div class="hamster__ear"></div>
                    <div class="hamster__eye"></div>
                    <div class="hamster__nose"></div>
                </div>
                <div class="hamster__limb hamster__limb--fr"></div>
                <div class="hamster__limb hamster__limb--fl"></div>
                <div class="hamster__limb hamster__limb--br"></div>
                <div class="hamster__limb hamster__limb--bl"></div>
                <div class="hamster__tail"></div>
            </div>
        </div>
        <div class="spoke"></div>
    </div>
    <p style="margin-top:20px; font-family:Vazirmatn, sans-serif; color:#1565c0; font-weight:600; font-size:14px;">در حال بارگذاری...</p>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        var loader = document.getElementById('loader-wrapper');
        if (loader) {
            loader.classList.add('hidden');
            setTimeout(function() {
                loader.style.display = 'none';
            }, 1000);
        }
    }, 1000);
});
</script>

<header class="site-header shadow-sm">
    <nav class="navbar navbar-expand-lg navbar-dark py-2">
        <div class="container">
            <!-- سه لوگو (لوگوی سوم با استایل متفاوت) -->
            <div class="d-flex align-items-center gap-3">
                <?php for ($i = 1; $i <= 3; $i++): ?>
                    <?php $href = logo_link($i); ?>
                    <div class="logo-slot <?= $i === 3 ? 'logo-slot-3' : '' ?>">
                        <?php if ($href !== ''): ?><a href="<?= e($href) ?>" target="_blank" rel="noopener"><?php endif; ?>
                        <img src="<?= e(logo_url($i)) ?>" alt="<?= e(logo_alt($i)) ?>">
                        <?php if ($href !== ''): ?></a><?php endif; ?>
                    </div>
                <?php endfor; ?>
            </div>

            <a class="navbar-brand fw-bold ms-lg-3" href="<?= e(base_url()) ?>">      </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="<?= e(base_url()) ?>">صفحه اصلی</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= e(base_url('courses.php')) ?>">دوره‌ها</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= e(base_url('blog.php')) ?>">اطلاعیه‌ها</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= e(base_url('about.php')) ?>">درباره ما</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= e(base_url('contact.php')) ?>">تماس با ما</a></li>
                </ul>

                <!-- جعبه جستجوی زنده -->
                <form class="search-wrapper me-3" autocomplete="off" action="<?= e(base_url('search.php')) ?>" method="get" onsubmit="return false;">
                    <div class="form-group">
                        <input type="search" name="q" id="navSearchInput" class="form-control" placeholder="جستجو...">
                        <button type="button" class="search-btn" id="navSearchBtn"><i class="flaticon-search"></i></button>
                        <button type="button" class="clear-btn" id="navClearBtn">&times;</button>
                    </div>
                    <div id="searchDropdown" class="search-dropdown"></div>
                </form>

                <?php if (auth_check()): ?>
                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle" type="button" id="userMenuBtn" data-bs-toggle="dropdown" aria-expanded="false">
                            <?= e(auth_user()['full_name']) ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenuBtn">
                            <?php if (auth_role() === 'student'): ?>
                                <li><a class="dropdown-item" href="<?= e(base_url('student/index.php')) ?>">پنل دانشجو</a></li>
                            <?php elseif (auth_role() === 'teacher'): ?>
                                <li><a class="dropdown-item" href="<?= e(base_url('teacher/index.php')) ?>">پنل استاد</a></li>
                            <?php elseif (auth_role() === 'admin'): ?>
                                <li><a class="dropdown-item" href="<?= e(base_url('admin/index.php')) ?>">پنل مدیریت</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= e(base_url('logout.php')) ?>">خروج</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="<?= e(login_url()) ?>" class="button-53 btn-header">ورود</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
</header>
<main>

<!-- ========== اسکریپت جستجوی زنده ========== -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('navSearchInput');
    const dropdown = document.getElementById('searchDropdown');
    const searchBtn = document.getElementById('navSearchBtn');
    const clearBtn = document.getElementById('navClearBtn');
    let timeout = null;

    if (!input) return;

    // نمایش/مخفی دکمه ضربدر
    input.addEventListener('input', function () {
        clearBtn.classList.toggle('visible', this.value.trim().length > 0);
    });
    clearBtn.addEventListener('click', function () {
        input.value = '';
        clearBtn.classList.remove('visible');
        dropdown.classList.remove('show');
        input.focus();
    });

    function performSearch(query) {
        if (query.length < 2) {
            dropdown.classList.remove('show');
            return;
        }
        fetch('<?= e(base_url('api/search.php')) ?>?q=' + encodeURIComponent(query) + '&limit=5')
            .then(res => res.json())
            .then(data => {
                dropdown.innerHTML = '';
                // ترکیب نتایج دوره‌ها و اطلاعیه‌ها (حداکثر ۵ تا)
                let results = [];
                if (data.courses && data.courses.results) results = results.concat(data.courses.results);
                if (data.announcements && data.announcements.results) results = results.concat(data.announcements.results);
                results = results.slice(0, 5);

                if (results.length === 0) {
                    dropdown.innerHTML = '<div class="dropdown-item text-muted">موردی یافت نشد</div>';
                } else {
                    results.forEach(item => {
                        const el = document.createElement('div');
                        el.className = 'dropdown-item';
                        el.innerHTML = `
                            <span class="badge">${item.type === 'course' ? 'دوره' : 'اطلاعیه'}</span>
                            <strong>${item.highlighted_title}</strong>
                            <div class="desc">${item.highlighted_description}</div>
                        `;
                        el.addEventListener('click', () => { window.location.href = item.url; });
                        dropdown.appendChild(el);
                    });
                    // لینک همه نتایج
                    const all = document.createElement('div');
                    all.className = 'all-results';
                    all.textContent = 'مشاهده همه نتایج';
                    all.addEventListener('click', () => {
                        window.location.href = '<?= e(base_url('search.php')) ?>?q=' + encodeURIComponent(query);
                    });
                    dropdown.appendChild(all);
                }
                dropdown.classList.add('show');
            });
    }

    input.addEventListener('input', function () {
        clearTimeout(timeout);
        const query = this.value.trim();
        clearBtn.classList.toggle('visible', query.length > 0);
        timeout = setTimeout(() => performSearch(query), 300);
    });

    searchBtn.addEventListener('click', function () {
        const query = input.value.trim();
        if (query.length > 0) {
            window.location.href = '<?= e(base_url('search.php')) ?>?q=' + encodeURIComponent(query);
        }
    });

    document.addEventListener('click', function (e) {
        if (!input.contains(e.target) && !dropdown.contains(e.target) && !searchBtn.contains(e.target) && !clearBtn.contains(e.target)) {
            dropdown.classList.remove('show');
        }
    });

    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const query = input.value.trim();
            if (query.length > 0) {
                window.location.href = '<?= e(base_url('search.php')) ?>?q=' + encodeURIComponent(query);
            }
            dropdown.classList.remove('show');
        }
    });
});
</script>