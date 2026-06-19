<?php
require_once __DIR__ . '/../includes/bootstrap.php';

if (php_sapi_name() !== 'cli') {
    die('فقط از خط فرمان اجرا شود.');
}

$now = date('Y-m-d H:i:s');
$twoDaysAgo = date('Y-m-d H:i:s', strtotime('-2 days'));

// جلساتی که کلاس آنلاین داشته‌اند، ۲ روز از زمان برگزاری گذشته و هنوز ویدئو آپلود نشده
$stmt = db()->prepare("
    SELECT cs.id AS session_id, cs.scheduled_at, cs.course_id, c.title AS course_title, c.teacher_id
    FROM course_sessions cs
    JOIN courses c ON c.id = cs.course_id
    WHERE cs.adobe_connect_url IS NOT NULL 
      AND cs.adobe_connect_url != ''
      AND cs.scheduled_at IS NOT NULL
      AND cs.scheduled_at <= :deadline
      AND (cs.video_path IS NULL OR cs.video_path = '')
      AND NOT EXISTS (
          SELECT 1 FROM reports r
          WHERE r.type = 'video_missing'
            AND r.status != 'resolved'
            AND r.description LIKE CONCAT('%\"course_session_id\":', cs.id, '%')
      )
");
$stmt->execute(['deadline' => $twoDaysAgo]);
$sessions = $stmt->fetchAll();

foreach ($sessions as $session) {
    // نام استاد
    $teacherStmt = db()->prepare("SELECT full_name FROM users WHERE id = ?");
    $teacherStmt->execute([$session['teacher_id']]);
    $teacherName = $teacherStmt->fetchColumn() ?: 'نامشخص';

    $description = json_encode([
        'course_title' => $session['course_title'],
        'teacher_name' => $teacherName,
        'course_session_id' => $session['session_id'],
        'scheduled_at' => $session['scheduled_at']
    ], JSON_UNESCAPED_UNICODE);

    db()->prepare("
        INSERT INTO reports (user_id, user_role, type, title, description)
        VALUES (?, 'system', 'video_missing', ?, ?)
    ")->execute([
        $session['teacher_id'],
        'ویدئوی جلسه آنلاین آپلود نشده است.',
        $description
    ]);
}

echo "بررسی کامل شد. " . count($sessions) . " گزارش جدید ایجاد شد.\n";