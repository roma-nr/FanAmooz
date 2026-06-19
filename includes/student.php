<?php

declare(strict_types=1);

function student_first_login_done(): bool
{
    return (int) (auth_user()['first_login_done'] ?? 0) === 1;
}

function require_student_auth(bool $allowOnboarding = false): void
{
    if (!auth_check() || auth_role() !== 'student') {
        flash('error', 'لطفاً وارد حساب دانشجویی شوید.');
        redirect(login_url());
    }

    if (!$allowOnboarding && !student_first_login_done()) {
        redirect(base_url('student/interests.php'));
    }
}

function attempt_student_login(int $institutionId, string $studentCode, string $nationalId): bool
{
    $studentCode = trim($studentCode);
    $nationalId = trim($nationalId);
    if (!validate_national_id($nationalId)) {
        return false;
    }
    $stmt = db()->prepare(
        'SELECT u.id, u.role, u.full_name, u.username, u.first_login_done, u.institution_id, u.is_active
         FROM users u
         WHERE u.role = ? AND u.institution_id = ? AND u.student_code = ? AND u.national_id = ?
         LIMIT 1'
    );
    $stmt->execute(['student', $institutionId, $studentCode, $nationalId]);
    $user = $stmt->fetch();
    if (!$user || !(int) $user['is_active']) {
        return false;
    }
    auth_login($user);
    return true;
}

function student_enrolled_courses(int $userId): array
{
    $stmt = db()->prepare(
        "SELECT c.*, e.id AS enrollment_id, e.status AS enrollment_status, e.final_grade, e.enrolled_at,
                cat.name AS category_name, u.full_name AS teacher_name
         FROM enrollments e
         JOIN courses c ON c.id = e.course_id
         LEFT JOIN course_categories cat ON cat.id = c.category_id
         LEFT JOIN users u ON u.id = c.teacher_id
         WHERE e.user_id = ? AND e.status IN ('active','completed','pending_payment')
         ORDER BY e.enrolled_at DESC"
    );
    $stmt->execute([$userId]);

    return $stmt->fetchAll();
}

function create_student_user(
    string $fullName,
    string $studentCode,
    string $nationalId,
    int $institutionId,
    ?string $phone = null
): int {
    if (!validate_national_id($nationalId)) {
        throw new InvalidArgumentException('کد ملی نامعتبر است.');
    }

    $inst = db()->prepare('SELECT province_id FROM institutions WHERE id = ? AND is_active = 1');
    $inst->execute([$institutionId]);
    $row = $inst->fetch();
    if (!$row) {
        throw new InvalidArgumentException('دانشکده یافت نشد.');
    }

    $hash = password_hash($nationalId, PASSWORD_BCRYPT);

    $username = student_username($institutionId, $studentCode);

    $stmt = db()->prepare(
        'INSERT INTO users (role, username, password_hash, full_name, phone, province_id, institution_id, student_code, national_id, first_login_done, is_active)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 1)'
    );
    $stmt->execute([
        'student',
        $username,
        $hash,
        $fullName,
        $phone,
        (int) $row['province_id'],
        $institutionId,
        trim($studentCode),
        trim($nationalId),
    ]);

    return (int) db()->lastInsertId();
}

function student_username(int $institutionId, string $studentCode): string
{
    return trim($studentCode);
}

function validate_national_id(string $code): bool
{
    $code = preg_replace('/\D/', '', $code) ?? '';
    if (strlen($code) !== 10 || preg_match('/^(\d)\1{9}$/', $code)) {
        return false;
    }

    $check = (int) $code[9];
    $sum = 0;
    for ($i = 0; $i < 9; $i++) {
        $sum += (int) $code[$i] * (10 - $i);
    }
    $remainder = $sum % 11;

    return ($remainder < 2 && $check === $remainder) || ($remainder >= 2 && $check === 11 - $remainder);
}
