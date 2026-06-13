# Penjelasan Akademis Activity Diagram Aplikasi Pemesanan

Dokumen ini merangkum penjelasan konseptual dan teknis dari **13 Activity Diagram** yang memetakan alur kerja (*workflow*) sistem pemesanan. Narasi disusun menggunakan kaidah tata bahasa akademis, berfokus pada aliran logis, pemrosesan data, keamanan informasi, serta fungsionalitas komputasional, sehingga ideal untuk diadaptasi ke dalam **Bab Perancangan Sistem** pada dokumen Skripsi atau Tugas Akhir.

---

### 1. Activity Diagram Autentikasi Pengguna (Login)
* **Aktor yang Terlibat:** Pengguna Internal (Administrator, Kasir)
* **Deskripsi Alur:**
  Proses diawali ketika aktor mengakses antarmuka autentikasi (*login page*). Sistem merender formulir masuk dan secara bersamaan menghasilkan *token CSRF* (*Cross-Site Request Forgery*) sebagai langkah mitigasi keamanan berlapis. Aktor memasukkan parameter kredensial berupa nama pengguna (*username*) dan kata sandi (*password*). Saat data dikirimkan melalui metode HTTP POST, sistem melakukan verifikasi validitas *token CSRF* terlebih dahulu. Setelah divalidasi, sistem mencocokkan kredensial dengan skema autentikasi pada basis data menggunakan metode komparasi *hash*. Apabila validasi gagal, akses ditolak dan sistem melemparkan eksepsi visual (pesan galat). Apabila tervalidasi dengan benar, sistem melakukan regenerasi *Session ID* (untuk mencegah kerentanan *Session Fixation*), mencatat sesi aktif, dan mengarahkan (*redirect*) aktor menuju antarmuka dasbor sesuai hierarki hak akses (RBAC) masing-masing.

### 2. Activity Diagram Pemesanan Menu oleh Pelanggan
* **Aktor yang Terlibat:** Pelanggan
* **Deskripsi Alur:**
  Alur komputasional dimulai secara *contactless* saat pelanggan memindai kode *Quick Response* (QR) yang membawa *payload* parameter identifikasi meja spesifik. Sistem merespons dengan mengekstrak parameter tersebut dan merender antarmuka katalog produk (kategori menu) yang direlasikan dengan sesi meja. Pelanggan memilih entitas produk dan merekamnya ke dalam struktur data keranjang belanja (*cart*). Sistem secara dinamis mengalkulasi agregasi total harga pesanan. Saat pelanggan melakukan konfirmasi pembayaran (*checkout*), sistem memvalidasi ketersediaan operasional, menginisialisasi transaksi baru dengan merekam rincian ke dalam relasi tabel `orders` dan `order_items` pada basis data, lalu memproyeksikan pelanggan ke fase penyelesaian transaksi (pembayaran).

### 3. Activity Diagram Proses Pembayaran Terintegrasi (QRIS Midtrans)
* **Aktor yang Terlibat:** Pelanggan, Sistem API *Payment Gateway* (Midtrans)
* **Deskripsi Alur:**
  Diagram ini merepresentasikan integrasi sistem asinkron (*third-party interoperability*). Setelah pesanan terkonfirmasi, sistem internal mengirimkan *payload* permintaan yang mencakup *Order ID* dan nominal transaksi menuju titik akhir (*endpoint*) API Midtrans. Sebagai respons, Midtrans mengembalikan *Snap Token*, yang kemudian diproses oleh sistem lokal untuk merender representasi visual berupa *barcode* QRIS. Pelanggan mengeksekusi pembayaran menggunakan dompet digital (*e-wallet*). Sistem lokal melakukan mekanisme *listening* secara asinkron (melalui *webhook/callback handler*) untuk menerima notifikasi mutasi status dari peladen Midtrans. Saat sinyal konfirmasi pelunasan diterima, sistem memutakhirkan *state* transaksi pada basis data menjadi "Lunas" (*Settlement*) dan menyegarkan antarmuka pelanggan untuk menampilkan konfirmasi keberhasilan pesanan.

