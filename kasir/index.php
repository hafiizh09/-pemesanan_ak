<?php
require_once '../config/auth.php';
require_once '../config/db.php';
requireRole('kasir');

$kasir_id = $_SESSION['user_id'];

// Proses POST Shift
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    if (isset($_POST['start_shift'])) {
        // Cek apakah sudah ada shift aktif
        $stmt = $pdo->prepare("SELECT id FROM shifts WHERE kasir_id = ? AND status = 'active'");
        $stmt->execute([$kasir_id]);
        if (!$stmt->fetch()) {
            $pdo->prepare("INSERT INTO shifts (kasir_id) VALUES (?)")->execute([$kasir_id]);
        }
    } elseif (isset($_POST['end_shift'])) {
        // Cari shift aktif
        $stmt = $pdo->prepare("SELECT id FROM shifts WHERE kasir_id = ? AND status = 'active'");
        $stmt->execute([$kasir_id]);
        $isActiveShift = $stmt->fetch();
        
        if ($isActiveShift) {
            $shift_id = $isActiveShift['id'];
            
            // Hitung total sales dari pesanan yang selesai dan paid pada shift ini
            $stmtSales = $pdo->prepare("SELECT SUM(total_harga) FROM orders WHERE shift_id = ? AND status_bayar = 'paid' AND status_pesanan = 'selesai'");
            $stmtSales->execute([$shift_id]);
            $calculatedTotalSales = (float)$stmtSales->fetchColumn();
            
            // Tutup shift
            $stmtClose = $pdo->prepare("UPDATE shifts SET end_time = CURRENT_TIMESTAMP, total_sales = ?, status = 'completed' WHERE id = ?");
            $stmtClose->execute([$calculatedTotalSales, $shift_id]);
        }
    }
    header("Location: index.php");
    exit;
}

// Cek status shift saat ini
$stmt = $pdo->prepare("SELECT * FROM shifts WHERE kasir_id = ? AND status = 'active'");
$stmt->execute([$kasir_id]);
$isActiveShift = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kasir - System.</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: { 50: '#FFF7ED', 100: '#FFEDD5', 200: '#FED7AA', 500: '#F97316', 600: '#EA580C', 900: '#7C2D12' },
                        neutral: { 50: '#FDFBF7', 100: '#F5F5F5', 200: '#E5E5E5', 800: '#262626', 900: '#171717' }
                    }
                }
            }
        }
    </script>
    <?php
    $audioDir = '../assets/audio/';
    $audioFiles = glob($audioDir . 'notification.*');
    $currentAudio = !empty($audioFiles) ? $audioFiles[0] . '?v=' . filemtime($audioFiles[0]) : '';
    ?>
    <script>
        window.NOTIFICATION_AUDIO_URL = "<?= $currentAudio ?>";
    </script>
