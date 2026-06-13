<?php
require_once '../config/db.php';
require_once '../config/midtrans.php';
header('Content-Type: application/json');

// Menerima input JSON dari JavaScript
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || empty($data['meja_id']) || empty($data['items']) || empty($data['metode_bayar'])) {
    echo json_encode(['success' => false, 'message' => 'Data pesanan tidak lengkap']);
    exit;
}

$mejaId = (int)$data['meja_id'];
$metodeBayar = $data['metode_bayar']; // 'cash' atau 'qris'
$uangDibayar = isset($data['uang_dibayar']) ? $data['uang_dibayar'] : null;
$items = $data['items']; // array of {menu_id, qty, catatan}

try {
    $pdo->beginTransaction();

    $totalHarga = 0;
    $validItems = [];
    $midtransItems = [];

    foreach ($items as $item) {
        $menuId = (int)$item['menu_id'];
        $qty = (int)$item['qty'];
        $catatan = isset($item['catatan']) ? trim($item['catatan']) : '';

        if ($qty <= 0) continue;

        $stmtMenu = $pdo->prepare("SELECT id, nama_menu, harga, status FROM menus WHERE id = ?");
        $stmtMenu->execute([$menuId]);
        $menu = $stmtMenu->fetch();

        if ($menu && $menu['status'] === 'tersedia') {
            $subtotal = $menu['harga'] * $qty;
            $totalHarga += $subtotal;
            
            $validItems[] = [
                'menu_id' => $menu['id'],
                'qty' => $qty,
                'subtotal' => $subtotal,
                'catatan' => $catatan
            ];

            // Item detail untuk Midtrans
            $midtransItems[] = [
                'id' => (string)$menu['id'],
                'price' => (int)$menu['harga'],
                'quantity' => $qty,
                'name' => mb_strimwidth($menu['nama_menu'], 0, 50, '...') // limit 50 chars
            ];
        }
    }

    if (empty($validItems)) {
        throw new Exception("Semua item pesanan tidak valid atau stok habis.");
    }

    // Cek Shift Kasir Aktif
    $stmtShift = $pdo->query("SELECT id FROM shifts WHERE status = 'active' ORDER BY start_time ASC LIMIT 1");
    $activeShift = $stmtShift->fetch();
    
    if (!$activeShift) {
        throw new Exception("Mohon maaf, kasir sedang offline. Pesanan tidak dapat diproses saat ini.");
    }
    
    $shiftId = $activeShift['id'];

    // Insert ke tabel orders
    $stmtOrder = $pdo->prepare("INSERT INTO orders (meja_id, shift_id, total_harga, uang_dibayar, metode_bayar, status_bayar, status_pesanan) VALUES (?, ?, ?, ?, ?, 'unpaid', 'pending')");
    $stmtOrder->execute([$mejaId, $shiftId, $totalHarga, $uangDibayar, $metodeBayar]);
    $orderId = $pdo->lastInsertId();

    // Insert ke tabel order_details
    $stmtDetail = $pdo->prepare("INSERT INTO order_details (order_id, menu_id, qty, subtotal, catatan) VALUES (?, ?, ?, ?, ?)");
    foreach ($validItems as $validItem) {
        $stmtDetail->execute([
            $orderId, 
            $validItem['menu_id'], 
            $validItem['qty'], 
            $validItem['subtotal'], 
            $validItem['catatan']
        ]);
    }

    // Metode bayar QRIS sekarang menggunakan gambar statis dari admin, tidak perlu panggil Midtrans
    // Pelanggan akan diarahkan ke halaman upload bukti transfer di customer/qris_payment.php
    $qrisUrl = null;
    $midtransOrderId = null;

    $pdo->commit();

    echo json_encode([
        'success' => true, 
        'order_id' => $orderId,
        'message' => 'Pesanan berhasil dibuat'
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Gagal membuat pesanan: ' . $e->getMessage()]);
}
?>
