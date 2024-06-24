<?php
header('Content-Type: application/json');

require_once('../config/db.php');
$produkId = $_GET['id'];
$stmt = $conn->prepare('SELECT 
        p.id AS produk_id, 
        p.nama AS produk_nama, 
        p.deskripsi AS produk_deskripsi, 
        p.stock AS produk_stock,
        p.harga AS produk_harga, 
        p.created_at AS produk_created_at, 
        gp.gambar AS gambar_produk
    FROM 
        produk AS p
    LEFT JOIN gambar_produk AS gp ON p.id = gp.produk_id
    WHERE p.id = ?');
$stmt->bind_param('s', $produkId);
$stmt->execute();
$result = $stmt->get_result();

$items = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $row['gambar_produk'] = base64_encode($row['gambar_produk']);
        $items[] = $row;
    }
}

$stmt->close();
$conn->close();

$json_data = json_encode(array('products' => $items, 'total' => $result->num_rows));
if (json_last_error() !== JSON_ERROR_NONE) {
    echo "JSON encoding error: " . json_last_error_msg();
    exit();
}
echo $json_data;
?>
