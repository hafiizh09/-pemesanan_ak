# Penjelasan Akademis Sequence Diagram Aplikasi Pemesanan

Dokumen ini memuat abstraksi teknis dari **13 Sequence Diagram** (Diagram Urutan) yang mengilustrasikan interaksi temporal antara berbagai objek (*lifelines*) di dalam sistem pemesanan. *Sequence Diagram* digunakan pada tahap Perancangan Sistem Berorientasi Objek untuk mendeskripsikan secara kronologis bagaimana pesan (*messages/method calls*) dipertukarkan antara *Actor*, *View/UI*, *Controller*, dan *Database*. 

Dokumen ini disusun menggunakan nomenklatur akademis yang sesuai untuk dicantumkan pada bab **Perancangan Sistem** dalam skripsi.

---

### 1. Sequence Diagram Autentikasi Pengguna (Login)
* **Lifelines:** Pengguna, Sistem (UI), Sistem (Controller), Database
* **Deskripsi Interaksi:**
  Fase diawali ketika `Pengguna` memicu inisialisasi dengan mengakses `Sistem (UI)`. UI meminta parameter autentikasi dari `Sistem (Controller)`, yang kemudian merender *form* beserta token mitigasi CSRF kembali ke UI. Setelah `Pengguna` melakukan injeksi input (username & password), data dipancarkan (POST) kembali ke `Controller`. `Controller` melakukan validasi internal terhadap CSRF, kemudian mengeksekusi metode pemanggilan (`Verifikasi Kredensial`) ke entitas `Database` menggunakan komparasi fungsi *hash*. `Database` mengembalikan respons validitas. Apabila terautentikasi, `Controller` memicu fungsi `Regenerate Session ID` pada dirinya sendiri (*self-message*) untuk menghindari *Session Fixation*, sebelum akhirnya memproyeksikan perintah *redirect* ke dasbor pada lapisan `UI`.

### 2. Sequence Diagram Pemesanan Menu oleh Pelanggan
* **Lifelines:** Pelanggan, Sistem (UI), Sistem (Controller), Database
* **Deskripsi Interaksi:**
  Alur dipicu (*triggered*) saat `Pelanggan` mengeksekusi fungsionalitas pindai kode QR. `Sistem (UI)` mengarahkan permintaan ke `Controller` dengan menyertakan *payload* ID Meja. `Controller` menginstruksikan `Database` untuk melakukan instruksi agregasi dan *fetch* (*SELECT*) tabel kategori dan menu. Data dikembalikan dan diteruskan ke `UI` untuk dirender. Selama fase kompilasi pesanan, setiap penambahan *item* memicu komunikasi internal antara `UI` dan `Controller` untuk mengalkulasi nilai total agregat sementara (*subtotal/grand total*). Saat pengguna mengirimkan perintah *Checkout*, `Controller` menerima *payload* transaksional dan menginstruksikan `Database` untuk merekam data secara relasional ke tabel `orders` dan `order_items`. Transaksi diakhiri dengan perutean ke antarmuka Pembayaran.

### 3. Sequence Diagram Proses Pembayaran Terintegrasi (QRIS Midtrans)
* **Lifelines:** Pelanggan, Sistem Internal, Midtrans API
* **Deskripsi Interaksi:**
  Mengilustrasikan arsitektur *Service-Oriented* (SOA). `Sistem Internal` memanggil antarmuka eksternal (`Midtrans API`) melalui transmisi *HTTP Request* berisikan rincian pesanan. `Midtrans API` memberikan respons balik (*return message*) berupa `Snap Token`. `Sistem Internal` meneruskan representasi visual *token* ini kepada `Pelanggan` dalam bentuk matriks QRIS. `Pelanggan` melakukan konfirmasi di luar sistem (melalui entitas perbankan). Sistem `Midtrans` menginisiasi panggilan (*callback/webhook*) kembali ke *endpoint* `Sistem Internal` yang bertindak sebagai *listener*. Menanggapi *callback* ini, `Sistem Internal` mengeksekusi mutasi internal (*Update Status: Lunas*) tanpa campur tangan antarmuka grafis pelanggan, dan barulah UI disinkronisasi untuk menampilkan status akhir.

