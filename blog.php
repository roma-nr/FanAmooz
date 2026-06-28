<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'اخبار و اطلاعیه‌ها';

// اطلاعیه‌ها
$stmt = db()->query("SELECT * FROM announcements WHERE is_active = 1 ORDER BY published_at DESC");
$announcements = $stmt->fetchAll();

// لینک‌های مفید برای سایدبار
$links = db()->query("SELECT * FROM useful_links WHERE is_active = 1 AND show_on_home = 1 ORDER BY sort_order LIMIT 5")->fetchAll();

require __DIR__ . '/includes/layout/header.php';
?>

<section class="blog-wrap ptb-100">
    <div class="container">
        <div class="row gx-5">
            <!-- ستون اصلی: اطلاعیه‌ها -->
            <div class="col-lg-8">
                <div class="section-title style1 text-center mb-50">
                    <span>اخبار و اطلاعیه‌ها</span>
                    <h2>جدیدترین مطالب</h2>
                </div>

                <?php if (empty($announcements)): ?>
                    <div class="alert alert-info">هیچ اطلاعیه‌ای یافت نشد.</div>
                <?php else: ?>
                    <div class="row" id="announcementsContainer">
                        <?php foreach ($announcements as $ann): ?>
                            <div class="col-md-6 col-xl-6">
                                <!-- کلاس blog-card اضافه شد -->
                                <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden blog-card">
                                    <?php if ($ann['image']): ?>
                                        <img src="<?= e(upload_url($ann['image'])) ?>" class="card-img-top" style="height: 150px; object-fit: cover;" alt="">
                                    <?php else: ?>
                                        <div class="bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center" style="height: 150px;">
                                            <i class="bi bi-newspaper fs-1 text-secondary"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div class="card-body">
                                        <span class="badge bg-primary mb-2">اطلاعیه</span>
                                        <a href="<?= e(base_url('blog-details.php?id=' . $ann['id'])) ?>" class="link style1">
                                            <h6 class="card-title"><?= e(mb_substr($ann['title'], 0, 35)) ?></h6>
                                        </a>
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
                <?php endif; ?>
            </div>

            <!-- سایدبار راست -->
            <div class="col-lg-4">
                <div class="sidebar sidebar-sticky">
                    <!-- جستجو -->
                    <div class="sidebar-widget search-box">
                        <div class="form-group">
                            <input type="search" placeholder="جستجو در اطلاعیه‌ها..." id="searchInput">
                            <button type="button" id="searchButton"><i class="flaticon-search"></i></button>
                        </div>
                    </div>
                    
                    <!-- لینک‌های مفید -->
                    <div class="sidebar-widget categories">
                        <h4>لینک‌های مفید</h4>
                        <div class="category-box style1">
                            <ul class="list-style">
                                <?php foreach ($links as $link): ?>
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
                                <?php if (empty($links)): ?>
                                    <li>لینکی ثبت نشده است.</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>

                    <!-- دسته‌بندی (بر اساس حوزه‌های تخصصی دوره‌ها) -->
                    <div class="sidebar-widget tags">
                        <h4>حوزه‌های تخصصی</h4>
                        <div class="tag-list">
                            <ul class="list-style">
                                <?php
                                $cats = db()->query("SELECT name, slug FROM course_categories WHERE is_active = 1 LIMIT 10")->fetchAll();
                                foreach ($cats as $cat):
                                ?>
                                    <li><a href="<?= e(base_url('courses.php?category=' . $cat['slug'])) ?>"><?= e($cat['name']) ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
(function() {
    const searchInput = document.getElementById('searchInput');
    const searchButton = document.getElementById('searchButton');
    
    function filterAnnouncements() {
        const filter = (searchInput.value || '').toLowerCase().trim();
        const cards = document.querySelectorAll('.blog-card');
        
        cards.forEach(card => {
            // عنوان داخل h6.card-title
            const titleEl = card.querySelector('.card-title');
            // متن کوتاه داخل p.card-text
            const textEl = card.querySelector('.card-text');
            
            const title = titleEl ? titleEl.innerText.toLowerCase() : '';
            const text = textEl ? textEl.innerText.toLowerCase() : '';
            
            // والد col-md-6
            const parentCol = card.closest('.col-md-6');
            if (parentCol) {
                if (filter === '' || title.includes(filter) || text.includes(filter)) {
                    parentCol.style.display = '';
                } else {
                    parentCol.style.display = 'none';
                }
            }
        });
    }
    
    if (searchInput) {
        searchInput.addEventListener('keyup', filterAnnouncements);
    }
    if (searchButton) {
        searchButton.addEventListener('click', filterAnnouncements);
    }
})();
</script>

<?php require __DIR__ . '/includes/layout/footer.php'; ?>