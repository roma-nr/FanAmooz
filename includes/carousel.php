<?php

function home_courses(): array
{
    try {
        // موقتاً شرط featured_on_home را برداشتیم تا همه دوره‌ها نمایش داده شوند
        $stmt = db()->prepare("
            SELECT c.*, cat.name AS category_name, u.full_name AS teacher_name
            FROM courses c
            LEFT JOIN course_categories cat ON cat.id = c.category_id
            LEFT JOIN users u ON u.id = c.teacher_id
            WHERE c.status = 'published'
            ORDER BY c.id DESC
            LIMIT 12
        ");
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;
    } catch (PDOException $e) {
        error_log('home_courses error: ' . $e->getMessage());
        return [];
    }
}

function home_announcements(): array
{
    try {
        // موقتاً شرط show_on_home را برداشتیم تا همه اطلاعیه‌ها نمایش داده شوند
        $stmt = db()->prepare("
            SELECT * FROM announcements
            WHERE is_active = 1
            ORDER BY published_at DESC
            LIMIT 12
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log('home_announcements error: ' . $e->getMessage());
        return [];
    }
}

function home_useful_links(): array
{
    try {
        $stmt = db()->prepare("
            SELECT * FROM useful_links
            WHERE is_active = 1
            ORDER BY sort_order ASC, id DESC
            LIMIT 12
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log('home_useful_links error: ' . $e->getMessage());
        return [];
    }
}


function render_carousel(string $id, array $slidesHtml, string $emptyMessage): void
{
    if ($slidesHtml === []) {
        echo '<p class="text-muted text-center py-4">' . e($emptyMessage) . '</p>';

        return;
    }
    ?>
    <div id="<?= e($id) ?>" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <?php foreach ($slidesHtml as $i => $_): ?>
                <button type="button" data-bs-target="#<?= e($id) ?>" data-bs-slide-to="<?= $i ?>"
                    class="<?= $i === 0 ? 'active' : '' ?>" aria-label="اسلاید <?= $i + 1 ?>"></button>
            <?php endforeach; ?>
        </div>
        <div class="carousel-inner">
            <?php foreach ($slidesHtml as $i => $html): ?>
                <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
                    <div class="container py-2">
                        <?= $html ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#<?= e($id) ?>" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#<?= e($id) ?>" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>
    <?php
}
