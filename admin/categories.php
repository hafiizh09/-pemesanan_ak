<?php
require_once '../config/auth.php';
require_once '../config/db.php';
requireRole('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    try {
        if (isset($_POST['add'])) {
            $categoryName = trim($_POST['nama_kategori']);
            if ($categoryName) {
                $stmt = $pdo->prepare("INSERT INTO categories (nama_kategori) VALUES (?)");
                $stmt->execute([$categoryName]);
                setFlash('success', 'Kategori berhasil ditambahkan.');
            }
        } elseif (isset($_POST['update'])) {
            $categoryId = (int)$_POST['edit_id'];
            $categoryName = trim($_POST['nama_kategori']);
            if ($categoryName) {
                $stmt = $pdo->prepare("UPDATE categories SET nama_kategori = ? WHERE id = ?");
                $stmt->execute([$categoryName, $categoryId]);
                setFlash('success', 'Kategori berhasil diperbarui.');
            }
        } elseif (isset($_POST['delete'])) {
            $categoryId = (int)$_POST['id'];
            $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
            $stmt->execute([$categoryId]);
            setFlash('success', 'Kategori berhasil dihapus.');
        }
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            setFlash('error', 'Gagal memproses data. Kategori mungkin sedang digunakan oleh menu lain atau duplikat.');
        } else {
            setFlash('error', 'Terjadi kesalahan sistem.');
        }
    }
    header("Location: categories.php");
    exit;
}

$editData = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $editData = $stmt->fetch();
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY nama_kategori")->fetchAll();
?>
<?php
$title = 'Categories';
require_once 'layout_header.php';
?>
<div class="p-4 md:p-8">
    <div class="bg-white rounded-[24px] shadow-sm border border-gray-100 p-5 md:p-8">
        <div class="flex flex-col sm:flex-row sm:justify-between items-start sm:items-center gap-4 sm:gap-0 mb-6 md:mb-8">
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">Kategori Menu</h1>
            <button onclick="openCategoryModal()" class="px-5 py-3 bg-brand-600 text-white rounded-2xl text-xs font-bold uppercase tracking-widest hover:bg-brand-700 transition-snappy shadow-lg shadow-brand-500/30 flex items-center gap-2">
                <i data-lucide="plus-circle" class="w-4 h-4"></i> Tambah Kategori
            </button>
        </div>

        <ul class="space-y-3">
            <?php foreach ($categories as $categoryItem): ?>
            <li class="flex justify-between items-center p-4 bg-white border border-gray-100 rounded-2xl hover:border-brand-200 transition-colors group">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-brand-50 flex items-center justify-center">
                        <i data-lucide="tag" class="w-4.5 h-4.5 text-brand-600"></i>
                    </div>
                    <span class="font-bold text-gray-900"><?= htmlspecialchars($categoryItem['nama_kategori']) ?></span>
                </div>
                <div class="flex items-center gap-2 opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity">
                    <a href="categories.php?edit=<?= $categoryItem['id'] ?>" class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-50 text-gray-600 hover:bg-brand-50 hover:text-brand-600 transition-colors" title="Edit">
                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                    </a>
                    <form method="POST" action="categories.php" class="inline" onsubmit="return confirm('Hapus kategori ini?');">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= $categoryItem['id'] ?>">
                        <button type="submit" name="delete" class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-50 text-red-500 hover:bg-red-50 transition-colors" title="Hapus">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<!-- Modal Popup -->
<div id="category-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeCategoryModal()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[calc(100%-2rem)] md:w-full max-w-md bg-white rounded-[28px] shadow-2xl border border-gray-100 p-6 md:p-8 max-h-[90vh] overflow-y-auto" style="animation: modalIn 0.3s ease-out">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold tracking-tight text-gray-900"><?= $editData ? 'Edit Kategori' : 'Tambah Kategori' ?></h2>
            <button onclick="closeCategoryModal()" class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-100 text-gray-500 hover:bg-gray-200 transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form method="POST" action="categories.php" class="space-y-4">
            <?= csrf_field() ?>
            <?php if ($editData): ?>
            <input type="hidden" name="edit_id" value="<?= $editData['id'] ?>">
            <?php endif; ?>
            <div class="bg-gray-50 rounded-2xl px-5 py-3 border border-gray-100 focus-within:border-brand-500 focus-within:bg-white transition-colors">
                <input type="text" name="nama_kategori" value="<?= $editData ? htmlspecialchars($editData['nama_kategori']) : '' ?>" required class="w-full bg-transparent border-none focus:outline-none text-sm font-medium text-gray-900" placeholder="Nama Kategori Baru...">
            </div>
            <?php if ($editData): ?>
            <button type="submit" name="update" class="w-full py-4 bg-brand-600 text-white rounded-2xl text-sm font-bold uppercase tracking-widest hover:bg-brand-700 transition-snappy shadow-lg shadow-brand-500/30 flex justify-center items-center gap-2"><i data-lucide="save" class="w-5 h-5"></i> Simpan Perubahan</button>
            <?php else: ?>
            <button type="submit" name="add" class="w-full py-4 bg-brand-600 text-white rounded-2xl text-sm font-bold uppercase tracking-widest hover:bg-brand-700 transition-snappy shadow-lg shadow-brand-500/30 flex justify-center items-center gap-2"><i data-lucide="plus-circle" class="w-5 h-5"></i> Tambah Kategori</button>
            <?php endif; ?>
        </form>
    </div>
</div>

<style>
@keyframes modalIn { from { opacity:0; transform:translate(-50%,-48%) scale(.96); } to { opacity:1; transform:translate(-50%,-50%) scale(1); } }
</style>

<script>
function openCategoryModal() {
    document.getElementById('category-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    if(window.lucide) lucide.createIcons();
}
function closeCategoryModal() {
    <?php if ($editData): ?>
    window.location.href = 'categories.php';
    <?php else: ?>
    document.getElementById('category-modal').classList.add('hidden');
    document.body.style.overflow = '';
    <?php endif; ?>
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeCategoryModal(); });

<?php if ($editData): ?>
openCategoryModal();
<?php endif; ?>
</script>

<?php require_once 'layout_footer.php'; ?>
