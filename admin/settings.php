<?php
require_once '../config/auth.php';
require_once '../config/db.php';
requireRole('admin');

$audioDir = '../assets/audio/';
$audioFiles = glob($audioDir . 'notification.*');
$currentAudio = !empty($audioFiles) ? $audioFiles[0] : null;

$qrisPath = '../assets/uploads/qris.webp';
$hasQris = file_exists($qrisPath);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    
    if (isset($_FILES['audio_file']) && $_FILES['audio_file']['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['audio_file']['tmp_name'];
        $fileName = $_FILES['audio_file']['name'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        $allowedExts = ['mp3', 'wav', 'ogg'];
        $allowedMimes = ['audio/mpeg', 'audio/wav', 'audio/ogg'];
        
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $fileTmp);
        finfo_close($finfo);
        
        if (in_array($fileExt, $allowedExts) && in_array($mime, $allowedMimes)) {
            // Hapus file notifikasi lama jika ada
            if ($currentAudio && file_exists($currentAudio)) {
                unlink($currentAudio);
            }
            
            $newPath = $audioDir . 'notification.' . $fileExt;
            if (move_uploaded_file($fileTmp, $newPath)) {
                setFlash('success', 'Audio notifikasi berhasil diubah.');
                header("Location: settings.php");
                exit;
            } else {
                setFlash('error', 'Gagal mengunggah file.');
            }
        } else {
            setFlash('error', 'Format file tidak didukung. Harap gunakan MP3, WAV, atau OGG.');
        }
    } elseif (isset($_FILES['qris_file']) && $_FILES['qris_file']['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['qris_file']['tmp_name'];
        $fileName = $_FILES['qris_file']['name'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        $allowedExts = ['jpg', 'jpeg', 'png', 'webp'];
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
        
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $fileTmp);
        finfo_close($finfo);
        
        if (in_array($fileExt, $allowedExts) && in_array($mime, $allowedMimes)) {
            $destDir = '../assets/uploads/';
            if (!is_dir($destDir)) {
                mkdir($destDir, 0755, true);
            }
            $destPath = $destDir . 'qris.webp';
            
            if (file_exists($destPath)) {
                unlink($destPath);
            }
            
            require_once '../config/image_helper.php';
            if (convertToWebp($fileTmp, $destPath)) {
                setFlash('success', 'Gambar QRIS Toko berhasil diunggah.');
                header("Location: settings.php");
                exit;
            } else {
                setFlash('error', 'Gagal mengonversi gambar QRIS ke format WebP.');
            }
        } else {
            setFlash('error', 'Format gambar tidak didukung. Harap gunakan JPG, PNG, atau WEBP.');
        }
    } else {
        setFlash('error', 'Silakan pilih file terlebih dahulu.');
    }
    header("Location: settings.php");
    exit;
}

$title = 'Pengaturan';
require_once 'layout_header.php';
?>
<header class="bg-white/80 backdrop-blur-md border-b border-gray-100 px-5 md:px-8 py-5 md:py-6 flex flex-col sm:flex-row sm:justify-between items-start sm:items-center gap-2 sm:gap-0 sticky top-0 md:relative z-10">
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-gray-900">Pengaturan Sistem</h2>
        <p class="text-sm text-gray-500 mt-1 font-medium">Konfigurasi pengaturan aplikasi dan notifikasi.</p>
    </div>
</header>

<div class="p-4 md:p-8 max-w-4xl space-y-8">
    <div class="bg-white rounded-[24px] shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-50">
            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <i data-lucide="volume-2" class="w-5 h-5 text-brand-600"></i> Suara Notifikasi Pesanan
            </h3>
            <p class="text-sm text-gray-500 mt-1">Ubah suara yang akan diputar di halaman kasir ketika ada pesanan baru masuk.</p>
        </div>
        <div class="p-6">
            <?php if ($currentAudio): ?>
                <div class="mb-6 p-4 bg-gray-50 border border-gray-200 rounded-2xl flex flex-col sm:flex-row sm:items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-brand-100 text-brand-600 flex items-center justify-center shrink-0">
                        <i data-lucide="music" class="w-6 h-6"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-bold text-gray-900">Audio Saat Ini:</p>
                        <p class="text-xs text-gray-500 font-medium"><?= basename($currentAudio) ?></p>
                    </div>
                    <button type="button" onclick="document.getElementById('preview-audio').play()" class="w-full sm:w-auto px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-xl text-xs font-bold hover:border-brand-600 hover:text-brand-600 transition-snappy flex justify-center items-center gap-2 shadow-sm">
                        <i data-lucide="play" class="w-4 h-4"></i> Play
                    </button>
                    <audio id="preview-audio" src="<?= $currentAudio ?>?v=<?= filemtime($currentAudio) ?>"></audio>
                </div>
            <?php else: ?>
                <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-2xl flex items-start gap-3">
                    <i data-lucide="info" class="w-5 h-5 text-yellow-600 mt-0.5 shrink-0"></i>
                    <div>
                        <p class="text-sm font-bold text-yellow-800">Belum Ada Audio Khusus</p>
                        <p class="text-xs text-yellow-700 mt-1">Sistem saat ini menggunakan suara "beep" standar bawaan browser. Unggah file audio untuk menggantinya.</p>
                    </div>
                </div>
            <?php endif; ?>

            <form action="settings.php" method="POST" enctype="multipart/form-data" class="space-y-4">
                <?= csrf_field() ?>
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Pilih File Audio</label>
                    <div class="flex items-center justify-center w-full">
                        <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-2xl cursor-pointer bg-gray-50 hover:bg-gray-100 hover:border-brand-500 transition-colors">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <i data-lucide="upload-cloud" class="w-8 h-8 text-gray-400 mb-2"></i>
                                <p class="mb-1 text-sm text-gray-500"><span class="font-semibold text-brand-600">Klik untuk unggah</span> atau drag and drop</p>
                                <p class="text-xs text-gray-500">MP3, WAV, atau OGG (Max. 5MB)</p>
                            </div>
                            <input id="dropzone-file" type="file" name="audio_file" class="hidden" accept=".mp3,.wav,.ogg" onchange="document.getElementById('file-name').textContent = this.files[0].name" />
                        </label>
                    </div>
                    <p id="file-name" class="text-sm font-medium text-gray-900 mt-2 text-center h-5"></p>
                </div>
                
                <div class="flex sm:justify-end pt-2">
                    <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-brand-600 text-white rounded-xl text-sm font-bold uppercase tracking-widest hover:bg-brand-700 transition-snappy shadow-lg shadow-brand-500/30 flex justify-center items-center gap-2">
                        <i data-lucide="save" class="w-4 h-4"></i> Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-[24px] shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-50">
            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <i data-lucide="scan-line" class="w-5 h-5 text-brand-600"></i> Gambar QRIS Toko
            </h3>
            <p class="text-sm text-gray-500 mt-1">Unggah gambar QRIS statis toko yang akan ditampilkan kepada pelanggan saat memesan menggunakan metode pembayaran QRIS.</p>
        </div>
        <div class="p-6">
            <?php if ($hasQris): ?>
                <div class="mb-6 p-4 bg-gray-50 border border-gray-200 rounded-2xl flex flex-col sm:flex-row sm:items-center gap-4">
                    <div class="w-24 h-24 bg-white border border-gray-200 rounded-2xl overflow-hidden shrink-0 flex items-center justify-center p-2 shadow-sm">
                        <img src="<?= $qrisPath ?>?v=<?= filemtime($qrisPath) ?>" alt="QRIS Toko" class="max-w-full max-h-full object-contain">
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-bold text-gray-900">QRIS Toko Saat Ini:</p>
                        <p class="text-xs text-gray-500 font-medium">assets/uploads/qris.webp</p>
                    </div>
                </div>
            <?php else: ?>
                <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-2xl flex items-start gap-3">
                    <i data-lucide="info" class="w-5 h-5 text-yellow-600 mt-0.5 shrink-0"></i>
                    <div>
                        <p class="text-sm font-bold text-yellow-800">QRIS Belum Diunggah</p>
                        <p class="text-xs text-yellow-700 mt-1">Sistem belum memiliki gambar QRIS statis. Unggah gambar QRIS agar pelanggan dapat melakukan pembayaran non-tunai secara mandiri.</p>
                    </div>
                </div>
            <?php endif; ?>

            <form action="settings.php" method="POST" enctype="multipart/form-data" class="space-y-4">
                <?= csrf_field() ?>
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Pilih Gambar QRIS</label>
                    <div class="flex items-center justify-center w-full">
                        <label for="qris-file-input" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-2xl cursor-pointer bg-gray-50 hover:bg-gray-100 hover:border-brand-500 transition-colors">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <i data-lucide="upload-cloud" class="w-8 h-8 text-gray-400 mb-2"></i>
                                <p class="mb-1 text-sm text-gray-500"><span class="font-semibold text-brand-600">Klik untuk unggah</span> atau drag and drop</p>
                                <p class="text-xs text-gray-500">JPG, PNG, atau WEBP (Max. 5MB)</p>
                            </div>
                            <input id="qris-file-input" type="file" name="qris_file" class="hidden" accept=".jpg,.jpeg,.png,.webp" onchange="document.getElementById('qris-file-name').textContent = this.files[0].name" />
                        </label>
                    </div>
                    <p id="qris-file-name" class="text-sm font-medium text-gray-900 mt-2 text-center h-5"></p>
                </div>
                
                <div class="flex sm:justify-end pt-2">
                    <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-brand-600 text-white rounded-xl text-sm font-bold uppercase tracking-widest hover:bg-brand-700 transition-snappy shadow-lg shadow-brand-500/30 flex justify-center items-center gap-2">
                        <i data-lucide="save" class="w-4 h-4"></i> Simpan QRIS
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'layout_footer.php'; ?>
