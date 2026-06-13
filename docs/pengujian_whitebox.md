# Dokumen Pengujian White Box (Sistem Pemesanan Cafe AK)

Dokumen ini berisi pengujian *White Box* untuk tiga fitur utama pada sistem pemesanan Cafe AK:
1. **Proses Login Multi-Role (`auth/login.php`)**
2. **Proses Checkout Pelanggan (`api/create_order.php`)**
3. **Proses Konfirmasi Pembayaran QRIS (`customer/qris_payment.php`)**

---

## 🔑 1. Pengujian White Box: Login (`auth/login.php`)

Sesuai dengan alur yang disederhanakan, pengujian *White Box* untuk proses login berfokus pada validasi input, pencarian user, verifikasi password, dan pengalihan (redirect) berdasarkan role.

### A. Flowchart & Flowgraph

#### 📊 Flowchart Proses Login
```mermaid
graph TD
    Node1([1. Mulai dan Input]) --> Node2{2. Input Kosong?}
    Node2 -- Ya --> Node3[3. Set Error: Input Kosong]
    Node2 -- Tidak --> Node4{4. User Ditemukan?}
    
    Node4 -- Tidak --> Node5[5. Set Error: User Tidak Ditemukan]
    Node4 -- Ya --> Node6{6. Password Cocok?}
    
    Node6 -- Tidak --> Node7[7. Set Error: Password Salah]
    Node6 -- Ya --> Node8{8. Role Admin?}
    
    Node8 -- Ya --> Node9[9. Redirect Dashboard Admin]
    Node8 -- Tidak --> Node10[10. Redirect Dashboard Kasir]
    
    Node3 --> Node11(((11. Selesai)))
    Node5 --> Node11
    Node7 --> Node11
    Node9 --> Node11
    Node10 --> Node11
```

#### 📈 Flowgraph
```mermaid
graph TD
    1((1)) --> 2((2))
    2 -- Ya --> 3((3))
    2 -- Tidak --> 4((4))
    
    4 -- Tidak --> 5((5))
    4 -- Ya --> 6((6))
    
    6 -- Tidak --> 7((7))
    6 -- Ya --> 8((8))
    
    8 -- Ya --> 9((9))
    8 -- Tidak --> 10((10))
    
    3 --> 11((11))
    5 --> 11((11))
    7 --> 11((11))
    9 --> 11((11))
    10 --> 11((11))
```

### B. Tabel Keterangan Node
| Node | Logika / Aktivitas | Deskripsi |
| :---: | :--- | :--- |
| **1** | `Mulai & Input` | Sistem menerima input *username* dan *password* dari pengguna. |
| **2** | `if (empty($user) \|\| empty($pass))` | Node Keputusan: Pengecekan apakah input kosong. |
| **3** | `Set Error` | Jika kosong (Ya), sistem mengatur pesan error validasi. |
| **4** | `if ($user_ditemukan)` | Node Keputusan: Pengecekan apakah username ada di database. |
| **5** | `Set Error` | Jika tidak ditemukan (Tidak), sistem mengatur pesan error user tidak terdaftar. |
| **6** | `if (password_verify(...))` | Node Keputusan: Pengecekan kecocokan password. |
| **7** | `Set Error` | Jika salah (Tidak), sistem mengatur pesan error password salah. |
| **8** | `if ($role == 'admin')` | Node Keputusan: Pengecekan role pengguna yang berhasil login. |
| **9** | `Redirect` | Jika Admin (Ya), sistem mengarahkan ke dashboard Admin. |
| **10** | `Redirect` | Jika Kasir (Tidak), sistem mengarahkan ke dashboard Kasir. |
| **11** | `Selesai` | Akhir proses (menampilkan halaman dengan pesan error atau berhasil masuk). |

### C. Perhitungan Cyclomatic Complexity (CC) & Jumlah Region
Berdasarkan visualisasi flowgraph di atas, kita dapat menghitung kompleksitas siklomatisnya:

* **Jumlah Sisi (Edges, E)** = 14
* **Jumlah Node (N)** = 11
* **Rumus**: $V(G) = E - N + 2$
* **Perhitungan**: $V(G) = 14 - 11 + 2 = 5$

