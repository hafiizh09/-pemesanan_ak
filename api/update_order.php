<?php
require_once '../config/db.php';
require_once '../config/auth.php';

// Endpoint ini dipanggil oleh Kasir via AJAX
if (!isLoggedIn() || $_SESSION['user_role'] !== 'kasir') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || empty($data['order_id']) || empty($data['action'])) {
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
    exit;
}

$orderId = (int)$data['order_id'];
$action = $data['action'];

try {
    if ($action === 'proses') {
        $stmt = $pdo->prepare("UPDATE orders SET status_pesanan = 'diproses' WHERE id = ?");
        $stmt->execute([$orderId]);
    } elseif ($action === 'selesai') {
        $stmtCek = $pdo->prepare("SELECT status_bayar, metode_bayar FROM orders WHERE id = ?");
        $stmtCek->execute([$orderId]);
        $order = $stmtCek->fetch();
        if ($order && $order['metode_bayar'] === 'cash' && $order['status_bayar'] === 'unpaid') {
            throw new Exception("Selesaikan pembayaran CASH terlebih dahulu sebelum menyelesaikan pesanan.");
        }
        
        $stmt = $pdo->prepare("UPDATE orders SET status_pesanan = 'selesai' WHERE id = ?");
        $stmt->execute([$orderId]);
    } elseif ($action === 'bayar') {
        $stmt = $pdo->prepare("UPDATE orders SET status_bayar = 'paid' WHERE id = ?");
        $stmt->execute([$orderId]);
    } elseif ($action === 'tolak') {
        $stmt = $pdo->prepare("UPDATE orders SET status_pesanan = 'dibatalkan' WHERE id = ?");
        $stmt->execute([$orderId]);
    } else {
        throw new Exception("Aksi tidak valid");
    }

    echo json_encode(['success' => true, 'message' => 'Status berhasil diubah']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Gagal mengubah status: ' . $e->getMessage()]);
}
?>
