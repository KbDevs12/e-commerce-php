<?php

require_once('../config/db.php');

function generateUUID() {
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}

function fetchPlaceholderImage($width, $height, $text) {
    $url = "https://via.placeholder.com/{$width}x{$height}?text=" . urlencode($text);
    $image = file_get_contents($url);
    return $image;
}

function insertDummyImageData($conn, $produk_id, $image) {
    $uuid = generateUUID(); // Menggunakan generateUUID() untuk mendapatkan UUID baru
    $sql = "INSERT INTO gambar_produk (id, produk_id, gambar) VALUES (?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssb', $uuid, $produk_id, $image);
    $stmt->send_long_data(2, $image);
    if ($stmt->execute()) {
        echo "Data gambar dummy berhasil dimasukkan untuk produk ID: $produk_id<br>";
    } else {
        echo "Error: " . $sql . "<br>" . $stmt->error . "<br>";
    }
    $stmt->close();
}

// Array produk ID yang belum memiliki gambar
$produk_ids = [
    '852c3767-87fd-430e-b672-21fa39ea3860',
    '897fbb60-6ab0-4044-a04b-6eeaba5b07ab',
    '8e754efa-9c6c-4183-bc65-bcd71a9eab9d',
    'b3d1d8d0-a136-415a-8be2-e8d13d32f31a',
    'b7b7b308-0797-4557-bafe-7cc068fd839c',
    'c2e1cbca-6217-4db1-ab1f-a149c03f6f89',
    'c754fc41-bbb8-49b3-92c9-572513e97026'
];

// Sisipkan 3 gambar dummy untuk setiap produk yang belum memiliki gambar
foreach ($produk_ids as $produk_id) {
    for ($i = 1; $i <= 3; $i++) {
        $text = "Produk_{$produk_id}_Gambar_{$i}";
        $image = fetchPlaceholderImage(300, 300, $text); // Ukuran gambar placeholder 300x300 dengan teks kustom
        insertDummyImageData($conn, $produk_id, $image);
    }
}

// Tutup koneksi setelah selesai
$conn->close();
?>
