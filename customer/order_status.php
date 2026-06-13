<?php
require_once '../config/db.php';

$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($orderId === 0) {
    die("Pesanan tidak ditemukan.");
}

// Ambil info pesanan awal
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$orderId]);
$order = $stmt->fetch();

if (!$order) {
    die("Pesanan tidak ditemukan.");
}

// Format waktu
$waktuPesan = date('d M Y, H:i', strtotime($order['waktu_pesan']));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Status Pesanan #<?= $orderId ?></title>
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
</head>
<body class="bg-[#FDFBF7] h-screen flex flex-col antialiased selection:bg-brand-500 selection:text-white text-gray-800">

    <header class="bg-white/80 backdrop-blur-md px-6 py-5 border-b border-gray-100 sticky top-0 z-20">
        <h1 class="text-xl font-bold tracking-tight text-gray-900">Order #<?= str_pad($orderId, 5, '0', STR_PAD_LEFT) ?></h1>
        <p class="text-xs text-gray-500 font-medium mt-0.5"><?= $waktuPesan ?></p>
    </header>

    <main class="flex-1 px-6 py-8 flex flex-col items-center">
        
        <!-- Status Indicator Pulse -->
        <div class="relative w-32 h-32 mb-8 flex justify-center items-center">
            <div id="status-pulse" class="absolute inset-0 rounded-full bg-brand-200 opacity-50 animate-pulse transition-snappy"></div>
            <div class="relative z-10 w-24 h-24 bg-white border border-brand-100 rounded-full flex justify-center items-center shadow-[0_10px_40px_rgba(249,115,22,0.1)]">
                <i id="status-icon" data-lucide="clock" class="w-8 h-8 text-brand-500"></i>
            </div>
        </div>

        <h2 id="status-text" class="text-2xl font-bold tracking-tight uppercase mb-2 text-center text-balance text-gray-900">Menunggu Konfirmasi</h2>
        <p id="status-desc" class="text-sm text-gray-500 text-center mb-8 max-w-[250px] text-pretty font-medium">Pesanan Anda telah dikirim ke kasir. Mohon tunggu sebentar.</p>

        <div class="w-full bg-white border border-gray-100 rounded-[24px] p-6 shadow-sm">
            <div class="flex justify-between items-center mb-4 pb-4 border-b border-gray-100">
                <span class="text-sm font-medium text-gray-500">Total Payment</span>
                <span class="text-lg font-bold text-brand-600 tabular-nums">Rp <?= number_format($order['total_harga'], 0, ',', '.') ?></span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm font-medium text-gray-500">Payment Method</span>
                <span class="text-sm font-bold uppercase tracking-widest text-gray-800"><?= htmlspecialchars($order['metode_bayar']) ?></span>
            </div>
            <div class="flex justify-between items-center mt-3">
                <span class="text-sm font-medium text-gray-500">Payment Status</span>
                <span id="payment-status" class="text-[10px] font-bold uppercase tracking-widest bg-brand-50 text-brand-600 px-2.5 py-1 rounded-lg border border-brand-100"><?= $order['status_bayar'] ?></span>
            </div>
        </div>

        <?php if ($order['metode_bayar'] === 'qris' && $order['status_bayar'] === 'unpaid'): ?>
            <div id="qris-info" class="mt-8 w-full flex flex-col items-center">
                <?php if (!empty($order['bukti_transfer'])): ?>
                    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-5 rounded-3xl text-center shadow-sm w-full max-w-sm flex flex-col items-center gap-3">
                        <i data-lucide="check-circle" class="w-8 h-8 text-emerald-600"></i>
                        <div>
                            <p class="text-sm font-bold">Bukti Transfer Dikirim</p>
                            <p class="text-xs text-emerald-700 mt-1 font-medium">Kasir akan segera memverifikasi pembayaran Anda. Mohon tunggu sebentar.</p>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="bg-red-50 border border-red-200 text-red-800 p-5 rounded-3xl text-center shadow-sm w-full max-w-sm flex flex-col items-center gap-3">
                        <i data-lucide="alert-circle" class="w-8 h-8 text-red-600"></i>
                        <div>
                            <p class="text-sm font-bold">Bukti Transfer Belum Diunggah</p>
                            <p class="text-xs text-red-700 mt-1 font-medium mb-4">Anda belum mengunggah bukti transfer untuk pesanan QRIS ini.</p>
                            <a href="qris_payment.php?id=<?= $orderId ?>" class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl text-xs font-bold uppercase tracking-widest transition-snappy shadow-md shadow-red-500/20 inline-flex items-center gap-1.5">
                                <i data-lucide="upload" class="w-3.5 h-3.5"></i> Unggah Bukti
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </main>

    <footer class="p-6 pb-10 flex justify-center">
        <a href="../index.php?meja=<?= $order['meja_id'] ?>" class="px-6 py-3.5 bg-brand-50 text-brand-600 rounded-2xl font-bold uppercase tracking-widest text-[11px] hover:bg-brand-100 transition-colors flex items-center gap-2 border border-brand-100">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Menu
        </a>
    </footer>

    <script>
        lucide.createIcons();

        const orderId = <?= $orderId ?>;
        
        async function pollStatus() {
            try {
                const res = await fetch(`../api/get_order_status.php?order_id=${orderId}`);
                const resData = await res.json();
                if (resData.success) {
                    const status = resData.data.status_pesanan;
                    const payment = resData.data.status_bayar;
                    
                    updateUI(status, payment);
                    
                    if (status !== 'selesai' && status !== 'dibatalkan') {
                        setTimeout(pollStatus, 5000); // Poll tiap 5 detik
                    }
                }
            } catch (e) {
                console.error("Polling error", e);
                setTimeout(pollStatus, 5000);
            }
        }

        function updateUI(status, payment) {
            const statusText = document.getElementById('status-text');
            const statusDesc = document.getElementById('status-desc');
            const pulse = document.getElementById('status-pulse');
            const iconWrap = document.getElementById('status-icon').parentNode;
            const payStatus = document.getElementById('payment-status');

            payStatus.textContent = payment;
            if (payment === 'paid') {
                payStatus.className = "text-[10px] font-bold uppercase tracking-widest bg-emerald-50 text-emerald-600 border border-emerald-200 px-2.5 py-1 rounded-lg";
                const qris = document.getElementById('qris-info');
                if (qris) qris.style.display = 'none';
            }

            // Custom UI berdasarkan status
            if (status === 'pending') {
                statusText.textContent = "Menunggu Konfirmasi";
                statusDesc.textContent = "Pesanan Anda telah dikirim ke kasir.";
                pulse.className = "absolute inset-0 rounded-full bg-brand-200 opacity-50 animate-pulse transition-snappy";
                iconWrap.innerHTML = '<i data-lucide="clock" class="w-8 h-8 text-brand-500"></i>';
            } else if (status === 'diproses') {
                statusText.textContent = "Pesanan Diproses";
                statusDesc.textContent = "Dapur sedang menyiapkan pesanan Anda.";
                pulse.className = "absolute inset-0 rounded-full bg-brand-500 opacity-20 animate-pulse transition-snappy";
                iconWrap.innerHTML = '<i data-lucide="chef-hat" class="w-8 h-8 text-brand-600"></i>';
            } else if (status === 'selesai') {
                statusText.textContent = "Order Complete";
                statusDesc.textContent = "Enjoy your meal! Thank you.";
                pulse.className = "hidden"; // Hentikan pulse
                iconWrap.innerHTML = '<i data-lucide="check" class="w-8 h-8 text-emerald-500"></i>';
                iconWrap.classList.add('bg-emerald-50', 'border-emerald-100');
                iconWrap.classList.remove('border-brand-100');
                removeActiveOrder(orderId);
            } else if (status === 'dibatalkan') {
                statusText.textContent = "Order Cancelled";
                statusDesc.textContent = "Sorry, your order could not be processed.";
                pulse.className = "hidden";
                iconWrap.innerHTML = '<i data-lucide="x" class="w-8 h-8 text-red-500"></i>';
                iconWrap.classList.add('bg-red-50', 'border-red-100');
                iconWrap.classList.remove('border-brand-100');
                removeActiveOrder(orderId);
            }
            lucide.createIcons();
        }

        function removeActiveOrder(id) {
            localStorage.removeItem('active_order_id'); // cleanup old format
            const activeOrdersStr = localStorage.getItem('active_orders');
            if (activeOrdersStr) {
                try {
                    let orders = JSON.parse(activeOrdersStr);
                    orders = orders.filter(oid => oid != id);
                    if (orders.length > 0) {
                        localStorage.setItem('active_orders', JSON.stringify(orders));
                    } else {
                        localStorage.removeItem('active_orders');
                    }
                } catch (e) {}
            }
        }

        // Mulai polling
        pollStatus();
    </script>
</body>
</html>
