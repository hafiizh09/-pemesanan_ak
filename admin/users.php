<?php
require_once '../config/auth.php';
require_once '../config/db.php';
requireRole('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    try {
        if (isset($_POST['add'])) {
            $username = trim($_POST['username']);
            $password = $_POST['password'];
            $allowedRoles = ['admin', 'kasir'];
            $role = in_array($_POST['role'] ?? '', $allowedRoles) ? $_POST['role'] : 'kasir';
            
            if ($username && $password) {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
                $stmt->execute([$username, $hashed, $role]);
                setFlash('success', 'User berhasil ditambahkan.');
            }
        } elseif (isset($_POST['update'])) {
            $id = (int)$_POST['edit_id'];
            $username = trim($_POST['username']);
            $password = $_POST['password'];
            $allowedRoles = ['admin', 'kasir'];
            $role = in_array($_POST['role'] ?? '', $allowedRoles) ? $_POST['role'] : 'kasir';
            
            if ($username) {
                if ($password) {
                    $hashed = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE users SET username = ?, password = ?, role = ? WHERE id = ?");
                    $stmt->execute([$username, $hashed, $role, $id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE users SET username = ?, role = ? WHERE id = ?");
                    $stmt->execute([$username, $role, $id]);
                }
                setFlash('success', 'User berhasil diperbarui.');
            }
        } elseif (isset($_POST['delete'])) {
            $id = (int)$_POST['id'];
            if ($id !== $_SESSION['user_id']) { 
                $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
                $stmt->execute([$id]);
                setFlash('success', 'User berhasil dihapus.');
            } else {
                setFlash('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
            }
        }
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            setFlash('error', 'Gagal memproses data. Username sudah terdaftar.');
        } else {
            setFlash('error', 'Terjadi kesalahan sistem.');
        }
    }
    header("Location: users.php");
    exit;
}

$editData = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT id, username, role FROM users WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $editData = $stmt->fetch();
}

$users = $pdo->query("SELECT id, username, role FROM users ORDER BY role, username")->fetchAll();
?>
<?php
$title = 'Users Management';
require_once 'layout_header.php';
?>
<div class="p-4 md:p-8">
    <div class="bg-white rounded-[24px] shadow-sm border border-gray-100 p-5 md:p-8">
        <div class="flex flex-col sm:flex-row sm:justify-between items-start sm:items-center gap-4 sm:gap-0 mb-6 md:mb-8">
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">Manajemen Pengguna</h1>
            <button onclick="openUserModal()" class="px-5 py-3 bg-brand-600 text-white rounded-2xl text-xs font-bold uppercase tracking-widest hover:bg-brand-700 transition-snappy shadow-lg shadow-brand-500/30 flex items-center gap-2">
                <i data-lucide="user-plus" class="w-4 h-4"></i> Tambah Pengguna
            </button>
        </div>

        <ul class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <?php foreach ($users as $u): ?>
            <li class="flex flex-col sm:flex-row sm:items-center justify-between p-4 bg-white border border-gray-100 rounded-2xl hover:border-brand-200 transition-colors group gap-4 sm:gap-0">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-lg <?= $u['role'] === 'admin' ? 'bg-emerald-50 text-emerald-600' : 'bg-brand-50 text-brand-600' ?>">
                        <?= strtoupper(substr($u['username'], 0, 1)) ?>
                    </div>
                    <div>
                        <span class="font-bold text-gray-900 block"><?= htmlspecialchars($u['username']) ?></span>
                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wider"><?= $u['role'] ?></span>
                    </div>
                </div>
                <div class="flex items-center justify-end w-full sm:w-auto gap-2 opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity mt-3 sm:mt-0 border-t border-gray-50 sm:border-0 pt-3 sm:pt-0">
                    <a href="users.php?edit=<?= $u['id'] ?>" class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-50 text-gray-600 hover:bg-brand-50 hover:text-brand-600 transition-colors" title="Edit">
                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                    </a>
                    <?php if ($u['id'] !== $_SESSION['user_id']): ?>
                    <form method="POST" action="users.php" class="inline" onsubmit="return confirm('Hapus user ini?');">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= $u['id'] ?>">
                        <button type="submit" name="delete" class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-50 text-red-500 hover:bg-red-50 transition-colors" title="Hapus">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<!-- Modal Popup -->
