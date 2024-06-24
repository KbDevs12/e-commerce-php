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

// Ambil detail user
$stmt = $conn->prepare("SELECT u.username, u.email, du.alamat, du.telepon 
                        FROM users u 
                        LEFT JOIN detail_user du ON u.id = du.id_user 
                        WHERE u.id = ?");
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_details = $result->fetch_assoc();

// Cek apakah detail user lengkap
$details_complete = !empty($user_details['alamat']) && !empty($user_details['telepon']);

// Ambil item di keranjang
$stmt = $conn->prepare("SELECT c.id, c.quantity, p.id as product_id, p.nama, p.harga 
                        FROM keranjang c 
                        JOIN produk p ON c.produk_id = p.id 
                        WHERE c.user_id = ? AND c.status = 'pending'");
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
    <title>Checkout - Olshop</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="bg-gray-100 font-sans">
    <?php include '../components/navbar.php' ?>

    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6">Checkout</h1>

        <?php if (!$details_complete) : ?>
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6" role="alert">
                <p class="font-bold">Perhatian!</p>
                <p>Anda perlu melengkapi detail profil Anda sebelum melanjutkan checkout.</p>
                <a href="profile.php" class="text-blue-500 hover:text-blue-700">Lengkapi profil</a>
            </div>
        <?php else : ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-white shadow-md rounded-lg p-6">
                    <h2 class="text-xl font-bold mb-4">Detail Pengiriman</h2>
                    <form id="checkout-form">
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Nama</label>
                            <p><?php echo htmlspecialchars($user_details['username']); ?></p>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                            <p><?php echo htmlspecialchars($user_details['email']); ?></p>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Alamat</label>
                            <p><?php echo htmlspecialchars($user_details['alamat']); ?></p>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Nomor Telepon</label>
                            <p><?php echo htmlspecialchars($user_details['telepon']); ?></p>
                        </div>
                        <div class="mb-4">
                            <label for="payment_method" class="block text-gray-700 text-sm font-bold mb-2">Metode Pembayaran</label>
                            <select id="payment_method" name="payment_method" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                                <option value="">Pilih metode pembayaran</option>
                                <option value="tunai">Tunai</option>
                                <option value="dana">DANA</option>
                                <option value="ovo">OVO</option>
                                <option value="gopay">GoPay</option>
                                <option value="kartu kredit">Kartu Kredit</option>
                            </select>
                        </div>
                    </form>
                </div>

                <div class="bg-white shadow-md rounded-lg p-6">
                    <h2 class="text-xl font-bold mb-4">Ringkasan Pesanan</h2>
                    <table class="w-full mb-4">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-2">Produk</th>
                                <th class="text-right py-2">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cart_items as $item) : ?>
                                <tr class="border-b">
                                    <td class="py-2"><?php echo htmlspecialchars($item['nama']); ?> (x<?php echo $item['quantity']; ?>)</td>
                                    <td class="text-right py-2">Rp <?php echo number_format($item['harga'] * $item['quantity'], 0, ',', '.'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td class="py-2 font-bold">Total</td>
                                <td class="text-right py-2 font-bold">Rp <?php echo number_format($total, 0, ',', '.'); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                    <button id="place-order-btn" class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">
                        Proses Pesanan
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php include '../components/footer.php' ?>

    <script>
        $(document).ready(function() {
            $('#place-order-btn').click(function() {
                if ($('#checkout-form')[0].checkValidity()) {
                    var formData = $('#checkout-form').serialize();
                    $.ajax({
                        url: '../api/order/process_order.php',
                        method: 'POST',
                        data: formData,
                        success: function(response) {
                            var result = JSON.parse(response);
                            if (result.success) {
                                alert('Pesanan berhasil diproses!');
                                window.location.href = 'order_confirmation.php?order_id=' + result.order_id;
                            } else {
                                alert('Terjadi kesalahan: ' + result.message);
                            }
                        },
                        error: function() {
                            alert('Terjadi kesalahan saat memproses pesanan.');
                        }
                    });
                } else {
                    alert('Harap pilih metode pembayaran.');
                }
            });
        });
    </script>
</body>

</html>