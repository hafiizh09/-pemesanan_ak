<?php
require_once '../config/auth.php';
require_once '../config/db.php';

header('Content-Type: application/json');

// Hanya admin yang boleh
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Akses ditolak']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$menuId = isset($input['menu_id']) ? (int)$input['menu_id'] : 0;

if (!$menuId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID menu tidak valid']);
    exit;
}

try {
    // Ambil status saat ini
    $stmt = $pdo->prepare("SELECT status FROM menus WHERE id = ?");
    $stmt->execute([$menuId]);
    $menu = $stmt->fetch();

    if (!$menu) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Menu tidak ditemukan']);
        exit;
    }

    // Toggle: tersedia <-> habis
    $newStatus = ($menu['status'] === 'tersedia') ? 'habis' : 'tersedia';

    $update = $pdo->prepare("UPDATE menus SET status = ? WHERE id = ?");
    $update->execute([$newStatus, $menuId]);

    echo json_encode([
        'success' => true,
        'new_status' => $newStatus,
        'message' => 'Status menu berhasil diubah menjadi ' . $newStatus
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Gagal mengubah status menu']);
}
