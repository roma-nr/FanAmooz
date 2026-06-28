<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';

require __DIR__ . '/includes/layout/header.php';
require_once __DIR__ . '/includes/carousel.php';
$pageTitle = 'صفحه اصلی';

$courses = home_courses();
$announcements = home_announcements();
$usefulLinks = home_useful_links();
?>

<style>
/* جلوگیری از انتخاب متن هنگام swipe */
.owl-carousel, .owl-carousel * {
    user-select: none;
    -webkit-user-select: none;
}
/* سایدبار چسبان */
.sidebar-sticky {
    position: sticky;
    top: 90px;
}

</style>

<section class="pt-100 pb-75">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="h3 text-primary">دوره‌های آموزشی</h2>
                    <a href="<?= e(base_url('courses.php')) ?>" class="btn style1 btn-sm" style="background-color: #ffe54c; color: #4e7eab">همه دوره‌ها</a>
                </div>

                <?php if (empty($courses)): ?>
                    <div class="alert alert-info">هنوز دوره‌ای برای نمایش وجود ندارد.</div>
                <?php else: ?>
                    <div class="owl-carousel owl-theme" id="courseOwl">
                        <?php foreach ($courses as $course): ?>
                            <div class="item px-2">
                                <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden">
                                    <?php $img = $course['image'] ? upload_url($course['image']) : asset_url('img/courses/course-placeholder.jpg'); ?>
                                    <img src="<?= e($img) ?>" class="card-img-top" style="height: 180px; object-fit: cover;" alt="">
                                    <div class="card-body text-center">
                                        <a href="<?= e(base_url('course.php?slug=' . urlencode($course['slug']))) ?>"><h5 class="card-title"><?= e($course['title']) ?></h5></a>
                                        <p class="small text-muted"><?= e($course['teacher_name'] ?? '') ?></p>
                                        <p class="small text-muted"><i class="bi bi-clock"></i> <?= (int)($course['session_count'] ?? 0) ?> جلسه</p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="col-lg-4">
                <div class="sidebar sidebar-sticky">
                    <div class="sidebar-widget categories" style="background: #fff; padding: 25px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.08);">
                        <h4>لینک‌های مفید</h4>
                        <div class="category-box style1">
                            <ul class="list-style">
                                <?php if (empty($usefulLinks)): ?>
                                    <li>لینکی ثبت نشده است.</li>
                                <?php else: ?>
                                    <?php foreach ($usefulLinks as $link): ?>
                                        <li>
                                            <a href="<?= e($link['url']) ?>" target="_blank" rel="noopener">
                                                <i class="ri-arrow-right-s-line"></i>
                                                <?= e($link['title']) ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-white">
    <div class="container">
        <div class="row g-4">
            <div class="col-6 col-md-3">
                <div class="text-center p-4 rounded-4 bg-info bg-opacity-10 h-100 d-flex flex-column align-items-center justify-content-center">
                    <i class="bi bi-laptop fs-1 text-primary mb-2"></i>
                    <h5 class="fw-bold text-primary">آموزش تخصصی</h5>
                    <p class="small text-muted mb-0">دوره‌های کامپیوتر و مهارت‌های دیجیتال</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="text-center p-4 rounded-4 bg-info bg-opacity-10 h-100 d-flex flex-column align-items-center justify-content-center">
                    <i class="bi bi-person-badge fs-1 text-primary mb-2"></i>
                    <h5 class="fw-bold text-primary">اساتید مجرب</h5>
                    <p class="small text-muted mb-0">تدریس توسط اساتید حرفه‌ای و متخصص</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="text-center p-4 rounded-4 bg-info bg-opacity-10 h-100 d-flex flex-column align-items-center justify-content-center">
                    <i class="bi bi-award fs-1 text-primary mb-2"></i>
                    <h5 class="fw-bold text-primary">گواهی معتبر</h5>
                    <p class="small text-muted mb-0">گواهی پایان دوره با شماره یکتا</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="text-center p-4 rounded-4 bg-info bg-opacity-10 h-100 d-flex flex-column align-items-center justify-content-center">
                    <i class="bi bi-headset fs-1 text-primary mb-2"></i>
                    <h5 class="fw-bold text-primary">پشتیبانی ۲۴/۷</h5>
                    <p class="small text-muted mb-0">پاسخگویی به سوالات و مشکلات شما</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="pb-100 bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 text-primary">آخرین مقالات و اطلاعیه‌ها</h2>
            <a href="<?= e(base_url('blog.php')) ?>" class="btn btn-outline-primary btn-sm">مشاهده همه</a>
        </div>

        <?php if (empty($announcements)): ?>
            <div class="alert alert-info text-center">هنوز مقاله‌ای ثبت نشده است.</div>
        <?php else: ?>
            <div class="owl-carousel owl-theme" id="announcementOwl">
                <?php foreach ($announcements as $ann): ?>
                    <div class="item px-2">
                        <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden">
                            <?php if ($ann['image']): ?>
                                <img src="<?= e(upload_url($ann['image'])) ?>" class="card-img-top" style="height: 150px; object-fit: cover;" alt="">
                            <?php else: ?>
                                <div class="bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center" style="height: 150px;">
                                    <i class="bi bi-newspaper fs-1 text-secondary"></i>
                                </div>
                            <?php endif; ?>
                            <div class="card-body">
                                <span class="badge bg-primary mb-2">اطلاعیه</span>
                                <a href="<?= e(base_url('blog-details.php?id=' . $ann['id'])) ?>" class="link style1"><h6 class="card-title"><?= e(mb_substr($ann['title'], 0, 35)) ?></h6></a>
                                <p class="card-text small text-muted"><?= e(mb_substr(strip_tags($ann['summary'] ?? $ann['body'] ?? ''), 0, 60)) ?>...</p>
                            </div>
                            <div class="card-footer bg-white border-0 text-muted small d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-calendar3"></i> <?= e(format_date($ann['published_at'])) ?></span>
                                <a href="<?= e(base_url('blog-details.php?id=' . $ann['id'])) ?>" class="btn btn-sm btn-outline-primary">ادامه مطلب</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    

    // ========== کروسل دوره‌ها (Owl) ==========
    if ($("#courseOwl").length) {
        $("#courseOwl").owlCarousel({
            rtl: true,
            loop: true,
            margin: 10,
            nav: true,
            navText: ['<i class="bi bi-chevron-right"></i>', '<i class="bi bi-chevron-left"></i>'],
            dots: false,
            autoplay: true,
            autoplayTimeout: 5000,
            autoplayHoverPause: true,
            slideBy: 1,
            responsive: {
                0: { items: 1 },
                768: { items: 2 },
                992: { items: 3 }
            }
        });
    }

    // ========== کروسل اطلاعیه‌ها (Owl) ==========
    if ($("#announcementOwl").length) {
        $("#announcementOwl").owlCarousel({
            rtl: true,
            loop: true,
            margin: 10,
            nav: true,
            navText: ['<i class="bi bi-chevron-right"></i>', '<i class="bi bi-chevron-left"></i>'],
            dots: false,
            autoplay: true,
            autoplayTimeout: 6000,
            slideBy: 1,
            responsive: {
                0: { items: 1 },
                576: { items: 2 },
                992: { items: 4 }
            }
        });
    }
});
</script>

<?php require __DIR__ . '/includes/layout/footer.php'; ?>