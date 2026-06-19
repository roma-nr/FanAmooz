<?php

declare(strict_types=1);

function enrollment_status_label(string $status): string
{
    return match ($status) {
        'pending_payment' => 'در انتظار پرداخت',
        'active' => 'فعال',
        'completed' => 'تکمیل‌شده',
        'cancelled' => 'لغو شده',
        default => $status,
    };
}

function course_status_label(string $status): string
{
    return match ($status) {
        'draft' => 'پیش‌نویس',
        'published' => 'منتشرشده',
        'archived' => 'بایگانی',
        default => $status,
    };
}

function course_by_slug(string $slug, bool $publishedOnly = true): ?array
{
    $sql = "SELECT c.*, cat.name AS category_name, u.full_name AS teacher_name
            FROM courses c
            LEFT JOIN course_categories cat ON cat.id = c.category_id
            LEFT JOIN users u ON u.id = c.teacher_id
            WHERE c.slug = ?";
    if ($publishedOnly) {
        $sql .= " AND c.status = 'published'";
    }
    $sql .= ' LIMIT 1';
    $stmt = db()->prepare($sql);
    $stmt->execute([$slug]);

    $row = $stmt->fetch();

    return $row ?: null;
}

function course_by_id(int $id): ?array
{
    $stmt = db()->prepare(
        "SELECT c.*, cat.name AS category_name, u.full_name AS teacher_name
         FROM courses c
         LEFT JOIN course_categories cat ON cat.id = c.category_id
         LEFT JOIN users u ON u.id = c.teacher_id
         WHERE c.id = ? LIMIT 1"
    );
    $stmt->execute([$id]);
    $row = $stmt->fetch();

    return $row ?: null;
}

function course_visible_to_student(array $course, ?int $studentInstitutionId): bool
{
    if (empty($course['institution_id'])) {
        return true;
    }

    return $studentInstitutionId !== null && (int) $course['institution_id'] === $studentInstitutionId;
}

function student_enrollment(int $userId, int $courseId): ?array
{
    $stmt = db()->prepare('SELECT * FROM enrollments WHERE user_id = ? AND course_id = ? LIMIT 1');
    $stmt->execute([$userId, $courseId]);

    $row = $stmt->fetch();

    return $row ?: null;
}

function student_has_active_enrollment(int $userId, int $courseId): bool
{
    $en = student_enrollment($userId, $courseId);

    return $en !== null && in_array($en['status'], ['active', 'completed'], true);
}

