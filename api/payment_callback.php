<?php
require_once '../config/db.php';
require_once '../config/midtrans.php';

// Midtrans mengirimkan raw JSON di body
$json_result = file_get_contents('php://input');
$data = json_decode($json_result, true);

if (!$data) {
    http_response_code(400);
    exit;
}

$order_id = $data['order_id'];
$status_code = $data['status_code'];
$gross_amount = $data['gross_amount'];
$server_key = MIDTRANS_SERVER_KEY;
$signature_key = $data['signature_key'];

// Verifikasi signature
$my_signature_key = hash('sha512', $order_id . $status_code . $gross_amount . $server_key);

if ($my_signature_key !== $signature_key) {
    http_response_code(403);
    echo "Invalid signature";
    exit;
}

$transaction_status = $data['transaction_status'];

// order_id dari midtrans formatnya: ORDER-{ID}-{TIME}
// Ekstrak ID asli
$parts = explode('-', $order_id);
if (count($parts) >= 2) {
    $real_order_id = (int)$parts[1];

    if ($transaction_status == 'capture' || $transaction_status == 'settlement') {
        $stmt = $pdo->prepare("UPDATE orders SET status_bayar = 'paid' WHERE id = ?");
        $stmt->execute([$real_order_id]);
    } else if ($transaction_status == 'cancel' || $transaction_status == 'deny' || $transaction_status == 'expire') {
        $stmt = $pdo->prepare("UPDATE orders SET status_pesanan = 'dibatalkan' WHERE id = ? AND status_bayar = 'unpaid'");
        $stmt->execute([$real_order_id]);
    }
}

http_response_code(200);
echo "OK";
?>
