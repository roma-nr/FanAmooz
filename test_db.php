<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/includes/bootstrap.php';

echo "اتصال به دیتابیس: " . (db() ? '✅ موفق' : '❌ ناموفق') . "<br>";

// تست INSERT ساده
try {
    $pdo = db();
    $sql = "INSERT INTO courses (title, slug, status, category_id, teacher_id) 
            VALUES ('تست', 'test-course', 'draft', 1, 1)";
    $result = $pdo->exec($sql);
    echo "نتیجه INSERT: " . ($result ? '✅ موفق (ID: ' . $pdo->lastInsertId() . ')' : '❌ ناموفق') . "<br>";
    
    // نمایش ساختار جدول
    $columns = $pdo->query("DESCRIBE courses")->fetchAll();
    echo "<pre>ساختار جدول courses:\n";
    foreach ($columns as $col) {
        echo $col['Field'] . " - " . $col['Type'] . "\n";
    }
    echo "</pre>";
    
} catch (Exception $e) {
    echo "❌ خطا: " . $e->getMessage();
}