### 4. Activity Diagram Pemantauan Status Pesanan
* **Aktor yang Terlibat:** Pelanggan
* **Deskripsi Alur:**
  Mengilustrasikan mekanisme pelacakan pasca-transaksi (*post-transaction tracking*). Pelanggan berinteraksi dengan antarmuka pemantauan dengan memanfaatkan ID Pesanan unik sebagai parameter kueri. Sistem menerima parameter, menghubungkannya (*join/query*) dengan basis data, dan mengembalikan *state* pesanan aktual (misalnya: *Pending*, *Processing*, atau *Completed*). Status ini kemudian divisualisasikan kepada pelanggan. Alur ini memastikan transparansi fungsional tanpa pelanggan harus merefresh kredensial masuk secara terus-menerus.

### 5. Activity Diagram Manajemen Dasbor Kasir
* **Aktor yang Terlibat:** Kasir
* **Deskripsi Alur:**
  Proses fungsional bagi operator transaksional. Begitu Kasir memasuki ruang dasbor, sistem mengeksekusi kueri analitik *real-time* untuk memuat parameter krusial seperti statistik pendapatan *shift*, antrean pesanan tertunda, dan memproyeksikan modul *Point of Sales* (POS). Kasir berfungsi sebagai entitas penyelesai eksekusi (*execution handler*); ia memantau *queue* pesanan dari *front-end* pelanggan, memvalidasi pesanan yang berstatus *Pending*, memperbarui *state* menjadi *Selesai*, serta men-generate *output* akhir berupa struk transaksi melalui modul utilitas cetak (*printing module*). Segala mutasi keadaan ini direkam kembali ke basis data secara seketika.

### 6. Activity Diagram Manajemen Dasbor Administrator
* **Aktor yang Terlibat:** Administrator
* **Deskripsi Alur:**
  Merepresentasikan lapisan visibilitas administratif tertinggi (*high-level oversight*). Ketika Administrator mengakses antarmuka *home dashboard*, serangkaian fungsi agregasi data dieksekusi secara konkuren. Sistem menyajikan antarmuka visual berbasis metrik (*data visualization*), yang merangkum kalkulasi volume penjualan, persentase pertumbuhan, jumlah inventaris menu aktif, serta aktivitas masuk harian. Diagram ini bertindak sebagai persimpangan utama (Hub), di mana Administrator dapat menganalisis indikator kinerja sistem sebelum memutuskan navigasi ke modul manipulasi data spesifik lainnya.

### 7. Activity Diagram Pengelolaan Entitas Kategori
* **Aktor yang Terlibat:** Administrator
* **Deskripsi Alur:**
  Modul ini memetakan operasi manipulasi *Create, Read, Update, Delete* (CRUD) pada himpunan data kategorisasi. Sistem menyajikan tabel relasional kategori menu yang diambil dari basis data. Administrator memiliki kapabilitas penuh untuk mendaftarkan kategori baru, menyunting nomenklatur/deskripsi, atau mengeksekusi penghapusan kategori (yang telah divalidasi tidak memiliki dependensi relasional). Setiap prapemrosesan operasi mencakup validasi tipe masukan untuk mencegah serangan *SQL Injection*, diikuti dengan eksekusi pernyataan *Data Manipulation Language* (DML).

### 8. Activity Diagram Pengelolaan Entitas Menu (Produk)
* **Aktor yang Terlibat:** Administrator
* **Deskripsi Alur:**
  Alur manajemen objek inti pada sistem. Administrator menavigasi ke laman inventaris menu. Sistem memetakan daftar rekaman produk beserta pengikatan (*foreign key constraints*) terhadap entitas kategori. Jika Administrator melakukan penambahan atau pemutakhiran data, alur memproses penangkapan input tekstual (harga, deskripsi) beserta sub-rutin pengolahan berkas binari (*image file upload*). Sistem melakukan pemindaian validitas MIME *Type* dan batas dimensi resolusi untuk aset citra, mengalokasikan penyimpanan berkas pada peladen (*file storage*), lalu mencatat jalur referensial (*absolute path/URL*) citra tersebut ke dalam basis data bersamaan dengan simpanan metadata produk.

