<?php
require_once '../config/auth.php';
require_once '../config/db.php';
requireRole('admin');

$tab = $_GET['tab'] ?? 'daily';
$dateFilter = $_GET['date'] ?? date('Y-m-d');

if ($tab === 'daily') {
    $stmt = $pdo->prepare("
        SELECT o.id, t.nomor_meja, o.waktu_pesan, o.total_harga, o.metode_bayar 
        FROM orders o 
        JOIN tables t ON o.meja_id = t.id 
        WHERE DATE(o.waktu_pesan) = ? AND o.status_bayar = 'paid' AND o.status_pesanan = 'selesai'
        ORDER BY o.waktu_pesan DESC
    ");
    $stmt->execute([$dateFilter]);
    $fetchedReports = $stmt->fetchAll();
    $totalDailySales = array_sum(array_column($fetchedReports, 'total_harga'));
} elseif ($tab === 'shifts') {
    $stmt = $pdo->prepare("
        SELECT s.id, u.username as kasir_name, s.start_time, s.end_time, s.status, s.total_sales
        FROM shifts s
        JOIN users u ON s.kasir_id = u.id
        WHERE DATE(s.start_time) = ?
        ORDER BY s.start_time DESC
    ");
    $stmt->execute([$dateFilter]);
    $fetchedShifts = $stmt->fetchAll();
    $totalShiftSales = array_sum(array_column($fetchedShifts, 'total_sales'));
}
?>
<?php
$title = 'Reports';
require_once 'layout_header.php';
?>
<div class="p-4 md:p-8">
    <div class="bg-white rounded-[24px] shadow-sm border border-gray-100 p-5 md:p-8">
        <div class="flex flex-col sm:flex-row sm:justify-between items-start sm:items-center gap-4 sm:gap-0 mb-6 md:mb-8">
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">Laporan Sistem</h1>
        </div>

        <div class="flex gap-4 mb-8 border-b border-gray-100 pb-6">
            <a href="?tab=daily&date=<?= htmlspecialchars($dateFilter) ?>" class="px-6 py-2.5 rounded-2xl text-xs font-bold uppercase tracking-widest transition-snappy <?= $tab === 'daily' ? 'bg-brand-600 text-white shadow-md shadow-brand-500/20' : 'bg-gray-50 text-gray-500 hover:bg-brand-50 hover:text-brand-600' ?>">Penjualan Harian</a>
            <a href="?tab=shifts&date=<?= htmlspecialchars($dateFilter) ?>" class="px-6 py-2.5 rounded-2xl text-xs font-bold uppercase tracking-widest transition-snappy <?= $tab === 'shifts' ? 'bg-brand-600 text-white shadow-md shadow-brand-500/20' : 'bg-gray-50 text-gray-500 hover:bg-brand-50 hover:text-brand-600' ?>">Shift Kasir</a>
        </div>

        <form method="GET" class="flex flex-col sm:flex-row sm:items-center gap-4 mb-8 border-b border-gray-100 pb-8">
            <input type="hidden" name="tab" value="<?= htmlspecialchars($tab) ?>">
            <div class="bg-gray-50 rounded-2xl px-5 py-3 border border-gray-100 focus-within:border-brand-500 focus-within:bg-white transition-colors">
                <label class="text-[10px] uppercase tracking-widest font-bold text-gray-500 block mb-1">Pilih Tanggal</label>
                <input type="date" name="date" value="<?= htmlspecialchars($dateFilter) ?>" class="bg-transparent border-none focus:outline-none text-sm font-bold text-gray-900 cursor-pointer">
            </div>
            <button type="submit" class="px-6 py-4 mt-4 bg-brand-600 text-white rounded-2xl text-xs font-bold uppercase tracking-widest hover:bg-brand-700 transition-snappy shadow-md shadow-brand-500/20 flex items-center justify-center gap-2">
                <i data-lucide="filter" class="w-4 h-4"></i> Terapkan Filter
            </button>
        </form>

        <?php if ($tab === 'daily'): ?>
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 sm:gap-0 bg-brand-50/50 border border-brand-100 rounded-[20px] p-5 sm:p-6 shadow-sm text-center sm:text-left">
            <div>
                <span class="block text-xs font-bold uppercase tracking-widest text-brand-600 mb-1">Total Penjualan</span>
                <span class="text-sm font-medium text-gray-500"><?= date('d F Y', strtotime($dateFilter)) ?></span>
            </div>
            <span class="text-3xl font-bold tracking-tight text-gray-900">Rp <?= number_format($totalDailySales, 0, ',', '.') ?></span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm border-collapse min-w-[600px]">
                <thead>
                    <tr class="border-b-2 border-gray-100">
                        <th class="pb-4 font-bold text-gray-400 uppercase tracking-widest text-[11px]">ID Pesanan</th>
                        <th class="pb-4 font-bold text-gray-400 uppercase tracking-widest text-[11px]">Meja</th>
                        <th class="pb-4 font-bold text-gray-400 uppercase tracking-widest text-[11px]">Waktu</th>
                        <th class="pb-4 font-bold text-gray-400 uppercase tracking-widest text-[11px]">Metode</th>
                        <th class="pb-4 font-bold text-gray-400 uppercase tracking-widest text-[11px] text-right">Nominal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php foreach ($fetchedReports as $reportData): ?>
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="py-4 font-medium text-gray-900">#<?= $reportData['id'] ?></td>
                        <td class="py-4 font-bold text-gray-900">Meja <?= htmlspecialchars($reportData['nomor_meja']) ?></td>
                        <td class="py-4 text-gray-500"><?= date('H:i', strtotime($reportData['waktu_pesan'])) ?></td>
                        <td class="py-4">
                            <span class="px-3 py-1 text-[10px] uppercase font-bold tracking-widest rounded-lg <?= $reportData['metode_bayar'] === 'cash' ? 'bg-emerald-50 text-emerald-600' : 'bg-blue-50 text-blue-600' ?>">
                                <?= $reportData['metode_bayar'] ?>
                            </span>
                        </td>
                        <td class="py-4 text-right font-bold text-brand-600">Rp <?= number_format($reportData['total_harga'], 0, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (!$fetchedReports): ?>
                    <tr><td colspan="5" class="py-12 text-center text-gray-400 font-medium bg-gray-50/50 rounded-b-[20px]">Belum ada penjualan di tanggal ini.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php elseif ($tab === 'shifts'): ?>
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 sm:gap-0 bg-brand-50/50 border border-brand-100 rounded-[20px] p-5 sm:p-6 shadow-sm text-center sm:text-left">
            <div>
                <span class="block text-xs font-bold uppercase tracking-widest text-brand-600 mb-1">Total Setoran Kasir</span>
                <span class="text-sm font-medium text-gray-500"><?= date('d F Y', strtotime($dateFilter)) ?></span>
            </div>
            <span class="text-3xl font-bold tracking-tight text-gray-900">Rp <?= number_format($totalShiftSales, 0, ',', '.') ?></span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm border-collapse min-w-[700px]">
                <thead>
                    <tr class="border-b-2 border-gray-100">
                        <th class="pb-4 font-bold text-gray-400 uppercase tracking-widest text-[11px]">ID Shift</th>
                        <th class="pb-4 font-bold text-gray-400 uppercase tracking-widest text-[11px]">Kasir</th>
                        <th class="pb-4 font-bold text-gray-400 uppercase tracking-widest text-[11px]">Mulai</th>
                        <th class="pb-4 font-bold text-gray-400 uppercase tracking-widest text-[11px]">Selesai</th>
                        <th class="pb-4 font-bold text-gray-400 uppercase tracking-widest text-[11px]">Status</th>
                        <th class="pb-4 font-bold text-gray-400 uppercase tracking-widest text-[11px] text-right">Total Setoran</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php foreach ($fetchedShifts as $shiftData): ?>
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="py-4 font-medium text-gray-900">#<?= $shiftData['id'] ?></td>
                        <td class="py-4 font-bold text-gray-900 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-brand-100 flex items-center justify-center text-brand-600 font-bold text-xs">
                                <?= strtoupper(substr($shiftData['kasir_name'], 0, 1)) ?>
                            </div>
                            <?= htmlspecialchars($shiftData['kasir_name']) ?>
                        </td>
                        <td class="py-4 text-gray-500"><?= date('H:i', strtotime($shiftData['start_time'])) ?></td>
                        <td class="py-4 text-gray-500"><?= $shiftData['end_time'] ? date('H:i', strtotime($shiftData['end_time'])) : '-' ?></td>
                        <td class="py-4">
                            <span class="px-3 py-1 text-[10px] uppercase font-bold tracking-widest rounded-lg <?= $shiftData['status'] === 'active' ? 'bg-emerald-50 text-emerald-600' : 'bg-gray-100 text-gray-500' ?>">
                                <?= $shiftData['status'] ?>
                            </span>
                        </td>
                        <td class="py-4 text-right font-bold text-brand-600 text-base">Rp <?= number_format($shiftData['total_sales'], 0, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (!$fetchedShifts): ?>
                    <tr><td colspan="6" class="py-12 text-center text-gray-400 font-medium bg-gray-50/50 rounded-b-[20px]">Tidak ada data shift kasir di tanggal ini.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

    </div>
</div>
<?php require_once 'layout_footer.php'; ?>
