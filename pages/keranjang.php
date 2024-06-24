<?php
session_start();

// Periksa apakah user sudah login
$user_logged_in = isset($_SESSION['user_id']);
$user_id = $user_logged_in ? $_SESSION['user_id'] : null;

if (!$user_logged_in) {
    header('Location: login.php');
    exit;
}

include '../config/db.php';

// Ambil item di keranjang beserta gambar produk
$stmt = $conn->prepare("SELECT c.id, c.quantity, p.id as product_id, p.nama, p.harga, gp.gambar_produk 
                        FROM keranjang c 
                        JOIN produk p ON c.produk_id = p.id 
                        LEFT JOIN (
                            SELECT produk_id, gambar as gambar_produk
                            FROM gambar_produk
                            GROUP BY produk_id
                        ) gp ON p.id = gp.produk_id
                        WHERE c.user_id = ? AND status= 'pending'");
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$cart_items = $result->fetch_all(MYSQLI_ASSOC);

// Hitung total
$total = 0;
foreach ($cart_items as $item) {
    $total += $item['harga'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja - Olshop</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 font-sans">
    <?php include '../components/navbar.php' ?>

    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6">Keranjang Belanja</h1>

        <?php if (empty($cart_items)) : ?>
            <div class="bg-white shadow-md rounded-lg p-6 text-center">
                <p class="text-xl mb-4">Keranjang Anda kosong</p>
                <a href="produk.php" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                    Mulai Belanja
                </a>
            </div>
        <?php else : ?>
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">Produk</th>
                            <th class="py-3 px-6 text-center">Harga</th>
                            <th class="py-3 px-6 text-center">Jumlah</th>
                            <th class="py-3 px-6 text-center">Total</th>
                            <th class="py-3 px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm font-light">
                        <?php foreach ($cart_items as $item) : ?>
                            <tr class="border-b border-gray-200 hover:bg-gray-100">
                                <td class="py-3 px-6 text-left whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="mr-2">
                                            <img class="w-16 h-16 rounded" src="data:image/jpeg;base64,<?php echo base64_encode($item['gambar_produk']); ?>" alt="<?php echo $item['nama']; ?>">
                                        </div>
                                        <span class="font-medium"><?php echo $item['nama']; ?></span>
                                    </div>
                                </td>
                                <td class="py-3 px-6 text-center">
                                    Rp <?php echo number_format($item['harga'], 0, ',', '.'); ?>
                                </td>
                                <td class="py-3 px-6 text-center">
                                    <div class="flex justify-center items-center">
                                        <button class="quantity-btn minus bg-gray-200 px-2 rounded-l" data-id="<?php echo $item['id']; ?>">-</button>
                                        <input type="number" class="quantity-input w-16 text-center border-t border-b border-gray-200" value="<?php echo $item['quantity']; ?>" min="1" data-id="<?php echo $item['id']; ?>">
                                        <button class="quantity-btn plus bg-gray-200 px-2 rounded-r" data-id="<?php echo $item['id']; ?>">+</button>
                                    </div>
                                </td>
                                <td class="py-3 px-6 text-center">
                                    Rp <span class="item-total"><?php echo number_format($item['harga'] * $item['quantity'], 0, ',', '.'); ?></span>
                                </td>
                                <td class="py-3 px-6 text-center">
                                    <button class="remove-item bg-red-500 hover:bg-red-600 text-white font-bold py-1 px-2 rounded" data-id="<?php echo $item['id']; ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-6 bg-white shadow-md rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <span class="text-xl font-bold">Total:</span>
                    <span class="text-2xl font-bold text-blue-600">Rp <span id="cart-total"><?php echo number_format($total, 0, ',', '.'); ?></span></span>
                </div>
                <button id="checkout-btn" class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">
                    Lanjutkan ke Pembayaran
                </button>
            </div>
        <?php endif; ?>
    </div>

    <?php include '../components/footer.php' ?>

    <script>
        $(document).ready(function() {
            // Fungsi untuk memperbarui jumlah
            function updateQuantity(id, newQuantity) {
                $.ajax({
                    url: '../api/produk/update_cart.php',
                    method: 'POST',
                    data: {
                        id: id,
                        quantity: newQuantity
                    },
                    success: function(response) {
                        location.reload(); // Reload halaman untuk memperbarui tampilan
                    }
                });
            }

            // Event listener untuk tombol plus dan minus
            $('.quantity-btn').click(function() {
                var id = $(this).data('id');
                var input = $(this).siblings('.quantity-input');
                var currentValue = parseInt(input.val());
                if ($(this).hasClass('plus')) {
                    input.val(currentValue + 1);
                } else if ($(this).hasClass('minus') && currentValue > 1) {
                    input.val(currentValue - 1);
                }
                updateQuantity(id, input.val());
            });

            // Event listener untuk input jumlah
            $('.quantity-input').change(function() {
                var id = $(this).data('id');
                updateQuantity(id, $(this).val());
            });

            // Event listener untuk tombol hapus
            $('.remove-item').click(function() {
                var id = $(this).data('id');
                if (confirm('Apakah Anda yakin ingin menghapus item ini dari keranjang?')) {
                    $.ajax({
                        url: '../api/produk/remove_from_cart.php',
                        method: 'POST',
                        data: {
                            id: id
                        },
                        success: function(response) {
                            location.reload(); // Reload halaman untuk memperbarui tampilan
                        }
                    });
                }
            });

            // Event listener untuk tombol checkout
            $('#checkout-btn').click(function() {
                window.location.href = 'checkout.php';
            });
        });
    </script>
</body>

</html>