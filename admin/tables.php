<?php
require_once '../config/auth.php';
require_once '../config/db.php';
requireRole('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    try {
        if (isset($_POST['add'])) {
            $no = trim($_POST['nomor_meja']);
            if ($no) {
                $stmt = $pdo->prepare("INSERT INTO tables (nomor_meja) VALUES (?)");
                $stmt->execute([$no]);
                setFlash('success', 'Meja berhasil ditambahkan.');
            }
        } elseif (isset($_POST['update'])) {
            $id = (int)$_POST['edit_id'];
            $no = trim($_POST['nomor_meja']);
            if ($no) {
                $stmt = $pdo->prepare("UPDATE tables SET nomor_meja = ? WHERE id = ?");
                $stmt->execute([$no, $id]);
                setFlash('success', 'Meja berhasil diperbarui.');
            }
        } elseif (isset($_POST['delete'])) {
            $id = (int)$_POST['id'];
            $stmt = $pdo->prepare("DELETE FROM tables WHERE id = ?");
            $stmt->execute([$id]);
            setFlash('success', 'Meja berhasil dihapus.');
        }
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            setFlash('error', 'Gagal memproses data. Meja mungkin memiliki riwayat pesanan aktif atau nomor meja sudah ada.');
        } else {
            setFlash('error', 'Terjadi kesalahan sistem.');
        }
    }
    header("Location: tables.php");
    exit;
}

$editData = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM tables WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $editData = $stmt->fetch();
}

$tables = $pdo->query("SELECT * FROM tables ORDER BY CAST(nomor_meja AS UNSIGNED)")->fetchAll();
$baseUrl = $appUrl . "/index.php?meja=";
?>
<?php
$title = 'Tables & QR';
require_once 'layout_header.php';
?>
<div class="p-4 md:p-8">
    <div class="bg-white rounded-[24px] shadow-sm border border-gray-100 p-5 md:p-8">
        <div class="flex flex-col sm:flex-row sm:justify-between items-start sm:items-center gap-4 sm:gap-0 mb-6 md:mb-8">
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">Meja & QR Code</h1>
            <button onclick="openTableModal()" class="px-5 py-3 bg-brand-600 text-white rounded-2xl text-xs font-bold uppercase tracking-widest hover:bg-brand-700 transition-snappy shadow-lg shadow-brand-500/30 flex items-center gap-2">
                <i data-lucide="plus-circle" class="w-4 h-4"></i> Tambah Meja
            </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
            <?php foreach ($tables as $t): 
                $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($baseUrl . $t['id']);
            ?>
            <div class="bg-white border border-gray-100 rounded-[20px] p-5 flex flex-col items-center hover:border-brand-200 hover:shadow-lg hover:shadow-brand-500/5 transition-all group">
                <div class="w-full flex justify-between items-center mb-4">
                    <span class="font-bold text-gray-900 bg-brand-50 text-brand-600 px-3 py-1 rounded-lg text-sm">Meja <?= htmlspecialchars($t['nomor_meja']) ?></span>
                    <div class="flex items-center gap-2 opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity">
                        <a href="tables.php?edit=<?= $t['id'] ?>" class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-50 text-gray-500 hover:bg-brand-50 hover:text-brand-600 transition-colors" title="Edit">
                            <i data-lucide="edit-2" class="w-4 h-4"></i>
                        </a>
                        <form method="POST" action="tables.php" class="inline" onsubmit="return confirm('Hapus meja ini?');">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id" value="<?= $t['id'] ?>">
                            <button type="submit" name="delete" class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-50 text-red-400 hover:bg-red-50 hover:text-red-600 transition-colors" title="Hapus">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </div>
                </div>
                
                <div class="p-3 bg-white rounded-2xl shadow-sm border border-gray-100 mb-4 group-hover:scale-105 transition-transform">
                    <img src="<?= $qrUrl ?>" alt="QR Table <?= $t['nomor_meja'] ?>" class="w-32 h-32">
                </div>
                
                <a href="<?= $qrUrl ?>" download="QR_Meja_<?= $t['nomor_meja'] ?>.png" target="_blank" class="w-full flex items-center justify-center gap-2 text-center text-xs font-bold text-gray-600 bg-gray-50 hover:bg-brand-50 hover:text-brand-600 py-3 rounded-xl transition-colors mt-auto">
                    <i data-lucide="download" class="w-4 h-4"></i> Download QR
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Modal Popup -->
<div id="table-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeTableModal()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[calc(100%-2rem)] md:w-full max-w-sm bg-white rounded-[28px] shadow-2xl border border-gray-100 p-6 md:p-8 max-h-[90vh] overflow-y-auto" style="animation: modalIn 0.3s ease-out">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold tracking-tight text-gray-900"><?= $editData ? 'Edit Meja' : 'Tambah Meja' ?></h2>
            <button onclick="closeTableModal()" class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-100 text-gray-500 hover:bg-gray-200 transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form method="POST" action="tables.php" class="space-y-4">
            <?= csrf_field() ?>
            <?php if ($editData): ?>
            <input type="hidden" name="edit_id" value="<?= $editData['id'] ?>">
            <?php endif; ?>
            <div class="bg-gray-50 rounded-2xl px-5 py-3 border border-gray-100 focus-within:border-brand-500 focus-within:bg-white transition-colors">
                <input type="text" name="nomor_meja" value="<?= $editData ? htmlspecialchars($editData['nomor_meja']) : '' ?>" required class="w-full bg-transparent border-none focus:outline-none text-sm font-medium text-gray-900" placeholder="Nomor Meja (contoh: 10 atau VIP-1)">
            </div>
            <?php if ($editData): ?>
            <button type="submit" name="update" class="w-full py-4 bg-brand-600 text-white rounded-2xl text-sm font-bold uppercase tracking-widest hover:bg-brand-700 transition-snappy shadow-lg shadow-brand-500/30 flex justify-center items-center gap-2"><i data-lucide="save" class="w-5 h-5"></i> Simpan Perubahan</button>
            <?php else: ?>
            <button type="submit" name="add" class="w-full py-4 bg-brand-600 text-white rounded-2xl text-sm font-bold uppercase tracking-widest hover:bg-brand-700 transition-snappy shadow-lg shadow-brand-500/30 flex justify-center items-center gap-2"><i data-lucide="plus-circle" class="w-5 h-5"></i> Tambah Meja</button>
            <?php endif; ?>
        </form>
    </div>
</div>

<style>
@keyframes modalIn { from { opacity:0; transform:translate(-50%,-48%) scale(.96); } to { opacity:1; transform:translate(-50%,-50%) scale(1); } }
</style>

<script>
function openTableModal() {
    document.getElementById('table-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    if(window.lucide) lucide.createIcons();
}
function closeTableModal() {
    <?php if ($editData): ?>
    window.location.href = 'tables.php';
    <?php else: ?>
    document.getElementById('table-modal').classList.add('hidden');
    document.body.style.overflow = '';
    <?php endif; ?>
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeTableModal(); });

<?php if ($editData): ?>
openTableModal();
<?php endif; ?>
</script>

<?php require_once 'layout_footer.php'; ?>
