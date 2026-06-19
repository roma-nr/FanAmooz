<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$stmt = db()->prepare('SELECT title, body FROM cms_pages WHERE slug = ?');
$stmt->execute(['about']);
$page = $stmt->fetch() ?: ['title' => 'درباره ما', 'body' => ''];
$pageTitle = $page['title'];

require __DIR__ . '/includes/layout/header.php';
?>

<div class="container py-5">
    <h1 class="section-title h3 text-primary"><?= e($page['title']) ?></h1>
    <div class="bg-white p-4 rounded shadow-sm"><?= $page['body'] ?></div>
</div>

<?php require __DIR__ . '/includes/layout/footer.php'; ?>