Metode *Predicate Node* (Node Keputusan):
* Terdapat 4 Predicate Node (Node 2, 4, 6, dan 8).
* **Rumus**: $V(G) = P + 1$
* **Perhitungan**: $V(G) = 4 + 1 = 5$

**Jumlah Region (Daerah)**:
* **Jumlah Region = 5** (Terdapat 4 area tertutup di dalam graf dan 1 area terbuka di luar graf).

*Maka, terdapat **5 Jalur Independen** dalam proses login.*

### D. Jalur Independen (Independent Paths)
Berdasarkan perhitungan CC sebanyak 5, berikut adalah 5 jalur independen yang menguji setiap kemungkinan logika:

1. **Path 1 (Input Kosong)**: 
   `1 -> 2 -> 3 -> 11`
2. **Path 2 (User Tidak Ditemukan)**: 
   `1 -> 2 -> 4 -> 5 -> 11`
3. **Path 3 (Password Salah)**: 
   `1 -> 2 -> 4 -> 6 -> 7 -> 11`
4. **Path 4 (Login Sukses - Role Admin)**: 
   `1 -> 2 -> 4 -> 6 -> 8 -> 9 -> 11`
5. **Path 5 (Login Sukses - Role Kasir)**: 
   `1 -> 2 -> 4 -> 6 -> 8 -> 10 -> 11`

---

## 🛒 2. Pengujian White Box: Checkout Pelanggan (`api/create_order.php`)

### A. Flowchart & Flowgraph
Alur checkout ketika pelanggan mengirim data pesanan (format JSON) ke API backend didefinisikan ke dalam bagan alur (*flowchart*) dan graf alur (*flowgraph*) berikut:

#### 📊 Flowchart
![Flowchart Checkout Pelanggan](whitebox_diagrams/flowchart_create_order.png)

```mermaid
graph TD
    Node1((1: Receive JSON Data)) --> Node2{2: Apakah data lengkap?}
    Node2 -- Tidak --> Node3[3: Output Error Lengkap & Exit]
    Node2 -- Ya --> Node4[4: Begin Transaction & Init variables]
    
    Node3 --> ExitNode(((Exit)))
    
    Node4 --> Node5{5: Loop Item: Masih ada items?}
    Node5 -- Ya --> Node6[6: Query data menu & status]
    
    Node6 --> Node7{7: Apakah menu ada & tersedia?}
    Node7 -- Ya --> Node8[8: Hitung subtotal & Tambah ke validItems]
    Node7 -- Tidak --> Node5
    Node8 --> Node5
    
    Node5 -- Tidak --> Node9{9: Apakah validItems kosong?}
    Node9 -- Ya --> Node10[10: Throw Exception: Menu habis/tidak valid]
    Node9 -- Tidak --> Node11{11: Cari Shift Aktif & Cek apakah ada?}
    
    Node10 --> Node15[15: Rollback Transaction & Output JSON Error]
    
    Node11 -- Tidak --> Node12[12: Throw Exception: Kasir Offline]
    Node11 -- Ya --> Node13[13: Insert Orders, Insert Details & Commit]
    
    Node12 --> Node15
    Node13 --> Node14[14: Output JSON Sukses]
    Node13 -- Exception --> Node15
    
    Node14 --> ExitNode
    Node15 --> ExitNode
```

#### 📈 Flowgraph
![Flowgraph Checkout Pelanggan](whitebox_diagrams/flowgraph_create_order.png)