### 4. Sequence Diagram Pemantauan Status Pesanan
* **Lifelines:** Pelanggan, Sistem (UI), Sistem (Controller), Database
* **Deskripsi Interaksi:**
  Merupakan rutinitas *querying* sederhana berorientasi *read-only*. `Pelanggan` meminta pembaruan dengan mengarahkan kueri HTTP (*GET*) berisi *identifier* pesanan pada `Sistem (UI)`. Permintaan dilempar menuju lapisan logis (`Controller`), yang menginstruksikan lapisan penyimpan persisten (`Database`) untuk mengambil rekam jejak rekaman (*record*) status. `Database` merespons dengan string status (*state*). `Controller` memberikan *feedback* sinkron ke `UI` untuk mengubah elemen DOM sesuai respons basis data tersebut.

### 5. Sequence Diagram Manajemen Dasbor Kasir
* **Lifelines:** Kasir, Sistem (UI), Sistem (Controller), Database
* **Deskripsi Interaksi:**
  Fungsionalitas ini beroperasi dalam alur interaksi tingkat tinggi. Saat `Kasir` meminta visualisasi *dashboard*, `Controller` mengambil kumpulan data asinkron (*polling* antrean *real-time*) dari `Database`. `Database` mengembalikan vektor data pesanan tertunda (*pending orders*). `UI` menampilkan komponen antarmuka POS. Ketika `Kasir` memanipulasi *state* transaksi menjadi "Selesai", sebuah *request* modifikasi dipancarkan ke `Controller`, yang selanjutnya merambatkan kueri *UPDATE* ke relasi pesanan di dalam `Database`. `Database` mengonfirmasi keberhasilan DML, lalu `Controller` memerintahkan `UI` untuk melakukan pembangkitan representasi cetak (*Receipt Generation*).

### 6. Sequence Diagram Manajemen Dasbor Administrator
* **Lifelines:** Administrator, Sistem (UI), Sistem (Controller), Database
* **Deskripsi Interaksi:**
  Proses analisis data *dashboard* administratif ini memiliki alur *fetch-render* yang padat. Pemanggilan yang dilakukan oleh `Administrator` melalui `Sistem (UI)` ke arah `Controller` diubah menjadi satu seri agregasi terkompilasi (*COUNT, SUM, GROUP BY*) pada entitas `Database`. Data metrik yang meliputi volume transaksi harian dan statistik katalog dikembalikan ke `Controller`. Lapisan `Controller` mentransformasikan respons mentah ini ke dalam struktur matriks objek, lalu meneruskannya kepada entitas `UI` untuk proses rendering bagan grafis (*chart rendering*).

### 7. Sequence Diagram Pengelolaan Entitas Kategori
* **Lifelines:** Administrator, Sistem (UI), Sistem (Controller), Database
* **Deskripsi Interaksi:**
  Fase interaktif *Data Definition/Manipulation*. Awal mula berisikan pemuatan indeks data, ketika `Controller` melakukan kueri `SELECT` terhadap entitas kategori di `Database`. Saat `Administrator` mengeksekusi penambahan atau pemutakhiran kategori, fungsi interaksi dikirim dari `UI` menuju `Controller`. Sebagai implementasi keamanan preventif (*Guard Clause*), `Controller` memanggil dirinya sendiri (*self-message*) untuk melaksanakan sanitasi masukan (*SQL Injection validation*). Setelah bersih, perintah *Data Manipulation Language* (DML) dikirim ke `Database`. Sesudah konfirmasi sukses didapat dari DBMS, tabel grafis pada `UI` diinstruksikan untuk disegarkan ulang.

### 8. Sequence Diagram Pengelolaan Entitas Menu (Produk)
* **Lifelines:** Administrator, Sistem (UI), Sistem (Controller), Database, Storage (Sistem Berkas Lokal/Awan)
* **Deskripsi Interaksi:**
  Alur manipulasi yang melibatkan entitas *file system* eksternal. Pesan awal berisi *payload* form (*multipart/form-data*) dikirim oleh `Administrator` melalui `Sistem (UI)` ke arah `Controller`. `Controller` melakukan pemanggilan internal ganda untuk memvalidasi tipe MIME (*MIME Extension Check*). Alih-alih langsung mengakses relasi data, `Controller` terlebih dahulu memanggil antarmuka sistem berkas (`Storage`) untuk melakukan *Write Stream* terhadap aset citra. `Storage` mengembalikan penunjuk lokasi fisik (*absolute path*). String lokasi ini kemudian diinjeksikan bersama variabel *metadata* lain untuk ditransaksikan sebagai rekaman tunggal ke dalam `Database`. Eksepsi berhasil diinformasikan ke lapisan presentasi.

