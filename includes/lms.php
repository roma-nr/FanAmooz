<?php

declare(strict_types=1);

function lms_tables_ready(): bool
{
    try {
        db()->query('SELECT 1 FROM assignments LIMIT 1');

        return true;
    } catch (PDOException) {
        return false;
    }
}

function assignment_is_overdue(array $assignment): bool
{
    if (empty($assignment['due_at'])) {
        return false;
    }

    return strtotime($assignment['due_at']) < time();
}

function student_can_submit_assignment(array $assignment, ?array $submission): bool
{
    if ($submission !== null && $submission['score'] !== null) {
        return false;
    }
    if (!assignment_is_overdue($assignment)) {
        return true;
    }

    return $submission !== null;
}

function submission_status_label(?array $submission, array $assignment): string
{
    if ($submission === null) {
        return assignment_is_overdue($assignment) ? 'مهلت گذشته' : 'ارسال نشده';
    }
    if ($submission['score'] !== null) {
        return 'نمره‌دهی شده';
    }

    return 'در انتظار تصحیح';
}

function submission_status_badge_class(?array $submission, array $assignment): string
{
    if ($submission === null) {
        return assignment_is_overdue($assignment) ? 'bg-danger' : 'bg-warning text-dark';
    }
    if ($submission['score'] !== null) {
        return 'bg-success';
    }

    return 'bg-info text-dark';
}

function student_pending_assignments_count(int $userId): int
{
    if (!lms_tables_ready()) {
        return 0;
    }
    try {
        $stmt = db()->prepare(
            "SELECT COUNT(*) FROM assignments a
             INNER JOIN enrollments e ON e.course_id = a.course_id AND e.user_id = ? AND e.status IN ('active','completed')
             LEFT JOIN assignment_submissions s ON s.assignment_id = a.id AND s.user_id = ?
             WHERE s.id IS NULL AND (a.due_at IS NULL OR a.due_at >= NOW())"
        );
        $stmt->execute([$userId, $userId]);

        return (int) $stmt->fetchColumn();
    } catch (PDOException) {
        return 0;
    }
}

/**
 * @return array<int, array<string, mixed>>
 */
function student_all_assignments(int $userId, ?string $filter = null): array
{
    if (!lms_tables_ready()) {
        return [];
    }

    $sql = "SELECT a.*, c.title AS course_title, c.slug AS course_slug,
                   s.id AS submission_id, s.score, s.feedback, s.submitted_at, s.body_text, s.file_path
            FROM assignments a
            INNER JOIN courses c ON c.id = a.course_id
            INNER JOIN enrollments e ON e.course_id = a.course_id AND e.user_id = ? AND e.status IN ('active','completed')
            LEFT JOIN assignment_submissions s ON s.assignment_id = a.id AND s.user_id = ?
            WHERE 1=1";
    $params = [$userId, $userId];

    if ($filter === 'pending') {
        $sql .= ' AND s.id IS NULL AND (a.due_at IS NULL OR a.due_at >= NOW())';
    } elseif ($filter === 'submitted') {
        $sql .= ' AND s.id IS NOT NULL AND s.score IS NULL';
    } elseif ($filter === 'graded') {
        $sql .= ' AND s.score IS NOT NULL';
    } elseif ($filter === 'overdue') {
        $sql .= ' AND s.id IS NULL AND a.due_at IS NOT NULL AND a.due_at < NOW()';
    }

    $sql .= ' ORDER BY a.due_at IS NULL, a.due_at ASC, a.id DESC';

    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();

    foreach ($rows as &$row) {
        $sub = $row['submission_id'] ? [
            'id' => $row['submission_id'],
            'score' => $row['score'],
            'feedback' => $row['feedback'],
            'submitted_at' => $row['submitted_at'],
            'body_text' => $row['body_text'],
            'file_path' => $row['file_path'],
        ] : null;
        $row['_submission'] = $sub;
    }

    return $rows;
}

function course_assignments(int $courseId): array
{
    if (!lms_tables_ready()) {
        return [];
    }
    $stmt = db()->prepare('SELECT * FROM assignments WHERE course_id = ? ORDER BY due_at IS NULL, due_at, id DESC');
    $stmt->execute([$courseId]);

    return $stmt->fetchAll();
}

function assignment_by_id(int $id, int $courseId): ?array
{
    $stmt = db()->prepare('SELECT * FROM assignments WHERE id = ? AND course_id = ? LIMIT 1');
    $stmt->execute([$id, $courseId]);

    $row = $stmt->fetch();

    return $row ?: null;
}

