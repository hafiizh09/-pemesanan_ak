<?php
require_once '../config/auth.php';
require_once '../config/db.php';
requireRole('admin');

$uploadDir = '../assets/uploads/menus/';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    try {
        if (isset($_POST['add']) || isset($_POST['update'])) {
            $categoryId = (int)$_POST['kategori_id'];
            $menuName = trim($_POST['nama_menu']);
            $description = trim($_POST['deskripsi']);
            $price = (float)$_POST['harga'];
            $menuStatus = $_POST['status'];
            $isNew = isset($_POST['is_new']) ? 1 : 0;
            $isBestseller = isset($_POST['is_bestseller']) ? 1 : 0;
            $imageUrl = null;

            // Validasi & Upload File Securely
            if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
                $tempFilePath = $_FILES['gambar']['tmp_name'];
                
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($finfo, $tempFilePath);
                finfo_close($finfo);
                
                $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
                $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
                $allowedExts = ['jpg', 'jpeg', 'png', 'webp'];
                
                if (in_array($mime, $allowedMimes) && in_array($ext, $allowedExts)) {
                    // Gunakan nama acak untuk keamanan, ubah ekstensi ke webp
                    $newFilename = time() . '_' . bin2hex(random_bytes(8)) . '.webp';
                    require_once '../config/image_helper.php';
                    if (convertToWebp($tempFilePath, $uploadDir . $newFilename)) {
                        $imageUrl = 'assets/uploads/menus/' . $newFilename;
                    } else {
                        setFlash('error', 'Upload gagal: Tidak dapat memproses gambar menjadi WebP.');
                        header("Location: menus.php");
                        exit;
                    }
                } else {
                    setFlash('error', 'Upload gagal: Format file tidak diizinkan. Gunakan JPG, PNG, atau WEBP.');
                    header("Location: menus.php");
                    exit;
                }
            }

            if (isset($_POST['add'])) {
                $stmt = $pdo->prepare("INSERT INTO menus (kategori_id, nama_menu, deskripsi, harga, status, is_new, is_bestseller, gambar_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$categoryId, $menuName, $description, $price, $menuStatus, $isNew, $isBestseller, $imageUrl]);
                setFlash('success', 'Menu berhasil ditambahkan.');
            } elseif (isset($_POST['update'])) {
                $menuId = (int)$_POST['edit_id'];
                if ($imageUrl) {
                    $stmtOld = $pdo->prepare("SELECT gambar_url FROM menus WHERE id = ?");
                    $stmtOld->execute([$menuId]);
                    $oldImg = $stmtOld->fetchColumn();
                    
                    if ($oldImg && file_exists('../' . $oldImg)) unlink('../' . $oldImg);
                    
                    $stmt = $pdo->prepare("UPDATE menus SET kategori_id=?, nama_menu=?, deskripsi=?, harga=?, status=?, is_new=?, is_bestseller=?, gambar_url=? WHERE id=?");
                    $stmt->execute([$categoryId, $menuName, $description, $price, $menuStatus, $isNew, $isBestseller, $imageUrl, $menuId]);
                } else {
                    $stmt = $pdo->prepare("UPDATE menus SET kategori_id=?, nama_menu=?, deskripsi=?, harga=?, status=?, is_new=?, is_bestseller=? WHERE id=?");
                    $stmt->execute([$categoryId, $menuName, $description, $price, $menuStatus, $isNew, $isBestseller, $menuId]);
                }
                setFlash('success', 'Menu berhasil diperbarui.');
            }
        } elseif (isset($_POST['delete'])) {
            $menuId = (int)$_POST['id'];
            $stmtOld = $pdo->prepare("SELECT gambar_url FROM menus WHERE id = ?");
            $stmtOld->execute([$menuId]);
            $oldImg = $stmtOld->fetchColumn();
            
            if ($oldImg && file_exists('../' . $oldImg)) unlink('../' . $oldImg);
            
            $stmt = $pdo->prepare("DELETE FROM menus WHERE id = ?");
            $stmt->execute([$menuId]);
            setFlash('success', 'Menu berhasil dihapus.');
        }
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            setFlash('error', 'Gagal memproses data. Menu ini sedang digunakan pada riwayat pesanan (Constraint).');
        } else {
            setFlash('error', 'Terjadi kesalahan sistem.');
        }
    }
    header("Location: menus.php");
    exit;
}

