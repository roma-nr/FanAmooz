<?php

declare(strict_types=1);

function phase6_tables_ready(): bool
{
    try {
        db()->query('SELECT 1 FROM course_messages LIMIT 1');
        db()->query('SELECT 1 FROM live_sessions LIMIT 1');

        return true;
    } catch (PDOException) {
        return false;
    }
}

function live_session_status(array $session): string
{
    if (!empty($session['is_cancelled'])) {
        return 'cancelled';
    }
    $start = strtotime($session['scheduled_at']);
    if ($start === false) {
        return 'unknown';
    }
    $end = $start + ((int) ($session['duration_minutes'] ?? 90) * 60);
    $now = time();
    if ($now < $start) {
        return 'upcoming';
    }
    if ($now <= $end) {
        return 'live';
    }

    return 'ended';
}

function live_session_status_label(string $status): string
{
    return match ($status) {
        'upcoming' => 'به‌زودی',
        'live' => 'در حال برگزاری',
        'ended' => 'پایان‌یافته',
        'cancelled' => 'لغو شده',
        default => '—',
    };
}

function live_session_status_badge(string $status): string
{
    return match ($status) {
        'upcoming' => 'bg-primary',
        'live' => 'bg-success',
        'ended' => 'bg-secondary',
        'cancelled' => 'bg-danger',
        default => 'bg-light text-dark',
    };
}

function validate_url(string $url): bool
{
    return filter_var($url, FILTER_VALIDATE_URL) !== false
        && preg_match('#^https?://#i', $url);
}

function course_live_sessions(int $courseId, bool $includeCancelled = false): array
{
    if (!phase6_tables_ready()) {
        return [];
    }
    $sql = 'SELECT * FROM live_sessions WHERE course_id = ?';
    if (!$includeCancelled) {
        $sql .= ' AND is_cancelled = 0';
    }
    $sql .= ' ORDER BY scheduled_at ASC';
    $stmt = db()->prepare($sql);
    $stmt->execute([$courseId]);

    return $stmt->fetchAll();
}

function live_session_by_id(int $id, int $courseId): ?array
{
    $stmt = db()->prepare('SELECT * FROM live_sessions WHERE id = ? AND course_id = ? LIMIT 1');
    $stmt->execute([$id, $courseId]);

    $row = $stmt->fetch();

    return $row ?: null;
}

/**
 * @return array<int, array<string, mixed>>
 */
function student_live_sessions_all(int $userId, ?string $filter = null): array
{
    if (!phase6_tables_ready()) {
        return [];
    }

    $sql = "SELECT ls.*, c.title AS course_title, c.slug AS course_slug, u.full_name AS teacher_name
            FROM live_sessions ls
            INNER JOIN courses c ON c.id = ls.course_id
            LEFT JOIN users u ON u.id = c.teacher_id
            INNER JOIN enrollments e ON e.course_id = c.id AND e.user_id = ?
                AND e.status IN ('active','completed')
            WHERE ls.is_cancelled = 0";
    $params = [$userId];

    if ($filter === 'upcoming') {
        $sql .= ' AND ls.scheduled_at >= NOW()';
    } elseif ($filter === 'past') {
        $sql .= ' AND DATE_ADD(ls.scheduled_at, INTERVAL ls.duration_minutes MINUTE) < NOW()';
    } elseif ($filter === 'today') {
        $sql .= ' AND DATE(ls.scheduled_at) = CURDATE()';
    }

    $sql .= ' ORDER BY ls.scheduled_at ASC LIMIT 100';
    $stmt = db()->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll();
}

function student_next_live_session(int $userId): ?array
{
    $rows = student_live_sessions_all($userId, 'upcoming');
    $now = time();
    foreach ($rows as $row) {
        $status = live_session_status($row);
        if ($status === 'upcoming' || $status === 'live') {
            return $row;
        }
    }

    return null;
}