function assignment_submission(int $assignmentId, int $userId): ?array
{
    if (!lms_tables_ready()) {
        return null;
    }
    $stmt = db()->prepare('SELECT * FROM assignment_submissions WHERE assignment_id = ? AND user_id = ? LIMIT 1');
    $stmt->execute([$assignmentId, $userId]);

    $row = $stmt->fetch();

    return $row ?: null;
}

function assignment_submissions_for_assignment(int $assignmentId): array
{
    $stmt = db()->prepare(
        'SELECT s.*, u.full_name, u.student_code
         FROM assignment_submissions s
         JOIN users u ON u.id = s.user_id
         WHERE s.assignment_id = ?
         ORDER BY s.submitted_at DESC'
    );
    $stmt->execute([$assignmentId]);

    return $stmt->fetchAll();
}

function enrolled_students_for_course(int $courseId): array
{
    $stmt = db()->prepare(
        "SELECT u.id, u.full_name, u.student_code, e.id AS enrollment_id, e.final_grade, e.status AS enrollment_status
         FROM enrollments e
         JOIN users u ON u.id = e.user_id
         WHERE e.course_id = ? AND e.status IN ('active','completed')
         ORDER BY u.full_name"
    );
    $stmt->execute([$courseId]);

    return $stmt->fetchAll();
}

function student_assignment_average(int $userId, int $courseId): ?float
{
    if (!lms_tables_ready()) {
        return null;
    }
    $stmt = db()->prepare(
        "SELECT AVG((s.score / NULLIF(a.max_score, 0)) * 100) AS avg_pct
         FROM assignment_submissions s
         INNER JOIN assignments a ON a.id = s.assignment_id AND a.course_id = ?
         WHERE s.user_id = ? AND s.score IS NOT NULL"
    );
    $stmt->execute([$courseId, $userId]);
    $val = $stmt->fetchColumn();

    return $val !== false && $val !== null ? round((float) $val, 2) : null;
}

function student_attendance_percent(int $userId, int $courseId): ?float
{
    if (!lms_tables_ready()) {
        return null;
    }
    $stmt = db()->prepare(
        "SELECT
            COUNT(*) AS total,
            SUM(CASE WHEN sa.present = 1 THEN 1 ELSE 0 END) AS present_count
         FROM course_sessions cs
         LEFT JOIN session_attendance sa ON sa.course_session_id = cs.id AND sa.user_id = ?
         WHERE cs.course_id = ?"
    );
    $stmt->execute([$userId, $courseId]);
    $row = $stmt->fetch();
    if (!$row || (int) $row['total'] === 0) {
        return null;
    }

    return round(((int) $row['present_count'] / (int) $row['total']) * 100, 1);
}

/**
 * @return array<int, array<string, mixed>>
 */
function student_attendance_by_course(int $userId, int $courseId): array
{
    if (!lms_tables_ready()) {
        return [];
    }
    $stmt = db()->prepare(
        "SELECT cs.id, cs.session_number, cs.title,
                sa.present, sa.recorded_at
         FROM course_sessions cs
         LEFT JOIN session_attendance sa ON sa.course_session_id = cs.id AND sa.user_id = ?
         WHERE cs.course_id = ?
         ORDER BY cs.sort_order, cs.session_number, cs.id"
    );
    $stmt->execute([$userId, $courseId]);

    return $stmt->fetchAll();
}

function certificate_request_for_enrollment(int $enrollmentId): ?array
{
    try {
        $stmt = db()->prepare('SELECT * FROM certificate_requests WHERE enrollment_id = ? LIMIT 1');
        $stmt->execute([$enrollmentId]);

        $row = $stmt->fetch();

        return $row ?: null;
    } catch (PDOException) {
        return null;
    }
}

function student_can_request_certificate(array $enrollment, array $course): bool
{
    if ($enrollment['status'] !== 'active' && $enrollment['status'] !== 'completed') {
        return false;
    }

    $grade = $enrollment['final_grade'];
    if ($grade === null) {
        return false;
    }

    return (float) $grade >= (float) $course['min_pass_grade'];
}

function certificate_status_label(string $status): string
{
    return match ($status) {
        'pending' => 'در انتظار بررسی',
        'approved' => 'تأیید شده',
        'rejected' => 'رد شده',
        default => $status,
    };
}

function format_schedule_course(array $course): string
{
    $parts = [];
    if (!empty($course['session_count'])) {
        $parts[] = (int) $course['session_count'] . ' جلسه';
    }
    if (!empty($course['start_date'])) {
        $parts[] = 'شروع: ' . format_date($course['start_date']);
    }
    if (!empty($course['end_date'])) {
        $parts[] = 'پایان: ' . format_date($course['end_date']);
    }
    if (!empty($course['schedule_notes'])) {
        $parts[] = $course['schedule_notes'];
    }

    return $parts !== [] ? implode(' | ', $parts) : '—';
}