### B. Tabel Keterangan Node
| Node / Logika | Deskripsi |
| :--- | :--- |
| **1** | Menerima data input JSON dari client side (`meja_id`, `metode_bayar`, `uang_dibayar`, `items`). |
| **2** | Pengecekan kondisi kelengkapan data: `!$data || empty(meja_id) || empty(items) || empty(metode_bayar)`. |
| **3** | Mengirim output error "Data pesanan tidak lengkap" dan memberhentikan script. |
| **4** | Memulai transaksi database PDO (`beginTransaction`), menetapkan `$totalHarga = 0` dan `$validItems = []`. |
| **5** | Perulangan item (`foreach ($items as $item)`): Pengecekan apakah masih ada item dalam daftar pesanan. |
| **6** | Eksekusi query mengambil data harga dan status keaktifan menu dari tabel `menus`. |
| **7** | Pengecekan kondisi: Apakah menu ditemukan dan berstatus `'tersedia'`. |
| **8** | Menghitung subtotal per item, menambahkan subtotal ke total harga, dan memasukkan item ke array `$validItems`. |
| **9** | Pengecekan kondisi: Apakah array `$validItems` bernilai kosong. |
| **10** | Lempar Exception: "Semua item pesanan tidak valid atau stok habis." |
| **11** | Eksekusi pencarian shift kasir aktif dan mengecek apakah terdapat shift kasir bernilai aktif (`$activeShift`). |
| **12** | Lempar Exception: "Mohon maaf, kasir sedang offline." |
| **13** | Eksekusi insert data ke tabel `orders` dan `order_details`, serta melakukan `commit` transaksi. |
| **14** | Mengirim output JSON sukses: `success => true` dan ID pesanan. |
| **15** | Blok catch: Melakukan `rollBack` transaksi database dan mengirimkan pesan error sebagai response JSON. |

### C. Perhitungan Cyclomatic Complexity (CC) & Jumlah Region
* **Jumlah Sisi (Edges, E)** = 21
* **Jumlah Node (N)** = 16 (termasuk Exit)
* **Rumus**: $V(G) = E - N + 2$
* **Perhitungan**: $V(G) = 21 - 16 + 2 = 7$

Metode Predicate Node ($V(G) = P + 1$ di mana $P$ adalah node keputusan):
1. Node 2 (Cek kelengkapan parameter)
2. Node 5 (Looping item pesanan)
3. Node 7 (Cek menu ada & tersedia)
4. Node 9 (Cek validItems kosong)
5. Node 11 (Cek ketersediaan shift aktif)
6. Node 13 (Exception check saat insert database)
* **Jumlah Predikat (P)** = 6
* **Perhitungan**: $V(G) = 6 + 1 = 7$

* **Jumlah Region (Daerah)** = **7 Region**
  * *Region 1 s.d. 6*: Wilayah/area tertutup yang dibatasi oleh siklus lintasan graf alur.
  * *Region 7*: Wilayah luar di sekeliling graf alur (open area).

*Maka, terdapat **7 Jalur Independen**.*

### D. Jalur Independen (Independent Paths)
1. **Jalur 1**: 1 - 2 (Tidak) - 3 - Exit
2. **Jalur 2**: 1 - 2 (Ya) - 4 - 5 (Selesai loop langsung) - 9 (Ya) - 10 - 15 - Exit
3. **Jalur 3**: 1 - 2 (Ya) - 4 - 5 (Looping item) - 6 - 7 (Tidak) - 5 (Selesai loop) - 9 (Ya) - 10 - 15 - Exit
4. **Jalur 4**: 1 - 2 (Ya) - 4 - 5 (Looping item) - 6 - 7 (Ya) - 8 - 5 (Selesai loop) - 9 (Tidak) - 11 (Tidak) - 12 - 15 - Exit
5. **Jalur 5**: 1 - 2 (Ya) - 4 - 5 (Looping item) - 6 - 7 (Ya) - 8 - 5 (Selesai loop) - 9 (Tidak) - 11 (Ya) - 13 (Sukses) - 14 - Exit
6. **Jalur 6**: 1 - 2 (Ya) - 4 - 5 (Looping item) - 6 - 7 (Ya) - 8 - 5 (Selesai loop) - 9 (Tidak) - 11 (Ya) - 13 (Exception saat insert) - 15 - Exit
7. **Jalur 7**: 1 - 2 (Ya) - 4 - 5 (Looping item pertama) - 6 - 7 (Tidak) - 5 (Looping item kedua) - 6 - 7 (Ya) - 8 - 5 (Selesai) - 9 (Tidak) - 11 (Ya) - 13 (Sukses) - 14 - Exit

---

