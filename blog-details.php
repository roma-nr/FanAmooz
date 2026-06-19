<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    http_response_code(404);
    exit('اطلاعیه یافت نشد.');
}

$stmt = db()->prepare("SELECT * FROM announcements WHERE id = ? AND is_active = 1");
$stmt->execute([$id]);
$ann = $stmt->fetch();
if (!$ann) {
    http_response_code(404);
    exit('اطلاعیه یافت نشد.');
}

$pageTitle = $ann['title'];

// دریافت ۵ اطلاعیه اخیر (غیر از این یکی)
$recentStmt = db()->prepare("SELECT id, title, published_at FROM announcements WHERE is_active = 1 AND id != ? ORDER BY published_at DESC LIMIT 5");
$recentStmt->execute([$id]);
$recent = $recentStmt->fetchAll();

require __DIR__ . '/includes/layout/header.php';
?>

<section class="blog-details-wrap ptb-100">
    <div class="container">
        <div class="row gx-5">
            <div class="col-lg-8">
                <article>
                    <div class="post-img">
                        <?php if ($ann['image']): ?>
                            <img src="<?= e(upload_url($ann['image'])) ?>" alt="<?= e($ann['title']) ?>">
                        <?php else: ?>
                            <img src="<?= e(asset_url('img/blog-placeholder.jpg')) ?>" alt="image">
                        <?php endif; ?>
                        <a href="<?= e(base_url('blog.php')) ?>" class="blog-category">اطلاعیه</a>
                    </div>
                    <ul class="post-metainfo list-style">
                        <li><i class="flaticon-user"></i> مدیر سیستم</li>
                        <li><i class="flaticon-calendar-1"></i> <?= e(format_date($ann['published_at'])) ?></li>
                    </ul>
                    <h3><?= e($ann['title']) ?></h3>
                    <div class="post-para">
                        <?= $ann['body'] ?? '' ?>
                    </div>
                </article>
                <div class="post-navigation mt-5">
                    <a href="<?= e(base_url('blog.php')) ?>" class="btn btn-primary">بازگشت به لیست اطلاعیه‌ها</a>
                </div>
            </div>

            <!-- سایدبار راست: اطلاعیه‌های اخیر -->
            <div class="col-lg-4">
                <div class="sidebar">
                    <div class="sidebar-widget popular-post">
                        <h4>اطلاعیه‌های اخیر</h4>
                        <div class="popular-post-widget">
                            <?php foreach ($recent as $r): ?>
                                <div class="pp-post-item">
                                    <div class="pp-post-info">
                                        <span><i class="flaticon-calendar-1"></i> <?= e(format_date($r['published_at'])) ?></span>
                                        <h6><a href="<?= e(base_url('blog-details.php?id=' . $r['id'])) ?>"><?= e($r['title']) ?></a></h6>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <?php if (empty($recent)): ?>
                                <p>اطلاعیه دیگری یافت نشد.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/layout/footer.php'; ?>