$editData = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM menus WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $editData = $stmt->fetch();
}

$fetchedMenus = $pdo->query("SELECT m.*, c.nama_kategori FROM menus m JOIN categories c ON m.kategori_id = c.id ORDER BY m.nama_menu")->fetchAll();
$fetchedCategories = $pdo->query("SELECT * FROM categories")->fetchAll();
?>
<?php
$title = 'Menus';
require_once 'layout_header.php';
?>
<div class="p-4 md:p-8">
    <!-- Menu List (Full Width) -->
    <div class="bg-white rounded-[24px] shadow-sm border border-gray-100 overflow-hidden">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:justify-between items-start sm:items-center gap-4 sm:gap-0 p-5 md:p-8 pb-4 md:pb-6">
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">Daftar Menu</h1>
            <button onclick="openMenuModal()" class="px-5 py-3 bg-brand-600 text-white rounded-2xl text-xs font-bold uppercase tracking-widest hover:bg-brand-700 transition-snappy shadow-lg shadow-brand-500/30 flex items-center gap-2">
                <i data-lucide="plus-circle" class="w-4 h-4"></i> Tambah Menu
            </button>
        </div>

        <ul class="divide-y divide-gray-100">
            <?php foreach ($fetchedMenus as $menuItem): ?>
            <li class="flex flex-col sm:flex-row sm:items-center justify-between px-4 md:px-6 py-4 hover:bg-gray-50/50 transition-colors group" id="menu-row-<?= $menuItem['id'] ?>">
                <!-- Left: Image + Info -->
                <div class="flex items-center gap-4 flex-1 min-w-0">
                    <?php if ($menuItem['gambar_url']): ?>
                    <img src="../<?= $menuItem['gambar_url'] ?>" alt="<?= htmlspecialchars($menuItem['nama_menu']) ?>" class="w-14 h-14 rounded-[12px] object-cover bg-gray-50 border border-gray-100 shrink-0">
                    <?php else: ?>
                    <div class="w-14 h-14 rounded-[12px] bg-gray-50 flex items-center justify-center text-gray-300 border border-gray-100 shrink-0"><i data-lucide="image" class="w-5 h-5"></i></div>
                    <?php endif; ?>
                    <div class="min-w-0">
                        <span class="font-bold text-gray-900 flex items-center gap-2 mb-0.5 text-[14px]">
                            <span class="truncate"><?= htmlspecialchars($menuItem['nama_menu']) ?></span>
                            <?php if($menuItem['is_new']): ?><span class="text-[9px] bg-blue-100 text-blue-700 px-2 py-0.5 rounded-md uppercase font-bold tracking-widest shrink-0">New</span><?php endif; ?>
                            <?php if($menuItem['is_bestseller']): ?><span class="text-[9px] bg-brand-100 text-brand-700 px-2 py-0.5 rounded-md uppercase font-bold tracking-widest shrink-0">Best</span><?php endif; ?>
                        </span>
                        <span class="text-[13px] font-medium text-gray-500"><?= $menuItem['nama_kategori'] ?> &bull; <span class="text-brand-600 font-bold">Rp <?= number_format($menuItem['harga'],0,',','.') ?></span></span>
                    </div>
                </div>

                <!-- Right: Toggle + Actions -->
                <div class="flex items-center justify-between w-full sm:w-auto gap-4 shrink-0 mt-4 sm:mt-0 border-t border-gray-100 sm:border-0 pt-4 sm:pt-0">
                    <label class="relative inline-flex items-center cursor-pointer w-[130px] justify-end" title="<?= $menuItem['status'] == 'tersedia' ? 'Klik untuk nonaktifkan' : 'Klik untuk aktifkan' ?>">
                        <input type="checkbox" class="sr-only peer menu-toggle" data-menu-id="<?= $menuItem['id'] ?>" <?= $menuItem['status'] == 'tersedia' ? 'checked' : '' ?>>
                        <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-emerald-500 transition-colors duration-300 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:shadow-sm after:transition-all after:duration-300 peer-checked:after:translate-x-5"></div>
                        <span class="ml-2.5 text-[11px] font-bold uppercase tracking-widest toggle-label w-[70px] <?= $menuItem['status'] == 'tersedia' ? 'text-emerald-600' : 'text-red-500' ?>"><?= $menuItem['status'] == 'tersedia' ? 'Tersedia' : 'Habis' ?></span>
                    </label>
                    <div class="flex items-center gap-2 opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity">
                        <a href="menus.php?edit=<?= $menuItem['id'] ?>" class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-100 text-gray-500 hover:bg-brand-50 hover:text-brand-600 transition-colors" title="Edit">
                            <i data-lucide="edit-2" class="w-4 h-4"></i>
                        </a>
                        <form method="POST" action="menus.php" class="inline" onsubmit="return confirm('Hapus menu ini?');">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id" value="<?= $menuItem['id'] ?>">
                            <button type="submit" name="delete" class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-100 text-red-500 hover:bg-red-50 transition-colors" title="Hapus">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<!-- Modal Popup Tambah/Edit Menu -->
