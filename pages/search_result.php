<?php
session_start();
require_once '../config/db.php'; // Pastikan ini adalah file yang menghubungkan ke database

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

$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
$searchResults = searchProducts($keyword);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - Olshop</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Rubik+Mono+One&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../src/css/app.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <script src="https://unpkg.com/phosphor-icons"></script>
</head>

<body class="font-['Kanit']">
    <?php include '../components/navbar.php'; ?>

    <div class="container mx-auto mt-8">
        <h2 class="text-2xl font-bold mb-4">Hasil Pencarian untuk "<?php echo htmlspecialchars($keyword); ?>":</h2>

        <?php if (empty($searchResults)) : ?>
            <p>Tidak ada hasil yang ditemukan.</p>
        <?php else : ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 py-4">
                <?php foreach ($searchResults as $product) : ?>
                    <div class="border rounded-lg p-4 shadow-md">
                        <?php if ($product['gambar_produk']) : ?>
                            <img src="data:image/jpeg;base64,<?php echo $product['gambar_produk']; ?>" alt="<?php echo htmlspecialchars($product['produk_nama']); ?>" class="w-full h-48 object-cover mb-4">
                        <?php endif; ?>
                        <h3 class="text-lg font-semibold"><?php echo htmlspecialchars($product['produk_nama']); ?></h3>
                        <p class="text-gray-600 mb-2"><?php echo htmlspecialchars($product['produk_deskripsi']); ?></p>
                        <p class="font-bold">Rp <?php echo number_format($product['produk_harga'], 0, ',', '.'); ?></p>
                        <p class="text-sm text-gray-500">Stock: <?php echo $product['produk_stock']; ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <?php include '../components/footer.php'; ?>
</body>

</html>