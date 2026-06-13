<?php
require_once '../config/auth.php';
require_once '../config/db.php';
requireRole('admin');

$uploadDir = '../assets/uploads/promos/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    try {
        if (isset($_POST['add']) || isset($_POST['update'])) {
            $judul = trim($_POST['judul']);
            $isActive = isset($_POST['is_active']) ? 1 : 0;
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
                    $newFilename = time() . '_' . bin2hex(random_bytes(8)) . '.webp';
                    require_once '../config/image_helper.php';
                    if (convertToWebp($tempFilePath, $uploadDir . $newFilename)) {
                        $imageUrl = 'assets/uploads/promos/' . $newFilename;
                    } else {
                        setFlash('error', 'Upload gagal: Tidak dapat memproses gambar menjadi WebP.');
                        header("Location: promos.php");
                        exit;
                    }
                } else {
                    setFlash('error', 'Upload gagal: Format file tidak diizinkan.');
                    header("Location: promos.php");
                    exit;
                }
            }

            if (isset($_POST['add'])) {
                if (!$imageUrl) {
                    setFlash('error', 'Gambar wajib diupload untuk promo baru.');
                    header("Location: promos.php");
                    exit;
                }
                $stmt = $pdo->prepare("INSERT INTO promos (judul, gambar_url, is_active) VALUES (?, ?, ?)");
                $stmt->execute([$judul, $imageUrl, $isActive]);
                setFlash('success', 'Promo berhasil ditambahkan.');
            } elseif (isset($_POST['update'])) {
                $promoId = (int)$_POST['edit_id'];
                if ($imageUrl) {
                    $stmtOld = $pdo->prepare("SELECT gambar_url FROM promos WHERE id = ?");
                    $stmtOld->execute([$promoId]);
                    $oldImg = $stmtOld->fetchColumn();
                    
                    if ($oldImg && file_exists('../' . $oldImg)) unlink('../' . $oldImg);
                    
                    $stmt = $pdo->prepare("UPDATE promos SET judul=?, is_active=?, gambar_url=? WHERE id=?");
                    $stmt->execute([$judul, $isActive, $imageUrl, $promoId]);
                } else {
                    $stmt = $pdo->prepare("UPDATE promos SET judul=?, is_active=? WHERE id=?");
                    $stmt->execute([$judul, $isActive, $promoId]);
                }
                setFlash('success', 'Promo berhasil diperbarui.');
            }
        } elseif (isset($_POST['delete'])) {
            $promoId = (int)$_POST['id'];
            $stmtOld = $pdo->prepare("SELECT gambar_url FROM promos WHERE id = ?");
            $stmtOld->execute([$promoId]);
            $oldImg = $stmtOld->fetchColumn();
            
            if ($oldImg && file_exists('../' . $oldImg)) unlink('../' . $oldImg);
            
            $stmt = $pdo->prepare("DELETE FROM promos WHERE id = ?");
            $stmt->execute([$promoId]);
            setFlash('success', 'Promo berhasil dihapus.');
        }
    } catch (PDOException $e) {
        error_log('[Admin Promos] DB Error: ' . $e->getMessage());
        setFlash('error', 'Terjadi kesalahan sistem. Silakan coba lagi.');
    }
    header("Location: promos.php");
    exit;
}

$editData = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM promos WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $editData = $stmt->fetch();
}

$promos = $pdo->query("SELECT * FROM promos ORDER BY created_at DESC")->fetchAll();
?>
<?php
$title = 'Promos & Banners';
require_once 'layout_header.php';
?>
<div class="p-4 md:p-8">
    <div class="bg-white rounded-[24px] shadow-sm border border-gray-100 p-5 md:p-8">
        <div class="flex flex-col sm:flex-row sm:justify-between items-start sm:items-center gap-4 sm:gap-0 mb-6 md:mb-8">
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">Banner Promo</h1>
            <button onclick="openPromoModal()" class="px-5 py-3 bg-brand-600 text-white rounded-2xl text-xs font-bold uppercase tracking-widest hover:bg-brand-700 transition-snappy shadow-lg shadow-brand-500/30 flex items-center gap-2">
                <i data-lucide="plus-circle" class="w-4 h-4"></i> Tambah Promo
            </button>
        </div>

        <ul class="space-y-4">
            <?php foreach ($promos as $promo): ?>
            <li class="flex flex-col sm:flex-row sm:items-center justify-between p-4 bg-white border border-gray-100 rounded-[20px] hover:border-brand-200 hover:shadow-md transition-all group gap-4 sm:gap-0">
                <div class="flex items-center gap-4">
                    <img src="../<?= $promo['gambar_url'] ?>" alt="<?= htmlspecialchars($promo['judul']) ?>" class="w-24 h-14 object-cover bg-gray-50 rounded-xl border border-gray-100">
                    <div>
                        <span class="font-bold text-gray-900 block"><?= htmlspecialchars($promo['judul']) ?></span>
                        <span class="text-[10px] uppercase font-bold tracking-widest <?= $promo['is_active'] ? 'text-emerald-500' : 'text-gray-400' ?>"><?= $promo['is_active'] ? 'Aktif' : 'Nonaktif' ?></span>
                    </div>
                </div>
                <div class="flex items-center justify-end w-full sm:w-auto gap-2 opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity mt-3 sm:mt-0 border-t border-gray-50 sm:border-0 pt-3 sm:pt-0">
                    <a href="promos.php?edit=<?= $promo['id'] ?>" class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-50 text-gray-600 hover:bg-brand-50 hover:text-brand-600 transition-colors" title="Edit">
                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                    </a>
                    <form method="POST" action="promos.php" class="inline" onsubmit="return confirm('Hapus promo ini?');">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= $promo['id'] ?>">
                        <button type="submit" name="delete" class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-50 text-red-500 hover:bg-red-50 transition-colors" title="Hapus">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>
            </li>
            <?php endforeach; ?>
            <?php if(empty($promos)): ?>
            <li class="text-sm font-medium text-gray-500 text-center py-8 bg-gray-50 rounded-2xl border border-dashed border-gray-200">Belum ada promo banner yang ditambahkan.</li>
            <?php endif; ?>
        </ul>
    </div>
