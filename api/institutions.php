<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');

$provinceId = (int) ($_GET['province_id'] ?? 0);

if ($provinceId <= 0) {
    echo json_encode([]);

    exit;
}

$stmt = db()->prepare(
    'SELECT id, name FROM institutions WHERE province_id = ? AND is_active = 1 ORDER BY sort_order, name'
);
$stmt->execute([$provinceId]);

echo json_encode($stmt->fetchAll(), JSON_UNESCAPED_UNICODE);