### 9. Sequence Diagram Manajemen Meja & Pembuatan Kode QR
* **Lifelines:** Administrator, Sistem (UI), Sistem (Controller), QR Generator (Modul Eksternal), Database
* **Deskripsi Interaksi:**
  Proses generatif di mana manipulasi rekaman memicu fungsi sekunder. `Sistem (UI)` memprakarsai permintaan pendaftaran ID Meja ke `Controller`. Pemanggilan awal diarahkan ke `Database` untuk melaksanakan perekaman (*INSERT*), yang mengembalikan balasan konfirmasi beserta identitas entitas baru (*Primary Key* / ID). Bermodalkan pengidentifikasi unik ini, `Controller` memanggil layanan modul komputasional (*QR Generator*) untuk menghasilkan matriks matriks biner visual. *QR Generator* memancarkan gambar (*image blob/file*) ke `Controller`. Objek akhir dikembalikan ke `UI` untuk dimanifestasikan sebagai *printable resource*.

### 10. Sequence Diagram Manajemen Banner/Promo
* **Lifelines:** Administrator, Sistem (UI), Sistem (Controller), Storage, Database
* **Deskripsi Interaksi:**
  Sintesis fungsi manajemen data dengan penyimpanan media visual. Pemanggilan berawal dari transmisi data grafis via `Sistem (UI)`. `Controller` mengalokasikan entitas media tersebut ke dalam `Storage` dan menanti konfirmasi path penempatan (URL). Berdasarkan konfigurasi *switch* ketersediaan dari *user input* (Nilai Boolean Aktif/Pasif), `Controller` mengeksekusi operasi basis data untuk merekam *path* media berdampingan dengan konstanta Boolean statusnya. Proses rampung ketika balasan dikirim dari `Database`, menginstruksikan `UI` untuk merender ulang parameter promosi yang tersedia.

### 11. Sequence Diagram Agregasi Laporan Penjualan
* **Lifelines:** Administrator, Sistem (UI), Sistem (Controller), Database
* **Deskripsi Interaksi:**
  Proses kalkulasi dengan volume beban yang tinggi secara algoritmik. Kueri diawali dengan penyediaan rentang parameter (tanggal mulai, tanggal akhir) oleh `Administrator`. Parameter ini dienkapsulasi oleh `UI` untuk disampaikan ke `Controller`. `Controller` men-generate dan meneruskan instruksi analitik bertingkat (*Aggregate Functions*) kepada `Database`. Saat kompilasi rampung, serangkaian larik data dikirimkan kembali ke `Controller`. `Controller` bertugas dua hal secara paralel (secara konseptual); mengirim representasi struktur tabel (DataTables) ke `UI`, dan jika opsi unduh (ekspor PDF/Excel) dieksekusi, mensintesis *stream file* dan mengarahkan peladen untuk melemparkan perintah unduhan kepada peramban (*browser*) klien.

### 12. Sequence Diagram Manajemen Akses Pengguna
* **Lifelines:** Administrator, Sistem (UI), Sistem (Controller), Database
* **Deskripsi Interaksi:**
  Merepresentasikan manajemen *Role-Based Access Control* (RBAC) dengan lapisan pengamanan kriptografi. Instruksi pembuatan akun dilemparkan ke `Controller` dari ruang administratif `UI`. Lapisan fungsional `Controller` wajib mengeksekusi panggilan fungsi independen secara internal (*Hashing Method/Bcrypt*) demi mensekresi *string plaintext* menjadi format *salt/hash*. Bentuk rahasia ini, tidak pernah diekspos, disuplai ke `Database` sebagai perintah injeksi profil. Siklus diakhiri dengan sinkronisasi antarmuka `UI` yang kini memuat identitas terdaftar yang baru dengan konfigurasi akses absolut.

### 13. Sequence Diagram Konfigurasi Pengaturan Sistem
* **Lifelines:** Administrator, Sistem (UI), Sistem (Controller), Database
* **Deskripsi Interaksi:**
  Interaksi mutasi global sistem (*Global Variables Modification*). `Sistem (UI)` mengakomodasi pengubahan parameter *environment* (*API Keys*, Nomenklatur) dengan menyerahkan himpunan *state* baru ke `Controller`. Karena parameter ini vital, pemanggilan konsekutif internal untuk mengeksekusi filter sanitasi (*XSS prevention / Injection sanitization*) dirilis. Jika tervalidasi, komitmen perubahan dikirim ke tabel konfigurasi terpusat pada `Database`. Nilai mutasi direspons balik kepada entitas sistem secara keseluruhan, yang berarti pembaruan nilai *prop/state* pada antarmuka *front-end* pelanggan secara instan tanpa interupsi *runtime* server yang signifikan.
