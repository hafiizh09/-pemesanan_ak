# Aturan & Konvensi Proyek (RULES)

Dokumen ini adalah panduan **MUTLAK** untuk setiap pengembang (developer) yang ingin melanjutkan, memodifikasi, atau berkontribusi pada repositori **Sistem Pemesanan Kafe Berbasis QR Code** ini. 

Proyek ini dibangun dengan standar rekayasa (*engineering*) tingkat tinggi dan estetika desain kelas elit. Pelanggaran terhadap konvensi di bawah ini akan merusak integritas arsitektur dan kohesi visual proyek.

---

## 1. Aturan Desain Visual & UI/UX (The Spatial Kinetics)

Aplikasi ini menganut mazhab **Swiss Minimalist & Neo-Brutalism Light**. Antarmuka adalah kanvas spasial, bukan sekadar tempelan elemen.

*   **DILARANG Keras Menggunakan Komponen Generik:** Jangan gunakan kelas Bootstrap usang atau merakit UI "murahan". Semua elemen harus dirancang secara *custom* menggunakan utilitas Tailwind CSS.
*   **Tipografi & Kepadatan Data:** 
    * Gunakan properti `text-balance` untuk judul (H1-H6) dan `text-pretty` untuk paragraf deskripsi agar tepian teks sejajar indah.
    * Seluruh angka (terutama harga dan kuantitas) **WAJIB** menggunakan kelas `tabular-nums` agar karakter memiliki lebar monospasi dan tidak menggeser layout saat nominalnya berubah.
*   **Fisika Animasi (Kinetika):**
    * DILARANG menggunakan animasi CSS linear yang kaku. 
    * Transisi **WAJIB** menggunakan kurva asimtotik tajam seperti `ease-[cubic-bezier(0.16,1,0.3,1)]` atau kelas kustom `transition-snappy` yang sudah ada, untuk memberikan sensasi respons taktil (haptic).
    * Semua tombol interaktif wajib memiliki efek `active:scale-[0.98]`.
*   **Warna & Material:**
    * Latar belakang utama adalah putih/abu-abu ekstrem (`#FAFAFA` atau `neutral-50`).
    * Aksen primer adalah hitam absolut (`#000000`). Hindari abu-abu nanggung untuk batas pinggir (gunakan border super tipis `border-neutral-200`).
*   **Seni Kekosongan (Empty States):** Jangan merusak layar dengan teks "Data Kosong". Gunakan ikon yang redup dipadu dengan ruang kosong (whitespace) yang lega dan tipografi yang artistik.

---

## 2. Aturan Rekayasa Perangkat Lunak (Clean Code Architecture)

*   **Aturan Penamaan (Nomenclature):**
    * DILARANG menggunakan singkatan ambigu seperti `res`, `req`, `err`, `data`, `val`, atau `temp`. Gunakan nama presisi: `apiResponse`, `httpRequest`, `orderData`, `isPaymentSuccess`.
    * Variabel *boolean* **WAJIB** diawali dengan verba penegas: `is`, `has`, `should`, `can` (contoh: `isLoggedIn`, `hasActiveShift`).
    * Fungsi harus diawali kata kerja imperatif: `fetchOrders()`, `calculateTotal()`.
*   **Anti-Nesting (The Guard Clauses):**
    * DILARANG KERAS membuat `if/else` bersarang (nested) lebih dari 2 lapis.
    * Biasakan menggunakan pola *Early Return*. Evaluasi kondisi gagal terlebih dahulu, lempar pengecualian (*exception*), dan keluar dari fungsi sesegera mungkin.
*   **Sentralisasi & "Magic Numbers":**
    * DILARANG menulis nilai statis secara acak (contoh: status `'pending'`, `'paid'`) berulang-ulang tanpa alasan logis. Gunakan referensi struktur kontrol yang jelas di database.
*   **Arsitektur Modular (Pemisahan Domain Folder):**
    * **MUTLAK**: Dilarang mencampur file di direktori *root*. 
    * Autentikasi harus masuk ke folder `auth/`.
    * Tampilan publik/pelanggan masuk ke `customer/`.
    * Kredensial dan konfigurasi utama masuk ke `config/`.
    * File SQL / Migrasi masuk ke `database/`.
    * Pelanggaran terhadap isolasi folder ini akan menyebabkan aplikasi rentan dan berantakan.

---

## 3. Keamanan & Integritas Basis Data

*   **Pencegahan SQL Injection (Mutlak):**
    * Jangan pernah menyisipkan variabel langsung ke dalam *string query* SQL.
    * **WAJIB** menggunakan PDO Prepared Statements (`$pdo->prepare()`) dengan array eksekusi `execute([...])` untuk *SEMUA* kueri yang melibatkan data eksternal.
*   **Anti-Price-Tampering (Validasi Sisi Server):**
    * Dilarang mempercayai harga atau data numerik krusial yang dikirim dari klien (Javascript). Harga untuk kalkulasi *checkout* harus ditarik dari tabel `menus` di *database* backend secara langsung pada saat transaksi dibuat (seperti di [api/create_order.php](file:///c:/laragon/www/PEMESANAN-AK/api/create_order.php)).
*   **Proteksi CSRF (Cross-Site Request Forgery):**
    * Setiap pengiriman form mutasi data (POST, UPDATE, DELETE) dari panel Admin/Kasir **WAJIB** menyertakan fungsi `csrf_field()` di dalam tag HTML form dan divalidasi dengan `verifyCsrf()` di logika PHP penangkapnya.

---

## 4. Konvensi Integrasi Pihak Ketiga (API & Midtrans)

*   **Pemanggilan Asinkron (AJAX):**
    * Logika frontend harus diusahakan menggunakan `Fetch API` modern (menggantikan XMLHttpRequest) dan selalu dibungkus di dalam blok `try { ... } catch (e) { ... }`.
*   **Webhook & Callback Keamanan:**
    * Semua file *listener webhook* (seperti [api/payment_callback.php](file:///c:/laragon/www/PEMESANAN-AK/api/payment_callback.php)) tidak boleh tereksekusi tanpa memverifikasi kriptografi keaslian pengirim (contoh: pencocokan *Signature Key* SHA512 dari Midtrans).
*   **Arsitektur Gateway:**
    * Modifikasi fungsionalitas Midtrans API hanya boleh dilakukan pada satu file pembungkus terpusat di `config/midtrans.php`. Jangan menyebarkan logika *cURL* HTTP mentah secara acak di file-file lain.

Dengan mengikuti dokumen **RULES.md** ini, Anda turut menjaga proyek ini agar tetap menjadi perangkat lunak berskala *enterprise* dengan eksekusi *masterpiece*. Selamat mengoding!
