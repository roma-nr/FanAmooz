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

// تراکنش‌های اخیر (پرداخت آنلاین)
$recentPayments = report_recent_payments(15);

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

<!-- تب‌ها همانند قبل، فقط در تب پرداخت‌ها از recentPayments استفاده می‌کنیم -->

<?php if ($tab === 'payments'): ?>
<div class="row g-3 mb-4">
    <div class="col-md-4"><div class="card p-3 border-0 shadow-sm">پرداخت موفق: <strong><?= $payments['paid_count'] ?></strong><br><?= e(format_price($payments['paid_total'])) ?></div></div>
    <div class="col-md-4"><div class="card p-3 border-0 shadow-sm">در انتظار: <strong><?= $payments['pending_count'] ?></strong></div></div>
    <div class="col-md-4"><div class="card p-3 border-0 shadow-sm">رد‌شده: <strong><?= $payments['failed_count'] ?></strong></div></div>
</div>
<div class="table-responsive bg-white rounded shadow-sm">
    <table class="table table-sm mb-0">
        <thead class="table-light"><tr><th>دانشجو</th><th>دوره</th><th>مبلغ (تومان)</th><th>وضعیت</th><th>کد پیگیری</th><th>تاریخ</th></tr></thead>
        <tbody>
            <?php foreach ($recentPayments as $p): ?>
            <tr>
                <td><?= e($p['full_name']) ?></td>
                <td><?= e($p['course_title']) ?></td>
                <td><?= number_format((int)$p['amount']) ?></td>
                <td><?= e($p['status']) ?></td>
                <td dir="ltr"><?= e($p['ref_id'] ?? '---') ?></td>
                <td><?= e(format_datetime($p['created_at'])) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (!$recentPayments): ?><tr><td colspan="6" class="text-muted text-center">تراکنشی نیست</td></tr><?php endif; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php require dirname(__DIR__) . '/includes/layout/admin_footer.php'; ?>