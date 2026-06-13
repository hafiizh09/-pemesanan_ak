<?php
require_once '../config/auth.php';
require_once '../config/db.php';
requireRole('admin');

// Statistik Hari Ini
$today = date('Y-m-d');
$stmtSales = $pdo->prepare("SELECT SUM(total_harga) as total_sales, COUNT(id) as total_orders FROM orders WHERE DATE(waktu_pesan) = ? AND status_bayar = 'paid'");
$stmtSales->execute([$today]);
$stats = $stmtSales->fetch();
$totalSales = $stats['total_sales'] ?? 0;
$totalOrders = $stats['total_orders'] ?? 0;

$stmtMenus = $pdo->query("SELECT COUNT(id) FROM menus");
$totalMenus = $stmtMenus->fetchColumn();

// Ambil meja pertama untuk preview
$stmtFirstTable = $pdo->query("SELECT id FROM tables ORDER BY CAST(nomor_meja AS UNSIGNED) LIMIT 1");
$firstTable = $stmtFirstTable->fetch();
$previewUrl = $firstTable ? '../index.php?meja=' . $firstTable['id'] : '#';
?>
<?php
$title = 'Dashboard';
require_once 'layout_header.php';
?>
<header class="bg-transparent px-5 md:px-8 py-6 md:py-8 flex flex-col sm:flex-row sm:justify-between items-start sm:items-end gap-4 sm:gap-0">
    <div>
        <h2 class="text-3xl font-bold tracking-tight text-gray-900">Overview</h2>
        <p class="text-sm text-gray-500 mt-1 font-medium">Ringkasan performa kafe Anda hari ini.</p>
    </div>
    <?php if ($firstTable): ?>
    <a href="<?= $previewUrl ?>" target="_blank" class="flex items-center gap-2 px-6 py-3 bg-white border border-gray-200 rounded-2xl text-[13px] font-bold text-gray-700 shadow-sm hover:border-brand-300 hover:text-brand-600 transition-snappy">
        <i data-lucide="smartphone" class="w-4 h-4"></i> Preview Menu
    </a>
    <?php endif; ?>
</header>

<div class="px-5 md:px-8 pb-8 grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6">
    <div class="bg-white rounded-[24px] p-6 flex flex-col shadow-sm border border-gray-100 relative overflow-hidden group">
        <div class="absolute top-0 right-0 p-6 opacity-5 group-hover:opacity-10 transition-opacity">
            <i data-lucide="wallet" class="w-24 h-24 text-brand-600"></i>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-brand-50 flex items-center justify-center mb-6">
            <i data-lucide="wallet" class="w-6 h-6 text-brand-600"></i>
        </div>
        <span class="text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-1">Pendapatan Hari Ini</span>
        <span class="text-3xl font-bold tracking-tight text-gray-900 tabular-nums">Rp <?= number_format($totalSales, 0, ',', '.') ?></span>
    </div>
    
    <div class="bg-white rounded-[24px] p-6 flex flex-col shadow-sm border border-gray-100 relative overflow-hidden group">
        <div class="absolute top-0 right-0 p-6 opacity-5 group-hover:opacity-10 transition-opacity">
            <i data-lucide="shopping-bag" class="w-24 h-24 text-blue-600"></i>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center mb-6">
            <i data-lucide="shopping-bag" class="w-6 h-6 text-blue-600"></i>
        </div>
        <span class="text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-1">Total Pesanan</span>
        <span class="text-3xl font-bold tracking-tight text-gray-900 tabular-nums"><?= $totalOrders ?></span>
    </div>

    <div class="bg-white rounded-[24px] p-6 flex flex-col shadow-sm border border-gray-100 relative overflow-hidden group">
        <div class="absolute top-0 right-0 p-6 opacity-5 group-hover:opacity-10 transition-opacity">
            <i data-lucide="coffee" class="w-24 h-24 text-emerald-600"></i>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center mb-6">
            <i data-lucide="coffee" class="w-6 h-6 text-emerald-600"></i>
        </div>
        <span class="text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-1">Menu Aktif</span>
        <span class="text-3xl font-bold tracking-tight text-gray-900 tabular-nums"><?= $totalMenus ?></span>
    </div>
</div>
<?php require_once 'layout_footer.php'; ?>
