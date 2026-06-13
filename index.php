<?php
require_once 'config/db.php';

// Cek param meja
$meja_id = isset($_GET['meja']) ? (int)$_GET['meja'] : 0;
if ($meja_id === 0) {
    header("Location: auth/login.php");
    exit;
}

// Cek apakah meja valid
$stmtMeja = $pdo->prepare("SELECT nomor_meja FROM tables WHERE id = ?");
$stmtMeja->execute([$meja_id]);
$meja = $stmtMeja->fetch();

if (!$meja) {
    die("Meja tidak terdaftar.");
}

// Ambil Promo
$promos = $pdo->query("SELECT * FROM promos WHERE is_active = 1 ORDER BY created_at DESC")->fetchAll();

// Ambil Kategori dan Menu
$categories = $pdo->query("SELECT * FROM categories ORDER BY nama_kategori")->fetchAll();
$menus = $pdo->query("SELECT * FROM menus WHERE status = 'tersedia' ORDER BY nama_menu")->fetchAll();

$menuData = [];
foreach ($menus as $m) {
    $menuData[$m['id']] = $m;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Menu - Meja <?= htmlspecialchars($meja['nomor_meja']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#FFF7ED',
                            100: '#FFEDD5',
                            500: '#F97316',
                            600: '#EA580C',
                            900: '#7C2D12'
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-[#FDFBF7] pb-[120px] antialiased selection:bg-brand-500 selection:text-white" data-meja-id="<?= $meja_id ?>">

    <!-- Glass Header -->
    <header class="sticky top-0 z-40 glass px-5 py-5 flex justify-between items-start flex-col gap-1 rounded-b-3xl">
        <div class="flex justify-between items-center w-full">
            <div class="flex items-center">
                <img src="assets/img/logo.png" alt="Cafe Logo" class="h-6 object-contain">
            </div>
            <div class="bg-white text-brand-600 px-3 py-1.5 rounded-2xl text-xs font-bold flex items-center gap-1.5 shadow-sm border border-gray-100">
                <i data-lucide="map-pin" class="w-3.5 h-3.5"></i> Meja <?= htmlspecialchars($meja['nomor_meja']) ?>
            </div>
        </div>
    </header>

    <?php if(!empty($promos)): ?>
    <!-- Promo Banner Slider (Infinite) -->
    <div class="px-5 mt-4">
        <div class="overflow-hidden rounded-3xl relative" id="promo-slider">
            <div class="flex transition-transform duration-500 ease-in-out" id="promo-track">
                <?php foreach($promos as $promo): ?>
                <div class="w-full shrink-0 aspect-[21/9] relative">
                    <img src="<?= htmlspecialchars($promo['gambar_url']) ?>" alt="<?= htmlspecialchars($promo['judul']) ?>" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent flex items-end p-4">
                        <h3 class="text-white font-bold text-lg"><?= htmlspecialchars($promo['judul']) ?></h3>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <!-- Dot Indicators -->
            <?php if(count($promos) > 1): ?>
            <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-1.5 z-10" id="promo-dots">
                <?php foreach($promos as $i => $p): ?>
                <button class="w-2 h-2 rounded-full transition-all duration-300 <?= $i === 0 ? 'bg-white w-5' : 'bg-white/50' ?>" data-dot="<?= $i ?>"></button>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Menus Grid -->
    <main id="menu-grid" class="px-5 mt-2 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 max-w-7xl mx-auto pb-32 transition-opacity duration-300">
        <?php foreach ($menus as $m): ?>
            <div class="menu-item flex flex-col bg-white rounded-[24px] p-2.5 glass-card transition-snappy relative group" data-cat="<?= $m['kategori_id'] ?>" data-id="<?= $m['id'] ?>">
                <!-- Image -->
                <div class="w-full aspect-square rounded-[18px] bg-gray-50 img-placeholder overflow-hidden relative mb-3">
                    <?php if ($m['gambar_url']): ?>
                        <img src="<?= htmlspecialchars($m['gambar_url']) ?>" alt="<?= htmlspecialchars($m['nama_menu']) ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                    <?php else: ?>
                        <div class="absolute inset-0 flex items-center justify-center text-gray-300">
                            <i data-lucide="image" class="w-8 h-8 opacity-40"></i>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Badges -->
                    <div class="absolute top-2 left-2 flex flex-col gap-1">
                        <?php if($m['is_bestseller']): ?>
                        <span class="bg-brand-600 text-white text-[10px] font-bold px-2 py-1 rounded-lg uppercase tracking-widest shadow-md flex items-center gap-1">
                            <i data-lucide="star" class="w-3 h-3 fill-white"></i> Best Seller
                        </span>
                        <?php endif; ?>
                        <?php if($m['is_new']): ?>
                        <span class="bg-blue-600 text-white text-[10px] font-bold px-2 py-1 rounded-lg uppercase tracking-widest shadow-md flex items-center gap-1">
                            <i data-lucide="sparkles" class="w-3 h-3"></i> New
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Content -->
                <div class="flex-1 flex flex-col px-1 pb-1">
                    <h3 class="font-bold text-[14px] leading-snug text-gray-900 mb-1 line-clamp-2"><?= htmlspecialchars($m['nama_menu']) ?></h3>
                    <p class="text-[11px] text-gray-400 line-clamp-2 mb-3 leading-relaxed font-medium"><?= htmlspecialchars($m['deskripsi']) ?></p>
                    
                    <div class="mt-auto flex justify-between items-center pt-1">
                        <span class="font-bold text-[14px] text-gray-900 tabular-nums tracking-tight">Rp <?= number_format($m['harga'], 0, ',', '.') ?></span>
                        <div id="menu-action-<?= $m['id'] ?>">
                            <button onclick="addToCart(<?= $m['id'] ?>, '<?= addslashes($m['nama_menu']) ?>', <?= $m['harga'] ?>)" class="add-btn w-9 h-9 flex items-center justify-center rounded-[12px] transition-snappy" aria-label="Tambah">
                                <i data-lucide="plus" class="w-5 h-5"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </main>

    <!-- Bottom Fixed Container -->
    <div class="fixed bottom-0 left-0 right-0 z-40 flex flex-col pointer-events-none pb-safe">
        
        <!-- Floating Cart Button -->
        <div id="cart-floater" class="px-5 mb-4 w-full transform translate-y-[150%] opacity-0 transition-snappy flex justify-center pointer-events-none">
            <button onclick="toggleCart()" class="w-full max-w-sm bg-white/85 backdrop-blur-md text-gray-900 border border-white/60 p-1.5 pr-6 pl-1.5 rounded-full flex justify-between items-center shadow-[0_8px_30px_rgba(0,0,0,0.08)] pointer-events-auto active:scale-[0.98] transition-all">
                <div class="flex items-center gap-3">
                    <div class="bg-brand-600 text-white text-[13px] font-bold w-12 h-12 rounded-full flex items-center justify-center shadow-inner" id="cart-count">0</div>
                    <span class="text-[14px] font-bold tracking-wide">Lihat Pesanan</span>
                </div>
                <span class="font-bold tabular-nums text-[15px] text-brand-600" id="cart-total">Rp 0</span>
            </button>
        </div>

        <!-- Categories Filter (Bottom Navbar) -->
        <div class="bg-white/60 backdrop-blur-md border-t border-white/50 shadow-[0_-10px_40px_rgba(0,0,0,0.04)] pointer-events-auto w-full relative">
            
            <div id="category-scroll" class="px-5 py-3 overflow-x-auto whitespace-nowrap hide-scroll relative z-0" style="scroll-behavior: smooth;">
                <div class="flex gap-2.5 pr-5 w-max">
                    <button class="filter-btn active shrink-0 flex items-center justify-center px-6 py-3 bg-brand-600/90 backdrop-blur-sm text-white border border-transparent rounded-2xl text-[13px] font-semibold transition-snappy active:scale-95 shadow-md shadow-brand-500/20" data-cat="all">Semua Menu</button>
                    <?php foreach ($categories as $cat): ?>
                        <button class="filter-btn shrink-0 flex items-center justify-center px-6 py-3 bg-white/50 backdrop-blur-sm border border-white/60 text-gray-700 rounded-2xl text-[13px] font-semibold transition-snappy active:scale-95 shadow-sm" data-cat="<?= $cat['id'] ?>">
                            <?= htmlspecialchars($cat['nama_kategori']) ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
    </div>

    <!-- Swipe Hint Overlay (Centered on Screen) -->
    <div id="swipe-hint" class="hidden opacity-0 fixed inset-0 z-[60] flex items-center justify-center bg-gray-900/40 backdrop-blur-sm pointer-events-none transition-opacity duration-500">
        <div class="flex flex-col items-center gap-3 bg-white px-8 py-6 rounded-3xl shadow-2xl scale-110">
            <div class="flex items-center gap-4 swipe-animation text-brand-600">
                <i data-lucide="arrow-left" class="w-6 h-6 opacity-70"></i>
                <i data-lucide="hand" class="w-12 h-12"></i>
                <i data-lucide="arrow-right" class="w-6 h-6 opacity-70"></i>
            </div>
            <p class="text-[15px] font-bold text-gray-800 tracking-wide mt-2">Geser Kategori Menu</p>
        </div>
    </div>

    <!-- Overlay -->
    <div id="cart-overlay" class="bottom-sheet-overlay" onclick="toggleCart()"></div>

    <!-- Bottom Sheet Cart -->
    <div id="cart-sheet" class="bottom-sheet flex flex-col max-h-[85vh]">
        <div class="drag-handle"></div>
        <div class="flex justify-between items-center px-6 pb-4">
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Pesanan Saya</h2>
            <button onclick="toggleCart()" class="w-8 h-8 flex items-center justify-center bg-gray-100 text-gray-500 rounded-full hover:bg-gray-200 transition-colors">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        
        <div class="flex-1 overflow-y-auto px-6 py-2 hide-scroll" id="cart-items">
            <!-- Rendered by JS -->
        </div>

        <div class="p-6 bg-white border-t border-gray-100 mt-auto rounded-t-[32px] shadow-[0_-10px_20px_rgba(0,0,0,0.02)] relative z-10">
            <div class="flex justify-between items-center mb-3 bg-gray-50/80 p-4 rounded-2xl border border-gray-100">
                <div class="flex items-center gap-3 text-gray-700">
                    <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center shadow-sm">
                        <i data-lucide="wallet" class="w-4 h-4 text-brand-600"></i>
                    </div>
                    <span class="text-[14px] font-bold">Pembayaran</span>
                </div>
                <select id="payment-method" class="payment-select text-right text-[14px]">
                    <option value="cash">Kasir (Tunai)</option>
                    <option value="qris">QRIS (Otomatis)</option>
                </select>
            </div>
            
            <div id="cash-input-container" class="mb-5 bg-gray-50/80 p-4 rounded-2xl border border-gray-100 transition-all duration-300 overflow-hidden">
                <div class="flex justify-between items-center mb-3">
                    <span class="text-[13px] font-bold text-gray-700">Nominal Uang Tunai</span>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" id="uang-pas-checkbox" class="w-4 h-4 text-brand-600 rounded border-gray-300 focus:ring-brand-500 accent-brand-600">
                        <span class="text-[12px] font-bold text-brand-600">Uang Pas</span>
                    </label>
                </div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 font-bold text-[14px]">Rp</span>
                    </div>
                    <input type="number" id="uang-dibayar" placeholder="0" min="0" class="w-full text-[14px] bg-white text-gray-900 rounded-xl py-2.5 pl-10 pr-3 outline-none focus:ring-2 focus:ring-brand-500/50 border border-gray-200 transition-shadow font-bold tabular-nums">
                </div>
            </div>
            <button onclick="submitOrder()" class="w-full py-4 bg-brand-600 text-white rounded-2xl shadow-lg shadow-brand-500/30 active:scale-[0.98] transition-snappy flex justify-between items-center px-6">
                <span class="text-[15px] font-bold">Pesan Sekarang</span>
                <span class="text-[14px] font-bold bg-white/20 px-3 py-1 rounded-xl tracking-wide backdrop-blur-sm border border-white/20" id="sheet-total">Rp 0</span>
            </button>
        </div>
    </div>

    <script>lucide.createIcons();</script>
    <script src="assets/js/app.js?v=<?= time() ?>"></script>
</body>
</html>
