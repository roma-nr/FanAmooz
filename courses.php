<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'دوره‌ها';
$categorySlug = $_GET['category'] ?? '';
$search = trim($_GET['search'] ?? '');

$sql = "SELECT c.*, cat.name AS category_name
        FROM courses c
        LEFT JOIN course_categories cat ON cat.id = c.category_id
        WHERE c.status = 'published'";
$params = [];

if (!empty($categorySlug)) {
    $sql .= " AND cat.slug = ?";
    $params[] = $categorySlug;
}
if (!empty($search)) {
    $sql .= " AND (c.title LIKE ? OR c.description LIKE ?)";
    $like = "%$search%";
    $params[] = $like;
    $params[] = $like;
}
$sql .= " ORDER BY c.title";
$stmt = db()->prepare($sql);
$stmt->execute($params);
$courses = $stmt->fetchAll();

$categories = db()->query("SELECT id, name, slug FROM course_categories WHERE is_active = 1 ORDER BY sort_order")->fetchAll();

require __DIR__ . '/includes/layout/header.php';
?>

<section class="blog-wrap ptb-100">
    <div class="container">
        <div class="row gx-5">
            <!-- ستون اصلی: دوره‌ها -->
            <div class="col-lg-8">
                <div class="section-title style1 text-center mb-50">
                    <span>دوره‌های آموزشی</span>
                    <h2>جدیدترین دوره‌های تخصصی کامپیوتر</h2>
                </div>
                <?php if (empty($courses)): ?>
                    <div class="alert alert-info">دوره‌ای با این مشخصات یافت نشد.</div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($courses as $c): ?>
                            <div class="col-md-6 col-xl-6">
                                <div class="service-card style1">
                                    <div class="service-img">
                                        <?php $img = $c['image'] ? upload_url($c['image']) : asset_url('img/course-placeholder.jpg'); ?>
                                        <img src="<?= e($img) ?>" alt="<?= e($c['title']) ?>">
                                    </div>
                                    <div class="service-info">
                                        <span class="service-icon"><i class="flaticon-cloud-computing"></i></span>
                                        <h3><a href="<?= e(base_url('course.php?slug=' . urlencode($c['slug']))) ?>"><?= e($c['title']) ?></a></h3>
                                        <p><?= e(mb_substr(strip_tags($c['description'] ?? ''), 0, 80)) ?>...</p>
                                        <!-- اصلاح: استفاده از $c به جای $course -->
                                        <p class="small text-muted mb-2">
                                            <i class="bi bi-clock"></i> <?= (int)($c['session_count'] ?? 0) ?> جلسه
                                            <?php if (!empty($c['session_days'])): ?>
                                                &bull; <?= e($c['session_days']) ?>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- سایدبار راست: جستجو + دسته‌بندی -->
            <div class="col-lg-4">
                <div class="sidebar sidebar-sticky">
                    <!-- جستجو -->
                    <div class="sidebar-widget search-box">
                        <div class="form-group">
                            <input type="search" name="search" id="searchInput" class="form-control" placeholder="جستجوی دوره..." value="<?= e($search) ?>">
                            <button type="button" id="searchBtn"><i class="flaticon-search"></i></button>
                        </div>
                        <?php if (!empty($search) || !empty($categorySlug)): ?>
                            <div class="mt-2">
                                <a href="<?= e(base_url('courses.php')) ?>" class="btn btn-sm btn-outline-secondary">حذف فیلتر</a>
                            </div>
                        <?php endif; ?>
                    </div>
                    <!-- دسته‌بندی‌ها -->
                    <div class="sidebar-widget categories">
                        <h4>دسته‌بندی دوره‌ها</h4>
                        <div class="category-box style1">
                            <ul class="list-style">
                                <?php foreach ($categories as $cat): ?>
                                    <li>
                                        <a href="<?= e(base_url('courses.php?category=' . $cat['slug'] . (!empty($search) ? '&search=' . urlencode($search) : ''))) ?>">
                                            <i class="ri-arrow-right-s-line"></i> <?= e($cat['name']) ?>
                                        </a>
                                    </li>
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
// جستجو با کلیک روی دکمه یا زدن Enter
document.getElementById('searchBtn')?.addEventListener('click', function() {
    var searchVal = document.getElementById('searchInput').value.trim();
    var url = new URL(window.location.href);
    if (searchVal !== '') {
        url.searchParams.set('search', searchVal);
    } else {
        url.searchParams.delete('search');
    }
    window.location.href = url.toString();
});

document.getElementById('searchInput')?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        document.getElementById('searchBtn').click();
    }
});
</script>

<?php require __DIR__ . '/includes/layout/footer.php'; ?>