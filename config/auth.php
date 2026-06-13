<?php
require_once __DIR__ . '/env.php';
loadEnv(__DIR__ . '/../.env');

$appUrl = rtrim($_ENV['APP_URL'] ?? ('http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '/' . basename(dirname(__DIR__))), '/');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Memeriksa apakah user sudah login.
 * Jika belum, redirect ke halaman login.
 */
function requireLogin() {
    global $appUrl;
    if (!isset($_SESSION['user_id'])) {
        header("Location: " . $appUrl . "/auth/login.php");
        exit;
    }
}

/**
 * Memeriksa apakah user memiliki role tertentu.
 * Jika tidak, redirect ke halaman unathorized/dashboard.
 * @param string $role 'admin' atau 'kasir'
 */
function requireRole($role) {
    global $appUrl;
    requireLogin();
    if ($_SESSION['user_role'] !== $role) {
        if ($_SESSION['user_role'] === 'admin') {
            header("Location: " . $appUrl . "/admin/index.php");
        } else {
            header("Location: " . $appUrl . "/kasir/index.php");
        }
        exit;
    }
}

/**
 * Mengecek status login saat ini
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Membuat dan mendapatkan token CSRF
 * @return string
 */
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Mencetak input hidden CSRF untuk form
 */
function csrf_field() {
    echo '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token()) . '">';
}

/**
 * Memvalidasi token CSRF dari POST request
 */
function verifyCsrf() {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Invalid CSRF token. Request blocked.");
    }
}

/**
 * Menyimpan flash message ke session
 * @param string $type 'success' atau 'error'
 * @param string $message Pesan yang ingin ditampilkan
 */
function setFlash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Mendapatkan flash message dan langsung menghapusnya
 * @return array|null
 */
function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
?>
