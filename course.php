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
$role = auth_role();

if ($isLoggedIn) {
    $enrollment = student_enrollment((int) auth_id(), (int) $course['id']);
    // محدودیت مؤسسه فقط برای دانشجویان
    if ($role === 'student') {
        $instStmt = db()->prepare('SELECT institution_id FROM users WHERE id = ?');
        $instStmt->execute([auth_id()]);
        $instId = $instStmt->fetchColumn();
        $visible = course_visible_to_student($course, $instId !== false ? (int) $instId : null);
    } else {
        $visible = true; // اساتید و ادمین محدودیت مؤسسه ندارند
    }
    $canEnroll = $visible
              && !$isTeacherOfThisCourse
              && ($enrollment === null || $enrollment['status'] === 'pending');
}

require __DIR__ . '/includes/layout/header.php';
?>

<style>
.sidebar-sticky {
    position: sticky;
    top: 90px;
}
</style>

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
                        <p><strong>هزینه:</strong> 
                            <?php if ((int) $course['is_paid']): ?>
                                <?= e(format_price($course['price'])) ?>
                            <?php else: ?>
                                رایگان
                            <?php endif; ?>
                        </p>
                    </div>

                    <?php if ($role === 'student'): ?>
                        <!-- دانشجو -->
                        <?php if ($enrollment && in_array($enrollment['status'], ['active', 'completed'], true)): ?>
                            <a href="<?= e(base_url('student/my_course.php?slug=' . urlencode($course['slug']))) ?>" class="btn btn-success">ورود به محتوای دوره</a>
                        <?php elseif ($enrollment && $enrollment['status'] === 'pending'): ?>
                            <div class="alert alert-warning">ثبت‌نام اولیه انجام شده؛ لطفاً پرداخت را تکمیل کنید.</div>
                            <a href="<?= e(base_url('student/pay.php?course_id=' . (int) $enrollment['id'])) ?>" class="btn btn-primary">تکمیل پرداخت</a>
                        <?php elseif ($canEnroll): ?>
                            <form method="post" action="<?= e(base_url('student/enroll.php')) ?>">
                                <?= csrf_field() ?>
                                <input type="hidden" name="course_id" value="<?= (int) $course['id'] ?>">
                                <button type="submit" class="btn btn-primary">
                                    <?= (int) $course['is_paid'] ? 'ثبت‌نام و پرداخت' : 'ثبت‌نام رایگان' ?>
                                </button>
                            </form>
                            <div class="mt-3 alert alert-info small">
                                <?php if ($course['status'] === 'published'): ?>
                                    <strong>شرایط انصراف:</strong> دانشجوی گرامی، شما می‌توانید تا پیش از پایان ۲۵٪ جلسات دوره، درخواست انصراف دهید. در این صورت، کل مبلغ پرداختی به کیف پول شما بازگردانده خواهد شد. پس از گذشت ۲۵٪، امکان انصراف و بازگشت وجه وجود ندارد.
                                <?php elseif ($course['status'] === 'archived'): ?>
                                    این دوره به پایان رسیده و محتوای آن به‌صورت کامل در دسترس است. با توجه به آرشیو بودن دوره، امکان انصراف و بازگشت وجه وجود ندارد.
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-secondary">این دوره برای شما قابل ثبت‌نام نیست یا قبلاً ثبت‌نام کرده‌اید.</div>
                        <?php endif; ?>

                    <?php elseif ($role === 'teacher'): ?>
                        <?php if ($isTeacherOfThisCourse): ?>
                            <a href="<?= e(base_url('teacher/courses.php')) ?>" class="btn btn-primary">مدیریت دوره</a>
                        <?php else: ?>
                            <div class="alert alert-info">اساتید امکان ثبت‌نام در دوره‌ها را ندارند.</div>
                        <?php endif; ?>

                    <?php elseif ($role === 'admin'): ?>
                        <div class="alert alert-info">شما مدیر سیستم هستید. ثبت‌نام از طریق پنل دانشجو امکان‌پذیر است.</div>

                    <?php else: ?>
                        <a href="<?= e(login_url()) ?>" class="btn btn-primary">ورود برای ثبت‌نام</a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="sidebar sidebar-sticky">
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
                    <?php
                    $similarStmt = db()->prepare("
                        SELECT id, title, slug, image FROM courses
                        WHERE status IN ('published','archived') AND category_id = ? AND id != ?
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