function published_courses_for_student(?int $institutionId, int $limit = 100): array
{
    $sql = "SELECT c.*, cat.name AS category_name, u.full_name AS teacher_name
            FROM courses c
            LEFT JOIN course_categories cat ON cat.id = c.category_id
            LEFT JOIN users u ON u.id = c.teacher_id
            WHERE c.status = 'published'
              AND (c.institution_id IS NULL OR c.institution_id = ?)
            ORDER BY c.featured_on_home DESC, c.title
            LIMIT ?";
    $stmt = db()->prepare($sql);
    $stmt->bindValue(1, $institutionId ?? 0, PDO::PARAM_INT);
    $stmt->bindValue(2, $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

function student_recommended_courses(int $userId, int $limit = 12): array
{
    $instStmt = db()->prepare('SELECT institution_id FROM users WHERE id = ?');
    $instStmt->execute([$userId]);
    $institutionId = $instStmt->fetchColumn();
    $institutionId = $institutionId !== false ? (int) $institutionId : null;

    $sql = "SELECT DISTINCT c.*, cat.name AS category_name, u.full_name AS teacher_name
            FROM courses c
            LEFT JOIN course_categories cat ON cat.id = c.category_id
            LEFT JOIN users u ON u.id = c.teacher_id
            INNER JOIN student_interests si ON si.user_id = ?
            INNER JOIN interests i ON i.id = si.interest_id AND i.category_id = c.category_id
            WHERE c.status = 'published'
              AND (c.institution_id IS NULL OR c.institution_id = ?)
            ORDER BY c.featured_on_home DESC, c.title
            LIMIT ?";
    $stmt = db()->prepare($sql);
    $stmt->bindValue(1, $userId, PDO::PARAM_INT);
    $stmt->bindValue(2, $institutionId ?? 0, PDO::PARAM_INT);
    $stmt->bindValue(3, $limit, PDO::PARAM_INT);
    $stmt->execute();

    $courses = $stmt->fetchAll();

    if ($courses !== []) {
        return $courses;
    }

    return published_courses_for_student($institutionId, $limit);
}

function course_sessions_list(int $courseId): array
{
    $stmt = db()->prepare(
        'SELECT * FROM course_sessions WHERE course_id = ? ORDER BY sort_order, session_number, id'
    );
    $stmt->execute([$courseId]);

    return $stmt->fetchAll();
}

function teacher_owns_course(int $teacherId, int $courseId): bool
{
    $stmt = db()->prepare('SELECT id FROM courses WHERE id = ? AND teacher_id = ? LIMIT 1');
    $stmt->execute([$courseId, $teacherId]);

    return (bool) $stmt->fetch();
}

function teacher_courses(int $teacherId): array
{
    $stmt = db()->prepare(
        "SELECT c.*, cat.name AS category_name,
                (SELECT COUNT(*) FROM course_sessions s WHERE s.course_id = c.id) AS session_count,
                (SELECT COUNT(*) FROM enrollments e WHERE e.course_id = c.id AND e.status IN ('active','completed')) AS student_count
         FROM courses c
         LEFT JOIN course_categories cat ON cat.id = c.category_id
         WHERE c.teacher_id = ?
         ORDER BY c.updated_at DESC"
    );
    $stmt->execute([$teacherId]);

    return $stmt->fetchAll();
}

function zarinpal_amount_from_toman(float|string $toman): int
{
    return (int) round((float) $toman * 10);
}

function payment_gateway_configured(): bool
{
    return setting('zarinpal_merchant_id', '') !== '';
}

function enroll_student_in_course(int $userId, int $courseId): void
{
    $course = course_by_id($courseId);
    if (!$course || $course['status'] !== 'published') {
        throw new RuntimeException('دوره برای ثبت‌نام در دسترس نیست.');
    }

    $userStmt = db()->prepare('SELECT institution_id FROM users WHERE id = ? AND role = ?');
    $userStmt->execute([$userId, 'student']);
    $student = $userStmt->fetch();
    if (!$student) {
        throw new RuntimeException('حساب دانشجو نامعتبر است.');
    }

    $institutionId = $student['institution_id'] !== null ? (int) $student['institution_id'] : null;
    if (!course_visible_to_student($course, $institutionId)) {
        throw new RuntimeException('این دوره برای دانشکده شما تعریف نشده است.');
    }

    $existing = student_enrollment($userId, $courseId);
    if ($existing !== null) {
        if ($existing['status'] === 'pending_payment') {
            redirect(base_url('student/upload_receipt.php?enrollment_id=' . $existing['id']));
        }
        if (in_array($existing['status'], ['active', 'completed'], true)) {
            throw new RuntimeException('شما قبلاً در این دوره ثبت‌نام کرده‌اید.');
        }
        throw new RuntimeException('وضعیت ثبت‌نام شما: ' . enrollment_status_label($existing['status']));
    }

    $isPaid = (int) $course['is_paid'] === 1 && (float) $course['price'] > 0;

    $pdo = db();  // ← مهم: این خط را حتماً داشته باش
    $pdo->beginTransaction();

    try {
        // ثبت enrollment اولیه
        $stmt = $pdo->prepare("INSERT INTO enrollments (user_id, course_id, status) VALUES (?, ?, ?)");
        $status = $isPaid ? 'pending_payment' : 'active';
        $stmt->execute([$userId, $courseId, $status]);
        $enrollmentId = (int) $pdo->lastInsertId();

        if (!$isPaid) {
            // دوره رایگان
            $pdo->commit();
            flash('success', 'ثبت‌نام با موفقیت انجام شد.');
            redirect(base_url('student/my_course.php?slug=' . urlencode($course['slug'])));
        } else {
            // دوره پولی – فقط رکورد enrollment با وضعیت pending_payment ساخته شد
            $pdo->commit();
            flash('info', 'برای فعال‌سازی دوره، لطفاً فیش پرداخت را بارگذاری کنید.');
            redirect(base_url('student/upload_receipt.php?enrollment_id=' . $enrollmentId));
			
        }
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }
}

function start_course_payment(int $userId, int $courseId): never
{
    $course = course_by_id($courseId);
    if (!$course) {
        throw new RuntimeException('دوره یافت نشد.');
    }

    $enrollment = student_enrollment($userId, $courseId);
    if ($enrollment === null || $enrollment['status'] !== 'pending_payment') {
        throw new RuntimeException('ثبت‌نام در انتظار پرداخت یافت نشد.');
    }

    $payStmt = db()->prepare(
        "SELECT * FROM payments WHERE user_id = ? AND course_id = ? AND status = 'pending' ORDER BY id DESC LIMIT 1"
    );
    $payStmt->execute([$userId, $courseId]);
    $payment = $payStmt->fetch();
    if (!$payment) {
        throw new RuntimeException('رکورد پرداخت یافت نشد.');
    }

    require_once __DIR__ . '/payment/zarinpal.php';

    $amountRial = zarinpal_amount_from_toman($payment['amount']);
    $callback = base_url('student/payment_callback.php');
    $user = db()->prepare('SELECT email, phone FROM users WHERE id = ?');
    $user->execute([$userId]);
    $u = $user->fetch();

    $result = zarinpal_request(
        $amountRial,
        'ثبت‌نام دوره: ' . $course['title'],
        $callback,
        $u['email'] ?? null,
        $u['phone'] ?? null
    );

    db()->prepare('UPDATE payments SET authority = ? WHERE id = ?')->execute([
        $result['authority'],
        (int) $payment['id'],
    ]);

    redirect($result['pay_url']);
}

function verify_course_payment(string $authority, string $status): void
{
    if ($status !== 'OK') {
        flash('error', 'پرداخت توسط شما لغو شد.');
        redirect(base_url('student/courses.php'));
    }

    $stmt = db()->prepare("SELECT * FROM payments WHERE authority = ? AND status = 'pending' LIMIT 1");
    $stmt->execute([$authority]);
    $payment = $stmt->fetch();
    if (!$payment) {
        flash('error', 'تراکنش یافت نشد.');
        redirect(base_url('student/courses.php'));
    }

    require_once __DIR__ . '/payment/zarinpal.php';

    $amountRial = zarinpal_amount_from_toman($payment['amount']);
    try {
        $verified = zarinpal_verify($authority, $amountRial);
    } catch (RuntimeException $e) {
        db()->prepare("UPDATE payments SET status = 'failed' WHERE id = ?")->execute([(int) $payment['id']]);
        flash('error', $e->getMessage());
        redirect(base_url('student/pay.php?course_id=' . (int) $payment['course_id']));
    }

    $pdo = db();
    $pdo->beginTransaction();
    $pdo->prepare("UPDATE payments SET status = 'paid', ref_id = ?, paid_at = NOW() WHERE id = ?")->execute([
        $verified['ref_id'],
        (int) $payment['id'],
    ]);
    $pdo->prepare("UPDATE enrollments SET status = 'active' WHERE id = ?")->execute([(int) $payment['enrollment_id']]);
    $pdo->commit();

    $course = course_by_id((int) $payment['course_id']);
    flash('success', 'پرداخت موفق بود. ثبت‌نام شما فعال شد. کد پیگیری: ' . $verified['ref_id']);
    redirect(base_url('student/my_course.php?slug=' . urlencode($course['slug'] ?? '')));
}
