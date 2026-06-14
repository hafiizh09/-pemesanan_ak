# Dokumen Perancangan: Use Case Diagram
## Sistem Pemesanan Kafe Berbasis QR Code (Pemesanan AK)

Dokumen ini memetakan interaksi aktor terhadap sistem dalam bentuk **Use Case Diagram** dengan alur terurut dari atas ke bawah (*Start* hingga *Exit*) seperti contoh yang diberikan.

---

### 📊 Use Case Diagram dengan Alur Aliran (Flowchart Layout)

Berikut adalah visualisasi Use Case Diagram. Semua fungsi utama diurutkan secara vertikal dari proses awal hingga akhir transaksi, dengan Pelanggan di sisi kiri, serta Kasir & Admin di sisi kanan.

```mermaid
flowchart LR
    %% Pengaturan Gaya Node Aktor
    classDef actorStyle fill:#ffffff,stroke:#333,stroke-width:2px;
    classDef useCaseStyle fill:#fdfbf7,stroke:#171717,stroke-width:1.5px;
    
    %% Aktor Sisi Kiri
    subgraph LeftActors [Aktor Kiri]
        Pelanggan["👤 Pelanggan"]
    end
    
    %% Batasan Sistem (System Boundary) & Urutan Use Case (Tengah)
    subgraph SystemBoundary [Sistem Pemesanan Kafe]
        %% Pusat Use Case (Urutan Alur Vertikal)
        UC_Start(["Start"])
        UC_ScanQR(["Scan QR Code"])
        UC_PilihMenu(["Pilih & Filter Menu"])
        UC_Keranjang(["Kelola Keranjang"])
        UC_Checkout(["Checkout Pesanan"])
        UC_MetodeBayar(["Pilih Metode Bayar"])
        
        %% Sub-Use Case dari Metode Bayar
        UC_BayarCash(["Bayar Tunai / Cash"])
        UC_BayarQRIS(["Bayar QRIS (Statis)"])
        UC_UploadQRIS(["Upload Bukti Transfer"])
        
        UC_Verifikasi(["Verifikasi Pembayaran"])
        UC_ProsesPesanan(["Proses & Siapkan Pesanan"])
        UC_Exit(["Exit"])

        %% Panah Alur Proses dari Atas ke Bawah
        UC_Start --> UC_ScanQR
        UC_ScanQR --> UC_PilihMenu
        UC_PilihMenu --> UC_Keranjang
        UC_Keranjang --> UC_Checkout
        UC_Checkout --> UC_MetodeBayar
        
        UC_MetodeBayar --> UC_BayarCash
        UC_MetodeBayar --> UC_BayarQRIS
        UC_BayarQRIS --> UC_UploadQRIS
        
        UC_BayarCash --> UC_Verifikasi
        UC_UploadQRIS --> UC_Verifikasi
        
        UC_Verifikasi --> UC_ProsesPesanan
        UC_ProsesPesanan --> UC_Exit
    end
    
    %% Aktor Sisi Kanan
    subgraph RightActors [Aktor Kanan]
        Kasir["👤 Kasir"]
        Admin["👤 Admin / Owner"]
    end

    %% Hubungan Pelanggan (Sisi Kiri)
    Pelanggan --> UC_Start
    Pelanggan --> UC_ScanQR
    Pelanggan --> UC_PilihMenu
    Pelanggan --> UC_Keranjang
    Pelanggan --> UC_Checkout
    Pelanggan --> UC_MetodeBayar
    Pelanggan --> UC_UploadQRIS
    Pelanggan --> UC_Exit

    %% Hubungan Kasir (Sisi Kanan)
    Kasir --> UC_Start
    Kasir --> UC_MetodeBayar
    Kasir --> UC_Verifikasi
    Kasir --> UC_ProsesPesanan
    Kasir --> UC_Exit

    %% Hubungan Admin / Owner (Sisi Kanan)
    Admin --> UC_Start
    Admin --> UC_Verifikasi
    Admin --> UC_ProsesPesanan
    Admin --> UC_Exit

    %% Aplikasi Gaya Visual
    class Pelanggan,Kasir,Admin actorStyle;
    class UC_Start,UC_ScanQR,UC_PilihMenu,UC_Keranjang,UC_Checkout,UC_MetodeBayar,UC_BayarCash,UC_BayarQRIS,UC_UploadQRIS,UC_Verifikasi,UC_ProsesPesanan,UC_Exit useCaseStyle;
```

---

### 📝 Penjelasan Detail Alur Use Case

Sesuai dengan bagan alur di atas, sistem bekerja secara berurutan sebagai berikut:

1.  **Start**
    *   **Aktor**: Pelanggan, Kasir, Admin.
    *   **Deskripsi**: Titik awal masuknya seluruh pengguna ke dalam sistem operasional kafe.
2.  **Scan QR Code**
    *   **Aktor**: Pelanggan.
    *   **Deskripsi**: Pelanggan memindai QR Code di meja untuk mendapatkan parameter meja di URL.
3.  **Pilih & Filter Menu**
    *   **Aktor**: Pelanggan.
    *   **Deskripsi**: Pelanggan menjelajahi katalog menu berdasarkan kategori.
4.  **Kelola Keranjang**
    *   **Aktor**: Pelanggan.
    *   **Deskripsi**: Pelanggan menambah atau mengurangi kuantitas item dan menyisipkan catatan khusus.
5.  **Checkout Pesanan**
    *   **Aktor**: Pelanggan.
    *   **Deskripsi**: Pelanggan mengunci pesanan dan mengirimkannya ke database backend.
6.  **Pilih Metode Bayar**
    *   **Aktor**: Pelanggan, Kasir.
    *   **Deskripsi**: Menentukan apakah pembayaran akan diselesaikan langsung dengan uang tunai ke kasir atau dengan metode scan QRIS.
7.  **Bayar Tunai / Cash & Bayar QRIS (Statis)**
    *   Jika **QRIS**: Pelanggan melompat ke Use Case **Upload Bukti Transfer** (Bukti pembayaran).
    *   Jika **Tunai**: Pelanggan langsung ke meja kasir untuk penyelesaian.
8.  **Verifikasi Pembayaran**
    *   **Aktor**: Kasir, Admin.
    *   **Deskripsi**: Kasir atau Admin memeriksa uang fisik yang diterima atau gambar bukti transfer QRIS yang diunggah oleh pelanggan untuk menandai status pesanan menjadi `Paid`.
9.  **Proses & Siapkan Pesanan**
    *   **Aktor**: Kasir, Admin.
    *   **Deskripsi**: Status pesanan diubah ke `Diproses` (dapur menyiapkan pesanan) lalu menjadi `Selesai` saat disajikan.
10. **Exit**
    *   **Aktor**: Pelanggan, Kasir, Admin.
    *   **Deskripsi**: Pelanggan meninggalkan kafe dan kasir/admin menutup shift penjualan mereka.
