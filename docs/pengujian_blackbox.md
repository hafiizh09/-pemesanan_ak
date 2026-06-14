# Dokumen Pengujian Black Box (Sistem Pemesanan Cafe AK)

Dokumen ini berisi pengujian *Black Box* untuk tiga fitur utama pada sistem pemesanan Cafe AK.
Pengujian dilakukan berdasarkan fungsionalitas yang terlihat dari sisi pengguna (input dan output),
tanpa memperhatikan struktur kode internal program.

**Modul yang diuji:**
1. **Proses Login Multi-Role** (`auth/login.php`)
2. **Modul Pelanggan – Kode QR / Upload Bukti QRIS** (`customer/qris_payment.php`)
3. **Modul Kasir – Manajemen Shift** (`kasir/index.php`)

---

## 🔑 1. Pengujian Black Box: Login (`auth/login.php`)

### Deskripsi Fungsional

Halaman login berfungsi sebagai gerbang autentikasi sistem. Pengguna memasukkan *username* dan *password*, lalu sistem memverifikasi kredensial dan mengarahkan pengguna ke dashboard sesuai perannya (admin atau kasir). Jika sudah login, pengguna langsung diarahkan tanpa perlu memasukkan kredensial lagi.

### Teknik Pengujian

- **Equivalence Partitioning**: Membagi input ke dalam kelas valid dan tidak valid.
- **Boundary Value Analysis**: Menguji batas karakter kosong (kosong vs. diisi).

### Tabel Kasus Uji

| No | Skenario Uji | Data Input | Output yang Diharapkan | Hasil Uji |
| :--: | :--- | :--- | :--- | :--: |
| TC-L-01 | Username dan password dikosongkan, klik Login | Username: *(kosong)*, Password: *(kosong)* | Muncul pesan error: **"Username dan password wajib diisi."** Halaman login tetap tampil | ✅ Lulus |
| TC-L-02 | Hanya username diisi, password dikosongkan | Username: `admin`, Password: *(kosong)* | Muncul pesan error: **"Username dan password wajib diisi."** Halaman login tetap tampil | ✅ Lulus |
| TC-L-03 | Username dikosongkan, hanya password diisi | Username: *(kosong)*, Password: `rahasia123` | Muncul pesan error: **"Username dan password wajib diisi."** Halaman login tetap tampil | ✅ Lulus |
| TC-L-04 | Username tidak terdaftar di sistem | Username: `userpalsu`, Password: `apapun123` | Muncul pesan error: **"Username atau password salah."** Halaman login tetap tampil | ✅ Lulus |
| TC-L-05 | Username benar, password salah | Username: `admin`, Password: `passwordsalah` | Muncul pesan error: **"Username atau password salah."** Halaman login tetap tampil | ✅ Lulus |
| TC-L-06 | Username dan password admin benar | Username: `admin`, Password: *(password admin yang valid)* | Login berhasil, pengguna diarahkan ke halaman **Dashboard Admin** (`admin/index.php`) | ✅ Lulus |
| TC-L-07 | Username dan password kasir benar | Username: `kasir1`, Password: *(password kasir yang valid)* | Login berhasil, pengguna diarahkan ke halaman **Dashboard Kasir** (`kasir/index.php`) | ✅ Lulus |
| TC-L-08 | Mengakses halaman login saat sudah login sebagai admin | Pengguna sudah memiliki sesi aktif sebagai admin | Pengguna langsung diarahkan ke **Dashboard Admin** tanpa menampilkan form login | ✅ Lulus |
| TC-L-09 | Mengakses halaman login saat sudah login sebagai kasir | Pengguna sudah memiliki sesi aktif sebagai kasir | Pengguna langsung diarahkan ke **Dashboard Kasir** tanpa menampilkan form login | ✅ Lulus |

---

## 📱 2. Pengujian Black Box: Modul Pelanggan – Kode QR (`customer/qris_payment.php`)

### Deskripsi Fungsional

Halaman ini menampilkan kode QR QRIS untuk pembayaran pesanan. Pelanggan wajib mengunggah foto bukti transfer setelah melakukan pembayaran. Sistem memvalidasi keberadaan pesanan, metode pembayaran, format file yang diunggah, dan memprosesnya sebelum menyimpan ke database.

### Teknik Pengujian

- **Equivalence Partitioning**: Kelas valid (format file diizinkan) vs. tidak valid (format tidak didukung).
- **Boundary Value Analysis**: Batas kondisi pesanan (ID valid/tidak valid, status dibayar/belum).

### Tabel Kasus Uji

