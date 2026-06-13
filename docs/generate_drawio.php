<?php
$dir = __DIR__ . '/activity_diagrams';
if (!is_dir($dir)) mkdir($dir, 0777, true);

function getXml($name, $id, $nodesEdges) {
    return '<?xml version="1.0" encoding="UTF-8"?>
<mxfile host="draw.io" version="24.0.0" type="device">
  <diagram name="' . $name . '" id="' . $id . '">
    <mxGraphModel dx="1422" dy="762" grid="1" gridSize="10" guides="1" tooltips="1" connect="1" arrows="1" fold="1" page="1" pageScale="1" pageWidth="1654" pageHeight="827" math="0" shadow="0">
      <root>
        <mxCell id="0"/><mxCell id="1" parent="0"/>
' . $nodesEdges . '
      </root>
    </mxGraphModel>
  </diagram>
</mxfile>';
}

$files = [
    '01_login.drawio' => getXml('Halaman Login', 'diag-login', <<<XML
        <mxCell id="2" value="START" style="ellipse;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#000000;fontStyle=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="30" y="165" width="70" height="50" as="geometry"/></mxCell>
        <mxCell id="3" value="Membuka Halaman Login" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="130" y="160" width="140" height="60" as="geometry"/></mxCell>
        <mxCell id="4" value="Merender Form Login &amp; Generate CSRF Token" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="300" y="160" width="150" height="60" as="geometry"/></mxCell>
        <mxCell id="5" value="Input Username &amp; Password" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="480" y="160" width="140" height="60" as="geometry"/></mxCell>
        <mxCell id="6" value="Klik Tombol Login (POST)" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="650" y="160" width="130" height="60" as="geometry"/></mxCell>
        <mxCell id="7" value="Verifikasi CSRF Token" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="810" y="160" width="130" height="60" as="geometry"/></mxCell>
        <mxCell id="8" value="Kredensial Valid di DB?" style="rhombus;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="970" y="150" width="140" height="80" as="geometry"/></mxCell>
        <mxCell id="9" value="Regenerate Session ID (Anti Fixation)" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1140" y="160" width="160" height="60" as="geometry"/></mxCell>
        <mxCell id="10" value="Set Session Variables (user_id, username, role)" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1330" y="160" width="160" height="60" as="geometry"/></mxCell>
        <mxCell id="11" value="Redirect Sesuai Role (admin / kasir)" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1520" y="160" width="150" height="60" as="geometry"/></mxCell>
        <mxCell id="12" value="END" style="ellipse;whiteSpace=wrap;html=1;fillColor=#000000;fontColor=#ffffff;strokeColor=#000000;fontStyle=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1700" y="165" width="70" height="50" as="geometry"/></mxCell>
        <mxCell id="13" value="Tampil Pesan Error Login" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="970" y="320" width="140" height="60" as="geometry"/></mxCell>
        <mxCell id="20" edge="1" source="2" target="3" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="21" edge="1" source="3" target="4" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="22" edge="1" source="4" target="5" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="23" edge="1" source="5" target="6" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="24" edge="1" source="6" target="7" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="25" edge="1" source="7" target="8" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="26" value="Ya" edge="1" source="8" target="9" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="27" edge="1" source="9" target="10" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="28" edge="1" source="10" target="11" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="29" edge="1" source="11" target="12" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="30" value="Tidak" edge="1" source="8" target="13" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="31" edge="1" source="13" target="5" parent="1">
          <mxGeometry relative="1" as="geometry">
            <Array as="points"><mxPoint x="1040" y="410"/><mxPoint x="550" y="410"/></Array>
          </mxGeometry>
        </mxCell>
XML),
    '02_menu_pelanggan.drawio' => getXml('Menu Pelanggan', 'diag-customer-menu', <<<XML
        <mxCell id="2" value="START" style="ellipse;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#000000;fontStyle=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="30" y="165" width="70" height="50" as="geometry"/></mxCell>
        <mxCell id="3" value="Scan QR Code di Meja" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="130" y="160" width="130" height="60" as="geometry"/></mxCell>
        <mxCell id="4" value="Buka URL index.php?meja={id}" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="290" y="160" width="150" height="60" as="geometry"/></mxCell>
        <mxCell id="5" value="Meja Valid di DB?" style="rhombus;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="470" y="150" width="130" height="80" as="geometry"/></mxCell>
        <mxCell id="6" value="Fetch Menu, Kategori &amp; Promo dari DB" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="630" y="160" width="160" height="60" as="geometry"/></mxCell>
        <mxCell id="7" value="Render Halaman Menu dengan Filter Kategori" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="820" y="160" width="160" height="60" as="geometry"/></mxCell>
        <mxCell id="8" value="Tambah Item ke Keranjang (Cart)" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1010" y="160" width="150" height="60" as="geometry"/></mxCell>
        <mxCell id="9" value="Buka Cart Sheet &amp; Pilih Metode Bayar" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1190" y="160" width="160" height="60" as="geometry"/></mxCell>
        <mxCell id="10" value="Metode Cash?" style="rhombus;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1380" y="150" width="130" height="80" as="geometry"/></mxCell>
        <mxCell id="11" value="Input Nominal Uang yang Dibayar" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1380" y="320" width="150" height="60" as="geometry"/></mxCell>
        <mxCell id="12" value="Submit Pesanan (POST api/create_order.php)" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1540" y="160" width="170" height="60" as="geometry"/></mxCell>
        <mxCell id="13" value="Metode QRIS?" style="rhombus;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1740" y="150" width="130" height="80" as="geometry"/></mxCell>
        <mxCell id="14" value="Redirect ke customer/qris_payment.php" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1900" y="110" width="180" height="60" as="geometry"/></mxCell>
        <mxCell id="15" value="Redirect ke customer/order_status.php" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1900" y="220" width="180" height="60" as="geometry"/></mxCell>
        <mxCell id="16" value="END" style="ellipse;whiteSpace=wrap;html=1;fillColor=#000000;fontColor=#ffffff;strokeColor=#000000;fontStyle=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="2110" y="165" width="70" height="50" as="geometry"/></mxCell>
        <mxCell id="17" value="Halaman Error (Meja Tidak Ditemukan)" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="470" y="320" width="160" height="60" as="geometry"/></mxCell>
        <mxCell id="18" value="END" style="ellipse;whiteSpace=wrap;html=1;fillColor=#000000;fontColor=#ffffff;strokeColor=#000000;fontStyle=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="660" y="325" width="70" height="50" as="geometry"/></mxCell>
        <mxCell id="30" edge="1" source="2" target="3" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="31" edge="1" source="3" target="4" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="32" edge="1" source="4" target="5" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="33" value="Ya" edge="1" source="5" target="6" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="34" value="Tidak" edge="1" source="5" target="17" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="35" edge="1" source="17" target="18" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="36" edge="1" source="6" target="7" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="37" edge="1" source="7" target="8" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="38" edge="1" source="8" target="9" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="39" edge="1" source="9" target="10" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="40" value="Ya" edge="1" source="10" target="11" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="41" edge="1" source="11" target="12" parent="1">
          <mxGeometry relative="1" as="geometry">
            <Array as="points"><mxPoint x="1455" y="410"/><mxPoint x="1625" y="410"/></Array>
          </mxGeometry>
        </mxCell>
        <mxCell id="42" value="Tidak" edge="1" source="10" target="12" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="43" edge="1" source="12" target="13" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="44" value="Ya" edge="1" source="13" target="14" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="45" value="Tidak" edge="1" source="13" target="15" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="46" edge="1" source="14" target="16" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="47" edge="1" source="15" target="16" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
XML),
    '03_pembayaran_qris.drawio' => getXml('Pembayaran QRIS', 'diag-qris', <<<XML
        <mxCell id="2" value="START" style="ellipse;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#000000;fontStyle=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="30" y="165" width="70" height="50" as="geometry"/></mxCell>
        <mxCell id="3" value="Buka qris_payment.php?id={order_id}" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="130" y="160" width="160" height="60" as="geometry"/></mxCell>
        <mxCell id="4" value="Fetch Data Order dari DB" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="320" y="160" width="140" height="60" as="geometry"/></mxCell>
        <mxCell id="5" value="Pesanan Valid, QRIS &amp; Unpaid?" style="rhombus;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="490" y="148" width="160" height="84" as="geometry"/></mxCell>
        <mxCell id="6" value="Tampil QR Code Statis Toko &amp; Total Tagihan" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="680" y="160" width="170" height="60" as="geometry"/></mxCell>
        <mxCell id="7" value="Pilih &amp; Upload Bukti Transfer (Foto)" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="880" y="160" width="160" height="60" as="geometry"/></mxCell>
        <mxCell id="8" value="File Valid? (MIME + Ekstensi)" style="rhombus;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1070" y="150" width="150" height="80" as="geometry"/></mxCell>
        <mxCell id="9" value="convertToWebp() via GD Library" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1250" y="160" width="160" height="60" as="geometry"/></mxCell>
        <mxCell id="10" value="UPDATE orders SET bukti_transfer" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1440" y="160" width="160" height="60" as="geometry"/></mxCell>
        <mxCell id="11" value="Redirect ke order_status.php" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1630" y="160" width="150" height="60" as="geometry"/></mxCell>
        <mxCell id="12" value="END" style="ellipse;whiteSpace=wrap;html=1;fillColor=#000000;fontColor=#ffffff;strokeColor=#000000;fontStyle=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1810" y="165" width="70" height="50" as="geometry"/></mxCell>
        <mxCell id="13" value="Redirect ke order_status.php (sudah paid)" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="490" y="320" width="170" height="60" as="geometry"/></mxCell>
        <mxCell id="14" value="Tampil Pesan Error Format File" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1070" y="320" width="150" height="60" as="geometry"/></mxCell>
        <mxCell id="20" edge="1" source="2" target="3" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="21" edge="1" source="3" target="4" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="22" edge="1" source="4" target="5" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="23" value="Ya" edge="1" source="5" target="6" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="24" value="Tidak" edge="1" source="5" target="13" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="25" edge="1" source="13" target="12" parent="1">
          <mxGeometry relative="1" as="geometry">
            <Array as="points"><mxPoint x="575" y="410"/><mxPoint x="1845" y="410"/></Array>
          </mxGeometry>
        </mxCell>
        <mxCell id="26" edge="1" source="6" target="7" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="27" edge="1" source="7" target="8" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="28" value="Valid" edge="1" source="8" target="9" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="29" edge="1" source="9" target="10" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="30" edge="1" source="10" target="11" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="31" edge="1" source="11" target="12" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="32" value="Tidak Valid" edge="1" source="8" target="14" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="33" edge="1" source="14" target="7" parent="1">
          <mxGeometry relative="1" as="geometry">
            <Array as="points"><mxPoint x="1145" y="420"/><mxPoint x="960" y="420"/></Array>
          </mxGeometry>
        </mxCell>
XML),
    '04_status_pesanan.drawio' => getXml('Status Pesanan', 'diag-order-status', <<<XML
        <mxCell id="2" value="START" style="ellipse;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#000000;fontStyle=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="30" y="165" width="70" height="50" as="geometry"/></mxCell>
        <mxCell id="3" value="Buka order_status.php?id={id}" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="130" y="160" width="150" height="60" as="geometry"/></mxCell>
        <mxCell id="4" value="Fetch Data Order &amp; Detail dari DB" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="310" y="160" width="150" height="60" as="geometry"/></mxCell>
        <mxCell id="5" value="Render Halaman Status Awal (Pending)" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="490" y="160" width="160" height="60" as="geometry"/></mxCell>
        <mxCell id="6" value="Poll AJAX api/get_order_status.php (tiap 5 detik)" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="680" y="160" width="180" height="60" as="geometry"/></mxCell>
        <mxCell id="7" value="Status Berubah?" style="rhombus;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="890" y="150" width="140" height="80" as="geometry"/></mxCell>
        <mxCell id="8" value="Update UI (Ikon, Teks, Warna Status)" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1060" y="160" width="160" height="60" as="geometry"/></mxCell>
        <mxCell id="9" value="Status Final? (Selesai / Dibatalkan)" style="rhombus;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1250" y="150" width="160" height="80" as="geometry"/></mxCell>
        <mxCell id="10" value="Hentikan Polling &amp; Bersihkan localStorage" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1440" y="160" width="170" height="60" as="geometry"/></mxCell>
        <mxCell id="11" value="END" style="ellipse;whiteSpace=wrap;html=1;fillColor=#000000;fontColor=#ffffff;strokeColor=#000000;fontStyle=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1640" y="165" width="70" height="50" as="geometry"/></mxCell>
        <mxCell id="20" edge="1" source="2" target="3" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="21" edge="1" source="3" target="4" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="22" edge="1" source="4" target="5" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="23" edge="1" source="5" target="6" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="24" edge="1" source="6" target="7" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="25" value="Ya" edge="1" source="7" target="8" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="26" edge="1" source="8" target="9" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="27" value="Ya" edge="1" source="9" target="10" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="28" edge="1" source="10" target="11" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="29" value="Tidak" edge="1" source="7" target="6" parent="1">
          <mxGeometry relative="1" as="geometry">
            <Array as="points"><mxPoint x="960" y="280"/><mxPoint x="770" y="280"/></Array>
          </mxGeometry>
        </mxCell>
        <mxCell id="30" value="Tidak (Lanjut Poll)" edge="1" source="9" target="6" parent="1">
          <mxGeometry relative="1" as="geometry">
            <Array as="points"><mxPoint x="1330" y="310"/><mxPoint x="770" y="310"/></Array>
          </mxGeometry>
        </mxCell>
XML),
    '05_dashboard_kasir.drawio' => getXml('Dashboard Kasir', 'diag-kasir', <<<XML
        <mxCell id="2" value="START" style="ellipse;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#000000;fontStyle=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="30" y="165" width="70" height="50" as="geometry"/></mxCell>
        <mxCell id="3" value="Login sebagai Kasir (requireRole)" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="130" y="160" width="150" height="60" as="geometry"/></mxCell>
        <mxCell id="4" value="Cek Shift Aktif di DB" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="310" y="160" width="130" height="60" as="geometry"/></mxCell>
        <mxCell id="5" value="Shift Aktif Ada?" style="rhombus;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="470" y="150" width="130" height="80" as="geometry"/></mxCell>
        <mxCell id="6" value="Tampil Layar Kunci (Shift Ditutup)" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="470" y="320" width="160" height="60" as="geometry"/></mxCell>
        <mxCell id="7" value="Klik &quot;Mulai Shift Saya&quot; (POST)" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="660" y="320" width="150" height="60" as="geometry"/></mxCell>
        <mxCell id="8" value="INSERT INTO shifts (status=active)" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="840" y="320" width="160" height="60" as="geometry"/></mxCell>
        <mxCell id="9" value="Tampil Dashboard Live Orders" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="640" y="160" width="160" height="60" as="geometry"/></mxCell>
        <mxCell id="10" value="Poll AJAX get_orders.php (tiap 3 detik)" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="830" y="160" width="180" height="60" as="geometry"/></mxCell>
        <mxCell id="11" value="Ada Pesanan Baru?" style="rhombus;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1040" y="150" width="140" height="80" as="geometry"/></mxCell>
        <mxCell id="12" value="Mainkan Alert Audio &amp; Render Kartu Pesanan" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1210" y="160" width="180" height="60" as="geometry"/></mxCell>
        <mxCell id="13" value="Kasir Pilih Aksi" style="rhombus;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1420" y="150" width="130" height="80" as="geometry"/></mxCell>
        <mxCell id="14" value="UPDATE status_pesanan = diproses" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1580" y="60" width="180" height="60" as="geometry"/></mxCell>
        <mxCell id="15" value="UPDATE status_bayar = paid" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1580" y="150" width="180" height="60" as="geometry"/></mxCell>
        <mxCell id="16" value="UPDATE status_pesanan = selesai" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1580" y="240" width="180" height="60" as="geometry"/></mxCell>
        <mxCell id="17" value="UPDATE status_pesanan = dibatalkan" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1580" y="330" width="180" height="60" as="geometry"/></mxCell>
        <mxCell id="18" value="End Shift?" style="rhombus;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1790" y="155" width="120" height="80" as="geometry"/></mxCell>
        <mxCell id="19" value="Hitung Total Sales &amp; UPDATE shifts (completed)" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1940" y="160" width="180" height="60" as="geometry"/></mxCell>
        <mxCell id="22" value="END" style="ellipse;whiteSpace=wrap;html=1;fillColor=#000000;fontColor=#ffffff;strokeColor=#000000;fontStyle=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="2150" y="165" width="70" height="50" as="geometry"/></mxCell>
        <mxCell id="30" edge="1" source="2" target="3" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="31" edge="1" source="3" target="4" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="32" edge="1" source="4" target="5" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="33" value="Tidak" edge="1" source="5" target="6" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="34" value="Ya" edge="1" source="5" target="9" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="35" edge="1" source="6" target="7" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="36" edge="1" source="7" target="8" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="37" edge="1" source="8" target="9" parent="1">
          <mxGeometry relative="1" as="geometry">
            <Array as="points"><mxPoint x="920" y="410"/><mxPoint x="720" y="410"/></Array>
          </mxGeometry>
        </mxCell>
        <mxCell id="38" edge="1" source="9" target="10" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="39" edge="1" source="10" target="11" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="40" value="Ya" edge="1" source="11" target="12" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="41" value="Tidak" edge="1" source="11" target="10" parent="1">
          <mxGeometry relative="1" as="geometry">
            <Array as="points"><mxPoint x="1110" y="270"/><mxPoint x="920" y="270"/></Array>
          </mxGeometry>
        </mxCell>
        <mxCell id="42" edge="1" source="12" target="13" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="43" value="Proses" edge="1" source="13" target="14" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="44" value="Konfirmasi Bayar" edge="1" source="13" target="15" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="45" value="Selesai" edge="1" source="13" target="16" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="46" value="Tolak" edge="1" source="13" target="17" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="47" edge="1" source="14" target="18" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="48" edge="1" source="15" target="18" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="49" edge="1" source="16" target="18" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="50" edge="1" source="17" target="18" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="51" value="Ya (End Shift)" edge="1" source="18" target="19" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="52" edge="1" source="19" target="22" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="53" value="Tidak (Lanjut)" edge="1" source="18" target="10" parent="1">
          <mxGeometry relative="1" as="geometry">
            <Array as="points"><mxPoint x="1850" y="500"/><mxPoint x="920" y="500"/></Array>
          </mxGeometry>
        </mxCell>
XML),
    '06_dashboard_admin.drawio' => getXml('Dashboard Admin', 'diag-admin-dash', <<<XML
        <mxCell id="2" value="START" style="ellipse;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#000000;fontStyle=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="30" y="165" width="70" height="50" as="geometry"/></mxCell>
        <mxCell id="3" value="Login sebagai Admin (requireRole)" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="130" y="160" width="150" height="60" as="geometry"/></mxCell>
        <mxCell id="4" value="Query Statistik Hari Ini dari DB (Total Sales, Orders, Menu)" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="310" y="160" width="200" height="60" as="geometry"/></mxCell>
        <mxCell id="5" value="Render Dashboard (3 Kartu Statistik)" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="540" y="160" width="160" height="60" as="geometry"/></mxCell>
        <mxCell id="6" value="Navigasi ke Modul Lain?" style="rhombus;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="730" y="150" width="150" height="80" as="geometry"/></mxCell>
        <mxCell id="7" value="Pilih Menu di Sidebar (Kategori / Menu / Meja / dst)" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="910" y="160" width="190" height="60" as="geometry"/></mxCell>
        <mxCell id="8" value="END" style="ellipse;whiteSpace=wrap;html=1;fillColor=#000000;fontColor=#ffffff;strokeColor=#000000;fontStyle=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1130" y="165" width="70" height="50" as="geometry"/></mxCell>
        <mxCell id="9" value="END" style="ellipse;whiteSpace=wrap;html=1;fillColor=#000000;fontColor=#ffffff;strokeColor=#000000;fontStyle=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="730" y="320" width="70" height="50" as="geometry"/></mxCell>
        <mxCell id="20" edge="1" source="2" target="3" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="21" edge="1" source="3" target="4" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="22" edge="1" source="4" target="5" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="23" edge="1" source="5" target="6" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="24" value="Ya" edge="1" source="6" target="7" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="25" edge="1" source="7" target="8" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="26" value="Tidak" edge="1" source="6" target="9" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
XML),
    '07_kategori.drawio' => getXml('Manajemen Kategori', 'diag-categories', <<<XML
        <mxCell id="2" value="START" style="ellipse;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#000000;fontStyle=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="30" y="165" width="70" height="50" as="geometry"/></mxCell>
        <mxCell id="3" value="Buka Halaman Manajemen Kategori" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="130" y="160" width="160" height="60" as="geometry"/></mxCell>
        <mxCell id="4" value="Fetch &amp; Render Semua Kategori dari DB" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="320" y="160" width="160" height="60" as="geometry"/></mxCell>
        <mxCell id="5" value="Pilih Aksi?" style="rhombus;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="510" y="150" width="120" height="80" as="geometry"/></mxCell>
        <mxCell id="6" value="Input Nama Kategori Baru" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="660" y="50" width="150" height="60" as="geometry"/></mxCell>
        <mxCell id="7" value="INSERT INTO categories" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="840" y="50" width="140" height="60" as="geometry"/></mxCell>
        <mxCell id="8" value="Load Data Kategori ke Form" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="660" y="160" width="160" height="60" as="geometry"/></mxCell>
        <mxCell id="9" value="UPDATE categories SET nama" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="850" y="160" width="160" height="60" as="geometry"/></mxCell>
        <mxCell id="10" value="Konfirmasi Hapus" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="660" y="270" width="150" height="60" as="geometry"/></mxCell>
        <mxCell id="11" value="DELETE FROM categories (CASCADE ke menus)" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="840" y="270" width="180" height="60" as="geometry"/></mxCell>
        <mxCell id="12" value="Flash Message &amp; Redirect ke categories.php" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1050" y="160" width="180" height="60" as="geometry"/></mxCell>
        <mxCell id="13" value="END" style="ellipse;whiteSpace=wrap;html=1;fillColor=#000000;fontColor=#ffffff;strokeColor=#000000;fontStyle=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1260" y="165" width="70" height="50" as="geometry"/></mxCell>
        <mxCell id="20" edge="1" source="2" target="3" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="21" edge="1" source="3" target="4" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="22" edge="1" source="4" target="5" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="23" value="Tambah" edge="1" source="5" target="6" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="24" value="Edit" edge="1" source="5" target="8" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="25" value="Hapus" edge="1" source="5" target="10" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="26" edge="1" source="6" target="7" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="27" edge="1" source="8" target="9" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="28" edge="1" source="10" target="11" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="29" edge="1" source="7" target="12" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="30" edge="1" source="9" target="12" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="31" edge="1" source="11" target="12" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="32" edge="1" source="12" target="13" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
XML),
    '08_menu.drawio' => getXml('Manajemen Menu', 'diag-menus', <<<XML
        <mxCell id="2" value="START" style="ellipse;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#000000;fontStyle=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="30" y="165" width="70" height="50" as="geometry"/></mxCell>
        <mxCell id="3" value="Buka Halaman Manajemen Menu" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="130" y="160" width="150" height="60" as="geometry"/></mxCell>
        <mxCell id="4" value="Fetch Menu + Kategori dari DB" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="310" y="160" width="150" height="60" as="geometry"/></mxCell>
        <mxCell id="5" value="Render Daftar Menu dengan Gambar" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="490" y="160" width="160" height="60" as="geometry"/></mxCell>
        <mxCell id="6" value="Pilih Aksi?" style="rhombus;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="680" y="150" width="120" height="80" as="geometry"/></mxCell>
        <mxCell id="7" value="Isi Form (Nama, Harga, Kategori, Status)" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="830" y="40" width="170" height="60" as="geometry"/></mxCell>
        <mxCell id="8" value="Upload Gambar → Validasi MIME → convertToWebp()" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1030" y="40" width="190" height="60" as="geometry"/></mxCell>
        <mxCell id="9" value="INSERT INTO menus" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1250" y="40" width="130" height="60" as="geometry"/></mxCell>
        <mxCell id="10" value="Load Data &amp; Update Form" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="830" y="155" width="160" height="60" as="geometry"/></mxCell>
        <mxCell id="11" value="UPDATE menus (Nama, Harga, Gambar, Status)" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1020" y="155" width="200" height="60" as="geometry"/></mxCell>
        <mxCell id="12" value="Konfirmasi Hapus" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="830" y="270" width="150" height="60" as="geometry"/></mxCell>
        <mxCell id="13" value="Hapus File + DELETE FROM menus" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1010" y="270" width="180" height="60" as="geometry"/></mxCell>
        <mxCell id="14" value="Flash Message &amp; Redirect ke menus.php" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1250" y="160" width="180" height="60" as="geometry"/></mxCell>
        <mxCell id="15" value="END" style="ellipse;whiteSpace=wrap;html=1;fillColor=#000000;fontColor=#ffffff;strokeColor=#000000;fontStyle=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1460" y="165" width="70" height="50" as="geometry"/></mxCell>
        <mxCell id="20" edge="1" source="2" target="3" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="21" edge="1" source="3" target="4" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="22" edge="1" source="4" target="5" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="23" edge="1" source="5" target="6" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="24" value="Tambah" edge="1" source="6" target="7" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="25" value="Edit" edge="1" source="6" target="10" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="26" value="Hapus" edge="1" source="6" target="12" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="27" edge="1" source="7" target="8" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="28" edge="1" source="8" target="9" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="29" edge="1" source="10" target="11" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="30" edge="1" source="12" target="13" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="31" edge="1" source="9" target="14" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="32" edge="1" source="11" target="14" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="33" edge="1" source="13" target="14" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="34" edge="1" source="14" target="15" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
XML),
    '09_meja_qr.drawio' => getXml('Meja dan QR Code', 'diag-tables', <<<XML
        <mxCell id="2" value="START" style="ellipse;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#000000;fontStyle=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="30" y="165" width="70" height="50" as="geometry"/></mxCell>
        <mxCell id="3" value="Buka Halaman Meja &amp; QR Code" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="130" y="160" width="150" height="60" as="geometry"/></mxCell>
        <mxCell id="4" value="Fetch Semua Meja dari DB" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="310" y="160" width="140" height="60" as="geometry"/></mxCell>
        <mxCell id="5" value="Generate QR URL via api.qrserver.com per Meja" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="480" y="160" width="190" height="60" as="geometry"/></mxCell>
        <mxCell id="6" value="Render Grid Meja + QR Code" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="700" y="160" width="150" height="60" as="geometry"/></mxCell>
        <mxCell id="7" value="Pilih Aksi?" style="rhombus;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="880" y="150" width="120" height="80" as="geometry"/></mxCell>
        <mxCell id="8" value="Input Nomor Meja Baru" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1030" y="50" width="140" height="60" as="geometry"/></mxCell>
        <mxCell id="9" value="INSERT INTO tables" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1200" y="50" width="130" height="60" as="geometry"/></mxCell>
        <mxCell id="10" value="Load Data &amp; Edit Nomor Meja" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1030" y="160" width="150" height="60" as="geometry"/></mxCell>
        <mxCell id="11" value="UPDATE tables SET nomor_meja" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1210" y="160" width="170" height="60" as="geometry"/></mxCell>
        <mxCell id="12" value="Konfirmasi Hapus" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1030" y="270" width="140" height="60" as="geometry"/></mxCell>
        <mxCell id="13" value="DELETE FROM tables" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1200" y="270" width="140" height="60" as="geometry"/></mxCell>
        <mxCell id="14" value="Download QR PNG via Browser" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1030" y="380" width="150" height="60" as="geometry"/></mxCell>
        <mxCell id="15" value="Flash Message &amp; Redirect" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1410" y="160" width="150" height="60" as="geometry"/></mxCell>
        <mxCell id="16" value="END" style="ellipse;whiteSpace=wrap;html=1;fillColor=#000000;fontColor=#ffffff;strokeColor=#000000;fontStyle=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1590" y="165" width="70" height="50" as="geometry"/></mxCell>
        <mxCell id="20" edge="1" source="2" target="3" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="21" edge="1" source="3" target="4" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="22" edge="1" source="4" target="5" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="23" edge="1" source="5" target="6" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="24" edge="1" source="6" target="7" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="25" value="Tambah" edge="1" source="7" target="8" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="26" value="Edit" edge="1" source="7" target="10" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="27" value="Hapus" edge="1" source="7" target="12" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="28" value="Download QR" edge="1" source="7" target="14" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="29" edge="1" source="8" target="9" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="30" edge="1" source="10" target="11" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="31" edge="1" source="12" target="13" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="32" edge="1" source="9" target="15" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="33" edge="1" source="11" target="15" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="34" edge="1" source="13" target="15" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="35" edge="1" source="14" target="15" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="36" edge="1" source="15" target="16" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
XML),
    '10_banner_promo.drawio' => getXml('Banner Promo', 'diag-promos', <<<XML
        <mxCell id="2" value="START" style="ellipse;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#000000;fontStyle=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="30" y="165" width="70" height="50" as="geometry"/></mxCell>
        <mxCell id="3" value="Buka Halaman Banner Promo" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="130" y="160" width="150" height="60" as="geometry"/></mxCell>
        <mxCell id="4" value="Fetch &amp; Render Semua Promo dari DB" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="310" y="160" width="170" height="60" as="geometry"/></mxCell>
        <mxCell id="5" value="Pilih Aksi?" style="rhombus;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="510" y="150" width="120" height="80" as="geometry"/></mxCell>
        <mxCell id="6" value="Input Judul &amp; Upload Gambar Banner" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="660" y="50" width="170" height="60" as="geometry"/></mxCell>
        <mxCell id="7" value="Validasi MIME &amp; Ekstensi" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="860" y="50" width="140" height="60" as="geometry"/></mxCell>
        <mxCell id="8" value="convertToWebp() &amp; INSERT INTO promos" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1030" y="50" width="190" height="60" as="geometry"/></mxCell>
        <mxCell id="9" value="Load Data &amp; Edit Form Promo" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="660" y="160" width="170" height="60" as="geometry"/></mxCell>
        <mxCell id="10" value="UPDATE promos (Judul, Gambar, is_active)" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="860" y="160" width="200" height="60" as="geometry"/></mxCell>
        <mxCell id="11" value="Konfirmasi Hapus Promo" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="660" y="270" width="160" height="60" as="geometry"/></mxCell>
        <mxCell id="12" value="Hapus File &amp; DELETE FROM promos" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="850" y="270" width="180" height="60" as="geometry"/></mxCell>
        <mxCell id="13" value="Flash Message &amp; Redirect ke promos.php" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1250" y="160" width="190" height="60" as="geometry"/></mxCell>
        <mxCell id="14" value="END" style="ellipse;whiteSpace=wrap;html=1;fillColor=#000000;fontColor=#ffffff;strokeColor=#000000;fontStyle=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1470" y="165" width="70" height="50" as="geometry"/></mxCell>
        <mxCell id="20" edge="1" source="2" target="3" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="21" edge="1" source="3" target="4" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="22" edge="1" source="4" target="5" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="23" value="Tambah" edge="1" source="5" target="6" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="24" value="Edit" edge="1" source="5" target="9" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="25" value="Hapus" edge="1" source="5" target="11" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="26" edge="1" source="6" target="7" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="27" edge="1" source="7" target="8" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="28" edge="1" source="9" target="10" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="29" edge="1" source="11" target="12" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="30" edge="1" source="8" target="13" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="31" edge="1" source="10" target="13" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="32" edge="1" source="12" target="13" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="33" edge="1" source="13" target="14" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
XML),
    '11_laporan_penjualan.drawio' => getXml('Laporan Penjualan', 'diag-reports', <<<XML
        <mxCell id="2" value="START" style="ellipse;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#000000;fontStyle=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="30" y="165" width="70" height="50" as="geometry"/></mxCell>
        <mxCell id="3" value="Buka Halaman Laporan Penjualan" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="130" y="160" width="160" height="60" as="geometry"/></mxCell>
        <mxCell id="4" value="Baca ?tab &amp; ?date dari GET Request" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="320" y="160" width="160" height="60" as="geometry"/></mxCell>
        <mxCell id="5" value="Tab = Daily atau Shifts?" style="rhombus;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="510" y="150" width="150" height="80" as="geometry"/></mxCell>
        <mxCell id="6" value="Query orders by date (paid &amp; selesai)" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="690" y="100" width="180" height="60" as="geometry"/></mxCell>
        <mxCell id="7" value="Query shifts by date dengan SUM sales" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="690" y="220" width="180" height="60" as="geometry"/></mxCell>
        <mxCell id="8" value="Render Tabel Laporan dengan Data" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="900" y="160" width="170" height="60" as="geometry"/></mxCell>
        <mxCell id="9" value="Filter Tanggal Lain?" style="rhombus;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1100" y="150" width="140" height="80" as="geometry"/></mxCell>
        <mxCell id="10" value="Submit Form Filter Tanggal" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1100" y="310" width="150" height="60" as="geometry"/></mxCell>
        <mxCell id="11" value="END" style="ellipse;whiteSpace=wrap;html=1;fillColor=#000000;fontColor=#ffffff;strokeColor=#000000;fontStyle=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1280" y="165" width="70" height="50" as="geometry"/></mxCell>
        <mxCell id="20" edge="1" source="2" target="3" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="21" edge="1" source="3" target="4" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="22" edge="1" source="4" target="5" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="23" value="Daily" edge="1" source="5" target="6" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="24" value="Shifts" edge="1" source="5" target="7" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="25" edge="1" source="6" target="8" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="26" edge="1" source="7" target="8" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="27" edge="1" source="8" target="9" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="28" value="Tidak" edge="1" source="9" target="11" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="29" value="Ya" edge="1" source="9" target="10" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="30" edge="1" source="10" target="4" parent="1">
          <mxGeometry relative="1" as="geometry">
            <Array as="points"><mxPoint x="1175" y="420"/><mxPoint x="400" y="420"/></Array>
          </mxGeometry>
        </mxCell>
XML),
    '12_pengguna.drawio' => getXml('Manajemen Pengguna', 'diag-users', <<<XML
        <mxCell id="2" value="START" style="ellipse;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#000000;fontStyle=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="30" y="165" width="70" height="50" as="geometry"/></mxCell>
        <mxCell id="3" value="Buka Halaman Manajemen Pengguna" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="130" y="160" width="160" height="60" as="geometry"/></mxCell>
        <mxCell id="4" value="Fetch &amp; Render Semua Users dari DB" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="320" y="160" width="160" height="60" as="geometry"/></mxCell>
        <mxCell id="5" value="Pilih Aksi?" style="rhombus;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="510" y="150" width="120" height="80" as="geometry"/></mxCell>
        <mxCell id="6" value="Input Username, Password &amp; Role" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="660" y="50" width="170" height="60" as="geometry"/></mxCell>
        <mxCell id="7" value="Validasi Role (whitelist: admin/kasir)" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="860" y="50" width="170" height="60" as="geometry"/></mxCell>
        <mxCell id="8" value="password_hash() &amp; INSERT INTO users" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1060" y="50" width="190" height="60" as="geometry"/></mxCell>
        <mxCell id="9" value="Load Data &amp; Edit Form User" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="660" y="160" width="160" height="60" as="geometry"/></mxCell>
        <mxCell id="10" value="UPDATE users (Username, Password, Role)" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="850" y="160" width="190" height="60" as="geometry"/></mxCell>
        <mxCell id="11" value="Konfirmasi Hapus" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="660" y="270" width="150" height="60" as="geometry"/></mxCell>
        <mxCell id="12" value="Cek Bukan Self-Delete (Guard)" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="840" y="270" width="170" height="60" as="geometry"/></mxCell>
        <mxCell id="13" value="DELETE FROM users" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1040" y="270" width="150" height="60" as="geometry"/></mxCell>
        <mxCell id="14" value="Flash Message &amp; Redirect ke users.php" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1280" y="160" width="180" height="60" as="geometry"/></mxCell>
        <mxCell id="15" value="END" style="ellipse;whiteSpace=wrap;html=1;fillColor=#000000;fontColor=#ffffff;strokeColor=#000000;fontStyle=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1490" y="165" width="70" height="50" as="geometry"/></mxCell>
        <mxCell id="20" edge="1" source="2" target="3" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="21" edge="1" source="3" target="4" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="22" edge="1" source="4" target="5" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="23" value="Tambah" edge="1" source="5" target="6" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="24" value="Edit" edge="1" source="5" target="9" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="25" value="Hapus" edge="1" source="5" target="11" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="26" edge="1" source="6" target="7" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="27" edge="1" source="7" target="8" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="28" edge="1" source="9" target="10" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="29" edge="1" source="11" target="12" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="30" edge="1" source="12" target="13" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="31" edge="1" source="8" target="14" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="32" edge="1" source="10" target="14" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="33" edge="1" source="13" target="14" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="34" edge="1" source="14" target="15" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
XML),
    '13_pengaturan.drawio' => getXml('Pengaturan Sistem', 'diag-settings', <<<XML
        <mxCell id="2" value="START" style="ellipse;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#000000;fontStyle=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="30" y="165" width="70" height="50" as="geometry"/></mxCell>
        <mxCell id="3" value="Buka Halaman Pengaturan Sistem" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="130" y="160" width="160" height="60" as="geometry"/></mxCell>
        <mxCell id="4" value="Cek File Audio Notifikasi &amp; Gambar QRIS Toko" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="320" y="160" width="190" height="60" as="geometry"/></mxCell>
        <mxCell id="5" value="Render Form Pengaturan (Audio + QRIS)" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="540" y="160" width="170" height="60" as="geometry"/></mxCell>
        <mxCell id="6" value="Jenis Upload?" style="rhombus;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="740" y="150" width="130" height="80" as="geometry"/></mxCell>
        <mxCell id="7" value="Pilih File Audio (MP3/WAV/OGG)" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="900" y="80" width="170" height="60" as="geometry"/></mxCell>
        <mxCell id="8" value="Validasi MIME Audio (finfo_file)" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1100" y="80" width="160" height="60" as="geometry"/></mxCell>
        <mxCell id="9" value="Hapus Audio Lama &amp; Simpan notification.{ext}" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1290" y="80" width="200" height="60" as="geometry"/></mxCell>
        <mxCell id="10" value="Pilih Gambar QRIS (JPG/PNG/WEBP)" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="900" y="240" width="170" height="60" as="geometry"/></mxCell>
        <mxCell id="11" value="Validasi MIME Gambar (finfo_file)" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1100" y="240" width="160" height="60" as="geometry"/></mxCell>
        <mxCell id="12" value="convertToWebp() &amp; Simpan qris.webp" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1290" y="240" width="190" height="60" as="geometry"/></mxCell>
        <mxCell id="13" value="Flash Message &amp; Redirect ke settings.php" style="rounded=0;whiteSpace=wrap;html=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1510" y="160" width="190" height="60" as="geometry"/></mxCell>
        <mxCell id="14" value="END" style="ellipse;whiteSpace=wrap;html=1;fillColor=#000000;fontColor=#ffffff;strokeColor=#000000;fontStyle=1;fontSize=11;" vertex="1" parent="1"><mxGeometry x="1730" y="165" width="70" height="50" as="geometry"/></mxCell>
        <mxCell id="20" edge="1" source="2" target="3" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="21" edge="1" source="3" target="4" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="22" edge="1" source="4" target="5" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="23" edge="1" source="5" target="6" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="24" value="Audio" edge="1" source="6" target="7" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="25" value="QRIS" edge="1" source="6" target="10" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="26" edge="1" source="7" target="8" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="27" edge="1" source="8" target="9" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="28" edge="1" source="10" target="11" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="29" edge="1" source="11" target="12" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="30" edge="1" source="9" target="13" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="31" edge="1" source="12" target="13" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
        <mxCell id="32" edge="1" source="13" target="14" parent="1"><mxGeometry relative="1" as="geometry"/></mxCell>
XML)
];

foreach ($files as $name => $content) {
    file_put_contents("$dir/$name", $content);
}

if (file_exists(__DIR__ . '/activity_diagram.drawio')) {
    unlink(__DIR__ . '/activity_diagram.drawio');
}

echo "Berhasil membuat 13 file diagram.";
