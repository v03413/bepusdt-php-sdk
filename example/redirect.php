<?php
$order_id = $_GET['order_id'] ?? 'unknown';
if ($order_id === 'unknown') {
    exit('订单号未知');
}

echo "支付完成，订单号: " . htmlspecialchars($order_id, ENT_QUOTES, 'UTF-8');