<div id="menu-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeMenuModal()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[calc(100%-2rem)] md:w-full md:max-w-lg bg-white rounded-[28px] shadow-2xl border border-gray-100 p-6 md:p-8 max-h-[90vh] overflow-y-auto" style="animation: modalIn 0.3s ease-out">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold tracking-tight text-gray-900"><?= $editData ? 'Edit Menu' : 'Tambah Menu Baru' ?></h2>
            <button onclick="closeMenuModal()" class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-100 text-gray-500 hover:bg-gray-200 transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form method="POST" action="menus.php" enctype="multipart/form-data" class="space-y-4">
            <?= csrf_field() ?>
            <?php if ($editData): ?>
            <input type="hidden" name="edit_id" value="<?= $editData['id'] ?>">
            <?php endif; ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-gray-50 rounded-2xl px-5 py-3 border border-gray-100 focus-within:border-brand-500 focus-within:bg-white transition-colors">
                    <input type="text" name="nama_menu" value="<?= $editData ? htmlspecialchars($editData['nama_menu']) : '' ?>" required class="w-full bg-transparent border-none focus:outline-none text-sm font-medium text-gray-900" placeholder="Nama Menu">
                </div>
                <div class="bg-gray-50 rounded-2xl px-5 py-3 border border-gray-100 focus-within:border-brand-500 focus-within:bg-white transition-colors flex items-center">
                    <span class="text-gray-400 text-sm font-bold mr-2">Rp</span>
                    <input type="number" name="harga" value="<?= $editData ? $editData['harga'] : '' ?>" required class="w-full bg-transparent border-none focus:outline-none text-sm font-medium text-gray-900" placeholder="Harga">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <select name="kategori_id" required class="bg-gray-50 rounded-2xl px-5 py-3.5 border border-gray-100 focus:border-brand-500 focus:bg-white transition-colors text-sm font-medium text-gray-900 outline-none cursor-pointer">
                    <option value="">Pilih Kategori...</option>
                    <?php foreach ($fetchedCategories as $categoryItem): ?><option value="<?= $categoryItem['id'] ?>" <?= ($editData && $editData['kategori_id'] == $categoryItem['id']) ? 'selected' : '' ?>><?= $categoryItem['nama_kategori'] ?></option><?php endforeach; ?>
                </select>
                <select name="status" class="bg-gray-50 rounded-2xl px-5 py-3.5 border border-gray-100 focus:border-brand-500 focus:bg-white transition-colors text-sm font-medium text-gray-900 outline-none cursor-pointer">
                    <option value="tersedia" <?= ($editData && $editData['status'] === 'tersedia') ? 'selected' : '' ?>>Tersedia</option>
                    <option value="habis" <?= ($editData && $editData['status'] === 'habis') ? 'selected' : '' ?>>Habis</option>
                </select>
            </div>
            <div class="bg-gray-50 rounded-2xl px-5 py-3 border border-gray-100 focus-within:border-brand-500 focus-within:bg-white transition-colors">
                <input type="text" name="deskripsi" value="<?= $editData ? htmlspecialchars($editData['deskripsi']) : '' ?>" class="w-full bg-transparent border-none focus:outline-none text-sm font-medium text-gray-900" placeholder="Deskripsi Singkat">
            </div>
            <div class="flex gap-6">
                <label class="flex items-center gap-3 cursor-pointer group">
                    <div class="relative flex items-center justify-center">
                        <input type="checkbox" name="is_new" value="1" <?= ($editData && $editData['is_new']) ? 'checked' : '' ?> class="peer appearance-none w-5 h-5 rounded-md border-2 border-gray-300 checked:bg-brand-600 checked:border-brand-600 transition-colors">
                        <i data-lucide="check" class="w-3 h-3 text-white absolute opacity-0 peer-checked:opacity-100 pointer-events-none"></i>
                    </div>
                    <span class="text-sm font-bold text-gray-700 group-hover:text-brand-600 transition-colors">Menu Baru</span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer group">
                    <div class="relative flex items-center justify-center">
                        <input type="checkbox" name="is_bestseller" value="1" <?= ($editData && $editData['is_bestseller']) ? 'checked' : '' ?> class="peer appearance-none w-5 h-5 rounded-md border-2 border-gray-300 checked:bg-brand-600 checked:border-brand-600 transition-colors">
                        <i data-lucide="check" class="w-3 h-3 text-white absolute opacity-0 peer-checked:opacity-100 pointer-events-none"></i>
                    </div>
                    <span class="text-sm font-bold text-gray-700 group-hover:text-brand-600 transition-colors">Best Seller</span>
                </label>
            </div>
            <div class="bg-brand-50/50 p-4 rounded-2xl border border-brand-100 border-dashed">
                <label class="block text-xs uppercase tracking-widest text-brand-600 mb-3 font-bold">
                    <?= $editData && $editData['gambar_url'] ? 'Ganti Foto' : 'Upload Foto' ?>
                    <?php if ($editData && $editData['gambar_url']): ?>
                        <span class="text-[10px] text-brand-500 normal-case font-medium ml-2 tracking-normal">(Gambar sudah di-upload)</span>
                    <?php endif; ?>
                </label>
                <input type="file" name="gambar" accept="image/*" <?= $editData ? '' : 'required' ?> class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-white file:text-brand-600 file:shadow-sm hover:file:bg-brand-50 cursor-pointer">
            </div>
            <?php if ($editData): ?>
            <button type="submit" name="update" class="w-full py-4 bg-brand-600 text-white rounded-2xl text-sm font-bold uppercase tracking-widest hover:bg-brand-700 transition-snappy shadow-lg shadow-brand-500/30 flex justify-center items-center gap-2"><i data-lucide="save" class="w-5 h-5"></i> Simpan Perubahan</button>
            <?php else: ?>
            <button type="submit" name="add" class="w-full py-4 bg-brand-600 text-white rounded-2xl text-sm font-bold uppercase tracking-widest hover:bg-brand-700 transition-snappy shadow-lg shadow-brand-500/30 flex justify-center items-center gap-2"><i data-lucide="plus-circle" class="w-5 h-5"></i> Tambah Menu</button>
            <?php endif; ?>
        </form>
    </div>