function course_messages_list(int $courseId, int $limit = 200, int $afterId = 0): array
{
    if (!phase6_tables_ready()) {
        return [];
    }
    if ($afterId > 0) {
        $stmt = db()->prepare(
            "SELECT m.*, u.full_name AS sender_name, u.role AS sender_role
             FROM course_messages m
             JOIN users u ON u.id = m.sender_id
             WHERE m.course_id = ? AND m.id > ?
             ORDER BY m.created_at ASC
             LIMIT ?"
        );
        $stmt->bindValue(1, $courseId, PDO::PARAM_INT);
        $stmt->bindValue(2, $afterId, PDO::PARAM_INT);
        $stmt->bindValue(3, $limit, PDO::PARAM_INT);
        $stmt->execute();
    } else {
        $stmt = db()->prepare(
            "SELECT m.*, u.full_name AS sender_name, u.role AS sender_role
             FROM course_messages m
             JOIN users u ON u.id = m.sender_id
             WHERE m.course_id = ?
             ORDER BY m.created_at ASC
             LIMIT ?"
        );
        $stmt->bindValue(1, $courseId, PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->execute();
    }

    return $stmt->fetchAll();
}

function send_course_message(int $courseId, int $senderId, string $body, ?string $filePath = null): int
{
    if (!phase6_tables_ready()) {
        throw new RuntimeException('سیستم پیام فعال نیست. migrate_phase6.php را اجرا کنید.');
    }
    $body = trim($body);
    if ($body === '' && ($filePath === null || $filePath === '')) {
        throw new InvalidArgumentException('پیام خالی است.');
    }

    $stmt = db()->prepare(
        'INSERT INTO course_messages (course_id, sender_id, body, file_path) VALUES (?,?,?,?)'
    );
    $stmt->execute([$courseId, $senderId, $body !== '' ? $body : '—', $filePath]);
    $id = (int) db()->lastInsertId();

    mark_course_messages_read($senderId, $courseId, $id);

    return $id;
}

function last_read_message_id(int $userId, int $courseId): int
{
    try {
        $stmt = db()->prepare(
            'SELECT last_read_message_id FROM course_message_reads WHERE user_id = ? AND course_id = ? LIMIT 1'
        );
        $stmt->execute([$userId, $courseId]);
        $val = $stmt->fetchColumn();

        return $val !== false ? (int) $val : 0;
    } catch (PDOException) {
        return 0;
    }
}

function mark_course_messages_read(int $userId, int $courseId, ?int $upToMessageId = null): void
{
    try {
        if ($upToMessageId === null) {
            $max = db()->prepare('SELECT COALESCE(MAX(id), 0) FROM course_messages WHERE course_id = ?');
            $max->execute([$courseId]);
            $upToMessageId = (int) $max->fetchColumn();
        }
        db()->prepare(
            'INSERT INTO course_message_reads (user_id, course_id, last_read_message_id)
             VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE last_read_message_id = GREATEST(last_read_message_id, VALUES(last_read_message_id))'
        )->execute([$userId, $courseId, $upToMessageId]);
    } catch (PDOException) {
        // جدول reads ممکن است نباشد
    }
}

function unread_messages_count(int $userId, int $courseId): int
{
    try {
        $lastRead = last_read_message_id($userId, $courseId);
        $stmt = db()->prepare(
            'SELECT COUNT(*) FROM course_messages
             WHERE course_id = ? AND id > ? AND sender_id != ?'
        );
        $stmt->execute([$courseId, $lastRead, $userId]);

        return (int) $stmt->fetchColumn();
    } catch (PDOException) {
        return 0;
    }
}

function student_total_unread_messages(int $userId): int
{
    if (!phase6_tables_ready()) {
        return 0;
    }
    try {
        $stmt = db()->prepare(
            "SELECT COUNT(*) FROM course_messages m
             INNER JOIN enrollments e ON e.course_id = m.course_id AND e.user_id = ?
                 AND e.status IN ('active','completed')
             LEFT JOIN course_message_reads r ON r.user_id = ? AND r.course_id = m.course_id
             WHERE m.sender_id != ?
               AND m.id > COALESCE(r.last_read_message_id, 0)"
        );
        $stmt->execute([$userId, $userId, $userId]);

        return (int) $stmt->fetchColumn();
    } catch (PDOException) {
        return 0;
    }
}

function teacher_total_unread_messages(int $teacherId): int
{
    if (!phase6_tables_ready()) {
        return 0;
    }
    try {
        $stmt = db()->prepare(
            "SELECT COUNT(*) FROM course_messages m
             INNER JOIN courses c ON c.id = m.course_id AND c.teacher_id = ?
             LEFT JOIN course_message_reads r ON r.user_id = ? AND r.course_id = m.course_id
             WHERE m.sender_id != ?
               AND m.id > COALESCE(r.last_read_message_id, 0)"
        );
        $stmt->execute([$teacherId, $teacherId, $teacherId]);

        return (int) $stmt->fetchColumn();
    } catch (PDOException) {
        return 0;
    }
}

/**
 * @return array<int, array<string, mixed>>
 */
function user_chat_courses(int $userId, string $role): array
{
    if (!phase6_tables_ready()) {
        return [];
    }

    if ($role === 'student') {
        $sql = "SELECT c.id, c.title, c.slug, u.full_name AS teacher_name,
                       (SELECT body FROM course_messages WHERE course_id = c.id ORDER BY id DESC LIMIT 1) AS last_body,
                       (SELECT created_at FROM course_messages WHERE course_id = c.id ORDER BY id DESC LIMIT 1) AS last_at,
                       (SELECT COUNT(*) FROM course_messages m
                        LEFT JOIN course_message_reads r ON r.user_id = ? AND r.course_id = c.id
                        WHERE m.course_id = c.id AND m.sender_id != ?
                          AND m.id > COALESCE(r.last_read_message_id, 0)) AS unread_count
                FROM courses c
                INNER JOIN enrollments e ON e.course_id = c.id AND e.user_id = ?
                    AND e.status IN ('active','completed')
                LEFT JOIN users u ON u.id = c.teacher_id
                ORDER BY last_at IS NULL, last_at DESC, c.title";
        $stmt = db()->prepare($sql);
        $stmt->execute([$userId, $userId, $userId]);
    } else {
        $sql = "SELECT c.id, c.title, c.slug,
                       (SELECT body FROM course_messages WHERE course_id = c.id ORDER BY id DESC LIMIT 1) AS last_body,
                       (SELECT created_at FROM course_messages WHERE course_id = c.id ORDER BY id DESC LIMIT 1) AS last_at,
                       (SELECT COUNT(*) FROM course_messages m
                        LEFT JOIN course_message_reads r ON r.user_id = ? AND r.course_id = c.id
                        WHERE m.course_id = c.id AND m.sender_id != ?
                          AND m.id > COALESCE(r.last_read_message_id, 0)) AS unread_count
                FROM courses c
                WHERE c.teacher_id = ?
                ORDER BY last_at IS NULL, last_at DESC, c.title";
        $stmt = db()->prepare($sql);
        $stmt->execute([$userId, $userId, $userId]);
    }

    return $stmt->fetchAll();
}

function user_can_access_course_chat(int $userId, string $role, int $courseId): bool
{
    if ($role === 'teacher') {
        $stmt = db()->prepare('SELECT id FROM courses WHERE id = ? AND teacher_id = ? LIMIT 1');
        $stmt->execute([$courseId, $userId]);

        return (bool) $stmt->fetch();
    }

    $stmt = db()->prepare(
        "SELECT id FROM enrollments WHERE course_id = ? AND user_id = ?
         AND status IN ('active','completed') LIMIT 1"
    );
    $stmt->execute([$courseId, $userId]);

    return (bool) $stmt->fetch();
}
