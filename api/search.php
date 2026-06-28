<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/bootstrap.php';

$query = trim($_GET['q'] ?? '');
$pageCourses = max(1, (int)($_GET['page_courses'] ?? 1));
$pageAnnouncements = max(1, (int)($_GET['page_announcements'] ?? 1));
$limit = 6; // تعداد در هر صفحه

header('Content-Type: application/json; charset=utf-8');

function highlight($text, $query) {
    if (empty($query) || empty($text)) return e($text);
    $words = preg_split('/\s+/u', $query);
    foreach ($words as $word) {
        $word = preg_quote($word, '/');
        $text = preg_replace("/($word)/ui", '<mark>$1</mark>', $text);
    }
    return $text;
}

function truncate($text, $length = 100) {
    return mb_strlen($text) > $length ? mb_substr($text, 0, $length) . '...' : $text;
}

if (mb_strlen($query) < 2) {
    echo json_encode([
        'courses'    => ['results' => [], 'total' => 0, 'page' => 1, 'total_pages' => 0],
        'announcements' => ['results' => [], 'total' => 0, 'page' => 1, 'total_pages' => 0]
    ]);
    exit;
}

$like = '%' . $query . '%';

// ---------- دوره‌ها ----------
$countStmt = db()->prepare("SELECT COUNT(*) FROM courses WHERE status = 'published' AND (title LIKE ? OR description LIKE ?)");
$countStmt->execute([$like, $like]);
$totalCourses = (int)$countStmt->fetchColumn();

$offsetCourses = ($pageCourses - 1) * $limit;
$stmt = db()->prepare("SELECT title, slug, description FROM courses WHERE status = 'published' AND (title LIKE ? OR description LIKE ?) ORDER BY title LIMIT $limit OFFSET $offsetCourses");
$stmt->execute([$like, $like]);
$courseResults = [];
foreach ($stmt as $row) {
    $courseResults[] = [
        'type'                  => 'course',
        'title'                 => e($row['title']),
        'highlighted_title'     => highlight($row['title'], $query),
        'description'           => truncate(strip_tags($row['description'] ?? ''), 100),
        'highlighted_description' => highlight(truncate(strip_tags($row['description'] ?? ''), 100), $query),
        'url'                   => base_url('course.php?slug=' . urlencode($row['slug']))
    ];
}

// ---------- اطلاعیه‌ها ----------
$countStmt = db()->prepare("SELECT COUNT(*) FROM announcements WHERE is_active = 1 AND (title LIKE ? OR body LIKE ?)");
$countStmt->execute([$like, $like]);
$totalAnnouncements = (int)$countStmt->fetchColumn();

$offsetAnnouncements = ($pageAnnouncements - 1) * $limit;
$stmt = db()->prepare("SELECT title, id, body FROM announcements WHERE is_active = 1 AND (title LIKE ? OR body LIKE ?) ORDER BY published_at DESC LIMIT $limit OFFSET $offsetAnnouncements");
$stmt->execute([$like, $like]);
$announceResults = [];
foreach ($stmt as $row) {
    $announceResults[] = [
        'type'                  => 'announcement',
        'title'                 => e($row['title']),
        'highlighted_title'     => highlight($row['title'], $query),
        'description'           => truncate(strip_tags($row['body'] ?? ''), 100),
        'highlighted_description' => highlight(truncate(strip_tags($row['body'] ?? ''), 100), $query),
        'url'                   => base_url('blog-details.php?id=' . (int)$row['id'])
    ];
}

echo json_encode([
    'courses' => [
        'results'     => $courseResults,
        'total'       => $totalCourses,
        'page'        => $pageCourses,
        'total_pages' => ceil($totalCourses / $limit)
    ],
    'announcements' => [
        'results'     => $announceResults,
        'total'       => $totalAnnouncements,
        'page'        => $pageAnnouncements,
        'total_pages' => ceil($totalAnnouncements / $limit)
    ]
], JSON_UNESCAPED_UNICODE);