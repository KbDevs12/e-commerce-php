<?php

require_once '../config/db.php';

function searchProducts($keyword)
{
    global $conn;

    $keyword = $conn->real_escape_string($keyword);

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
    WHERE
        p.nama LIKE '%$keyword%' OR p.deskripsi LIKE '%$keyword%'
    GROUP BY p.id";

    $result = $conn->query($query);
    $items = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $row['gambar_produk'] = base64_encode($row['gambar_produk']);
            $items[] = $row;
        }
    }

    return $items;
}

if (isset($_GET['keyword'])) {
    $keyword = $_GET['keyword'];
    $searchResults = searchProducts($keyword);

    header('Content-Type: application/json');
    echo json_encode($searchResults);
    exit;
}
