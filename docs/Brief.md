PROJECT BRIEF: SISTEM PEMESANAN KAFE BERBASIS QR CODE

1. Deskripsi Proyek

Proyek ini adalah sebuah aplikasi web (Web App) berbasis self-service yang dirancang untuk industri F&B (kafe/restoran). Sistem ini memungkinkan pelanggan untuk memindai (scan) QR Code yang ada di meja mereka, melihat menu, membuat pesanan, dan memilih metode pembayaran secara mandiri melalui smartphone mereka. Pesanan akan langsung diteruskan ke dashboard kasir secara real-time.

Tujuan Utama:

Meningkatkan efisiensi pemesanan dan mengurangi antrean di kasir.

Meminimalisir kesalahan pencatatan pesanan (karena pelanggan menginput sendiri).

Mempermudah manajemen menu dan rekap penjualan bagi pemilik bisnis.

2. Tumpukan Teknologi (Tech Stack)

Bahasa Pemrograman (Backend): PHP (Native, tanpa framework seperti Laravel atau CodeIgniter).

Database: MySQL (Relational Database Management System).

Frontend (Tampilan):

HTML5 & CSS3.

JavaScript (Native/Vanilla) untuk interaktivitas dasar.

Styling: CSS Native atau menggunakan library ringan seperti Bootstrap/Tailwind (opsional, disarankan Tailwind untuk desain yang lebih modern seperti Bento Grid/Glassmorphism).

Komunikasi Asinkron: AJAX (menggunakan XMLHttpRequest atau Fetch API di JavaScript) untuk pembaruan data real-time di kasir tanpa refresh halaman.

3. Daftar Fitur Berdasarkan Hak Akses (Role)

A. Sisi Pelanggan (Customer Interface)

Akses: Tanpa Login (melalui Scan QR Code).

QR Code Scanning & Routing: URL otomatis membaca parameter nomor meja (contoh: ?meja=12).

Katalog Menu Interaktif:

Menampilkan daftar menu beserta foto, nama, deskripsi singkat, dan harga.

Filter/Navigasi berdasarkan kategori (Makanan, Minuman, Dessert).

Indikator status (Tersedia / Habis).

Keranjang Pesanan (Cart):

Menambah/mengurangi kuantitas (qty) pesanan.

Kolom catatan opsional per item (contoh: "Esnya sedikit saja").

Kalkulasi subtotal dan total harga secara otomatis.

Checkout & Pembayaran:

Pemilihan metode pembayaran: Tunai (Cash) atau QRIS.

(Jika QRIS dinamis belum tersedia): Menampilkan instruksi pembayaran ke kasir atau menampilkan gambar QRIS statis toko.

Halaman Status Pesanan: Menampilkan ID Pesanan dan status terkini (Pending -> Diproses -> Selesai).

B. Sisi Kasir (Cashier Dashboard)

Akses: Wajib Login akun Kasir.

Sistem Login Kasir: Otentikasi standar menggunakan username dan password.

Dashboard Pemantauan (Real-time):

Tampilan daftar pesanan masuk (incoming orders).

Pembaruan otomatis menggunakan AJAX (tanpa perlu refresh manual).

Notifikasi suara (audio alert) saat ada pesanan baru masuk.

Manajemen Pesanan Masuk:

Melihat rincian pesanan dari suatu meja (item, jumlah, catatan).

Tombol aksi status: "Terima Pesanan / Diproses", "Tolak", "Selesai".

Verifikasi Pembayaran:

Tombol "Konfirmasi Pembayaran" (Ubah status dari Unpaid ke Paid) khusus untuk metode bayar Cash atau verifikasi manual QRIS statis.

C. Sisi Admin (Administrator Panel)

Akses: Wajib Login akun Admin.

Sistem Login Admin: Otentikasi untuk akses penuh.

Manajemen Kategori Menu (CRUD): Tambah, Edit, Hapus kategori (misal: "Kopi", "Non-Kopi", "Snack").

Manajemen Item Menu (CRUD):

Tambah/Edit/Hapus menu masakan.

Upload dan manajemen gambar menu.

Atur visibilitas/ketersediaan stok (Tersedia / Kosong).

Manajemen Data Meja (QR Generator):

Daftar meja yang tersedia.

Fungsi generate dan download gambar QR Code unik untuk masing-masing meja.

Laporan Penjualan (Sales Report):

Tabel rekapitulasi histori pesanan yang sudah Selesai dan Paid.

Filter sederhana (berdasarkan tanggal/hari).

Manajemen Pengguna (User Management): Tambah, Edit, Hapus akun staf Kasir.

4. Struktur Database Inti (MySQL)

Ini adalah kerangka dasar tabel yang dibutuhkan (dinormalisasi):

users: Menyimpan data login staf.

id, username, password (hashed), role (enum: 'admin', 'kasir').

tables: Menyimpan data meja dan URL QR-nya.

id, nomor_meja, url_qr.

categories: Menyimpan kategori makanan/minuman.

id, nama_kategori.

menus: Menyimpan detail produk.

id, kategori_id (FK), nama_menu, deskripsi, harga, gambar_url, status (enum: 'tersedia', 'habis').

orders: Menyimpan header transaksi pemesanan.

id, meja_id (FK), waktu_pesan, total_harga, metode_bayar (enum: 'cash', 'qris'), status_bayar (enum: 'unpaid', 'paid'), status_pesanan (enum: 'pending', 'diproses', 'selesai', 'dibatalkan').

order_details: Menyimpan rincian item per pesanan.

id, order_id (FK), menu_id (FK), qty, subtotal, catatan.

5. Flowchart Singkat (Alur Pengguna)

Pelanggan: Scan QR di Meja 5 -> Buka URL .../?meja=5 -> Lihat Menu -> Pilih Nasi Goreng (Qty:2) & Teh Es (Qty:2) -> Checkout -> Pilih Bayar Cash -> Pesanan tersimpan (Status: Pending, Unpaid).

Sistem: AJAX membaca database ada pesanan baru -> Bunyi 'Ting' di Komputer Kasir.

Kasir: Lihat pesanan Meja 5 -> Klik "Diproses" (dapur membuat makanan).

Pelanggan: Makan selesai -> Pergi ke kasir bayar tunai.

Kasir: Terima uang -> Cari pesanan Meja 5 -> Klik "Lunas & Selesai" -> Transaksi ditutup.