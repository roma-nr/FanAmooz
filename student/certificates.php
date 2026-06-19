<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_student_auth();

$pageTitle = 'گواهی‌های من';
$activeMenu = 'certificates';
$userId = (int) auth_id();
$items = student_certificates_list($userId);

require dirname(__DIR__) . '/includes/layout/student_header.php';
?>

<h1 class="h3 text-primary mb-4">گواهی‌های پایان دوره</h1>

<?php if (!phase7_certificates_ready()): ?>
    <div class="alert alert-warning">سیستم گواهی فعال نیست. با مدیر تماس بگیرید.</div>
<?php elseif (!$items): ?>
    <div class="alert alert-info">
        هنوز درخواست گواهی ثبت نکرده‌اید. پس از اتمام دوره و کسب نمره قبولی، از صفحه هر دوره در تب «گواهی» درخواست دهید.
    </div>
<?php else: ?>
    <div class="row g-3">
        <?php foreach ($items as $item): ?>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h2 class="h6"><?= e($item['course_title']) ?></h2>
                        <p class="small text-muted mb-2">
                            نمره: <?= e($item['final_grade'] ?? '—') ?> / حداقل: <?= e($item['min_pass_grade']) ?>
                        </p>
                        <p class="mb-2">
                            <span class="badge bg-<?= $item['status'] === 'approved' ? 'success' : ($item['status'] === 'rejected' ? 'danger' : 'warning text-dark') ?>">
                                <?= e(certificate_status_label($item['status'])) ?>
                            </span>
                        </p>
                        <?php if ($item['status'] === 'approved' && $item['certificate_number']): ?>
                            <p class="small mb-2">شماره گواهی: <code dir="ltr"><?= e($item['certificate_number']) ?></code></p>
                            <a href="<?= e(base_url('student/certificate_print.php?id=' . (int) $item['id'])) ?>" class="btn btn-success btn-sm" target="_blank">
                                <i class="bi bi-printer"></i> مشاهده و چاپ گواهی
                            </a>
                        <?php elseif ($item['status'] === 'pending'): ?>
                            <p class="small text-muted mb-0">درخواست شما در انتظار بررسی مدیر است.</p>
                        <?php elseif ($item['status'] === 'rejected'): ?>
                            <p class="small text-danger"><?= e($item['admin_note'] ?? 'درخواست رد شد.') ?></p>
                        <?php endif; ?>
                        <div class="mt-2">
                            <a href="<?= e(base_url('student/my_course.php?slug=' . urlencode($item['course_slug']) . '&tab=certificate')) ?>" class="small">صفحه دوره</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require dirname(__DIR__) . '/includes/layout/student_footer.php'; ?>
