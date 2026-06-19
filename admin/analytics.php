<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_auth('admin');

$pageTitle = 'گزارش‌های تحلیلی';
$activeMenu = 'analytical_reports';

$tab = $_GET['tab'] ?? 'overview';

$extended = report_dashboard_extended();
$interests = report_top_interests(20);
$byProvince = report_enrollments_by_province();
$byInstitution = report_enrollments_by_institution();
$byCategory = report_enrollments_by_category();
$byCourse = report_enrollments_by_course();
$certSummary = report_certificate_summary();
$teacherSummary = report_teacher_applications_summary();
$payments = report_payments_summary();

// دریافت گواهی‌های اخیر (اگر تابع admin_certificates_list وجود دارد)
if (function_exists('admin_certificates_list')) {
    $certList = admin_certificates_list(null, 50);
} else {
    $certList = db()->query("
        SELECT cr.*, u.full_name AS student_name, c.title AS course_title, e.final_grade
        FROM certificate_requests cr
        JOIN enrollments e ON e.id = cr.enrollment_id
        JOIN users u ON u.id = e.user_id
        JOIN courses c ON c.id = e.course_id
        ORDER BY cr.requested_at DESC
        LIMIT 50
    ")->fetchAll();
}

// فیش‌های آفلاین اخیر (جایگزین report_recent_payments)
$recentReceipts = db()->query("
    SELECT e.id, e.status, e.receipt_path, e.enrolled_at,
           u.full_name, c.title AS course_title, c.price
    FROM enrollments e
    JOIN users u ON u.id = e.user_id
    JOIN courses c ON c.id = e.course_id
    WHERE c.is_paid = 1 AND e.receipt_path IS NOT NULL
    ORDER BY e.enrolled_at DESC
    LIMIT 15
")->fetchAll();

require dirname(__DIR__) . '/includes/layout/admin_header.php';
?>

<h1 class="h3 mb-4">گزارش‌های تحلیلی</h1>

<ul class="nav nav-tabs mb-4 flex-wrap">
    <?php
    $tabs = [
        'overview' => 'خلاصه',
        'interests' => 'علاقه‌مندی‌ها',
        'enrollments' => 'ثبت‌نام‌ها',
        'certificates' => 'گواهی‌ها',
        'teachers' => 'اساتید',
        'payments' => 'پرداخت‌ها',
    ];
    foreach ($tabs as $key => $label):
    ?>
        <li class="nav-item">
            <a class="nav-link <?= $tab === $key ? 'active' : '' ?>" href="?tab=<?= e($key) ?>"><?= e($label) ?></a>
        </li>
    <?php endforeach; ?>
</ul>

<!-- ===== تب خلاصه ===== -->
<?php if ($tab === 'overview'): ?>
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card stat-card p-3 border-0">
            <div class="small text-muted">دانشجویان</div>
            <div class="stat-value"><?= $extended['students'] ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card p-3 border-0">
            <div class="small text-muted">اساتید تأییدشده</div>
            <div class="stat-value"><?= $extended['teachers'] ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card p-3 border-0">
            <div class="small text-muted">ثبت‌نام فعال</div>
            <div class="stat-value"><?= $extended['enrollments_active'] ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card p-3 border-0">
            <div class="small text-muted">گواهی در انتظار</div>
            <div class="stat-value"><?= $extended['certificates_pending'] ?></div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-bold">خلاصه گواهی‌ها</div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li>در انتظار: <strong><?= $certSummary['pending'] ?></strong></li>
                    <li>تأییدشده: <strong><?= $certSummary['approved'] ?></strong></li>
                    <li>ردشده: <strong><?= $certSummary['rejected'] ?></strong></li>
                </ul>
                <a href="certificates.php?status=pending" class="btn btn-sm btn-outline-primary mt-2">مدیریت گواهی‌ها</a>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-bold">خلاصه پرداخت‌ها</div>
            <div class="card-body">
                <p class="mb-1">موفق: <strong><?= $payments['paid_count'] ?></strong> — <?= e(format_price($payments['paid_total'])) ?></p>
                <p class="mb-1">در انتظار: <strong><?= $payments['pending_count'] ?></strong></p>
                <p class="mb-0">رد‌شده: <strong><?= $payments['failed_count'] ?></strong></p>
            </div>
        </div>
    </div>
</div>

<!-- ===== تب علاقه‌مندی‌ها ===== -->
<?php elseif ($tab === 'interests'): ?>
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white"><strong>محبوب‌ترین علاقه‌مندی‌های دانشجویان</strong></div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light"><tr><th>علاقه‌مندی</th><th>تعداد انتخاب</th><th>نمودار</th></tr></thead>
            <tbody>
                <?php $maxInt = $interests !== [] ? max(1, ...array_map('intval', array_column($interests, 'student_count'))) : 1; ?>
                <?php foreach ($interests as $row): ?>
                <tr>
                    <td><?= e($row['name']) ?></td>
                    <td><?= (int) $row['student_count'] ?></td>
                    <td style="width:40%">
                        <div class="progress" style="height:20px">
                            <div class="progress-bar bg-primary" style="width:<?= round(((int)$row['student_count'] / $maxInt) * 100) ?>%"></div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (!$interests): ?><tr><td colspan="3" class="text-muted text-center">داده‌ای نیست</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ===== تب ثبت‌نام‌ها ===== -->
<?php elseif ($tab === 'enrollments'): ?>
<div class="row g-4">
    <div class="col-lg-6">
        <h2 class="h6">بر اساس استان</h2>
        <div class="table-responsive bg-white rounded shadow-sm mb-4">
            <table class="table table-sm mb-0">
                <thead class="table-light"><tr><th>استان</th><th>ثبت‌نام</th><th>دانشجو</th></tr></thead>
                <tbody>
                    <?php foreach ($byProvince as $r): ?>
                    <tr><td><?= e($r['province_name'] ?? 'نامشخص') ?></td><td><?= (int)$r['enrollment_count'] ?></td><td><?= (int)$r['student_count'] ?></td></tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <h2 class="h6">بر اساس حوزه تخصصی</h2>
        <div class="table-responsive bg-white rounded shadow-sm">
            <table class="table table-sm mb-0">
                <thead class="table-light"><tr><th>حوزه</th><th>ثبت‌نام</th></tr></thead>
                <tbody>
                    <?php foreach ($byCategory as $r): ?>
                    <tr><td><?= e($r['category_name'] ?? '---') ?></td><td><?= (int)$r['enrollment_count'] ?></td></tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-lg-6">
        <h2 class="h6">بر اساس دانشکده (۲۰ مورد اول)</h2>
        <div class="table-responsive bg-white rounded shadow-sm mb-4" style="max-height:280px;overflow:auto">
            <table class="table table-sm mb-0">
                <thead class="table-light"><tr><th>استان</th><th>دانشکده</th><th>ثبت‌نام</th></tr></thead>
                <tbody>
                    <?php foreach ($byInstitution as $r): ?>
                    <tr><td><?= e($r['province_name']) ?></td><td><?= e($r['institution_name']) ?></td><td><?= (int)$r['enrollment_count'] ?></td></tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <h2 class="h6">بر اساس دوره</h2>
        <div class="table-responsive bg-white rounded shadow-sm" style="max-height:280px;overflow:auto">
            <table class="table table-sm mb-0">
                <thead class="table-light"><tr><th>دوره</th><th>ثبت‌نام</th><th>تکمیل</th></tr></thead>
                <tbody>
                    <?php foreach ($byCourse as $r): ?>
                    <tr><td><?= e($r['title']) ?></td><td><?= (int)$r['enrollment_count'] ?></td><td><?= (int)$r['completed_count'] ?></td></tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ===== تب گواهی‌ها ===== -->
<?php elseif ($tab === 'certificates'): ?>
<div class="row g-3 mb-3">
    <div class="col-md-4"><div class="alert alert-warning mb-0">در انتظار: <strong><?= $certSummary['pending'] ?></strong></div></div>
    <div class="col-md-4"><div class="alert alert-success mb-0">تأیید: <strong><?= $certSummary['approved'] ?></strong></div></div>
    <div class="col-md-4"><div class="alert alert-danger mb-0">رد: <strong><?= $certSummary['rejected'] ?></strong></div></div>
</div>
<div class="table-responsive bg-white rounded shadow-sm">
    <table class="table table-sm table-hover mb-0">
        <thead class="table-light"><tr><th>دانشجو</th><th>دوره</th><th>نمره</th><th>وضعیت</th><th>شماره</th></tr></thead>
        <tbody>
            <?php foreach ($certList as $c): ?>
            <tr>
                <td><?= e($c['student_name'] ?? '') ?></td>
                <td><?= e($c['course_title'] ?? '') ?></td>
                <td><?= e($c['final_grade'] ?? '---') ?></td>
                <td><?= e(certificate_status_label($c['status'])) ?></td>
                <td dir="ltr"><?= e($c['certificate_number'] ?? '---') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- ===== تب اساتید ===== -->
<?php elseif ($tab === 'teachers'): ?>
<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card p-3 border-0 shadow-sm">در انتظار: <strong><?= $teacherSummary['pending'] ?></strong></div></div>
    <div class="col-md-3"><div class="card p-3 border-0 shadow-sm">تأیید: <strong><?= $teacherSummary['approved'] ?></strong></div></div>
    <div class="col-md-3"><div class="card p-3 border-0 shadow-sm">رد: <strong><?= $teacherSummary['rejected'] ?></strong></div></div>
    <div class="col-md-3"><div class="card p-3 border-0 shadow-sm">کل: <strong><?= $teacherSummary['total'] ?></strong></div></div>
</div>
<p><a href="<?= e(base_url('admin/teacher_applications.php')) ?>" class="btn btn-primary btn-sm">مدیریت درخواست‌های همکاری</a></p>

<!-- ===== تب پرداخت‌ها ===== -->
<?php elseif ($tab === 'payments'): ?>
<div class="row g-3 mb-4">
    <div class="col-md-4"><div class="card p-3 border-0 shadow-sm">پرداخت موفق: <strong><?= $payments['paid_count'] ?></strong><br><?= e(format_price($payments['paid_total'])) ?></div></div>
    <div class="col-md-4"><div class="card p-3 border-0 shadow-sm">در انتظار: <strong><?= $payments['pending_count'] ?></strong></div></div>
    <div class="col-md-4"><div class="card p-3 border-0 shadow-sm">رد‌شده: <strong><?= $payments['failed_count'] ?></strong></div></div>
</div>
<div class="table-responsive bg-white rounded shadow-sm">
    <table class="table table-sm mb-0">
        <thead class="table-light"><tr><th>دانشجو</th><th>دوره</th><th>مبلغ (تومان)</th><th>وضعیت</th><th>فیش</th><th>تاریخ</th></tr></thead>
        <tbody>
            <?php foreach ($recentReceipts as $p): ?>
            <tr>
                <td><?= e($p['full_name']) ?></td>
                <td><?= e($p['course_title']) ?></td>
                <td><?= number_format((int)$p['price']) ?></td>
                <td><?= e($p['status']) ?></td>
                <td>
                    <?php if ($p['receipt_path']): ?>
                        <a href="<?= e(upload_url($p['receipt_path'])) ?>" target="_blank" class="btn btn-sm btn-outline-primary">مشاهده</a>
                    <?php else: ?>
                        ---
                    <?php endif; ?>
                </td>
                <td><?= e(format_datetime($p['enrolled_at'])) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (!$recentReceipts): ?><tr><td colspan="6" class="text-muted text-center">تراکنشی نیست</td></tr><?php endif; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php require dirname(__DIR__) . '/includes/layout/admin_footer.php'; ?>