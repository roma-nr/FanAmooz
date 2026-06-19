<?php

declare(strict_types=1);

function settings_all(): array
{
    static $cache = null;

    if (isset($GLOBALS['_settings_cache_reset'])) {
        $cache = null;
        unset($GLOBALS['_settings_cache_reset']);
    }

    if ($cache !== null) {
        return $cache;
    }

    try {
        $rows = db()->query('SELECT setting_key, setting_value FROM settings')->fetchAll();
    } catch (PDOException) {
        return [];
    }

    $cache = [];
    foreach ($rows as $row) {
        $cache[$row['setting_key']] = $row['setting_value'];
    }

    return $cache;
}

function setting(string $key, string $default = ''): string
{
    $all = settings_all();

    return (string) ($all[$key] ?? $default);
}

function setting_set(string $key, ?string $value): void
{
    $stmt = db()->prepare(
        'INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)
         ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)'
    );
    $stmt->execute([$key, $value]);
    $GLOBALS['_settings_cache_reset'] = true;
}

function setting_set_many(array $pairs): void
{
    foreach ($pairs as $key => $value) {
        setting_set((string) $key, $value === null ? null : (string) $value);
    }
}

function site_name(): string
{
    return setting('site_name', 'فن‌آموز');
}

function logo_url(int $n): string
{
    $path = setting('logo_' . $n);

    if ($path !== '') {
        return upload_url($path);
    }

    return asset_url('images/logo-placeholder.svg');
}

function logo_alt(int $n): string
{
    return setting('logo_' . $n . '_alt', 'لوگو ' . $n);
}

function logo_link(int $n): string
{
    return setting('logo_' . $n . '_url', '');
}
