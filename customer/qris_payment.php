<?php
require_once '../config/db.php';

$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($orderId === 0) {
    die("Pesanan tidak ditemukan.");
}

// Ambil info pesanan
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$orderId]);
$order = $stmt->fetch();

if (!$order) {
    die("Pesanan tidak ditemukan.");
}

// Jika pesanan sudah dibayar atau bukan metode QRIS, langsung arahkan ke halaman status
if ($order['metode_bayar'] !== 'qris' || $order['status_bayar'] === 'paid') {
    header("Location: order_status.php?id=" . $orderId);
    exit;
}

$error = '';
$qrisPath = '../assets/uploads/qris.webp';
$hasQris = file_exists($qrisPath);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['bukti_transfer']) && $_FILES['bukti_transfer']['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['bukti_transfer']['tmp_name'];
        $fileName = $_FILES['bukti_transfer']['name'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        $allowedExts = ['jpg', 'jpeg', 'png', 'webp'];
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
        
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $fileTmp);
        finfo_close($finfo);
        
        if (in_array($fileExt, $allowedExts) && in_array($mime, $allowedMimes)) {
            $destDir = '../assets/uploads/bukti_transfer/';
            if (!is_dir($destDir)) {
                mkdir($destDir, 0755, true);
            }
            
            $newFilename = $orderId . '_' . bin2hex(random_bytes(8)) . '.webp';
            $destPath = $destDir . $newFilename;
            
            require_once '../config/image_helper.php';
            if (convertToWebp($fileTmp, $destPath)) {
                $dbPath = 'assets/uploads/bukti_transfer/' . $newFilename;
                
                // Update bukti transfer ke database
                $stmtUpd = $pdo->prepare("UPDATE orders SET bukti_transfer = ? WHERE id = ?");
                $stmtUpd->execute([$dbPath, $orderId]);
                
                header("Location: order_status.php?id=" . $orderId);
                exit;
            } else {
                $error = 'Gagal memproses gambar bukti transfer.';
            }
        } else {
            $error = 'Format file tidak didukung. Harap gunakan gambar JPG, PNG, atau WEBP.';
        }
    } else {
        $error = 'Silakan pilih gambar bukti transfer terlebih dahulu.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Pembayaran QRIS #<?= $orderId ?></title>
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
<body class="bg-[#FDFBF7] min-h-screen flex flex-col antialiased selection:bg-brand-500 selection:text-white text-gray-800 pb-12">

    <header class="bg-white/80 backdrop-blur-md px-6 py-5 border-b border-gray-100 sticky top-0 z-20">
        <h1 class="text-xl font-bold tracking-tight text-gray-900">Pembayaran QRIS</h1>
        <p class="text-xs text-gray-500 font-medium mt-0.5">Order #<?= str_pad($orderId, 5, '0', STR_PAD_LEFT) ?></p>
    </header>

    <main class="flex-1 px-6 py-8 flex flex-col items-center max-w-md mx-auto w-full">
        <!-- Total Tagihan -->
        <div class="w-full bg-brand-50 border border-brand-100 rounded-3xl p-5 text-center mb-6 shadow-sm">
            <span class="text-[11px] font-bold uppercase tracking-widest text-brand-600 mb-1 block">Total Tagihan</span>
            <span class="text-3xl font-black text-brand-700 tabular-nums">Rp <?= number_format($order['total_harga'], 0, ',', '.') ?></span>
        </div>

        <!-- Tampilan QRIS -->
        <div class="w-full bg-white border border-gray-100 rounded-3xl p-6 shadow-sm flex flex-col items-center mb-6">
            <?php if ($hasQris): ?>
                <div class="bg-white p-2 border border-gray-100 rounded-2xl shadow-sm mb-4">
                    <img src="<?= $qrisPath ?>?v=<?= filemtime($qrisPath) ?>" alt="QRIS Code" class="w-48 h-48 object-contain">
                </div>
                <p class="text-center text-xs text-gray-400 font-medium leading-relaxed max-w-[280px]">Pindai kode QRIS di atas dengan aplikasi e-wallet atau mobile banking pilihan Anda untuk menyelesaikan pembayaran.</p>
            <?php else: ?>
                <div class="flex flex-col items-center justify-center py-8 text-yellow-600 text-center">
                    <i data-lucide="alert-triangle" class="w-12 h-12 mb-3"></i>
                    <p class="text-sm font-bold">QRIS Belum Tersedia</p>
                    <p class="text-xs text-gray-500 mt-1">Gambar QRIS toko belum dikonfigurasi oleh admin. Silakan tunjukkan pesanan Anda ke kasir untuk melakukan pembayaran manual.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Form Bukti Transfer -->
        <form action="" method="POST" enctype="multipart/form-data" class="w-full space-y-5">
            <?php if ($error): ?>
                <div class="bg-red-50 text-red-600 p-4 rounded-2xl text-xs font-semibold border border-red-100">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <div class="bg-white border border-gray-100 rounded-3xl p-6 shadow-sm">
                <label class="block text-xs uppercase tracking-widest text-gray-400 font-bold mb-3">Unggah Bukti Transfer</label>
                <div class="flex items-center justify-center w-full">
                    <label for="bukti-file-input" class="flex flex-col items-center justify-center w-full h-36 border-2 border-gray-200 border-dashed rounded-2xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6 px-4 text-center">
                            <i data-lucide="image" class="w-8 h-8 text-gray-400 mb-2"></i>
                            <p class="mb-1 text-xs text-gray-600"><span class="font-semibold text-brand-600">Klik untuk upload bukti</span></p>
                            <p class="text-[10px] text-gray-400 mt-0.5">Format: JPG, PNG, WEBP (Max. 5MB)</p>
                        </div>
                        <input id="bukti-file-input" type="file" name="bukti_transfer" class="hidden" accept=".jpg,.jpeg,.png,.webp" required onchange="handleFileSelect(this)" />
                    </label>
                </div>
                
                <!-- Preview area -->
                <div id="preview-container" class="hidden mt-4 pt-4 border-t border-gray-100 flex flex-col items-center">
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-2 align-self-start">Pratinjau Bukti:</p>
                    <img id="image-preview" src="#" alt="Pratinjau Bukti Transfer" class="max-h-48 rounded-xl object-contain shadow-sm border border-gray-100">
                </div>
            </div>

            <button type="submit" class="w-full py-4 bg-brand-600 text-white rounded-2xl shadow-lg shadow-brand-500/20 active:scale-[0.98] transition-snappy flex justify-center items-center gap-2 font-bold text-sm uppercase tracking-widest">
                <i data-lucide="check" class="w-4 h-4"></i> Selesai
            </button>
        </form>
    </main>

    <script>
        lucide.createIcons();

        function handleFileSelect(input) {
            const previewContainer = document.getElementById('preview-container');
            const previewImage = document.getElementById('image-preview');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewContainer.classList.remove('hidden');
                }
                
                reader.readAsDataURL(input.files[0]);
            } else {
                previewContainer.classList.add('hidden');
            }
        }
    </script>
</body>
</html>
