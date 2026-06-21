<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_auth('admin');

$pageTitle = 'داشبورد';
$activeMenu = 'dashboard';

$stats = report_dashboard_extended();

require dirname(__DIR__) . '/includes/layout/admin_header.php';
?>

<h1 class="h3 mb-4">داشبورد مدیریت</h1>

<?php if ($msg = flash('success')): ?>
    <div class="alert alert-success alert-dismissible fade show"><?= e($msg) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card stat-card p-3 border-0 h-100">
            <div class="text-muted small">دانشجویان</div>
            <div class="stat-value"><?= $stats['students'] ?></div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card stat-card p-3 border-0 h-100">
            <div class="text-muted small">اساتید</div>
            <div class="stat-value"><?= $stats['teachers'] ?></div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card stat-card p-3 border-0 h-100">
            <div class="text-muted small">دوره منتشرشده</div>
            <div class="stat-value"><?= $stats['courses_published'] ?></div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card stat-card p-3 border-0 h-100">
            <div class="text-muted small">ثبت‌نام فعال</div>
            <div class="stat-value"><?= $stats['enrollments_active'] ?></div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <a href="<?= e(base_url('admin/certificates.php?status=pending')) ?>" class="text-decoration-none text-reset">
            <div class="card stat-card p-3 border-0 h-100">
                <div class="text-muted small">گواهی در انتظار</div>
                <div class="stat-value"><?= $stats['certificates_pending'] ?></div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <a href="<?= e(base_url('admin/teacher_applications.php')) ?>" class="text-decoration-none text-reset">
            <div class="card stat-card p-3 border-0 h-100">
                <div class="text-muted small">استاد در انتظار</div>
                <div class="stat-value"><?= $stats['teachers_pending'] ?></div>
            </div>
        </a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h2 class="h6">پرداخت‌های موفق</h2>
                <p class="mb-0 fs-5 text-primary"><?= e(format_price($stats['payments']['paid_total'])) ?></p>
                <p class="small text-muted"><?= $stats['payments']['paid_count'] ?> تراکنش</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h2 class="h6">گواهی‌های صادرشده</h2>
                <p class="mb-0 fs-5 text-success"><?= $stats['certificates']['approved'] ?></p>
                <p class="small text-muted"><?= $stats['certificates']['pending'] ?> در انتظار بررسی</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex flex-column">
                <h2 class="h6">گزارش‌های تحلیلی</h2>
                <p class="small text-muted flex-grow-1">ثبت‌نام، علاقه‌مندی، پرداخت و گواهی</p>
                <a href="<?= e(base_url('admin/analytics.php')) ?>" class="btn btn-primary btn-sm">مشاهده گزارش‌ها</a>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h2 class="h5 mb-3">دسترسی سریع</h2>
        <div class="d-flex flex-wrap gap-2">
            <a href="<?= e(base_url('admin/students.php')) ?>" class="btn btn-outline-primary btn-sm">دانشجویان</a>
            <a href="<?= e(base_url('admin/courses.php')) ?>" class="btn btn-outline-primary btn-sm">دوره‌ها</a>
            <a href="<?= e(base_url('admin/certificates.php')) ?>" class="btn btn-outline-primary btn-sm">گواهی‌ها</a>
            <a href="<?= e(base_url('admin/analytics.php')) ?>" class="btn btn-outline-primary btn-sm">گزارش‌ها</a>
            <a href="<?= e(base_url('admin/settings.php')) ?>" class="btn btn-outline-secondary btn-sm">تنظیمات</a>
        </div>
    </div>
</div>

<?php require dirname(__DIR__) . '/includes/layout/admin_footer.php'; ?>
