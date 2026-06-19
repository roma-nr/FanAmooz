<?php

declare(strict_types=1);

function teacher_session_status(): string
{
    return (string) (auth_user()['teacher_status'] ?? 'none');
}

function require_teacher_auth(): void
{
    if (!auth_check() || auth_role() !== 'teacher') {
        flash('error', 'لطفاً با حساب استاد وارد شوید.');
        redirect(login_url());
    }
}

function attempt_teacher_login(string $username, string $password): bool
{
    if (!attempt_login(trim($username), $password)) {
        return false;
    }

    if (auth_role() !== 'teacher') {
        auth_logout();

        return false;
    }

    return true;
}

function teacher_refresh_session_status(): void
{
    $id = auth_id();
    if ($id === null) {
        return;
    }

    $stmt = db()->prepare("SELECT teacher_status, full_name, username, first_login_done, institution_id FROM users WHERE id = ? AND role = 'teacher' LIMIT 1");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if (!$row) {
        return;
    }

    $_SESSION['user']['teacher_status'] = $row['teacher_status'];
    $_SESSION['user']['full_name'] = $row['full_name'];
    $_SESSION['user']['username'] = $row['username'];
    $_SESSION['user']['first_login_done'] = (int) $row['first_login_done'];
    $_SESSION['user']['institution_id'] = $row['institution_id'] !== null ? (int) $row['institution_id'] : null;
}

function teacher_application_for_user(int $userId): ?array
{
    $stmt = db()->prepare('SELECT * FROM teacher_applications WHERE user_id = ? LIMIT 1');
    $stmt->execute([$userId]);

    $row = $stmt->fetch();

    return $row ?: null;
}

function require_teacher_approved(): void
{
    require_teacher_auth();
    teacher_refresh_session_status();

    if (teacher_session_status() !== 'approved') {
        flash('error', 'پس از تأیید درخواست همکاری می‌توانید دوره تعریف کنید.');
        redirect(base_url('teacher/index.php'));
    }
}

function create_teacher_user(
    string $fullName,
    string $username,
    string $password,
    ?string $email = null,
    ?string $phone = null
): int {
    $username = trim($username);
    if ($username === '') {
        throw new InvalidArgumentException('نام کاربری الزامی است.');
    }

    $check = db()->prepare('SELECT id FROM users WHERE username = ? LIMIT 1');
    $check->execute([$username]);
    if ($check->fetch()) {
        throw new InvalidArgumentException('این نام کاربری قبلاً ثبت شده است.');
    }

    $hash = password_hash($password, PASSWORD_BCRYPT);
    $stmt = db()->prepare(
        "INSERT INTO users (role, username, email, password_hash, full_name, phone, teacher_status, first_login_done, is_active)
         VALUES ('teacher', ?, ?, ?, ?, ?, 'approved', 1, 1)"
    );
    $stmt->execute([$username, $email, $hash, $fullName, $phone]);

    return (int) db()->lastInsertId();
}
