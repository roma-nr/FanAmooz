<?php
require_once __DIR__ . '/includes/bootstrap.php';

if (!is_post() || !verify_csrf()) {
    flash('error', 'درخواست نامعتبر.');
    redirect(base_url('contact.php'));
}

$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$userRole = trim($_POST['user_role'] ?? '');

if ($title === '' || $description === '' || $userRole === '') {
    flash('error', 'تمامی فیلدها الزامی هستند.');
    redirect(base_url('contact.php'));
}

$userId = auth_id();
$role = $userRole;

// اگر کاربر وارد شده باشد، نقش واقعی را از دیتابیس می‌گیریم
if ($userId) {
    $role = auth_role() ?? $userRole;
}

db()->prepare("INSERT INTO reports (user_id, user_role, type, title, description) VALUES (?, ?, 'user', ?, ?)")
   ->execute([$userId, $role, $title, $description]);

flash('success', 'گزارش شما با موفقیت ثبت شد. تیم پشتیبانی بررسی خواهد کرد.');
redirect(base_url('contact.php'));