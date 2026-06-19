<?php

declare(strict_types=1);

function auth_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function auth_id(): ?int
{
    $user = auth_user();

    return $user ? (int) $user['id'] : null;
}

function auth_check(): bool
{
    return auth_user() !== null;
}

function auth_role(): ?string
{
    $user = auth_user();

    return $user['role'] ?? null;
}

function auth_login(array $user): void
{
    session_regenerate_id(true);
    $_SESSION['user'] = [
        'id' => (int) $user['id'],
        'role' => $user['role'],
        'full_name' => $user['full_name'],
        'username' => $user['username'] ?? null,
        'first_login_done' => (int) ($user['first_login_done'] ?? 1),
        'institution_id' => isset($user['institution_id']) ? (int) $user['institution_id'] : null,
        'teacher_status' => $user['teacher_status'] ?? 'none',
    ];
}

function auth_logout(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }

    session_destroy();
}

function login_url(): string
{
    return base_url('login.php');
}

function redirect_after_login(): never
{
    $role = auth_role();

    if ($role === 'admin') {
        redirect(base_url('admin/index.php'));
    }

    if ($role === 'teacher') {
        redirect(base_url('teacher/index.php'));
    }

    if ($role === 'student') {
        redirect(student_first_login_done()
            ? base_url('student/index.php')
            : base_url('student/interests.php'));
    }

    auth_logout();
    flash('error', 'نقش کاربری نامعتبر است.');
    redirect(login_url());
}

function require_auth(?string $role = null): void
{
    if (!auth_check()) {
        flash('error', 'لطفاً وارد حساب کاربری شوید.');
        redirect(login_url());
    }

    if ($role !== null && auth_role() !== $role) {
        http_response_code(403);
        exit('دسترسی غیرمجاز.');
    }
}

function attempt_login(string $username, string $password): bool
{
    $stmt = db()->prepare(
        'SELECT id, role, username, full_name, password_hash, is_active, teacher_status, first_login_done, institution_id
         FROM users WHERE username = ? LIMIT 1'
    );
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if (!$user || !(int) $user['is_active']) {
        return false;
    }

    if (!password_verify($password, $user['password_hash'])) {
        return false;
    }

    auth_login($user);

    return true;
}
