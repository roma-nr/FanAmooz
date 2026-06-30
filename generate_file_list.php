<?php
declare(strict_types=1);

/**
 * تولید لیست کامل پروژه به همراه محتوای فایل‌های متنی
 * خروجی: project_files_contents.txt
 */

$baseDir = __DIR__;

// پسوندهای فایل‌های متنی که محتوایشان نمایش داده شود
$textExtensions = ['php', 'html', 'htm', 'css', 'js', 'json', 'xml', 'sql', 'md', 'txt', 'csv', 'env', 'htaccess', 'log', 'yml', 'yaml', 'bat', 'sh', 'inc', 'conf', 'dist', 'lock', 'makefile', 'editorconfig', 'gitattributes', 'gitignore'];

// پوشه‌ها و فایل‌هایی که باید نادیده گرفته شوند
$excludeDirs  = ['.git', 'vendor', 'node_modules', 'uploads', 'cache', 'assets/videos', 'assets/fonts', 'assets/img'];
$excludeFiles = ['.DS_Store', 'Thumbs.db', 'error_log', 'generate_file_list.php']; // خود این فایل هم در خروجی نیاید

// تابع تشخیص فایل متنی
function isTextFile(string $filePath): bool {
    global $textExtensions;
    $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    // فایل‌های بدون پسوند معروف (مثل .htaccess) هم بررسی شوند
    if (in_array($ext, $textExtensions)) return true;
    // اگر پسوند نداشت، با خواندن چند بایت اول بررسی کن (اختیاری)
    return false;
}

// تابع بازگشتی برای خواندن پوشه‌ها
function scanDirectory(string $dir, string $relativePath = ''): array {
    global $excludeDirs, $excludeFiles;
    $result = [];
    $items = scandir($dir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $fullPath = $dir . DIRECTORY_SEPARATOR . $item;
        $relPath  = ($relativePath === '') ? $item : $relativePath . '/' . $item;

        // بررسی مستثنیات پوشه‌ها
        if (is_dir($fullPath)) {
            if (in_array($relPath, $excludeDirs)) continue;
            $subItems = scanDirectory($fullPath, $relPath);
            $result[] = ['type' => 'dir', 'path' => $relPath];
            $result = array_merge($result, $subItems);
        } else {
            if (in_array($item, $excludeFiles)) continue;
            $entry = ['type' => 'file', 'path' => $relPath];
            if (isTextFile($fullPath)) {
                $entry['content'] = file_get_contents($fullPath);
            } else {
                $entry['content'] = '[فایل باینری – محتوا نمایش داده نمی‌شود]';
            }
            $result[] = $entry;
        }
    }
    return $result;
}

// اجرا
$allItems = scanDirectory($baseDir);

// تولید خروجی متنی
$output = "لیست کامل فایل‌های پروژه به همراه محتوا\n";
$output .= "تاریخ: " . date('Y-m-d H:i:s') . "\n";
$output .= str_repeat('=', 80) . "\n\n";

foreach ($allItems as $item) {
    if ($item['type'] === 'dir') {
        $output .= "\n=== دایرکتوری: " . $item['path'] . " ===\n";
    } else {
        $output .= "\n--- فایل: " . $item['path'] . " ---\n";
        $output .= $item['content'] . "\n";
    }
}

// ذخیره در فایل
$outputFile = $baseDir . DIRECTORY_SEPARATOR . 'project_files_contents.txt';
file_put_contents($outputFile, $output);

// نمایش نتیجه به کاربر
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <title>تولید لیست فایل‌ها</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <div class="container">
        <div class="alert alert-success">
            <h4>فایل با موفقیت تولید شد!</h4>
            <p>فایل خروجی: <code>project_files_contents.txt</code></p>
            <p>شامل <?= count(array_filter($allItems, fn($i) => $i['type']==='file')) ?> فایل و <?= count(array_filter($allItems, fn($i) => $i['type']==='dir')) ?> پوشه</p>
            <a href="project_files_contents.txt" class="btn btn-primary" download>دانلود فایل</a>
            <a href="generate_file_list.php" class="btn btn-outline-secondary">اجرای مجدد</a>
        </div>
        
        <h5>پیش‌نمایش (۵۰ خط اول)</h5>
        <pre class="bg-light p-3 border" style="max-height: 400px; overflow-y: scroll;"><?= e(implode("\n", array_slice(explode("\n", $output), 0, 50))) ?></pre>
        <p class="text-muted">برای دیدن کل محتوا فایل را دانلود کنید.</p>
    </div>
</body>
</html>