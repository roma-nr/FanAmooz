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
                    <div class="row">
                        <?php foreach ($announcements as $ann): ?>
                            <div class="col-md-6 col-xl-6">
                                <div class="blog-card style1">
                                    <div class="blog-img">
                                        <?php if ($ann['image']): ?>
                                            <img src="<?= e(upload_url($ann['image'])) ?>" alt="<?= e($ann['title']) ?>">
                                        <?php else: ?>
                                            <img src="<?= e(asset_url('img/blog-placeholder.jpg')) ?>" alt="image">
                                        <?php endif; ?>
                                    </div>
                                    <div class="blog-info">
                                        <a href="<?= e(base_url('blog-details.php?id=' . $ann['id'])) ?>" class="blog-category">اطلاعیه</a>
                                        <ul class="blog-metainfo list-style">
                                            <li><i class="flaticon-calendar-2"></i> <?= e(format_date($ann['published_at'])) ?></li>
                                        </ul>
                                        <h3><a href="<?= e(base_url('blog-details.php?id=' . $ann['id'])) ?>"><?= e($ann['title']) ?></a></h3>
                                        <p><?= e(mb_substr(strip_tags($ann['summary'] ?? $ann['body'] ?? ''), 0, 80)) ?>...</p>
                                        <a href="<?= e(base_url('blog-details.php?id=' . $ann['id'])) ?>" class="link style1">ادامه مطلب <i class="flaticon-right-arrow"></i></a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- سایدبار راست -->
            <div class="col-lg-4">
                <div class="sidebar">
                    <!-- جستجو (اختیاری) -->
                    <div class="sidebar-widget search-box">
                        <div class="form-group">
                            <input type="search" placeholder="جستجو در اطلاعیه‌ها..." id="searchInput">
                            <button type="submit"><i class="flaticon-search"></i></button>
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
// جستجوی ساده با جاوااسکریپت (فیلتر کردن اطلاعیه‌ها بدون رفرش)
document.getElementById('searchInput')?.addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let cards = document.querySelectorAll('.blog-card');
    cards.forEach(card => {
        let title = card.querySelector('h3 a')?.innerText.toLowerCase() || '';
        let text = card.querySelector('p')?.innerText.toLowerCase() || '';
        if (title.includes(filter) || text.includes(filter)) {
            card.closest('.col-md-6').style.display = '';
        } else {
            card.closest('.col-md-6').style.display = 'none';
        }
    });
});
</script>

<?php require __DIR__ . '/includes/layout/footer.php'; ?>