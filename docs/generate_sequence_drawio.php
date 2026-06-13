<?php
$dir = __DIR__ . '/sequence_diagrams';
if (!is_dir($dir)) mkdir($dir, 0777, true);

function generateSequenceXml($name, $id, $lifelines, $messages) {
    $xml = '<?xml version="1.0" encoding="UTF-8"?>
<mxfile host="draw.io" version="24.0.0" type="device">
  <diagram name="' . $name . '" id="' . $id . '">
    <mxGraphModel dx="1422" dy="762" grid="1" gridSize="10" guides="1" tooltips="1" connect="1" arrows="1" fold="1" page="1" pageScale="1" pageWidth="1654" pageHeight="827" math="0" shadow="0">
      <root>
        <mxCell id="0"/><mxCell id="1" parent="0"/>';
    
    $cellId = 2;
    $lifelinePositions = [];
    $totalHeight = count($messages) * 60 + 100;
    
    // Draw lifelines
    foreach ($lifelines as $index => $lifeline) {
        $x = $index * 250 + 100;
        $lifelinePositions[$lifeline] = $x + 50; // Center point
        $xml .= '<mxCell id="'.$cellId++.'" value="'.$lifeline.'" style="shape=umlLifeline;perimeter=lifelinePerimeter;whiteSpace=wrap;html=1;container=1;collapsible=0;recursiveResize=0;outlineConnect=0;fontStyle=1;" vertex="1" parent="1">
          <mxGeometry x="'.$x.'" y="50" width="100" height="'.$totalHeight.'" as="geometry"/>
        </mxCell>';
    }
    
    // Draw messages
    $y = 130;
    foreach ($messages as $msg) {
        $fromX = $lifelinePositions[$msg['from']];
        $toX = $lifelinePositions[$msg['to']];
        $label = $msg['label'];
        $style = $msg['type'] === 'return' 
                 ? "endArrow=open;dashed=1;html=1;rounded=0;edgeStyle=orthogonalEdgeStyle;fontSize=11;strokeWidth=1;" 
                 : "endArrow=block;html=1;rounded=0;edgeStyle=orthogonalEdgeStyle;fontSize=11;strokeWidth=1;";
                 
        $xml .= '<mxCell id="'.$cellId++.'" value="'.$label.'" style="'.$style.'" edge="1" parent="1">
          <mxGeometry width="50" height="50" relative="1" as="geometry">
            <mxPoint x="'.$fromX.'" y="'.$y.'" as="sourcePoint"/>
            <mxPoint x="'.$toX.'" y="'.$y.'" as="targetPoint"/>
            <Array as="points">
                <mxPoint x="'.$fromX.'" y="'.$y.'"/>
                <mxPoint x="'.$toX.'" y="'.$y.'"/>
            </Array>
            <mxPoint y="-10" as="offset"/>
          </mxGeometry>
        </mxCell>';
        
        $y += 60;
    }
    
    $xml .= '
      </root>
    </mxGraphModel>
  </diagram>
</mxfile>';
    
    return $xml;
}

