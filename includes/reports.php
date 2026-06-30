<?php
declare(strict_types=1);

function report_top_interests(int $limit = 15): array
{
    try {
        $stmt = db()->prepare(
            "SELECT i.name, i.slug, COUNT(si.user_id) AS student_count
             FROM interests i
             LEFT JOIN student_interests si ON si.interest_id = i.id
             WHERE i.is_active = 1
             GROUP BY i.id, i.name, i.slug
             ORDER BY student_count DESC, i.sort_order
             LIMIT ?"
        );
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException) {
        return [];
    }
}

function report_enrollments_by_province(): array
{
    $stmt = db()->query(
        "SELECT p.name AS province_name, COUNT(e.id) AS enrollment_count,
                COUNT(DISTINCT e.user_id) AS student_count
         FROM enrollments e
         JOIN users u ON u.id = e.user_id
         LEFT JOIN provinces p ON p.id = u.province_id
         WHERE e.status IN ('active','completed','pending')
         GROUP BY p.id, p.name
         ORDER BY enrollment_count DESC"
    );
    return $stmt->fetchAll();
}

function report_enrollments_by_institution(): array
{
    $stmt = db()->query(
        "SELECT p.name AS province_name, i.name AS institution_name,
                COUNT(e.id) AS enrollment_count
         FROM enrollments e
         JOIN users u ON u.id = e.user_id
         JOIN institutions i ON i.id = u.institution_id
         JOIN provinces p ON p.id = i.province_id
         WHERE e.status IN ('active','completed','pending')
         GROUP BY i.id, i.name, p.name
         ORDER BY enrollment_count DESC
         LIMIT 50"
    );
    return $stmt->fetchAll();
}

function report_enrollments_by_category(): array
{
    $stmt = db()->query(
        "SELECT cat.name AS category_name, COUNT(e.id) AS enrollment_count
         FROM enrollments e
         JOIN courses c ON c.id = e.course_id
         LEFT JOIN course_categories cat ON cat.id = c.category_id
         WHERE e.status IN ('active','completed')
         GROUP BY cat.id, cat.name
         ORDER BY enrollment_count DESC"
    );
    return $stmt->fetchAll();
}

function report_enrollments_by_course(): array
{
    $stmt = db()->query(
        "SELECT c.title, c.slug, c.price, c.is_paid,
                cat.name AS category_name,
                COUNT(e.id) AS enrollment_count,
                SUM(CASE WHEN e.status = 'completed' THEN 1 ELSE 0 END) AS completed_count
         FROM courses c
         LEFT JOIN enrollments e ON e.course_id = c.id AND e.status IN ('active','completed','pending')
         LEFT JOIN course_categories cat ON cat.id = c.category_id
         GROUP BY c.id, c.title, c.slug, c.price, c.is_paid, cat.name
         ORDER BY enrollment_count DESC
         LIMIT 50"
    );
    return $stmt->fetchAll();
}

function report_certificate_summary(): array
{
    if (!function_exists('phase7_certificates_ready') || !phase7_certificates_ready()) {
        return ['pending' => 0, 'approved' => 0, 'rejected' => 0, 'total' => 0];
    }
    $rows = db()->query("SELECT status, COUNT(*) AS cnt FROM certificate_requests GROUP BY status")->fetchAll();
    $out = ['pending' => 0, 'approved' => 0, 'rejected' => 0, 'total' => 0];
    foreach ($rows as $r) {
        $out[$r['status']] = (int) $r['cnt'];
        $out['total'] += (int) $r['cnt'];
    }
    return $out;
}

function report_teacher_applications_summary(): array
{
    $rows = db()->query("SELECT teacher_status, COUNT(*) AS cnt FROM users WHERE role = 'teacher' GROUP BY teacher_status")->fetchAll();
    $out = ['pending' => 0, 'approved' => 0, 'rejected' => 0, 'none' => 0, 'total' => 0];
    foreach ($rows as $r) {
        $key = $r['teacher_status'];
        $out[$key] = (int) $r['cnt'];
        $out['total'] += (int) $r['cnt'];
    }
    return $out;
}

function report_payments_summary(): array
{
    $stmt = db()->query("
        SELECT COUNT(*) AS paid_count, COALESCE(SUM(amount), 0) AS paid_total
        FROM payments
        WHERE status = 'paid'
    ");
    $paid = $stmt->fetch();

    $pending = (int) db()->query("SELECT COUNT(*) FROM payments WHERE status = 'pending'")->fetchColumn();
    $failed  = (int) db()->query("SELECT COUNT(*) FROM payments WHERE status = 'failed'")->fetchColumn();

    return [
        'paid_count'    => (int)($paid['paid_count'] ?? 0),
        'paid_total'    => (float)($paid['paid_total'] ?? 0),
        'pending_count' => $pending,
        'failed_count'  => $failed,
    ];
}

function report_recent_payments(int $limit = 20): array
{
    return db()->prepare("
        SELECT p.id, p.amount, p.status, p.ref_id, p.paid_at, p.created_at,
               u.full_name, c.title AS course_title
        FROM payments p
        JOIN users u ON u.id = p.user_id
        JOIN enrollments e ON e.id = p.enrollment_id
        JOIN courses c ON c.id = e.course_id
        ORDER BY p.created_at DESC
        LIMIT ?
    ")->execute([$limit])->fetchAll();
}

function report_dashboard_extended(): array
{
    return [
        'students'           => (int) db()->query("SELECT COUNT(*) FROM users WHERE role = 'student'")->fetchColumn(),
        'teachers'           => (int) db()->query("SELECT COUNT(*) FROM users WHERE role = 'teacher' AND teacher_status = 'approved'")->fetchColumn(),
        'courses_published'  => (int) db()->query("SELECT COUNT(*) FROM courses WHERE status = 'published'")->fetchColumn(),
        'enrollments_active' => (int) db()->query("SELECT COUNT(*) FROM enrollments WHERE status IN ('active','completed')")->fetchColumn(),
        'certificates_pending' => function_exists('pending_certificates_count') ? pending_certificates_count() : 0,
        'teachers_pending'   => (int) db()->query("SELECT COUNT(*) FROM users WHERE role = 'teacher' AND teacher_status = 'pending'")->fetchColumn(),
        'payments'           => report_payments_summary(),
        'certificates'       => report_certificate_summary(),
    ];
}