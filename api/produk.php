<?php

header('Content-Type: application/json');

require_once '../config/db.php';

$query = 'SELECT
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
    GROUP BY p.id';

$result = $conn->query($query);

$items = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $row['gambar_produk'] = base64_encode($row['gambar_produk']);
        $items[] = $row;
    }
}

$conn->close();

$json_data = json_encode($items);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo "JSON encoding error: " . json_last_error_msg();
    exit();
}
echo $json_data;
