<?php
require_once '../config/db.php';
require_once '../config/auth.php';

// Endpoint ini dipanggil oleh Kasir via AJAX secara berkala
if (!isLoggedIn() || $_SESSION['user_role'] !== 'kasir') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

try {
    // Ambil pesanan yang belum selesai atau dibatalkan
    $stmt = $pdo->query("
        SELECT o.id, t.nomor_meja, o.waktu_pesan, o.total_harga, o.uang_dibayar, o.metode_bayar, o.status_bayar, o.status_pesanan, o.bukti_transfer 
        FROM orders o 
        JOIN tables t ON o.meja_id = t.id 
        WHERE o.status_pesanan IN ('pending', 'diproses') 
        ORDER BY o.waktu_pesan DESC
    ");
    $orders = $stmt->fetchAll();

    // Ambil detail untuk masing-masing pesanan
    $ordersWithDetails = [];
    foreach ($orders as $order) {
        $stmtDetails = $pdo->prepare("
            SELECT d.qty, d.catatan, m.nama_menu 
            FROM order_details d 
            JOIN menus m ON d.menu_id = m.id 
            WHERE d.order_id = ?
        ");
        $stmtDetails->execute([$order['id']]);
        $details = $stmtDetails->fetchAll();
        
        $order['items'] = $details;
        $ordersWithDetails[] = $order;
    }

    echo json_encode([
        'success' => true,
        'data' => $ordersWithDetails
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Gagal mengambil data pesanan'
    ]);
}
?>
