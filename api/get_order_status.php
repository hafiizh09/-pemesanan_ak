<?php
require_once '../config/db.php';
header('Content-Type: application/json');

if (!isset($_GET['order_id'])) {
    echo json_encode(['success' => false, 'message' => 'Order ID tidak diberikan']);
    exit;
}

$orderId = (int)$_GET['order_id'];

try {
    $stmt = $pdo->prepare("SELECT status_pesanan, status_bayar FROM orders WHERE id = ?");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch();

    if ($order) {
        // Ambil summary item
        $stmtItems = $pdo->prepare("
            SELECT oi.qty as jumlah, m.nama_menu 
            FROM order_details oi
            JOIN menus m ON oi.menu_id = m.id
            WHERE oi.order_id = ?
        ");
        $stmtItems->execute([$orderId]);
        $items = $stmtItems->fetchAll();
        
        $itemNames = [];
        $itemsList = [];
        foreach ($items as $item) {
            $itemNames[] = $item['jumlah'] . 'x ' . $item['nama_menu'];
            $itemsList[] = ['qty' => $item['jumlah'], 'name' => $item['nama_menu']];
        }
        $itemsSummary = implode(', ', $itemNames);
        if (strlen($itemsSummary) > 45) {
            $itemsSummary = substr($itemsSummary, 0, 45) . '...';
        }

        echo json_encode([
            'success' => true,
            'data' => [
                'status_pesanan' => $order['status_pesanan'],
                'status_bayar' => $order['status_bayar'],
                'items_summary' => $itemsSummary ? $itemsSummary : 'Menu Custom/Lainnya',
                'items' => $itemsList
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Pesanan tidak ditemukan']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan sistem']);
}
?>
