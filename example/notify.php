<?php

use V03413\BepusdtPhpSdk\Bepusdt;

require_once __DIR__ . '/../vendor/autoload.php';

$api   = 'https://pay.example.com';          // 你已经搭建好的 BEpusdt 系统地址
$token = 'YOUR_API_TOKEN_HERE'; // 在 BEpusdt 系统后台的 API Token
$bep   = new Bepusdt($api, $token);

try {
    $data = $bep->notify();

    file_put_contents('./notify.txt', json_encode($data, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND);

    exit('ok');
} catch (Exception $e) {
    http_response_code(400);
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
    exit;
}
