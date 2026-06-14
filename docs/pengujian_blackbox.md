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

### Tabel Kasus Uji

| No | Skenario Uji | Data Input | Output yang Diharapkan | Hasil Uji |
| :--: | :--- | :--- | :--- | :--: |
| 1 | Username dan password dikosongkan, klik Login | Username: *(kosong)*, Password: *(kosong)* | Muncul pesan error: **"Username dan password wajib diisi."** Halaman login tetap tampil | ✅ Lulus |
| 2 | Username tidak terdaftar atau password salah | Username: `userpalsu`, Password: `apapun123` | Muncul pesan error: **"Username atau password salah."** Halaman login tetap tampil | ✅ Lulus |
| 3 | Username dan password admin benar | Username: `admin`, Password: *(password valid)* | Login berhasil, pengguna diarahkan ke halaman **Dashboard Admin** (`admin/index.php`) | ✅ Lulus |

---

## 📱 2. Pengujian Black Box: Modul Pelanggan – Kode QR (`customer/qris_payment.php`)

### Tabel Kasus Uji

| No | Skenario Uji | Data Input | Output yang Diharapkan | Hasil Uji |
| :--: | :--- | :--- | :--- | :--: |
| 1 | Akses halaman dengan ID pesanan yang tidak ada di database | URL: `qris_payment.php?id=99999` | Halaman berhenti dan menampilkan teks: **"Pesanan tidak ditemukan."** | ✅ Lulus |
| 2 | Upload file dengan format yang tidak didukung | File: `dokumen.pdf` | Muncul pesan error: **"Format file tidak didukung. Harap gunakan gambar JPG, PNG, atau WEBP."** | ✅ Lulus |
| 3 | Upload file gambar JPG yang valid | File: `bukti.jpg` *(gambar valid)* | Upload berhasil, pengguna diarahkan ke halaman **Status Pesanan** (`order_status.php`) | ✅ Lulus |

---

## 🖥️ 3. Pengujian Black Box: Modul Kasir – Manajemen Shift (`kasir/index.php`)

### Tabel Kasus Uji

| No | Skenario Uji | Data Input / Aksi | Output yang Diharapkan | Hasil Uji |
| :--: | :--- | :--- | :--- | :--: |
| 1 | Kasir login dan belum memulai shift | Sesi kasir aktif, belum ada shift aktif | Halaman menampilkan **overlay layar terkunci** dengan tombol **"Mulai Shift Saya"** | ✅ Lulus |
| 2 | Kasir klik tombol "Mulai Shift Saya" | Klik tombol **"Mulai Shift Saya"** | Shift baru dimulai, halaman di-refresh, status kasir berubah menjadi **"Online"** | ✅ Lulus |
| 3 | Kasir klik "End Shift" lalu mengkonfirmasi | Klik **"End Shift"**, pilih **Ya/OK** pada dialog konfirmasi | Shift ditutup, total penjualan dihitung, status kasir berubah menjadi **"Offline"** | ✅ Lulus |