| No | Skenario Uji | Data Input | Output yang Diharapkan | Hasil Uji |
| :--: | :--- | :--- | :--- | :--: |
| TC-Q-01 | Akses halaman tanpa parameter ID pesanan di URL | URL: `qris_payment.php` *(tanpa `?id=`)* | Halaman berhenti dan menampilkan teks: **"Pesanan tidak ditemukan."** | ✅ Lulus |
| TC-Q-02 | Akses halaman dengan ID pesanan yang tidak ada di database | URL: `qris_payment.php?id=99999` | Halaman berhenti dan menampilkan teks: **"Pesanan tidak ditemukan."** | ✅ Lulus |
| TC-Q-03 | Akses halaman untuk pesanan dengan metode bayar bukan QRIS | URL: `qris_payment.php?id=<id_pesanan_tunai>` | Pengguna langsung diarahkan ke halaman **Status Pesanan** (`order_status.php`) | ✅ Lulus |
| TC-Q-04 | Akses halaman untuk pesanan QRIS yang sudah berstatus *paid* | URL: `qris_payment.php?id=<id_pesanan_sudah_dibayar>` | Pengguna langsung diarahkan ke halaman **Status Pesanan** (`order_status.php`) | ✅ Lulus |
| TC-Q-05 | Akses halaman untuk pesanan QRIS yang belum dibayar | URL: `qris_payment.php?id=<id_pesanan_valid>` | Halaman menampilkan **total tagihan**, **kode QR**, dan **form unggah bukti transfer** | ✅ Lulus |
| TC-Q-06 | Klik tombol Selesai tanpa memilih file gambar | Tidak ada file yang dipilih, klik **Selesai** | Muncul pesan error: **"Silakan pilih gambar bukti transfer terlebih dahulu."** | ✅ Lulus |
| TC-Q-07 | Upload file dengan format yang tidak didukung | File: `dokumen.pdf` atau `animasi.gif` | Muncul pesan error: **"Format file tidak didukung. Harap gunakan gambar JPG, PNG, atau WEBP."** | ✅ Lulus |
| TC-Q-08 | Upload file gambar dengan ekstensi valid namun isi file rusak | File: `rusak.jpg` *(berisi data bukan gambar)* | Muncul pesan error: **"Gagal memproses gambar bukti transfer."** | ✅ Lulus |
| TC-Q-09 | Upload file gambar JPG yang valid dan berukuran normal | File: `bukti.jpg` *(gambar valid, < 5MB)* | Upload berhasil, pengguna diarahkan ke halaman **Status Pesanan** (`order_status.php`) | ✅ Lulus |
| TC-Q-10 | Upload file gambar PNG yang valid | File: `bukti.png` *(gambar valid, < 5MB)* | Upload berhasil, pengguna diarahkan ke halaman **Status Pesanan** (`order_status.php`) | ✅ Lulus |
| TC-Q-11 | Upload file gambar WEBP yang valid | File: `bukti.webp` *(gambar valid, < 5MB)* | Upload berhasil, pengguna diarahkan ke halaman **Status Pesanan** (`order_status.php`) | ✅ Lulus |

---

## 🖥️ 3. Pengujian Black Box: Modul Kasir – Manajemen Shift (`kasir/index.php`)

### Deskripsi Fungsional

Halaman dashboard kasir menampilkan antarmuka untuk mengelola shift kerja. Kasir harus memulai shift untuk bisa menerima pesanan. Saat shift aktif, kasir dapat melihat live orders, mengaktifkan notifikasi audio, dan mengakhiri shift. Ketika shift diakhiri, sistem otomatis menghitung total penjualan dan menutup shift.

### Teknik Pengujian

- **Equivalence Partitioning**: Kondisi shift aktif vs. tidak aktif, role valid vs. tidak valid.
- **Decision Table Testing**: Kombinasi kondisi aksi (start/end shift) dan status shift.

### Tabel Kasus Uji

| No | Skenario Uji | Data Input / Aksi | Output yang Diharapkan | Hasil Uji |
| :--: | :--- | :--- | :--- | :--: |
| TC-K-01 | Pengguna dengan role selain kasir (misal: admin) mengakses halaman kasir | Sesi aktif sebagai `admin`, akses langsung ke `kasir/index.php` | Pengguna diarahkan ke halaman login atau halaman tidak memiliki akses | ✅ Lulus |
| TC-K-02 | Pengguna yang belum login mengakses halaman kasir | Tidak ada sesi aktif, akses langsung ke `kasir/index.php` | Pengguna diarahkan ke halaman **Login** | ✅ Lulus |
| TC-K-03 | Kasir login dan belum memulai shift | Sesi kasir aktif, **belum ada shift** aktif | Halaman menampilkan **overlay layar terkunci** dengan tulisan "Shift Kasir Ditutup" dan tombol **"Mulai Shift Saya"** | ✅ Lulus |
| TC-K-04 | Kasir login dan sudah memiliki shift aktif | Sesi kasir aktif, **ada shift** aktif di database | Halaman menampilkan **dashboard Live Orders**, status kasir **"Online"** (hijau), tombol **"End Shift"** dan **"Enable Audio"** tampil | ✅ Lulus |
| TC-K-05 | Kasir klik tombol "Mulai Shift Saya" (belum ada shift aktif) | Klik tombol **"Mulai Shift Saya"** | Shift baru berhasil dimulai, halaman di-refresh, status kasir berubah menjadi **"Online"**, overlay layar terkunci hilang | ✅ Lulus |
| TC-K-06 | Kasir klik tombol "Mulai Shift Saya" berulang kali (shift sudah aktif) | Klik tombol **"Mulai Shift Saya"** saat shift sudah berjalan | Tidak terjadi duplikasi shift, halaman di-refresh, status tetap **"Online"** | ✅ Lulus |
| TC-K-07 | Kasir klik "End Shift" lalu membatalkan konfirmasi | Klik **"End Shift"**, pilih **Tidak/Cancel** pada dialog konfirmasi | Form tidak terkirim, kasir tetap di halaman dashboard, shift masih **aktif** | ✅ Lulus |
| TC-K-08 | Kasir klik "End Shift" lalu mengkonfirmasi | Klik **"End Shift"**, pilih **Ya/OK** pada dialog konfirmasi | Shift ditutup, total penjualan dihitung otomatis, halaman di-refresh, status kasir berubah menjadi **"Offline"** (abu-abu), overlay layar terkunci kembali muncul | ✅ Lulus |
| TC-K-09 | Kasir klik tombol "Enable Audio" | Klik tombol **"Enable Audio"** saat shift aktif | Notifikasi audio pesanan baru diaktifkan, teks tombol berubah dan dikonfirmasi di browser | ✅ Lulus |
| TC-K-10 | Kasir klik tombol Logout | Klik ikon **Logout** di sidebar | Sesi kasir dihapus, pengguna diarahkan ke halaman **Login** | ✅ Lulus |
