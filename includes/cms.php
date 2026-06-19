<?php

declare(strict_types=1);

function cms_page(string $slug): ?array
{
    $stmt = db()->prepare('SELECT * FROM cms_pages WHERE slug = ? LIMIT 1');
    $stmt->execute([$slug]);

    $row = $stmt->fetch();

    return $row ?: null;
}

function cms_page_save(string $slug, string $title, string $body): void
{
    $stmt = db()->prepare(
        'INSERT INTO cms_pages (slug, title, body) VALUES (?, ?, ?)
         ON DUPLICATE KEY UPDATE title = VALUES(title), body = VALUES(body)'
    );
    $stmt->execute([$slug, $title, $body]);
}