</div>

<style>
@keyframes modalIn { from { opacity:0; transform:translate(-50%,-48%) scale(.96); } to { opacity:1; transform:translate(-50%,-50%) scale(1); } }
</style>

<script>
function openMenuModal() {
    document.getElementById('menu-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    lucide.createIcons();
}
function closeMenuModal() {
    document.getElementById('menu-modal').classList.add('hidden');
    document.body.style.overflow = '';
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeMenuModal(); });

<?php if ($editData): ?>
openMenuModal();
<?php endif; ?>

document.querySelectorAll('.menu-toggle').forEach(toggle => {
    toggle.addEventListener('change', async function() {
        const menuId = this.dataset.menuId;
        const checkbox = this;
        const label = this.closest('label').querySelector('.toggle-label');
        checkbox.disabled = true;
        try {
            const res = await fetch('../api/toggle_menu_status.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ menu_id: parseInt(menuId) })
            });
            const data = await res.json();
            if (data.success) {
                if (data.new_status === 'tersedia') {
                    label.textContent = 'Tersedia';
                    label.classList.remove('text-red-500');
                    label.classList.add('text-emerald-600');
                } else {
                    label.textContent = 'Habis';
                    label.classList.remove('text-emerald-600');
                    label.classList.add('text-red-500');
                }
            } else {
                checkbox.checked = !checkbox.checked;
                alert(data.message || 'Gagal mengubah status');
            }
        } catch (e) {
            checkbox.checked = !checkbox.checked;
            alert('Terjadi kesalahan jaringan');
        } finally {
            checkbox.disabled = false;
        }
    });
});
</script>

<?php require_once 'layout_footer.php'; ?>
