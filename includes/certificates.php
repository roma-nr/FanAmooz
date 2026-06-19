<?php

declare(strict_types=1);

function phase7_certificates_ready(): bool
{
    try {
        db()->query('SELECT 1 FROM certificate_requests LIMIT 1');

        return true;
    } catch (PDOException) {
        return false;
    }
}

function generate_certificate_number(int $requestId): string
{
    $year = date('Y');
    $num = str_pad((string) $requestId, 6, '0', STR_PAD_LEFT);

    return 'FA-' . $year . '-' . $num;
}

function request_certificate_for_enrollment(int $enrollmentId, int $userId): void
{
    if (!phase7_certificates_ready()) {
        throw new RuntimeException('سیستم گواهی فعال نیست. migrate_phase7.php را اجرا کنید.');
    }

    $stmt = db()->prepare(
        "SELECT e.*, c.min_pass_grade, c.title
         FROM enrollments e
         JOIN courses c ON c.id = e.course_id
         WHERE e.id = ? AND e.user_id = ? LIMIT 1"
    );
    $stmt->execute([$enrollmentId, $userId]);
    $row = $stmt->fetch();
    if (!$row) {
        throw new RuntimeException('ثبت‌نام یافت نشد.');
    }

    $course = ['min_pass_grade' => $row['min_pass_grade']];
    if (!student_can_request_certificate($row, $course)) {
        throw new RuntimeException('شرایط درخواست گواهی (نمره و حد نصاب) فراهم نیست.');
    }

    if (certificate_request_for_enrollment($enrollmentId)) {
        throw new RuntimeException('درخواست قبلاً ثبت شده است.');
    }

    db()->prepare('INSERT INTO certificate_requests (enrollment_id, status) VALUES (?, ?)')->execute([
        $enrollmentId,
        'pending',
    ]);
}

function approve_certificate_request(int $requestId, int $adminId, ?string $adminNote = null): void
{
    $stmt = db()->prepare('SELECT id, status FROM certificate_requests WHERE id = ? LIMIT 1');
    $stmt->execute([$requestId]);
    $req = $stmt->fetch();
    if (!$req) {
        throw new RuntimeException('درخواست یافت نشد.');
    }

    $certNumber = generate_certificate_number($requestId);
    db()->prepare(
        'UPDATE certificate_requests SET status=?, certificate_number=?, admin_note=?, reviewed_at=NOW(), reviewed_by=? WHERE id=?'
    )->execute(['approved', $certNumber, $adminNote, $adminId, $requestId]);
}

function reject_certificate_request(int $requestId, int $adminId, ?string $adminNote = null): void
{
    db()->prepare(
        'UPDATE certificate_requests SET status=?, admin_note=?, reviewed_at=NOW(), reviewed_by=? WHERE id=?'
    )->execute(['rejected', $adminNote, $adminId, $requestId]);
}

/**
 * @return array<int, array<string, mixed>>
 */
function student_certificates_list(int $userId): array
{
    if (!phase7_certificates_ready()) {
        return [];
    }

    $stmt = db()->prepare(
        "SELECT cr.*, c.title AS course_title, c.slug AS course_slug, e.final_grade, c.min_pass_grade
         FROM certificate_requests cr
         JOIN enrollments e ON e.id = cr.enrollment_id
         JOIN courses c ON c.id = e.course_id
         WHERE e.user_id = ?
         ORDER BY cr.requested_at DESC"
    );
    $stmt->execute([$userId]);

    return $stmt->fetchAll();
}

function certificate_request_detail(int $requestId): ?array
{
    $stmt = db()->prepare(
        "SELECT cr.*, u.full_name AS student_name, u.student_code, u.national_id,
                i.name AS institution_name, p.name AS province_name,
                c.title AS course_title, c.slug AS course_slug,
                e.final_grade, e.enrolled_at, c.min_pass_grade,
                t.full_name AS teacher_name, cat.name AS category_name
         FROM certificate_requests cr
         JOIN enrollments e ON e.id = cr.enrollment_id
         JOIN users u ON u.id = e.user_id
         JOIN courses c ON c.id = e.course_id
         LEFT JOIN users t ON t.id = c.teacher_id
         LEFT JOIN course_categories cat ON cat.id = c.category_id
         LEFT JOIN institutions i ON i.id = u.institution_id
         LEFT JOIN provinces p ON p.id = u.province_id
         WHERE cr.id = ? LIMIT 1"
    );
    $stmt->execute([$requestId]);
    $row = $stmt->fetch();

    return $row ?: null;
}

function student_can_view_certificate(int $requestId, int $userId): bool
{
    $detail = certificate_request_detail($requestId);
    if (!$detail || $detail['status'] !== 'approved') {
        return false;
    }

    $stmt = db()->prepare('SELECT user_id FROM enrollments WHERE id = ? LIMIT 1');
    $stmt->execute([(int) $detail['enrollment_id']]);

    return (int) $stmt->fetchColumn() === $userId;
}

function admin_certificates_list(?string $statusFilter = null, int $limit = 300): array
{
    if (!phase7_certificates_ready()) {
        return [];
    }

    $sql = "SELECT cr.*, u.full_name AS student_name, u.student_code,
                   c.title AS course_title, e.final_grade,
                   p.name AS province_name, i.name AS institution_name
            FROM certificate_requests cr
            JOIN enrollments e ON e.id = cr.enrollment_id
            JOIN users u ON u.id = e.user_id
            JOIN courses c ON c.id = e.course_id
            LEFT JOIN institutions i ON i.id = u.institution_id
            LEFT JOIN provinces p ON p.id = u.province_id
            WHERE 1=1";
    $params = [];
    if ($statusFilter !== null && $statusFilter !== '' && $statusFilter !== 'all') {
        $sql .= ' AND cr.status = ?';
        $params[] = $statusFilter;
    }
    $sql .= ' ORDER BY cr.requested_at DESC LIMIT ' . (int) $limit;
    $stmt = db()->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll();
}

function pending_certificates_count(): int
{
    if (!phase7_certificates_ready()) {
        return 0;
    }

    return (int) db()->query("SELECT COUNT(*) FROM certificate_requests WHERE status = 'pending'")->fetchColumn();
}
