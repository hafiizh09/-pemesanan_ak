# Dokumen Pengujian Black Box (Sistem Pemesanan Cafe AK)

Dokumen ini berisi pengujian *Black Box* untuk tiga fitur utama pada sistem pemesanan Cafe AK.

**Modul yang diuji:**
1. **Proses Login** (`auth/login.php`)
2. **Modul Pelanggan – Kode QR** (`customer/qris_payment.php`)
3. **Modul Kasir** (`kasir/index.php`)

---

## 🔑 1. Pengujian Black Box: Login

**File Referensi: `auth/login.php`**

| No | Input / Event | Fungsi yang Diuji | Output yang Diharapkan | Hasil Uji |
| :--: | :--- | :--- | :--- | :--: |
| 1 | Username dan password dikosongkan, klik Login | Validasi Input Kosong ( `login.php:25-26` ) | Tampil pesan error: "Username dan password wajib diisi." di halaman login | Lulus |
| 2 | Username tidak terdaftar di database atau password salah, klik Login | Verifikasi Kredensial & Password ( `login.php:29-51` ) | Tampil pesan error: "Username atau password salah." di halaman login | Lulus |
| 3 | Username dan password admin/kasir yang benar, klik Login | Login Sukses & Redirect Role ( `login.php:33-48` ) | Login berhasil, pengguna diarahkan ke Dashboard sesuai role (Admin / Kasir) | Lulus |

---

## 📱 2. Pengujian Black Box: Modul Pelanggan – Kode QR

**File Referensi: `customer/qris_payment.php`**

| No | Input / Event | Fungsi yang Diuji | Output yang Diharapkan | Hasil Uji |
| :--: | :--- | :--- | :--- | :--: |
| 1 | Akses halaman QRIS dengan ID pesanan yang tidak ada di database | Validasi Pesanan ( `qris_payment.php:4-16` ) | Halaman berhenti dengan pesan: "Pesanan tidak ditemukan." | Lulus |
| 2 | Upload file berformat `.pdf` atau `.gif` yang tidak didukung, klik Selesai | Validasi Format File ( `qris_payment.php:34-64` ) | Tampil pesan error: "Format file tidak didukung. Harap gunakan gambar JPG, PNG, atau WEBP." | Lulus |
| 3 | Upload file gambar JPG/PNG/WEBP yang valid, klik Selesai | Upload & Simpan Bukti Transfer ( `qris_payment.php:51-59` ) | Bukti transfer berhasil disimpan, pengguna diarahkan ke halaman Status Pesanan | Lulus |

---

## 🖥️ 3. Pengujian Black Box: Modul Kasir

**File Referensi: `kasir/index.php`**

| No | Input / Event | Fungsi yang Diuji | Output yang Diharapkan | Hasil Uji |
| :--: | :--- | :--- | :--- | :--: |
| 1 | Kasir login dan belum memulai shift, membuka halaman dashboard | Cek Status Shift Aktif ( `index.php:42-44` ) | Halaman menampilkan overlay "Shift Kasir Ditutup" dengan tombol "Mulai Shift Saya" | Lulus |
| 2 | Kasir klik tombol "Mulai Shift Saya" saat belum ada shift aktif | Insert Shift Baru ( `index.php:11-16` ) | Shift baru berhasil dimulai, halaman di-refresh, status kasir berubah menjadi "Online" | Lulus |
| 3 | Kasir klik "End Shift" lalu konfirmasi Ya pada dialog | Hitung Total Sales & Tutup Shift ( `index.php:18-34` ) | Shift ditutup, total penjualan dihitung otomatis, status kasir berubah menjadi "Offline" | Lulus |
