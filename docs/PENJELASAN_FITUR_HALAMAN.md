# Penjelasan Akademis Fitur dan Fungsi Antarmuka Sistem

Dokumen ini menguraikan fungsionalitas komputasional dan antarmuka (*User Interface*) dari setiap laman yang terdapat pada sistem pemesanan. Narasi yang disajikan merupakan representasi konkret dari alur konseptual yang telah dipetakan pada dokumen *Activity Diagram*, dan disusun menggunakan kaidah tata bahasa akademis sebagai landasan struktural pada **Bab Perancangan Sistem** atau **Implementasi Antarmuka** dalam dokumen Skripsi.

---

### 1. Laman Autentikasi Sistem (*Login Page*)
*   **Representasi Alur:** Activity Diagram Autentikasi Pengguna (Login)
*   **Fungsi Laman:** Bertindak sebagai gerbang keamanan absolut (*gateway*) yang membatasi aksesibilitas menuju fungsionalitas inti (*core system*) berdasarkan hierarki otorisasi (*Role-Based Access Control* / RBAC). Laman ini memisahkan agen eksternal (publik) dari agen internal (Administrator dan Kasir).
*   **Fitur Utama:**
    *   **Formulir Kredensial Tervalidasi:** Menangkap parameter masukan pengguna berupa identitas (*username*) dan kata sandi (*password*) yang kemudian dikomparasi secara kriptografis menggunakan algoritma *hashing* (misal: *Bcrypt*).
    *   **Proteksi Lintas Situs (CSRF Protection):** Generasi token *Cross-Site Request Forgery* secara asinkron untuk memastikan integritas pengiriman paket data form.
    *   **Manajemen Sesi Tereduksi (*Session Handling*):** Regenerasi identifikasi sesi secara kontinu ketika validasi dinyatakan berhasil guna mencegah intrusi manipulasi identitas (*session fixation*).

### 2. Laman Pemesanan Menu Pelanggan (*Customer Catalog Page*)
*   **Representasi Alur:** Activity Diagram Pemesanan Menu oleh Pelanggan
*   **Fungsi Laman:** Menjadi titik kontak antarmuka pertama bagi entitas pelanggan. Laman ini mengeliminasi intervensi manusia (pramusaji) dengan mentransformasi mekanisme pemesanan fisik menjadi aliran manipulasi objek digital secara mandiri (*self-service*).
*   **Fitur Utama:**
    *   **Ekstraksi Parameter Spasial (*Spatial Binding*):** Menangkap pengidentifikasi meja melalui *Uniform Resource Locator* (URL) hasil pindaian representasi matriks (QR Code).
    *   **Renderisasi Katalog Produk Dinamis:** Menampilkan hierarki menu (kategori dan item produk) yang direpresentasikan dalam *grid* visual responsif, lengkap dengan filtrasi kategori asinkron.
    *   **Manajemen Status Keranjang Belanja (*Stateful Cart Management*):** Mengagregasi mutasi seleksi produk ke dalam modul penyimpanan temporer sebelum data ditransmisikan menjadi skema pesanan final.

### 3. Laman Integrasi Pembayaran (*Payment Interface*)
*   **Representasi Alur:** Activity Diagram Proses Pembayaran Terintegrasi (QRIS Midtrans)
*   **Fungsi Laman:** Menangani siklus penyelesaian *checkout* dan menjembatani arsitektur sistem internal dengan entitas *Payment Gateway* eksternal (Midtrans). Laman ini menyajikan lapisan abstraksi untuk memfasilitasi transaksi non-tunai secara nirkabel.
*   **Fitur Utama:**
    *   **Pemanggilan Token Asinkron (*Snap Token Generation*):** Menyerahkan *payload* data transaksi menuju antarmuka pemrograman aplikasi (API) penyedia layanan untuk memicu *callback response* berupa matriks QRIS dinamis.
    *   **Webhook Listener Integrasi:** Secara senyap menangkap *event payload* dari jaringan eksternal untuk melakukan penyelarasan keutuhan transaksi (*Settlement Mutator*) pada basis data.

