<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';

$slug = $_GET['slug'] ?? '';
$course = $slug !== '' ? course_by_slug($slug) : null;
if (!$course) {
    http_response_code(404);
    exit('دوره یافت نشد.');
}

$pageTitle = $course['title'];
$img = $course['image'] ? upload_url($course['image']) : asset_url('images/course-placeholder.svg');

$enrollment = null;
$canEnroll = false;
$isLoggedIn = auth_check();
$isTeacherOfThisCourse = ($isLoggedIn && (int)auth_id() === (int)$course['teacher_id']);

if ($isLoggedIn) {
    $enrollment = student_enrollment((int) auth_id(), (int) $course['id']);
    // محدودیت مؤسسه فقط برای دانشجویان
    if (auth_role() === 'student') {
        $instStmt = db()->prepare('SELECT institution_id FROM users WHERE id = ?');
        $instStmt->execute([auth_id()]);
        $instId = $instStmt->fetchColumn();
        $visible = course_visible_to_student($course, $instId !== false ? (int) $instId : null);
    } else {
        $visible = true; // اساتید محدودیت مؤسسه ندارند
    }
    $canEnroll = $visible
              && !$isTeacherOfThisCourse
              && ($enrollment === null || $enrollment['status'] === 'pending_payment');
}

require __DIR__ . '/includes/layout/header.php';
?>

<section class="service-details-wrap ptb-100">
    <div class="container">
        <div class="row">
            <div class="col-xl-8">
                <div class="service-desc">
                    <h2><?= e($course['title']) ?></h2>
                    <p class="text-muted">
                        <?= e($course['category_name'] ?? '') ?>
                        <?php if (!empty($course['teacher_name'])): ?>
                            — استاد: <?= e($course['teacher_name']) ?>
                        <?php endif; ?>
                    </p>
                    <div class="service-img mb-25">
                        <img src="<?= e($img) ?>" alt="<?= e($course['title']) ?>" class="img-fluid rounded">
                    </div>
                    <div class="course-description">
                        <?= nl2br(($course['description'] ?? '')) ?>
                    </div>

                    <div class="course-info mt-4">
                        
                        <p><strong>حداقل نمره قبولی:</strong> <?= e($course['min_pass_grade']) ?></p>
                        <p><strong>قیمت:</strong> 
                            <?php if ((int) $course['is_paid']): ?>
                                <?= e(format_price($course['price'])) ?>
                            <?php else: ?>
                                رایگان
                            <?php endif; ?>
                        </p>
                    </div>

                    <?php if ($isLoggedIn && !$isTeacherOfThisCourse): ?>
                        <?php if ($enrollment && in_array($enrollment['status'], ['active', 'completed'], true)): ?>
                            <a href="<?= e(base_url('student/my_course.php?slug=' . urlencode($course['slug']))) ?>" class="btn btn-success">ورود به محتوای دوره</a>
                        <?php elseif ($enrollment && in_array($enrollment['status'], ['pending_payment', 'waiting_approval'])): ?>
                            <div class="alert alert-warning">ثبت‌نام اولیه انجام شده؛ لطفاً فیش پرداخت را بارگذاری کنید.</div>
                            <a href="<?= e(base_url('student/upload_receipt.php?enrollment_id=' . (int) $enrollment['id'])) ?>" class="btn btn-primary">آپلود فیش پرداخت</a>
                        <?php elseif ($canEnroll): ?>
                            <form method="post" action="<?= e(base_url('student/enroll.php')) ?>">
                                <?= csrf_field() ?>
                                <input type="hidden" name="course_id" value="<?= (int) $course['id'] ?>">
                                <button type="submit" class="btn btn-primary">
                                    <?= (int) $course['is_paid'] ? 'ثبت‌نام و پرداخت' : 'ثبت‌نام رایگان' ?>
                                </button>
                            </form>
                        <?php else: ?>
                            <div class="alert alert-secondary">این دوره برای شما قابل ثبت‌نام نیست یا قبلاً ثبت‌نام کرده‌اید.</div>
                        <?php endif; ?>
                    <?php elseif ($isTeacherOfThisCourse): ?>
                        <div class="alert alert-info">شما مدرس این دوره هستید.</div>
                    <?php else: ?>
                        <a href="<?= e(login_url()) ?>" class="btn btn-primary">ورود برای ثبت‌نام</a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-xl-4">
                <!-- سایدبار (بدون تغییر) -->
                <div class="sidebar">
                    <div class="sidebar-widget categories">
                        <h4>اطلاعات دوره</h4>
                        <ul class="list-style">
                            <li><strong>تعداد جلسات:</strong> <?= (int) ($course['session_count'] ?? 0) ?></li>
                            <li><strong>تاریخ شروع:</strong> <?= $course['start_date'] ? e(format_date($course['start_date'])) : 'نامشخص' ?></li>
                            <li><strong>تاریخ پایان:</strong> <?= $course['end_date'] ? e(format_date($course['end_date'])) : 'نامشخص' ?></li>
                            <?php if (!empty($course['schedule_notes'])): ?>
                                <li><strong>برنامه زمانی:</strong> <?= e($course['schedule_notes']) ?></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <div class="sidebar-widget contact-widget bg-f" style="background-image: url('<?= e(asset_url('img/hero/hero-bg-2.jpg')) ?>');">
                        <div class="overlay bg-vulcan op-8"></div>
                        <p>سوالی دارید؟</p>
                        <h3>با ما تماس بگیرید</h3>
                        <a href="tel:<?= e(setting('contact_phone')) ?>"><?= e(setting('contact_phone')) ?></a>
                        <a href="mailto:<?= e(setting('contact_email')) ?>"><?= e(setting('contact_email')) ?></a>
                        <a href="<?= e(base_url('contact.php')) ?>" class="btn style1">تماس با ما</a>
                    </div>
                    <!-- دوره‌های مشابه (بدون تغییر) -->
                    <?php
                    $similarStmt = db()->prepare("
                        SELECT id, title, slug, image FROM courses
                        WHERE status = 'published' AND category_id = ? AND id != ?
                        LIMIT 3
                    ");
                    $similarStmt->execute([$course['category_id'], $course['id']]);
                    $similarCourses = $similarStmt->fetchAll();
                    if (!empty($similarCourses)): ?>
                    <div class="sidebar-widget categories mt-4">
                        <h4>دوره‌های مشابه</h4>
                        <div class="category-box style1">
                            <ul class="list-style">
                                <?php foreach ($similarCourses as $sim): ?>
                                    <li>
                                        <a href="<?= e(base_url('course.php?slug=' . $sim['slug'])) ?>">
                                            <i class="ri-arrow-right-s-line"></i>
                                            <?= e($sim['title']) ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/layout/footer.php'; ?>