</div>

<!-- Modal Popup -->
<div id="promo-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closePromoModal()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[calc(100%-2rem)] md:w-full max-w-lg bg-white rounded-[28px] shadow-2xl border border-gray-100 p-6 md:p-8 max-h-[90vh] overflow-y-auto" style="animation: modalIn 0.3s ease-out">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold tracking-tight text-gray-900"><?= $editData ? 'Edit Promo' : 'Tambah Promo Baru' ?></h2>
            <button onclick="closePromoModal()" class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-100 text-gray-500 hover:bg-gray-200 transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form method="POST" action="promos.php" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-2">
            <?= csrf_field() ?>
            <?php if ($editData): ?>
            <input type="hidden" name="edit_id" value="<?= $editData['id'] ?>">
            <?php endif; ?>
            
            <div class="bg-gray-50 rounded-2xl px-5 py-3 border border-gray-100 focus-within:border-brand-500 focus-within:bg-white transition-colors col-span-1 md:col-span-2">
                <input type="text" name="judul" value="<?= $editData ? htmlspecialchars($editData['judul']) : '' ?>" required class="w-full bg-transparent border-none focus:outline-none text-sm font-medium text-gray-900" placeholder="Judul Promo">
            </div>
            
            <div class="flex items-center gap-2 pt-1 col-span-1 md:col-span-2">
                <label class="flex items-center gap-3 cursor-pointer group">
                    <div class="relative flex items-center justify-center">
                        <input type="checkbox" name="is_active" value="1" <?= (!$editData || $editData['is_active']) ? 'checked' : '' ?> class="peer appearance-none w-5 h-5 rounded-md border-2 border-gray-300 checked:bg-brand-600 checked:border-brand-600 transition-colors">
                        <i data-lucide="check" class="w-3 h-3 text-white absolute opacity-0 peer-checked:opacity-100 pointer-events-none"></i>
                    </div>
                    <span class="text-sm font-bold text-gray-700 group-hover:text-brand-600 transition-colors">Tandai Aktif</span>
                </label>
            </div>
            
            <div class="col-span-1 md:col-span-2 bg-brand-50/50 p-4 rounded-2xl border border-brand-100 border-dashed">
                <label class="block text-xs uppercase tracking-widest text-brand-600 mb-3 font-bold">
                    <?= $editData && $editData['gambar_url'] ? 'Ganti Banner' : 'Upload Gambar Banner (Rekomendasi: rasio 16:9)' ?>
                </label>
                <input type="file" name="gambar" accept="image/*" <?= $editData ? '' : 'required' ?> class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-white file:text-brand-600 file:shadow-sm hover:file:bg-brand-50 cursor-pointer">
            </div>

            <?php if ($editData): ?>
            <button type="submit" name="update" class="col-span-1 md:col-span-2 py-4 bg-brand-600 text-white rounded-2xl text-sm font-bold uppercase tracking-widest hover:bg-brand-700 transition-snappy shadow-lg shadow-brand-500/30 flex justify-center items-center gap-2"><i data-lucide="save" class="w-5 h-5"></i> Simpan Perubahan</button>
            <?php else: ?>
            <button type="submit" name="add" class="col-span-1 md:col-span-2 py-4 bg-brand-600 text-white rounded-2xl text-sm font-bold uppercase tracking-widest hover:bg-brand-700 transition-snappy shadow-lg shadow-brand-500/30 flex justify-center items-center gap-2"><i data-lucide="plus-circle" class="w-5 h-5"></i> Tambah Promo</button>
            <?php endif; ?>
        </form>
    </div>
</div>

<style>
@keyframes modalIn { from { opacity:0; transform:translate(-50%,-48%) scale(.96); } to { opacity:1; transform:translate(-50%,-50%) scale(1); } }
</style>

<script>
function openPromoModal() {
    document.getElementById('promo-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    if(window.lucide) lucide.createIcons();
}
function closePromoModal() {
    <?php if ($editData): ?>
    window.location.href = 'promos.php';
    <?php else: ?>
    document.getElementById('promo-modal').classList.add('hidden');
    document.body.style.overflow = '';
    <?php endif; ?>
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closePromoModal(); });

<?php if ($editData): ?>
openPromoModal();
<?php endif; ?>
</script>

<?php require_once 'layout_footer.php'; ?>
