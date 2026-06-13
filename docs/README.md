# ☕ Pemesanan_AK - Smart Ordering System

Sistem Pemesanan Kafe *Self-Service* Berbasis QR Code kelas *Enterprise*, dirancang dengan estetika *Swiss Minimalist* dan standar *clean code* tinggi. Aplikasi ini memisahkan pengalaman pelanggan yang memesan langsung dari meja dengan dasbor kasir untuk manajemen operasional secara *real-time*.

## ✨ Fitur Unggulan

*   **Pemesanan Mandiri (QR Code):** Pelanggan memindai kode QR di meja untuk mengakses menu dan meracik keranjang mereka secara digital.
*   **Pembayaran Otomatis (Midtrans QRIS):** Integrasi *Payment Gateway* kelas atas menggunakan Midtrans Core API. Pelanggan memindai QRIS dinamis, dan sistem secara seketika mendeteksi keberhasilan pembayaran (melalui *webhook callback*) tanpa sentuhan fisik pada layar kasir!
*   **Stok Menu Real-Time (Asynchronous Polling):** Saat admin menandai menu sebagai "Habis", dalam hitungan detik, antarmuka pelanggan akan memblokir menu tersebut dan menghapusnya dari keranjang jika belum dibayar.
*   **Estetika UI/UX Premium:** Antarmuka responsif mengadopsi gaya desain kontemporer (*Glassmorphism*, transisi fisika asimtotik, tipografi *tabular-nums*, dan sistem *Toast Notification* kustom). Bebas dari komponen *template* murahan.
*   **Keamanan Ekstrem:** Dilindungi sepenuhnya dari *SQL Injection* via PDO *Prepared Statements*, proteksi *Cross-Site Request Forgery (CSRF)* pada setiap formulir, dan kebal dari serangan *price-tampering* karena seluruh validasi perhitungan diisolasi secara tertutup pada sisi peladen (*server-side*).

## 🛠️ Stack Teknologi

*   **Backend:** PHP 8.x Native (PDO)
*   **Frontend:** Vanilla JavaScript (Fetch API, Modul Asinkron)
*   **Styling:** Tailwind CSS (via CDN) + CSS Kustom
*   **Database:** MySQL / MariaDB (Relasional)
*   **Ikonografi:** Lucide Icons

## 🚀 Panduan Instalasi

Ikuti langkah ini untuk memasang proyek di lingkungan lokal Anda (Laragon/XAMPP):

1. **Kloning Repositori:**
   ```bash
   git clone https://github.com/Marvx-US/Pemesanan_ak.git
   cd Pemesanan_ak
   ```

2. **Pengaturan Basis Data:**
   * Masuk ke phpMyAdmin / HeidiSQL / DBeaver.
   * Buat basis data bernama `pemesanan_ak`.
   * Impor file **`database/schema.sql`** ke dalam basis data tersebut untuk membangun tabel (`menus`, `orders`, `order_details`, `shifts`, `users`).

3. **Konfigurasi Lingkungan:**
   * Buka **`config/db.php`**, sesuaikan variabel `$user` dan `$pass` dengan pengaturan database lokal Anda.
   * Buka **`config/midtrans.php`**, ganti teks `YOUR_SERVER_KEY_HERE` dengan *Server Key* Sandbox/Production Anda yang didapat dari [Dashboard Midtrans](https://dashboard.midtrans.com).

4. **Jalankan Aplikasi:**
   Buka *browser* ke alamat lokal *server* Anda (misal: `http://localhost/PEMESANAN-AK` atau `http://pemesanan-ak.test` jika menggunakan Laragon otomatis).

## 📂 Peta Arsitektur Proyek

*   `/admin/` : Dasbor manajemen katalog, laporan, dan data otentikasi.
*   `/kasir/` : Dasbor lalu lintas pesanan *real-time* berbasis *polling* ringan.
*   `/api/` : Titik masuk (*endpoint*) REST JSON asinkron. Mesin utama pemroses pesanan, status inventaris, dan penerima sinyal Webhook Midtrans.
*   `/auth/` : Pengendali otentikasi pengguna (Login/Logout).
*   `/config/` : Jantung utilitas (Konfigurasi basis data, perlindungan CSRF `auth.php`, dan utilitas cURL `midtrans.php`).
*   `/customer/` : Halaman interaksi pelanggan (seperti status pesanan).
*   `/database/` : Struktur tabel SQL.
*   `/docs/` : Dokumentasi arsitektur dan aturan (*rules*) proyek.
*   `/assets/` : Penampungan CSS Global dan Injeksi *Client-Side JavaScript* (`app.js`, `kasir.js`).
## 🤝 Kontribusi

**SANGAT PENTING:** Sebelum menulis satu baris kode pun, harap membaca manifesto utama di **`RULES.md`**. Proyek ini dikelola dengan standar yang ketat (seperti pelarangan *Nested If*, aturan penamaan variabel yang absolut, dan tata letak UI yang diwajibkan).
