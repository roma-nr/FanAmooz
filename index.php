<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';

require __DIR__ . '/includes/layout/header.php';
require_once __DIR__ . '/includes/carousel.php';
$pageTitle = 'صفحه اصلی';

// دریافت داده‌ها
$courses = home_courses();
$announcements = home_announcements();
$usefulLinks = home_useful_links();



?>


<!-- ========== کروسل دوره‌ها + سایدبار لینک‌های مفید ========== -->
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
				<div id="courseCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
        <?php 
        $chunks = array_chunk($courses, 3);
        foreach ($chunks as $index => $chunk): 
        ?>
            <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                <div class="row g-3">
                    <?php foreach ($chunk as $course): ?>
                        <div class="col-md-4">
                            
    <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden">
                                <?php $img = $course['image'] ? upload_url($course['image']) : asset_url('img/courses/course-placeholder.jpg'); ?>
                                <img src="<?= e($img) ?>" class="card-img-top" style="height: 180px; object-fit: cover;" alt="">
                                <div class="card-body text-center">
                                    <a href="<?= e(base_url('course.php?slug=' . urlencode($course['slug']))) ?>"><h5 class="card-title"><?= e($course['title']) ?></h5></a>
                                    <p class="small text-muted"><?= e($course['teacher_name'] ?? '') ?></p>
                                    <p class="small text-muted">
                                        <i class="bi bi-clock"></i> <?= (int)($course['session_count'] ?? 0) ?> جلسه
                                    </p>
                                   
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
</div>
                    
                <?php endif; ?>
            </div>
			<!-- کروسل دوره‌ها با Bootstrap -->


            <div class="col-lg-4">
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
                                            <?php if (!empty($link['description'])): ?>
                                                <span class="small">- <?= e(mb_substr($link['description'], 0, 40)) ?></span>
                                            <?php endif; ?>
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
</section>
<!-- ========== ویژگی‌های سایت (۴ مورد) ========== -->
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

<!-- ========== آمار سایت ========== -->
<!--<section class="py-5 bg-light">
    <div class="container">
        <div class="row g-4">
            <?php
            $studentCount = db()->query("SELECT COUNT(*) FROM users WHERE role = 'student'")->fetchColumn();
            $teacherCount = db()->query("SELECT COUNT(*) FROM users WHERE role = 'teacher' AND teacher_status = 'approved'")->fetchColumn();
            $courseCount = db()->query("SELECT COUNT(*) FROM courses WHERE status = 'published'")->fetchColumn();
            $certificateCount = db()->query("SELECT COUNT(*) FROM certificate_requests WHERE status = 'approved'")->fetchColumn();
            ?>
            <div class="col-6 col-md-3">
                <div class="text-center p-3 rounded-4 bg-danger bg-opacity-10">
                    <i class="bi bi-people fs-1 text-danger"></i>
                    <div class="fs-3 fw-bold text-danger"><?= number_format($studentCount) ?></div>
                    <div class="small text-muted">دانشجو</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="text-center p-3 rounded-4 bg-success bg-opacity-10">
                    <i class="bi bi-person-badge fs-1 text-success"></i>
                    <div class="fs-3 fw-bold text-success"><?= number_format($teacherCount) ?></div>
                    <div class="small text-muted">استاد</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="text-center p-3 rounded-4 bg-warning bg-opacity-10">
                    <i class="bi bi-book fs-1 text-warning"></i>
                    <div class="fs-3 fw-bold text-warning"><?= number_format($courseCount) ?></div>
                    <div class="small text-muted">دوره فعال</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="text-center p-3 rounded-4 bg-info bg-opacity-10">
                    <i class="bi bi-award fs-1 text-info"></i>
                    <div class="fs-3 fw-bold text-info"><?= number_format($certificateCount) ?></div>
                    <div class="small text-muted">گواهی صادرشده</div>
                </div>
            </div>
        </div>
    </div>
</section>-->

<!-- ========== کروسل مقالات / اطلاعیه‌ها (۴ تایی در دسکتاپ) ========== -->
<section class="pb-100 bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 text-primary">آخرین مقالات و اطلاعیه‌ها</h2>
            <a href="<?= e(base_url('blog.php')) ?>" class="btn btn-outline-primary btn-sm">مشاهده همه</a>
        </div>

        <?php if (empty($announcements)): ?>
            <div class="alert alert-info text-center">هنوز مقاله‌ای ثبت نشده است.</div>
        <?php else: ?>
            <div id="announcementCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <?php 
                    $chunks = array_chunk($announcements, 4); // ۴ تایی در هر اسلاید
                    foreach ($chunks as $index => $chunk): 
                    ?>
                        <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                            <div class="row g-3">
                                <?php foreach ($chunk as $ann): ?>
                                    <div class="col-md-6 col-lg-3">
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
                                                <h6 class="card-title"><?= e(mb_substr($ann['title'], 0, 35)) ?></h6>
                                                <p class="card-text small text-muted">
                                                    <?= e(mb_substr(strip_tags($ann['summary'] ?? $ann['body'] ?? ''), 0, 60)) ?>...
                                                </p>
                                            </div>
                                            <div class="card-footer bg-white border-0 text-muted small d-flex justify-content-between align-items-center">
                                                <span><i class="bi bi-calendar3"></i> <?= e(format_date($ann['published_at'])) ?></span>
                                                <a href="<?= e(base_url('blog-details.php?id=' . $ann['id'])) ?>" class="btn btn-sm btn-outline-primary">ادامه مطلب</a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- دکمه‌های قبلی/بعدی -->
                <button class="carousel-control-prev" type="button" data-bs-target="#announcementCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">قبلی</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#announcementCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">بعدی</span>
                </button>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php require __DIR__ . '/includes/layout/footer.php'; ?>