### 4. Laman Pemantauan Status Pesanan (*Order Tracking Interface*)
*   **Representasi Alur:** Activity Diagram Pemantauan Status Pesanan
*   **Fungsi Laman:** Mengilustrasikan kapabilitas pelacakan pasca-transaksi (*post-transaction tracking*) secara *real-time*. Laman ini memastikan transparansi fungsional tanpa mengharuskan pembaruan kredensial masuk secara iteratif oleh pelanggan.
*   **Fitur Utama:**
    *   **Penyatuan Parameter Pelacakan (*Query Parameter Binding*):** Menangkap ID Pesanan unik sebagai referensi untuk melakukan *query* status terkini pada basis data.
    *   **Visualisasi Persistensi Status:** Menampilkan probabilitas sirkulasi keadaan pesanan aktual secara asinkron (misalnya transisi status dari *Pending* ke *Processing*).

### 5. Dasbor Kasir (*Cashier Point of Sales Dashboard*)
*   **Representasi Alur:** Activity Diagram Manajemen Dasbor Kasir
*   **Fungsi Laman:** Merupakan ruang kerja analitik taktis bagi agen Kasir. Dasbor ini difokuskan pada agregasi data waktu nyata (*real-time*) yang bersifat transaksional dan operasional sinkron.
*   **Fitur Utama:**
    *   **Pemantauan Antrean Seketika (*Real-time Order Queueing*):** Merender struktur data pesanan masuk dari titik akhir pelanggan dan menyajikannya dalam format matriks komparatif (*Pending, Processing, Completed*).
    *   **Mutator Validasi Transaksi:** Memfasilitasi eksekusi pencatatan penerimaan dana fisik (*cash settlement*) serta mengunci *state* resolusi pesanan.
    *   **Inisialisasi Utilitas Pencetakan (*Thermal Print Driver Module*):** Merender format *HyperText* menjadi format teks linear (*receipt*) untuk dikomunikasikan pada periferal perangkat keras eksternal pencetak struk.

### 6. Dasbor Utama Administrator (*Administrator Home Dashboard*)
*   **Representasi Alur:** Activity Diagram Manajemen Dasbor Administrator
*   **Fungsi Laman:** Bertindak sebagai proyektor visibilitas makro (*high-level oversight*) bagi fungsional manajerial operasional. Laman ini mengonkatenasi seluruh variabel komputasi mentah menjadi sintesis informasi strategis yang holistik.
*   **Fitur Utama:**
    *   **Visualisasi Metrik Agregat (*Data Visualization*):** Menyajikan kalkulasi statistik deskriptif seperti ekuivalensi volume penjualan kotor, kalkulasi performa sirkulasi transaksi, dan rasio perputaran inventaris.
    *   **Proyeksi Parameter Kritis:** Representasi pemantauan metrik-metrik yang krusial sebagai landasan awal navigasi modul manipulasi hierarkis lainnya.

### 7. Laman Manajemen Kategori (*Category CRUD Interface*)
*   **Representasi Alur:** Activity Diagram Pengelolaan Entitas Kategori
*   **Fungsi Laman:** Memanipulasi skema logis pendistribusian produk. Laman ini memodifikasi taksonomi hierarkis yang secara langsung mendikte parameter abstraksi katalog di sisi antarmuka pelanggan.
*   **Fitur Utama:**
    *   **Operasi CRUD Tervalidasi:** Modifikasi rekaman kategori dengan menerapkan klausa penjagaan (*guard clauses*) anti-*SQL Injection* dan perlindungan parameter input masukan.
    *   **Integritas Relasional (*Constraint Checking*):** Penerapan algoritma validasi proteksi referensial (*foreign key mapping*) yang menahan instruksi penghapusan data jika entitas kategori masih memiliki dependensi dengan rekaman tabel produk.

### 8. Laman Inventaris Menu Produk (*Menu Inventory Management*)
*   **Representasi Alur:** Activity Diagram Pengelolaan Entitas Menu (Produk)
*   **Fungsi Laman:** Laman prapemrosesan operasional tingkat lanjut untuk merancang dan memodulasi struktur bisnis (produk) secara terperinci, dari penetapan kalkulasi ekonomis hingga proyeksi media grafis.
*   **Fitur Utama:**
    *   **Pengikatan Relasi Katalog (*Foreign Key Binding*):** Pelekatan nilai identitas kategori yang direferensikan melalui formulir seleksi berbasis basis data relasional.
    *   **Manipulasi Alokasi Berkas Biner (*Binary Image Handler*):** Pemrosesan ekstensi tipe MIME dan validasi keamanan berkas (*payload boundary*), disertai dengan penempatan referensial absolut aset (*storage URL*) menuju *repository* lokal peladen.