$diagrams = [
    '01_login_sequence' => [
        'name' => 'Login',
        'lifelines' => ['Pengguna', 'Sistem (UI)', 'Sistem (Controller)', 'Database'],
        'messages' => [
            ['from' => 'Pengguna', 'to' => 'Sistem (UI)', 'label' => '1. Akses Halaman Login', 'type' => 'call'],
            ['from' => 'Sistem (UI)', 'to' => 'Sistem (Controller)', 'label' => '2. Request Halaman', 'type' => 'call'],
            ['from' => 'Sistem (Controller)', 'to' => 'Sistem (UI)', 'label' => '3. Render Form & CSRF Token', 'type' => 'return'],
            ['from' => 'Pengguna', 'to' => 'Sistem (UI)', 'label' => '4. Input Username & Password', 'type' => 'call'],
            ['from' => 'Sistem (UI)', 'to' => 'Sistem (Controller)', 'label' => '5. Submit (POST) + CSRF', 'type' => 'call'],
            ['from' => 'Sistem (Controller)', 'to' => 'Sistem (Controller)', 'label' => '6. Validasi CSRF', 'type' => 'call'],
            ['from' => 'Sistem (Controller)', 'to' => 'Database', 'label' => '7. Verifikasi Kredensial (Hash)', 'type' => 'call'],
            ['from' => 'Database', 'to' => 'Sistem (Controller)', 'label' => '8. Kredensial Valid', 'type' => 'return'],
            ['from' => 'Sistem (Controller)', 'to' => 'Sistem (Controller)', 'label' => '9. Regenerate Session ID', 'type' => 'call'],
            ['from' => 'Sistem (Controller)', 'to' => 'Sistem (UI)', 'label' => '10. Redirect ke Dashboard', 'type' => 'return']
        ]
    ],
    '02_menu_pelanggan_sequence' => [
        'name' => 'Menu Pelanggan',
        'lifelines' => ['Pelanggan', 'Sistem (UI)', 'Sistem (Controller)', 'Database'],
        'messages' => [
            ['from' => 'Pelanggan', 'to' => 'Sistem (UI)', 'label' => '1. Scan QR Code Meja', 'type' => 'call'],
            ['from' => 'Sistem (UI)', 'to' => 'Sistem (Controller)', 'label' => '2. Request Menu (ID Meja)', 'type' => 'call'],
            ['from' => 'Sistem (Controller)', 'to' => 'Database', 'label' => '3. Fetch Data Menu & Kategori', 'type' => 'call'],
            ['from' => 'Database', 'to' => 'Sistem (Controller)', 'label' => '4. Data Produk', 'type' => 'return'],
            ['from' => 'Sistem (Controller)', 'to' => 'Sistem (UI)', 'label' => '5. Render Katalog Menu', 'type' => 'return'],
            ['from' => 'Pelanggan', 'to' => 'Sistem (UI)', 'label' => '6. Tambah Produk ke Keranjang', 'type' => 'call'],
            ['from' => 'Sistem (UI)', 'to' => 'Sistem (Controller)', 'label' => '7. Hitung Total & Validasi', 'type' => 'call'],
            ['from' => 'Sistem (Controller)', 'to' => 'Sistem (UI)', 'label' => '8. Update Cart UI', 'type' => 'return'],
            ['from' => 'Pelanggan', 'to' => 'Sistem (UI)', 'label' => '9. Konfirmasi Checkout', 'type' => 'call'],
            ['from' => 'Sistem (UI)', 'to' => 'Sistem (Controller)', 'label' => '10. Submit Order', 'type' => 'call'],
            ['from' => 'Sistem (Controller)', 'to' => 'Database', 'label' => '11. Insert (orders & items)', 'type' => 'call'],
            ['from' => 'Sistem (Controller)', 'to' => 'Sistem (UI)', 'label' => '12. Redirect ke Pembayaran', 'type' => 'return']
        ]
    ],
    '03_pembayaran_qris_sequence' => [
        'name' => 'Pembayaran QRIS',
        'lifelines' => ['Pelanggan', 'Sistem Internal', 'Midtrans API'],
        'messages' => [
            ['from' => 'Sistem Internal', 'to' => 'Midtrans API', 'label' => '1. Request Snap Token (Order ID, Nominal)', 'type' => 'call'],
            ['from' => 'Midtrans API', 'to' => 'Sistem Internal', 'label' => '2. Return Snap Token', 'type' => 'return'],
            ['from' => 'Sistem Internal', 'to' => 'Pelanggan', 'label' => '3. Tampilkan QRIS / Snap UI', 'type' => 'return'],
            ['from' => 'Pelanggan', 'to' => 'Midtrans API', 'label' => '4. Scan & Eksekusi Pembayaran', 'type' => 'call'],
            ['from' => 'Midtrans API', 'to' => 'Sistem Internal', 'label' => '5. Webhook/Callback (Status: Settlement)', 'type' => 'call'],
            ['from' => 'Sistem Internal', 'to' => 'Sistem Internal', 'label' => '6. Update Status (Lunas)', 'type' => 'call'],
            ['from' => 'Sistem Internal', 'to' => 'Pelanggan', 'label' => '7. Tampilkan Halaman Sukses', 'type' => 'return']
        ]
    ],
    '04_status_pesanan_sequence' => [
        'name' => 'Status Pesanan',
        'lifelines' => ['Pelanggan', 'Sistem (UI)', 'Sistem (Controller)', 'Database'],
        'messages' => [
            ['from' => 'Pelanggan', 'to' => 'Sistem (UI)', 'label' => '1. Akses Halaman Pelacakan (Order ID)', 'type' => 'call'],
            ['from' => 'Sistem (UI)', 'to' => 'Sistem (Controller)', 'label' => '2. Request Status', 'type' => 'call'],
            ['from' => 'Sistem (Controller)', 'to' => 'Database', 'label' => '3. Query Status Terkini', 'type' => 'call'],
            ['from' => 'Database', 'to' => 'Sistem (Controller)', 'label' => '4. Data Status', 'type' => 'return'],
            ['from' => 'Sistem (Controller)', 'to' => 'Sistem (UI)', 'label' => '5. Render Status Aktual', 'type' => 'return']
        ]
    ],
    '05_dashboard_kasir_sequence' => [
        'name' => 'Dashboard Kasir',
        'lifelines' => ['Kasir', 'Sistem (UI)', 'Sistem (Controller)', 'Database'],
        'messages' => [
            ['from' => 'Kasir', 'to' => 'Sistem (UI)', 'label' => '1. Buka Dashboard POS', 'type' => 'call'],
            ['from' => 'Sistem (UI)', 'to' => 'Sistem (Controller)', 'label' => '2. Request Data (Orders, Stats)', 'type' => 'call'],
            ['from' => 'Sistem (Controller)', 'to' => 'Database', 'label' => '3. Query Data Real-time', 'type' => 'call'],
            ['from' => 'Database', 'to' => 'Sistem (Controller)', 'label' => '4. Data Orders', 'type' => 'return'],
            ['from' => 'Sistem (Controller)', 'to' => 'Sistem (UI)', 'label' => '5. Render POS & Antrean', 'type' => 'return'],
            ['from' => 'Kasir', 'to' => 'Sistem (UI)', 'label' => '6. Update Status Pesanan (Selesai)', 'type' => 'call'],
            ['from' => 'Sistem (UI)', 'to' => 'Sistem (Controller)', 'label' => '7. Submit Perubahan Status', 'type' => 'call'],
            ['from' => 'Sistem (Controller)', 'to' => 'Database', 'label' => '8. Update State', 'type' => 'call'],
            ['from' => 'Database', 'to' => 'Sistem (Controller)', 'label' => '9. Konfirmasi', 'type' => 'return'],
            ['from' => 'Sistem (Controller)', 'to' => 'Sistem (UI)', 'label' => '10. Generate Struk Cetak', 'type' => 'return']
        ]
    ],
    '06_dashboard_admin_sequence' => [
        'name' => 'Dashboard Admin',
        'lifelines' => ['Administrator', 'Sistem (UI)', 'Sistem (Controller)', 'Database'],
        'messages' => [
            ['from' => 'Administrator', 'to' => 'Sistem (UI)', 'label' => '1. Akses Dashboard', 'type' => 'call'],
            ['from' => 'Sistem (UI)', 'to' => 'Sistem (Controller)', 'label' => '2. Request Aggregated Data', 'type' => 'call'],
            ['from' => 'Sistem (Controller)', 'to' => 'Database', 'label' => '3. Kueri Metrik (Penjualan, Produk)', 'type' => 'call'],
            ['from' => 'Database', 'to' => 'Sistem (Controller)', 'label' => '4. Return Aggregated Metrics', 'type' => 'return'],
            ['from' => 'Sistem (Controller)', 'to' => 'Sistem (UI)', 'label' => '5. Render Grafik & Metrik', 'type' => 'return']
        ]
    ],
    '07_kategori_sequence' => [
        'name' => 'Kategori',
        'lifelines' => ['Administrator', 'Sistem (UI)', 'Sistem (Controller)', 'Database'],
        'messages' => [
            ['from' => 'Administrator', 'to' => 'Sistem (UI)', 'label' => '1. Buka Modul Kategori', 'type' => 'call'],
            ['from' => 'Sistem (UI)', 'to' => 'Sistem (Controller)', 'label' => '2. Request Daftar Kategori', 'type' => 'call'],
            ['from' => 'Sistem (Controller)', 'to' => 'Database', 'label' => '3. Fetch Kategori', 'type' => 'call'],
            ['from' => 'Database', 'to' => 'Sistem (Controller)', 'label' => '4. Data Kategori', 'type' => 'return'],
            ['from' => 'Administrator', 'to' => 'Sistem (UI)', 'label' => '5. Tambah/Edit/Hapus Data', 'type' => 'call'],
            ['from' => 'Sistem (UI)', 'to' => 'Sistem (Controller)', 'label' => '6. Submit DML Request', 'type' => 'call'],
            ['from' => 'Sistem (Controller)', 'to' => 'Sistem (Controller)', 'label' => '7. Validasi Input (SQL Injection)', 'type' => 'call'],
            ['from' => 'Sistem (Controller)', 'to' => 'Database', 'label' => '8. Eksekusi Kueri DML', 'type' => 'call'],
            ['from' => 'Database', 'to' => 'Sistem (Controller)', 'label' => '9. Konfirmasi', 'type' => 'return'],
            ['from' => 'Sistem (Controller)', 'to' => 'Sistem (UI)', 'label' => '10. Refresh Tabel', 'type' => 'return']
        ]
    ],
    '08_menu_sequence' => [
        'name' => 'Menu (Produk)',
        'lifelines' => ['Administrator', 'Sistem (UI)', 'Sistem (Controller)', 'Database', 'Storage'],
        'messages' => [
            ['from' => 'Administrator', 'to' => 'Sistem (UI)', 'label' => '1. Input Data & Upload Gambar', 'type' => 'call'],
            ['from' => 'Sistem (UI)', 'to' => 'Sistem (Controller)', 'label' => '2. Submit Form (Multipart)', 'type' => 'call'],
            ['from' => 'Sistem (Controller)', 'to' => 'Sistem (Controller)', 'label' => '3. Validasi Ekstensi & MIME', 'type' => 'call'],
            ['from' => 'Sistem (Controller)', 'to' => 'Storage', 'label' => '4. Simpan File Gambar', 'type' => 'call'],
            ['from' => 'Storage', 'to' => 'Sistem (Controller)', 'label' => '5. Return Path/URL Gambar', 'type' => 'return'],
            ['from' => 'Sistem (Controller)', 'to' => 'Database', 'label' => '6. Insert/Update Metadata Menu', 'type' => 'call'],
            ['from' => 'Database', 'to' => 'Sistem (Controller)', 'label' => '7. Konfirmasi', 'type' => 'return'],
            ['from' => 'Sistem (Controller)', 'to' => 'Sistem (UI)', 'label' => '8. Tampilkan Notifikasi Sukses', 'type' => 'return']
        ]
    ],
    '09_meja_qr_sequence' => [
        'name' => 'Meja QR',
        'lifelines' => ['Administrator', 'Sistem (UI)', 'Sistem (Controller)', 'QR Generator', 'Database'],
        'messages' => [
            ['from' => 'Administrator', 'to' => 'Sistem (UI)', 'label' => '1. Registrasi Meja Baru', 'type' => 'call'],
            ['from' => 'Sistem (UI)', 'to' => 'Sistem (Controller)', 'label' => '2. Submit Data Meja', 'type' => 'call'],
            ['from' => 'Sistem (Controller)', 'to' => 'Database', 'label' => '3. Insert Data Meja', 'type' => 'call'],
            ['from' => 'Database', 'to' => 'Sistem (Controller)', 'label' => '4. ID Meja Baru', 'type' => 'return'],
            ['from' => 'Sistem (Controller)', 'to' => 'QR Generator', 'label' => '5. Generate Matriks QR (URL)', 'type' => 'call'],
            ['from' => 'QR Generator', 'to' => 'Sistem (Controller)', 'label' => '6. File QR Code', 'type' => 'return'],
            ['from' => 'Sistem (Controller)', 'to' => 'Sistem (UI)', 'label' => '7. Tampilkan QR untuk Dicetak', 'type' => 'return']
        ]
    ],
    '10_banner_promo_sequence' => [
        'name' => 'Banner Promo',
        'lifelines' => ['Administrator', 'Sistem (UI)', 'Sistem (Controller)', 'Storage', 'Database'],
        'messages' => [
            ['from' => 'Administrator', 'to' => 'Sistem (UI)', 'label' => '1. Upload Banner & Set Aktif', 'type' => 'call'],
            ['from' => 'Sistem (UI)', 'to' => 'Sistem (Controller)', 'label' => '2. Submit Asset', 'type' => 'call'],
            ['from' => 'Sistem (Controller)', 'to' => 'Storage', 'label' => '3. Simpan Resolusi Visual', 'type' => 'call'],
            ['from' => 'Storage', 'to' => 'Sistem (Controller)', 'label' => '4. Path Gambar', 'type' => 'return'],
            ['from' => 'Sistem (Controller)', 'to' => 'Database', 'label' => '5. Insert Status Boolean & Path', 'type' => 'call'],
            ['from' => 'Sistem (Controller)', 'to' => 'Sistem (UI)', 'label' => '6. Update Daftar Promo', 'type' => 'return']
        ]
    ],
    '11_laporan_penjualan_sequence' => [
        'name' => 'Laporan Penjualan',
        'lifelines' => ['Administrator', 'Sistem (UI)', 'Sistem (Controller)', 'Database'],
        'messages' => [
            ['from' => 'Administrator', 'to' => 'Sistem (UI)', 'label' => '1. Input Filter (Date Range)', 'type' => 'call'],
            ['from' => 'Sistem (UI)', 'to' => 'Sistem (Controller)', 'label' => '2. Request Laporan', 'type' => 'call'],
            ['from' => 'Sistem (Controller)', 'to' => 'Database', 'label' => '3. Execute Aggregate Query', 'type' => 'call'],
            ['from' => 'Database', 'to' => 'Sistem (Controller)', 'label' => '4. Data Sumasi Transaksi', 'type' => 'return'],
            ['from' => 'Sistem (Controller)', 'to' => 'Sistem (UI)', 'label' => '5. Render DataTables', 'type' => 'return'],
            ['from' => 'Administrator', 'to' => 'Sistem (UI)', 'label' => '6. Klik Export (Excel/PDF)', 'type' => 'call'],
            ['from' => 'Sistem (UI)', 'to' => 'Sistem (Controller)', 'label' => '7. Request Document', 'type' => 'call'],
            ['from' => 'Sistem (Controller)', 'to' => 'Sistem (UI)', 'label' => '8. Download File Stream', 'type' => 'return']
        ]
    ],
    '12_pengguna_sequence' => [
        'name' => 'Pengguna',
        'lifelines' => ['Administrator', 'Sistem (UI)', 'Sistem (Controller)', 'Database'],
        'messages' => [
            ['from' => 'Administrator', 'to' => 'Sistem (UI)', 'label' => '1. Tambah Akun / Suspend', 'type' => 'call'],
            ['from' => 'Sistem (UI)', 'to' => 'Sistem (Controller)', 'label' => '2. Submit Perintah', 'type' => 'call'],
            ['from' => 'Sistem (Controller)', 'to' => 'Sistem (Controller)', 'label' => '3. Hash Password (Bcrypt)', 'type' => 'call'],
            ['from' => 'Sistem (Controller)', 'to' => 'Database', 'label' => '4. Eksekusi Kueri Keamanan', 'type' => 'call'],
            ['from' => 'Database', 'to' => 'Sistem (Controller)', 'label' => '5. Konfirmasi', 'type' => 'return'],
            ['from' => 'Sistem (Controller)', 'to' => 'Sistem (UI)', 'label' => '6. Update Daftar User', 'type' => 'return']
        ]
    ],
    '13_pengaturan_sequence' => [
        'name' => 'Pengaturan',
        'lifelines' => ['Administrator', 'Sistem (UI)', 'Sistem (Controller)', 'Database'],
        'messages' => [
            ['from' => 'Administrator', 'to' => 'Sistem (UI)', 'label' => '1. Ubah Config (Nama, Midtrans Key)', 'type' => 'call'],
            ['from' => 'Sistem (UI)', 'to' => 'Sistem (Controller)', 'label' => '2. Submit Payload Config', 'type' => 'call'],
            ['from' => 'Sistem (Controller)', 'to' => 'Sistem (Controller)', 'label' => '3. Sanitasi Input (XSS/Filter)', 'type' => 'call'],
            ['from' => 'Sistem (Controller)', 'to' => 'Database', 'label' => '4. Update Tabel Config', 'type' => 'call'],
            ['from' => 'Database', 'to' => 'Sistem (Controller)', 'label' => '5. Konfirmasi', 'type' => 'return'],
            ['from' => 'Sistem (Controller)', 'to' => 'Sistem (UI)', 'label' => '6. Render Perubahan Global', 'type' => 'return']
        ]
    ]
];

foreach ($diagrams as $filename => $data) {
    $xml = generateSequenceXml($data['name'], 'diag-'.$filename, $data['lifelines'], $data['messages']);
    file_put_contents($dir . '/' . $filename . '.drawio', $xml);
}

echo "Berhasil membuat 13 file Sequence Diagram.\n";
