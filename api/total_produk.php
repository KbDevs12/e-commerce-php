<?php

header('Content-Type: application/json');

require_once('../config/db.php');

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$offset = ($page - 1) * $limit;

// Get total count
$total_query = "SELECT COUNT(DISTINCT p.id) as total FROM produk AS p";
$total_result = $conn->query($total_query);
$total_row = $total_result->fetch_assoc();
$total_products = $total_row['total'];

// Main query with pagination
$query = "SELECT 
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
GROUP BY p.id
LIMIT $limit OFFSET $offset";

$result = $conn->query($query);

$items = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $row['gambar_produk'] = base64_encode($row['gambar_produk']);
        $items[] = $row;
    }
}

$conn->close();

$json_data = json_encode(array('products' => $items, 'total' => $total_products));
if (json_last_error() !== JSON_ERROR_NONE) {
    echo "JSON encoding error: " . json_last_error_msg();
    exit();
}
echo $json_data;