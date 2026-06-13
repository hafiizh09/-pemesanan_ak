<?php
$activePage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title : 'Admin Panel' ?> - System.</title>
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
<body class="bg-[#FDFBF7] h-screen flex overflow-hidden antialiased selection:bg-brand-500 selection:text-white text-gray-800">

    <!-- Mobile Overlay -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-40 hidden md:hidden transition-opacity" onclick="toggleAdminSidebar()"></div>

    <!-- Admin Sidebar -->
    <aside id="admin-sidebar" class="w-[260px] bg-white border-r border-gray-100 flex flex-col shadow-[4px_0_24px_rgba(0,0,0,0.02)] z-50 fixed inset-y-0 left-0 transform -translate-x-full md:relative md:translate-x-0 transition-transform duration-300 ease-in-out">
        <div class="p-6 border-b border-gray-50 flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-brand-500 to-brand-600 flex items-center justify-center shadow-lg shadow-brand-500/30">
                <i data-lucide="coffee" class="w-6 h-6 text-white"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold tracking-tight text-gray-900 leading-none">Pemesanan</h1>
                <p class="text-[10px] uppercase tracking-widest text-brand-600 font-bold mt-1">Admin Panel</p>
            </div>
        </div>
        <nav class="flex-1 p-4 space-y-1 overflow-y-auto hide-scroll">
            <a href="index.php" class="flex items-center gap-3 px-4 py-3.5 rounded-2xl text-sm font-semibold transition-snappy <?= $activePage == 'index.php' ? 'bg-brand-600 text-white shadow-md shadow-brand-500/20' : 'text-gray-500 hover:bg-brand-50 hover:text-brand-600' ?>">
                <i data-lucide="layout-dashboard" class="w-4.5 h-4.5"></i> Dashboard
            </a>
            <a href="categories.php" class="flex items-center gap-3 px-4 py-3.5 rounded-2xl text-sm font-semibold transition-snappy <?= $activePage == 'categories.php' ? 'bg-brand-600 text-white shadow-md shadow-brand-500/20' : 'text-gray-500 hover:bg-brand-50 hover:text-brand-600' ?>">
                <i data-lucide="tags" class="w-4.5 h-4.5"></i> Kategori
            </a>
            <a href="menus.php" class="flex items-center gap-3 px-4 py-3.5 rounded-2xl text-sm font-semibold transition-snappy <?= $activePage == 'menus.php' ? 'bg-brand-600 text-white shadow-md shadow-brand-500/20' : 'text-gray-500 hover:bg-brand-50 hover:text-brand-600' ?>">
                <i data-lucide="coffee" class="w-4.5 h-4.5"></i> Menu
            </a>
            <a href="tables.php" class="flex items-center gap-3 px-4 py-3.5 rounded-2xl text-sm font-semibold transition-snappy <?= $activePage == 'tables.php' ? 'bg-brand-600 text-white shadow-md shadow-brand-500/20' : 'text-gray-500 hover:bg-brand-50 hover:text-brand-600' ?>">
                <i data-lucide="scan-line" class="w-4.5 h-4.5"></i> Meja & QR
            </a>
            <a href="promos.php" class="flex items-center gap-3 px-4 py-3.5 rounded-2xl text-sm font-semibold transition-snappy <?= $activePage == 'promos.php' ? 'bg-brand-600 text-white shadow-md shadow-brand-500/20' : 'text-gray-500 hover:bg-brand-50 hover:text-brand-600' ?>">
                <i data-lucide="image" class="w-4.5 h-4.5"></i> Banner Promo
            </a>
            <a href="reports.php" class="flex items-center gap-3 px-4 py-3.5 rounded-2xl text-sm font-semibold transition-snappy <?= $activePage == 'reports.php' ? 'bg-brand-600 text-white shadow-md shadow-brand-500/20' : 'text-gray-500 hover:bg-brand-50 hover:text-brand-600' ?>">
                <i data-lucide="bar-chart-3" class="w-4.5 h-4.5"></i> Laporan
            </a>
            <a href="users.php" class="flex items-center gap-3 px-4 py-3.5 rounded-2xl text-sm font-semibold transition-snappy <?= $activePage == 'users.php' ? 'bg-brand-600 text-white shadow-md shadow-brand-500/20' : 'text-gray-500 hover:bg-brand-50 hover:text-brand-600' ?>">
                <i data-lucide="users" class="w-4.5 h-4.5"></i> Pengguna
            </a>
            <a href="settings.php" class="flex items-center gap-3 px-4 py-3.5 rounded-2xl text-sm font-semibold transition-snappy <?= $activePage == 'settings.php' ? 'bg-brand-600 text-white shadow-md shadow-brand-500/20' : 'text-gray-500 hover:bg-brand-50 hover:text-brand-600' ?>">
                <i data-lucide="settings" class="w-4.5 h-4.5"></i> Pengaturan
            </a>
        </nav>
        <div class="p-5 border-t border-gray-50 bg-gray-50/50">
            <div class="flex items-center gap-3 bg-white p-2 rounded-2xl shadow-sm border border-gray-100">
                <div class="w-10 h-10 rounded-[14px] bg-brand-100 flex items-center justify-center text-brand-600 font-bold">
                    <?= strtoupper(substr($_SESSION['username'], 0, 1)) ?>
                </div>
                <div class="flex-1 overflow-hidden">
                    <p class="text-xs font-bold text-gray-900 truncate"><?= htmlspecialchars($_SESSION['username']) ?></p>
                    <p class="text-[10px] uppercase text-gray-500 tracking-wider">Admin</p>
                </div>
                <a href="../auth/logout.php" class="w-8 h-8 flex items-center justify-center rounded-xl text-red-500 hover:bg-red-50 transition-colors mr-1" title="Logout">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                </a>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-full overflow-auto relative min-w-0">
        
        <!-- Mobile Topbar -->
        <div class="md:hidden flex items-center justify-between bg-white/80 backdrop-blur-md px-5 py-4 border-b border-gray-100 sticky top-0 z-10">
            <div class="flex items-center gap-3">
                <button onclick="toggleAdminSidebar()" class="p-2 -ml-2 text-gray-600 hover:bg-gray-100 rounded-xl transition-colors">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>
                <h1 class="text-lg font-bold tracking-tight text-gray-900 leading-none">Admin Panel</h1>
            </div>
            <a href="../auth/logout.php" class="w-8 h-8 flex items-center justify-center rounded-xl text-red-500 hover:bg-red-50 transition-colors" title="Logout">
                <i data-lucide="log-out" class="w-4 h-4"></i>
            </a>
        </div>

        <?php $flash = getFlash(); if ($flash): ?>
        <div class="absolute top-8 right-8 z-50 animate-bounce">
            <div class="px-6 py-4 rounded-lg shadow-2xl flex items-center gap-3 <?= $flash['type'] === 'success' ? 'bg-black text-white' : 'bg-red-500 text-white' ?>">
                <i data-lucide="<?= $flash['type'] === 'success' ? 'check-circle' : 'alert-circle' ?>" class="w-5 h-5"></i>
                <span class="text-sm font-medium"><?= htmlspecialchars($flash['message']) ?></span>
            </div>
        </div>
        <script>
            setTimeout(() => {
                const flash = document.querySelector('.animate-bounce');
                if (flash) flash.style.display = 'none';
            }, 5000);
        </script>
        <?php endif; ?>