## 📸 3. Pengujian White Box: Konfirmasi Pembayaran QRIS (`customer/qris_payment.php`)

### A. Flowchart & Flowgraph
Alur upload bukti transfer oleh pelanggan pada halaman konfirmasi pembayaran:

```mermaid
graph TD
    Node1((1: Check REQUEST_METHOD == POST)) -- Tidak --> Node9[9: Render Halaman Upload & Detail Tagihan]
    Node1 -- Ya --> Node2{2: Apakah file bukti_transfer terunggah tanpa error?}
    
    Node2 -- Tidak --> Node3[3: Set error 'Pilih bukti transfer dahulu']
    Node2 -- Ya --> Node4{4: Apakah ekstensi & MIME file valid?}
    
    Node3 --> Node9
    
    Node4 -- Tidak --> Node5[5: Set error 'Format file tidak didukung']
    Node4 -- Ya --> Node6{6: Konversi gambar ke format WebP sukses?}
    
    Node5 --> Node9
    
    Node6 -- Tidak --> Node7[7: Set error 'Gagal memproses gambar']
    Node6 -- Ya --> Node8[8: Simpan path ke DB & Redirect ke order_status.php]
    
    Node7 --> Node9
    
    Node8 --> ExitNode(((Exit)))
    Node9 --> ExitNode
```

### B. Tabel Keterangan Node
| Node | Deskripsi |
| :--- | :--- |
| **1** | Pengecekan kondisi: Apakah user mengirimkan form (`$_SERVER['REQUEST_METHOD'] === 'POST'`). |
| **2** | Pengecekan kondisi: `isset($_FILES['bukti_transfer']) && error === UPLOAD_ERR_OK`. |
| **3** | Penentuan nilai error: "Silakan pilih gambar bukti transfer terlebih dahulu." |
| **4** | Pengecekan kondisi: Apakah ekstensi file ada dalam array `allowedExts` dan mime-type sesuai dengan `allowedMimes`. |
| **5** | Penentuan nilai error: "Format file tidak didukung. Harap gunakan gambar JPG, PNG, atau WEBP." |
| **6** | Eksekusi pemanggilan helper image `convertToWebp($fileTmp, $destPath)` dan mengecek nilai kembaliannya. |
| **7** | Penentuan nilai error: "Gagal memproses gambar bukti transfer." |
| **8** | Eksekusi update database `UPDATE orders SET bukti_transfer = ?` dan jalankan redirect ke `order_status.php`. |
| **9** | Menampilkan antarmuka HTML upload bukti transfer (menampilkan kode QRIS toko, pratinjau unggahan, dan pesan error jika ada). |

### C. Perhitungan Cyclomatic Complexity (CC)
* **Jumlah Sisi (Edges, E)** = 13
* **Jumlah Node (N)** = 10 (termasuk Exit)
* **Rumus**: $V(G) = E - N + 2$
* **Perhitungan**: $V(G) = 13 - 10 + 2 = 5$

Metode Predicate Node ($V(G) = P + 1$):
1. Node 1 (Pengecekan POST)
2. Node 2 (Pengecekan upload file berhasil)
3. Node 4 (Pengecekan kecocokan jenis file/MIME)
4. Node 6 (Pengecekan keberhasilan konversi gambar)
* **Jumlah Predikat (P)** = 4
* **Perhitungan**: $V(G) = 4 + 1 = 5$

*Maka, terdapat **5 Jalur Independen**.*

### D. Jalur Independen (Independent Paths)
1. **Jalur 1**: 1 (Tidak/GET) - 9 - Exit
2. **Jalur 2**: 1 (Ya/POST) - 2 (Tidak) - 3 - 9 - Exit
3. **Jalur 3**: 1 (Ya/POST) - 2 (Ya) - 4 (Tidak) - 5 - 9 - Exit
4. **Jalur 4**: 1 (Ya/POST) - 2 (Ya) - 4 (Ya) - 6 (Tidak) - 7 - 9 - Exit
5. **Jalur 5**: 1 (Ya/POST) - 2 (Ya) - 4 (Ya) - 6 (Ya) - 8 - Exit