<div id="user-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeUserModal()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[calc(100%-2rem)] md:w-full max-w-md bg-white rounded-[28px] shadow-2xl border border-gray-100 p-6 md:p-8 max-h-[90vh] overflow-y-auto" style="animation: modalIn 0.3s ease-out">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold tracking-tight text-gray-900"><?= $editData ? 'Edit Pengguna' : 'Tambah Pengguna' ?></h2>
            <button onclick="closeUserModal()" class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-100 text-gray-500 hover:bg-gray-200 transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form method="POST" action="users.php" class="space-y-4">
            <?= csrf_field() ?>
            <?php if ($editData): ?>
            <input type="hidden" name="edit_id" value="<?= $editData['id'] ?>">
            <?php endif; ?>
            
            <div class="space-y-1">
                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Username</label>
                <div class="bg-gray-50 rounded-2xl px-5 py-3 border border-gray-100 focus-within:border-brand-500 focus-within:bg-white transition-colors">
                    <input type="text" name="username" value="<?= $editData ? htmlspecialchars($editData['username']) : '' ?>" required class="w-full bg-transparent border-none focus:outline-none text-sm font-medium text-gray-900" placeholder="Username">
                </div>
            </div>

            <div class="space-y-1">
                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Password</label>
                <div class="bg-gray-50 rounded-2xl px-5 py-3 border border-gray-100 focus-within:border-brand-500 focus-within:bg-white transition-colors">
                    <input type="password" name="password" <?= $editData ? '' : 'required' ?> class="w-full bg-transparent border-none focus:outline-none text-sm font-medium text-gray-900" placeholder="<?= $editData ? 'Kosongkan jika tidak ingin mengubah' : 'Password' ?>">
                </div>
            </div>

            <div class="space-y-1">
                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Role</label>
                <div class="bg-gray-50 rounded-2xl px-5 py-3 border border-gray-100 focus-within:border-brand-500 focus-within:bg-white transition-colors">
                    <select name="role" required class="w-full bg-transparent border-none focus:outline-none text-sm font-medium text-gray-900">
                        <option value="kasir" <?= ($editData && $editData['role'] === 'kasir') ? 'selected' : '' ?>>Kasir</option>
                        <option value="admin" <?= ($editData && $editData['role'] === 'admin') ? 'selected' : '' ?>>Admin</option>
                    </select>
                </div>
            </div>

            <?php if ($editData): ?>
            <button type="submit" name="update" class="w-full py-4 bg-brand-600 text-white rounded-2xl text-sm font-bold uppercase tracking-widest hover:bg-brand-700 transition-snappy shadow-lg shadow-brand-500/30 flex justify-center items-center gap-2"><i data-lucide="save" class="w-5 h-5"></i> Simpan Perubahan</button>
            <?php else: ?>
            <button type="submit" name="add" class="w-full py-4 bg-brand-600 text-white rounded-2xl text-sm font-bold uppercase tracking-widest hover:bg-brand-700 transition-snappy shadow-lg shadow-brand-500/30 flex justify-center items-center gap-2"><i data-lucide="user-plus" class="w-5 h-5"></i> Tambah Pengguna</button>
            <?php endif; ?>
        </form>
    </div>
</div>

<style>
@keyframes modalIn { from { opacity:0; transform:translate(-50%,-48%) scale(.96); } to { opacity:1; transform:translate(-50%,-50%) scale(1); } }
</style>

<script>
function openUserModal() {
    document.getElementById('user-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    if(window.lucide) lucide.createIcons();
}
function closeUserModal() {
    <?php if ($editData): ?>
    window.location.href = 'users.php';
    <?php else: ?>
    document.getElementById('user-modal').classList.add('hidden');
    document.body.style.overflow = '';
    <?php endif; ?>
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeUserModal(); });

<?php if ($editData): ?>
openUserModal();
<?php endif; ?>
</script>

<?php require_once 'layout_footer.php'; ?>
