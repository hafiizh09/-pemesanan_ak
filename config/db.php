<?php
require_once __DIR__ . '/env.php';
loadEnv(__DIR__ . '/../.env');

$host = $_ENV['DB_HOST'] ?? 'localhost';
$dbname = $_ENV['DB_NAME'] ?? 'pemesanan_ak';
$username = $_ENV['DB_USER'] ?? 'root';
$password = $_ENV['DB_PASS'] ?? '';

try {
    // Buat koneksi PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    
    // Set PDO error mode ke exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Set default fetch mode ke associative array
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Menghindari Information Disclosure (Kebocoran struktur server/DB jika error)
    // Sebaiknya $e->getMessage() di-log ke file internal, bukan ditampilkan ke layar
    error_log("Database Connection Error: " . $e->getMessage());
    die("Maaf, sedang ada gangguan pada server database.");
}
?>
