<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');

if (!auth_check()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$courseId = (int) ($_GET['course_id'] ?? 0);
$afterId = (int) ($_GET['after_id'] ?? 0);
$userId = (int) auth_id();
$role = auth_role() ?? '';

if ($courseId <= 0 || !phase6_tables_ready()) {
    echo json_encode(['messages' => [], 'last_id' => $afterId]);
    exit;
}

if (!user_can_access_course_chat($userId, $role, $courseId)) {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
    exit;
}

$messages = course_messages_list($courseId, 100, $afterId);
$lastId = $afterId;
foreach ($messages as $m) {
    $lastId = max($lastId, (int) $m['id']);
}

if ($afterId === 0) {
    mark_course_messages_read($userId, $courseId, $lastId);
}

$out = [];
foreach ($messages as $m) {
    $isMine = (int) $m['sender_id'] === $userId;
    $out[] = [
        'id' => (int) $m['id'],
        'sender_name' => $m['sender_name'],
        'sender_role' => $m['sender_role'],
        'body' => $m['body'],
        'file_url' => $m['file_path'] ? upload_url($m['file_path']) : null,
        'created_at' => format_datetime($m['created_at']),
        'is_mine' => $isMine,
    ];
}

echo json_encode([
    'messages' => $out,
    'last_id' => $lastId,
    'unread' => unread_messages_count($userId, $courseId),
], JSON_UNESCAPED_UNICODE);
