<?php
/**
 * Midtrans Integration Helper
 */

require_once __DIR__ . '/env.php';
loadEnv(__DIR__ . '/../.env');

// Set to true for Production Environment
$isProduction = isset($_ENV['MIDTRANS_IS_PRODUCTION']) && filter_var($_ENV['MIDTRANS_IS_PRODUCTION'], FILTER_VALIDATE_BOOLEAN);
define('MIDTRANS_IS_PRODUCTION', $isProduction);

// Server Key
$serverKey = $_ENV['MIDTRANS_SERVER_KEY'] ?? 'YOUR_SERVER_KEY_HERE';
define('MIDTRANS_SERVER_KEY', $serverKey);


// Midtrans Core API Endpoint
define('MIDTRANS_API_URL', MIDTRANS_IS_PRODUCTION ? 'https://api.midtrans.com/v2' : 'https://api.sandbox.midtrans.com/v2');

/**
 * Buat Charge API Call ke Midtrans (QRIS)
 */
function midtrans_charge_qris($orderId, $grossAmount, $items = []) {
    $payload = [
        'payment_type' => 'qris',
        'transaction_details' => [
            'order_id' => 'ORDER-' . $orderId . '-' . time(), // Unik per transaksi
            'gross_amount' => (int)$grossAmount
        ],
        'item_details' => $items
    ];

    $ch = curl_init(MIDTRANS_API_URL . '/charge');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Basic ' . base64_encode(MIDTRANS_SERVER_KEY . ':')
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return [
        'code' => $httpCode,
        'response' => json_decode($response, true)
    ];
}
?>
