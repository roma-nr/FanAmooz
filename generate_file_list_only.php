<?php
require_once __DIR__ . '/includes/bootstrap.php';
// فقط مدیر می‌تواند ببیند
if (!auth_check() || auth_role() !== 'admin') {
    die('دسترسی غیرمجاز');
}

function listFiles($dir, &$results = array()) {
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file == '.' || $file == '..') continue;
        $path = $dir . DIRECTORY_SEPARATOR . $file;
        if (is_dir($path)) {
            $results[] = $path . '/';
            listFiles($path, $results);
        } else {
            $results[] = $path;
        }
    }
    return $results;
}

$root = __DIR__;
$allFiles = listFiles($root);

// حذف پوشه‌های غیرضروری (مثل vendor, node_modules, .git, uploads)
$exclude = ['vendor', 'node_modules', '.git', 'uploads', 'cache'];
$allFiles = array_filter($allFiles, function($file) use ($exclude) {
    foreach ($exclude as $ex) {
        if (strpos($file, DIRECTORY_SEPARATOR . $ex) !== false) return false;
    }
    return true;
});

// خروجی HTML برای ذخیره در Word
header('Content-Type: application/msword');
header('Content-Disposition: attachment; filename="project_files_list.doc"');
echo '<html><head><meta charset="UTF-8"><title>لیست فایل‌های پروژه</title></head><body>';
echo '<h1>لیست کامل فایل‌های پروژه فن‌آموز</h1>';
echo '<ul>';
foreach ($allFiles as $file) {
    $relative = str_replace($root, '', $file);
    echo '<li>' . htmlspecialchars($relative) . '</li>';
}
echo '</ul></body></html>';