### 9. Activity Diagram Manajemen Meja & Pembuatan Kode QR
* **Aktor yang Terlibat:** Administrator
* **Deskripsi Alur:**
  Pemetaan lokasional (*spatial binding*) dari meja fisik ke basis data sistem. Saat Administrator meregistrasi entitas meja (contoh: "Meja 10"), sistem tidak hanya menyimpan data identifikasi tersebut, tetapi juga memicu modul pustaka eksternal (contoh: fungsi *QR Code Generator*) untuk menghasilkan citra matriks *QR Code* 2D secara *on-the-fly*. Matriks QR ini mengonversi susunan Uniform Resource Locator (URL) yang berisi pengidentifikasi unik meja. Aset QR ini disimpan ke dalam *storage* untuk dirender dan diunduh oleh Administrator, kemudian dapat didistribusikan pada perabot fisik sebagai *entry point* pelanggan.

### 10. Activity Diagram Manajemen Banner/Promo
* **Aktor yang Terlibat:** Administrator
* **Deskripsi Alur:**
  Manajemen kampanye pemasaran digital melalui proyektor *carousel* visual. Administrator mengakses panel kendali promo dan menginisiasi unggahan (*upload*) grafis material promosi. Sama halnya dengan pengolahan menu, sistem memvalidasi keamanan berkas media. Nilai tambah pada fungsionalitas ini adalah penentuan saklar Boolean (Status: Aktif/Nonaktif). Objek antarmuka dengan *state* Boolean bernilai `True` (Aktif) akan diekstraksi oleh modul antarmuka pelanggan sebagai lapisan *banner overlay* informasional pada halaman utama.

### 11. Activity Diagram Agregasi Laporan Penjualan
* **Aktor yang Terlibat:** Administrator
* **Deskripsi Alur:**
  Proses yang memanifestasikan kebutuhan audit (*audit trail*) dan rekapitulasi data. Administrator mengoperasikan antarmuka pelaporan dengan memasukkan parameter spesifik berupa rentang kronologis (*Date Range Filter*). *Payload filter* ini diserahkan kepada mesin basis data, yang kemudian menjalankan sintaks kueri analitik. Subsistem ini menjumlahkan subtotal harga pokok, frekuensi transaksi, dan merepresentasikan konklusinya melalui format tabular *DataTables*. Jika administrator meminta tindakan *Export*, modul pelaporan akan mengompilasi representasi *HyperText* ke dalam tata letak dokumen formal seperti format *Spreadsheet* (Excel) atau *Portable Document Format* (PDF).

### 12. Activity Diagram Manajemen Akses Pengguna (User Control)
* **Aktor yang Terlibat:** Administrator
* **Deskripsi Alur:**
  Fungsionalitas yang memastikan kepatuhan terhadap keamanan sistem (*System Integrity Control*). Administrator diberikan otorisasi untuk menampilkan, menambahkan, atau menangguhkan (*suspend*) profil akun dari agen fungsional (Admin, Kasir). Setiap inisialisasi akun baru memerlukan registrasi kredensial kata sandi, yang diproses oleh sistem dengan mengenkripsinya melalui algoritma enkripsi *hash* searah (*one-way hash function* seperti *Bcrypt*). Dengan demikian, data privasi dicegah dari risiko kompromi data (*data breach*), dan arsitektur akses berbasis hierarki (RBAC) diimplementasikan secara konsisten.

### 13. Activity Diagram Konfigurasi Pengaturan Sistem
* **Aktor yang Terlibat:** Administrator
* **Deskripsi Alur:**
  Diagram yang menunjukkan proses modifikasi pada variabel *environment* dan pengaturan aplikasi *runtime* (*global state modification*). Administrator mengakses sub-sistem konfigurasi untuk menyelaraskan nilai *Key-Value* parameter dasar operasi toko (contoh: Penamaan Identitas Toko, Logo Fasad, Alamat, Nomor Telepon representatif, hingga Parameter Integrasi Key Midtrans). Sistem menangkap data input ini, melakukan filter sanitasi terhadap karakter invasif (*sanitization logic*), lalu memperbarui struktur data pengaturan pusat. Perubahan konfigurasi secara otomatis mendikte perubahan reflektif visual (seperti pergantian logo) dan modifikasi *behavioural* integrasi sistem pihak ketiga pada sesi perutean pengguna secara *real-time*.