</head>
<body class="bg-[#FDFBF7] h-screen flex overflow-hidden antialiased selection:bg-brand-500 selection:text-white text-gray-800">

    <!-- Mobile Overlay -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-40 hidden md:hidden transition-opacity" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <aside id="sidebar" class="w-[260px] bg-white border-r border-gray-100 flex flex-col shadow-[4px_0_24px_rgba(0,0,0,0.02)] z-50 fixed inset-y-0 left-0 transform -translate-x-full md:relative md:translate-x-0 transition-transform duration-300 ease-in-out">
        <div class="p-6 border-b border-gray-50 flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-brand-500 to-brand-600 flex items-center justify-center shadow-lg shadow-brand-500/30">
                <i data-lucide="coffee" class="w-6 h-6 text-white"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold tracking-tight text-gray-900 leading-none">Pemesanan</h1>
                <p class="text-[10px] uppercase tracking-widest text-brand-600 font-bold mt-1">Kasir Panel</p>
            </div>
        </div>
        <nav class="flex-1 p-4 space-y-2">
            <a href="#" class="flex items-center gap-3 px-4 py-3.5 bg-brand-600 text-white rounded-2xl text-sm font-semibold transition-snappy shadow-md shadow-brand-500/20">
                <i data-lucide="bell-ring" class="w-4.5 h-4.5"></i>
                Live Orders
            </a>
        </nav>
        <div class="p-5 border-t border-gray-50 bg-gray-50/50">
            <div class="flex items-center gap-3 bg-white p-2 rounded-2xl shadow-sm border border-gray-100">
                <div class="w-10 h-10 rounded-[14px] <?= $isActiveShift ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-400' ?> flex items-center justify-center font-bold relative">
                    <i data-lucide="user" class="w-4 h-4"></i>
                    <?php if ($isActiveShift): ?>
                    <span class="absolute top-0 right-0 w-3 h-3 bg-emerald-500 border-2 border-white rounded-full"></span>
                    <?php endif; ?>
                </div>
                <div class="flex-1 overflow-hidden">
                    <p class="text-xs font-bold text-gray-900 truncate"><?= htmlspecialchars($_SESSION['username']) ?></p>
                    <p class="text-[10px] uppercase tracking-wider <?= $isActiveShift ? 'text-emerald-500 font-bold' : 'text-gray-400' ?>"><?= $isActiveShift ? 'Online' : 'Offline' ?></p>
                </div>
                <a href="../auth/logout.php" class="w-8 h-8 flex items-center justify-center rounded-xl text-red-500 hover:bg-red-50 transition-colors mr-1" title="Logout">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                </a>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-full relative min-w-0">
        <header class="bg-white/80 backdrop-blur-md border-b border-gray-100 px-5 md:px-8 py-4 md:py-5 flex justify-between items-center z-10 sticky top-0">
            <div class="flex items-center gap-3">
                <button onclick="toggleSidebar()" class="md:hidden p-2 -ml-2 text-gray-600 hover:bg-gray-100 rounded-xl transition-colors">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>
                <h2 class="text-xl font-bold tracking-tight text-gray-900">Live Orders</h2>
            </div>
            
            <div class="flex gap-2 md:gap-4">
                <?php if ($isActiveShift): ?>
                <button id="enable-audio" onclick="enableAudio()" class="text-[11px] px-3 md:px-5 py-2.5 border border-gray-200 text-gray-600 rounded-xl font-bold uppercase tracking-widest hover:border-brand-600 hover:text-brand-600 transition-snappy flex items-center gap-2">
                    <i data-lucide="volume-2" class="w-3.5 h-3.5"></i> <span class="hidden sm:inline">Enable Audio</span>
                </button>
                <form method="POST" action="index.php" onsubmit="return confirm('Anda yakin ingin mengakhiri shift dan merekap penjualan?');">
                    <?= csrf_field() ?>
                    <button type="submit" name="end_shift" class="text-[11px] px-3 md:px-5 py-2.5 bg-red-50 text-red-600 rounded-xl font-bold uppercase tracking-widest hover:bg-red-100 transition-snappy flex items-center gap-2">
                        <i data-lucide="power" class="w-3.5 h-3.5"></i> <span class="hidden sm:inline">End Shift</span>
                    </button>
                </form>
                <?php endif; ?>
            </div>
        </header>

        <div class="flex-1 overflow-auto p-5 md:p-8 relative">
            <?php if (!$isActiveShift): ?>
            <!-- Layar Terkunci saat Offline -->
            <div class="absolute inset-0 bg-[#FDFBF7]/90 backdrop-blur-sm z-20 flex flex-col items-center justify-center">
                <div class="w-24 h-24 bg-brand-50 rounded-[32px] flex items-center justify-center mb-6 shadow-inner relative overflow-hidden group">
                    <div class="absolute inset-0 bg-brand-100 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <i data-lucide="coffee" class="w-10 h-10 text-brand-600 relative z-10"></i>
                </div>
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 mb-2">Shift Kasir Ditutup</h2>
                <p class="text-gray-500 mb-8 max-w-sm text-center font-medium leading-relaxed">Anda harus memulai shift untuk mulai menerima pesanan dari pelanggan.</p>
                <form method="POST" action="index.php">
                    <?= csrf_field() ?>
                    <button type="submit" name="start_shift" class="px-10 py-4 bg-brand-600 text-white rounded-2xl font-bold uppercase tracking-widest hover:bg-brand-700 transition-snappy shadow-xl shadow-brand-500/30 flex items-center gap-3">
                        <i data-lucide="play" class="w-5 h-5"></i> Mulai Shift Saya
                    </button>
                </form>
            </div>
            <?php endif; ?>

            <div id="orders-container" class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6 auto-rows-max">
                <!-- Data pesanan akan di-render di sini oleh kasir.js -->
                <div class="col-span-full flex flex-col items-center justify-center text-gray-400 h-[60vh]">
                    <div class="w-20 h-20 rounded-full bg-white border-2 border-dashed border-gray-200 flex items-center justify-center mb-4">
                        <i data-lucide="inbox" class="w-8 h-8 text-gray-300"></i>
                    </div>
                    <p class="text-sm font-medium tracking-wide">Menunggu pesanan baru masuk...</p>
                </div>
            </div>
        </div>
    </main>

    <script>
        lucide.createIcons();
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const isOpen = !sidebar.classList.contains('-translate-x-full');
            
            if (isOpen) {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            } else {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
            }
        }
    </script>
    <audio id="alert-sound" preload="auto">
        <?php if ($currentAudio): ?>
        <source src="<?= $currentAudio ?>" type="audio/mpeg">
        <?php else: ?>
        <source src="data:audio/mp3;base64,//NExAAAAANIAAAAAExBTUUzLjEwMKqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqq//NExAAAAANIAAAAAExBTUUzLjEwMKqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqq" type="audio/mpeg">
        <?php endif; ?>
    </audio>
    <script src="../assets/js/kasir.js?v=<?= time() ?>"></script>
</body>
</html>
