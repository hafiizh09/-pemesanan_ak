CREATE DATABASE IF NOT EXISTS pemesanan_ak;
USE pemesanan_ak;

-- Tabel Users (Admin & Kasir)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'kasir') NOT NULL DEFAULT 'kasir',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Meja
CREATE TABLE IF NOT EXISTS tables (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nomor_meja VARCHAR(10) NOT NULL UNIQUE,
    url_qr VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Kategori
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(50) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Menu
CREATE TABLE IF NOT EXISTS menus (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kategori_id INT NOT NULL,
    nama_menu VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    harga DECIMAL(10, 2) NOT NULL,
    gambar_url VARCHAR(255) DEFAULT NULL,
    status ENUM('tersedia', 'habis') NOT NULL DEFAULT 'tersedia',
    is_new BOOLEAN NOT NULL DEFAULT FALSE,
    is_bestseller BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (kategori_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- Tabel Promo Banner
CREATE TABLE IF NOT EXISTS promos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(100) NOT NULL,
    gambar_url VARCHAR(255) NOT NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Shifts Kasir
CREATE TABLE IF NOT EXISTS shifts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kasir_id INT NOT NULL,
    start_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    end_time TIMESTAMP NULL DEFAULT NULL,
    total_sales DECIMAL(10, 2) DEFAULT 0,
    status ENUM('active', 'completed') NOT NULL DEFAULT 'active',
    FOREIGN KEY (kasir_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Tabel Pesanan (Header)
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    meja_id INT NOT NULL,
    shift_id INT NULL,
    waktu_pesan TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total_harga DECIMAL(10, 2) NOT NULL DEFAULT 0,
    uang_dibayar DECIMAL(10, 2) NULL DEFAULT NULL,
    metode_bayar ENUM('cash', 'qris') NOT NULL DEFAULT 'cash',
    status_bayar ENUM('unpaid', 'paid') NOT NULL DEFAULT 'unpaid',
    status_pesanan ENUM('pending', 'diproses', 'selesai', 'dibatalkan') NOT NULL DEFAULT 'pending',
    midtrans_id VARCHAR(255) NULL,
    qris_url VARCHAR(255) NULL,
    bukti_transfer VARCHAR(255) NULL DEFAULT NULL,
    FOREIGN KEY (meja_id) REFERENCES tables(id) ON DELETE RESTRICT,
    FOREIGN KEY (shift_id) REFERENCES shifts(id) ON DELETE SET NULL
);

-- Tabel Detail Pesanan
CREATE TABLE IF NOT EXISTS order_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    menu_id INT NOT NULL,
    qty INT NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    catatan TEXT,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (menu_id) REFERENCES menus(id) ON DELETE RESTRICT
);

-- Data Dummy (Seed)
-- Password 'admin123' dan 'kasir123' di-hash menggunakan bcrypt (password_hash)
INSERT IGNORE INTO users (username, password, role) VALUES 
('admin', '$2y$10$2FGupgnnVkFHCT75o0fdh.pe8FcM9orvoj9YuGeHLHQrtRUYl8xfK', 'admin'),
('kasir1', '$2y$10$BBIQ9sHqyJPbVtG29gF4X.0Z0nVd62DY/Ln1JP38gVXb1/COxcms.', 'kasir');


INSERT IGNORE INTO tables (nomor_meja) VALUES ('1'), ('2'), ('3'), ('4'), ('5');

INSERT IGNORE INTO categories (nama_kategori) VALUES ('Coffee'), ('Non-Coffee'), ('Main Course'), ('Snacks');

-- Note: gambar_url placeholder, bisa diganti dengan file lokal saat upload admin
INSERT IGNORE INTO menus (kategori_id, nama_menu, deskripsi, harga, status) VALUES 
((SELECT id FROM categories WHERE nama_kategori = 'Coffee'), 'Americano', 'Espresso dengan tambahan air panas.', 20000.00, 'tersedia'),
((SELECT id FROM categories WHERE nama_kategori = 'Coffee'), 'Cafe Latte', 'Espresso dengan susu dan foam lembut.', 25000.00, 'tersedia'),
((SELECT id FROM categories WHERE nama_kategori = 'Non-Coffee'), 'Matcha Latte', 'Kombinasi bubuk teh hijau premium dan susu.', 28000.00, 'tersedia'),
((SELECT id FROM categories WHERE nama_kategori = 'Main Course'), 'Nasi Goreng Spesial', 'Nasi goreng dengan telur, ayam, dan kerupuk.', 35000.00, 'tersedia'),
((SELECT id FROM categories WHERE nama_kategori = 'Snacks'), 'French Fries', 'Kentang goreng renyah dengan taburan garam.', 18000.00, 'tersedia');
