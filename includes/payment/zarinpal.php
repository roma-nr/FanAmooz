<?php

declare(strict_types=1);

function zarinpal_request(int $amountRial, string $description, string $callback, ?string $email = null, ?string $phone = null): array
{
    $merchant = setting('zarinpal_merchant_id', '');
    if ($merchant === '') {
        throw new RuntimeException('درگاه پرداخت پیکربندی نشده است.');
    }

    $sandbox = (bool) setting('zarinpal_sandbox', false);
    $url = $sandbox
        ? 'https://sandbox.zarinpal.com/pg/v4/payment/request.json'
        : 'https://api.zarinpal.com/pg/v4/payment/request.json';

    $data = [
        'merchant_id'  => $merchant,
        'amount'       => $amountRial,
        'description'  => $description,
        'callback_url' => $callback,
        'metadata'     => [],
    ];
    if ($email !== null) $data['metadata']['email'] = $email;
    if ($phone !== null) $data['metadata']['mobile'] = $phone;

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        CURLOPT_POSTFIELDS     => json_encode($data),
        CURLOPT_TIMEOUT        => 30,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response === false || $httpCode !== 200) {
        throw new RuntimeException('خطا در اتصال به درگاه پرداخت.');
    }

    $result = json_decode($response, true);
    if (empty($result['data']['authority']) || $result['data']['code'] !== 100) {
        throw new RuntimeException('خطا از سمت زرین‌پال: ' . ($result['errors']['message'] ?? 'کد نامشخص'));
    }

    $payUrl = $sandbox
        ? 'https://sandbox.zarinpal.com/pg/StartPay/' . $result['data']['authority']
        : 'https://www.zarinpal.com/pg/StartPay/' . $result['data']['authority'];

    return [
        'authority' => $result['data']['authority'],
        'pay_url'   => $payUrl,
    ];
}

function zarinpal_verify(string $authority, int $amountRial): array
{
    $merchant = setting('zarinpal_merchant_id', '');
    $sandbox = (bool) setting('zarinpal_sandbox', false);
    $url = $sandbox
        ? 'https://sandbox.zarinpal.com/pg/v4/payment/verify.json'
        : 'https://api.zarinpal.com/pg/v4/payment/verify.json';

    $data = [
        'merchant_id' => $merchant,
        'amount'      => $amountRial,
        'authority'   => $authority,
    ];

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        CURLOPT_POSTFIELDS     => json_encode($data),
        CURLOPT_TIMEOUT        => 30,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response === false || $httpCode !== 200) {
        throw new RuntimeException('خطا در اتصال به درگاه تأیید پرداخت.');
    }

    $result = json_decode($response, true);
    if (($result['data']['code'] ?? 0) !== 100) {
        throw new RuntimeException('تأیید پرداخت ناموفق: ' . ($result['errors']['message'] ?? 'خطا'));
    }

    return [
        'ref_id' => $result['data']['ref_id'],
    ];
}