### 9. Laman Manajemen Meja & Generator Kode QR (*Spatial Mapping & QR Generator*)
*   **Representasi Alur:** Activity Diagram Manajemen Meja & Pembuatan Kode QR
*   **Fungsi Laman:** Mentranslasikan aset prasarana perabot fisik ke dalam struktur rekaman digital terintegrasi guna mendikte siklus *entry point* pemesanan.
*   **Fitur Utama:**
    *   **Penyusunan Entitas Lokasional:** Penamaan variabel pengidentifikasi (*Table ID*) pada skema persistensi relasional.
    *   **Renderisasi Kriptografis Matriks QR (*On-the-fly QR Generation*):** Transformasi URL lokal ke dalam formasi representasi matriks dua dimensi untuk memanifestasikan modul pemicu interaksi berbasis perangkat *mobile*.

### 10. Laman Manajemen Kampanye Promo (*Promo Campaign Interface*)
*   **Representasi Alur:** Activity Diagram Manajemen Banner/Promo
*   **Fungsi Laman:** Menginisialisasi pemuatan berkas objek grafis pada lapisan tampilan *hero section* aplikasi *frontend* untuk tujuan pemasaran situasional.
*   **Fitur Utama:**
    *   **Transisi Saklar Biner (*State Boolean Toggle*):** Fungsionalitas peralihan aktivasi tanpa siklus *destructive query* (penghapusan). Representasi *boolean* yang tervalidasi `True` memicu sistem untuk menautkan iterasi aset pada mekanisme *carousel slider* eksternal.

### 11. Laman Agregasi Laporan Penjualan (*Sales Analytics & Reporting*)
*   **Representasi Alur:** Activity Diagram Agregasi Laporan Penjualan
*   **Fungsi Laman:** Subsistem audit rekapitulatif fungsional yang menyajikan kalkulasi temporal dari seluruh historis eksekusi komersial. Laman ini ditujukan untuk mentransformasi serpihan *log* transaksi menjadi abstraksi buku besar (*ledger*).
*   **Fitur Utama:**
    *   **Parsing Kueri Temporal Rentang (*Date Range Query Parsing*):** Menangkap dua nilai parameter waktu kronologis komparatif untuk diserahkan sebagai *filter parameter* sintaks komputasi basis data.
    *   **Eksporter Utilitas Berkas (*Export Document Engine*):** Transformasi perenderan tabel *DataTables* berformat matriks ke dalam struktur dokumen logis yang ringkas (*Spreadsheet Excel* / format dokumen portabel (PDF)).

### 12. Laman Manajemen Akses Pengguna (*User Control Interface*)
*   **Representasi Alur:** Activity Diagram Manajemen Akses Pengguna (User Control)
*   **Fungsi Laman:** Sentral kendali arsitektur otorisasi. Laman ini digunakan untuk mendefinisikan batas ruang otoritas dari *end-user* sistem terstruktur (*Back-Office Handlers*).
*   **Fitur Utama:**
    *   **Registrasi Otentikasi Terenkripsi (*Cryptography Credentials*):** Formulir pendaftaran Kasir atau eksekutif baru yang memberlakukan fungsi *hash* asimetris pada rekam jejak identitas basis data.
    *   **Terminasi Akses Bersyarat (*Account Suspension*):** Otorisasi *superadmin* absolut untuk membatalkan dan mengeksekusi visibilitas (*revoke access manipulation*) terhadap akun fungsional.

### 13. Laman Konfigurasi Global Sistem (*System Environment Configuration*)
*   **Representasi Alur:** Activity Diagram Konfigurasi Pengaturan Sistem
*   **Fungsi Laman:** Skema modul modifikasi yang memengaruhi instansiasi parameter konfigurasi statis pada tingkatan level sistem (*application state*), menghindari modifikasi langsung di tingkatan baris kode *source*.
*   **Fitur Utama:**
    *   **Modulasi Nilai Variabel Keadaan (*Global Variable Mapping*):** Sinkronisasi antarmuka teks pada komponen representasional entitas, di antaranya nomenklatur toko, sinkronisasi tautan ikon grafis, serta nomor korespondensi.
    *   **Injeksi Keamanan Parameter Jaringan (*API Key Sanitization*):** Pengisian parameter kunci jabat tangan komputasional pihak ketiga (contoh: *Midtrans Server Key*) dengan skema penyaringan karakter asing yang terukur.
