<?php

declare(strict_types=1);

function e($value): string
{
    if ($value === null) return '';
    if (is_float($value) || is_int($value)) return (string) $value;
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}

function base_url(string $path = ''): string
{
    global $configApp;
    $base = rtrim($configApp['url'], '/');
    $path = ltrim($path, '/');

    return $path === '' ? $base : $base . '/' . $path;
}

function asset_url(string $path): string
{
    return base_url('assets/' . ltrim($path, '/'));
}

function upload_url(?string $filename): string
{
    if ($filename === null || $filename === '') {
        return '';
    }

    global $configApp;

    return rtrim($configApp['url'], '/') . rtrim($configApp['upload_url'], '/') . '/' . ltrim($filename, '/');
}

function is_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST';
}

function csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['_csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): bool
{
    $token = $_POST['_csrf'] ?? '';

    return is_string($token) && hash_equals(csrf_token(), $token);
}

function flash(string $key, ?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['_flash'][$key] = $message;

        return null;
    }

    $value = $_SESSION['_flash'][$key] ?? null;
    unset($_SESSION['_flash'][$key]);

    return $value;
}

function format_price(int|float|string $amount): string
{
    return number_format((float) $amount, 0, '.', ',') . ' تومان';
}

function format_date(?string $datetime): string
{
    if ($datetime === null || $datetime === '') {
        return '—';
    }

    $ts = strtotime($datetime);

    return $ts ? date('Y/m/d', $ts) : '—';
}

function format_datetime(?string $datetime): string
{
    if ($datetime === null || $datetime === '') {
        return '—';
    }

    $ts = strtotime($datetime);

    return $ts ? date('Y/m/d H:i', $ts) : '—';
}

function slugify(string $text): string
{
    $text = trim($text);
    $text = preg_replace('/\s+/u', '-', $text) ?? $text;
    $text = preg_replace('/[^\p{L}\p{N}\-]+/u', '', $text) ?? $text;

    return mb_strtolower($text, 'UTF-8') ?: 'item-' . time();
}

function handle_upload(array $file, string $subdir, array $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'pdf'], ?int $maxBytes = null): ?string
{
    $maxBytes = $maxBytes ?? 5 * 1024 * 1024;

    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('خطا در آپلود فایل.');
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed, true)) {
        throw new RuntimeException('نوع فایل مجاز نیست.');
    }

    if (($file['size'] ?? 0) > $maxBytes) {
        throw new RuntimeException('حجم فایل بیش از حد مجاز است.');
    }

    global $configApp;
    $dir = rtrim($configApp['upload_path'], '/\\') . DIRECTORY_SEPARATOR . $subdir;

    if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
        throw new RuntimeException('امکان ایجاد پوشه آپلود وجود ندارد.');
    }

    $filename = bin2hex(random_bytes(16)) . '.' . $ext;
    $dest = $dir . DIRECTORY_SEPARATOR . $filename;

    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        throw new RuntimeException('ذخیره فایل ناموفق بود.');
    }

    return $subdir . '/' . $filename;
}

function delete_upload(?string $relativePath): void
{
    if ($relativePath === null || $relativePath === '') {
        return;
    }

    global $configApp;
    $full = rtrim($configApp['upload_path'], '/\\') . DIRECTORY_SEPARATOR
        . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativePath);

    if (is_file($full)) {
        unlink($full);
    }
}
function old(string $key, $default = ''): string
{
    return e($_POST[$key] ?? $default);
}

