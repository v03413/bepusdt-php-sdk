# PHP SDK for BEpusdt

这是 BEpusdt 的 PHP SDK，旨在简化与 BEpusdt API 的集成和交互。

## 安装

使用 Composer 安装：

```bash
composer require v03413/bepusdt-php-sdk
```

## 使用示例

具体可参阅 `examples` 目录下的示例代码。

```php 
<?php

use V03413\BepusdtPhpSdk\Bepusdt;

require_once __DIR__ . '/../vendor/autoload.php';

$api   = 'https://pay.example.com';          // 你已经搭建好的 BEpusdt 系统地址
$token = 'YOUR_API_TOKEN_HERE';              // 在 BEpusdt 系统后台的 API Token
$bep   = new Bepusdt($api, $token);

try {

    $orderId = 'order_' . time();
    $result  = $bep->createTransaction(
        orderId: $orderId,
        amount: 10,
        notifyUrl: 'https://b.example.com/example/notify.php',
        redirectUrl: 'https://b.example.com/example/redirect.php?order_id=' . $orderId,
        tradeType: 'tron.trx',
    );

//    print_r($result);

    echo "交易订单创建成功: $orderId" . PHP_EOL;

    // 重定向到收银台
    header("Location: " . $result['data']['payment_url']);
} catch (Exception $e) {
    echo "交易订单创建失败:" . PHP_EOL;
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}

```