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

// Periksa apakah ada order_id
if (!isset($_GET['order_id'])) {
    header('Location: index.php');
    exit;
}

$order_id = $_GET['order_id'];

// Ambil detail pesanan
$stmt = $conn->prepare("SELECT o.id, o.order_date, o.status, o.payment_method, u.username, u.email, du.alamat, du.telepon 
                        FROM orders o 
                        JOIN users u ON o.user_id = u.id 
                        LEFT JOIN detail_user du ON u.id = du.id_user 
                        WHERE o.id = ? AND o.user_id = ? AND o.status='pending'");
$stmt->bind_param("ss", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if (!$order) {
    header('Location: index.php');
    exit;
}

// Ambil item pesanan
$stmt = $conn->prepare("SELECT oi.quantity, p.nama, p.harga 
                        FROM order_items oi 
                        JOIN produk p ON oi.product_id = p.id 
                        WHERE oi.order_id = ?");
$stmt->bind_param("s", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order_items = $result->fetch_all(MYSQLI_ASSOC);

// Hitung total
$total = 0;
foreach ($order_items as $item) {
    $total += $item['harga'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Pesanan - Olshop</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 font-sans">
    <?php include '../components/navbar.php' ?>

    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6">Konfirmasi Pesanan</h1>

        <div class="bg-white shadow-md rounded-lg p-6 mb-6">
            <h2 class="text-xl font-bold mb-4">Detail Pesanan</h2>
            <p><strong>Nomor Pesanan:</strong> <?php echo htmlspecialchars($order['id']); ?></p>
            <p><strong>Tanggal Pesanan:</strong> <?php echo date('d-m-Y', strtotime($order['order_date'])); ?></p>
            <p><strong>Status:</strong> <?php echo ucfirst(htmlspecialchars($order['status'])); ?></p>
            <p><strong>Metode Pembayaran:</strong> <?php echo ucfirst(htmlspecialchars($order['payment_method'])); ?></p>
        </div>

        <div class="bg-white shadow-md rounded-lg p-6 mb-6">
            <h2 class="text-xl font-bold mb-4">Detail Pengiriman</h2>
            <p><strong>Nama:</strong> <?php echo htmlspecialchars($order['username']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>
            <p><strong>Alamat:</strong> <?php echo htmlspecialchars($order['alamat']); ?></p>
            <p><strong>Nomor Telepon:</strong> <?php echo htmlspecialchars($order['telepon']); ?></p>
        </div>

        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-xl font-bold mb-4">Ringkasan Pesanan</h2>
            <table class="w-full mb-4">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-2">Produk</th>
                        <th class="text-right py-2">Harga</th>
                        <th class="text-right py-2">Jumlah</th>
                        <th class="text-right py-2">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order_items as $item) : ?>
                        <tr class="border-b">
                            <td class="py-2"><?php echo htmlspecialchars($item['nama']); ?></td>
                            <td class="text-right py-2">Rp <?php echo number_format($item['harga'], 0, ',', '.'); ?></td>
                            <td class="text-right py-2"><?php echo $item['quantity']; ?></td>
                            <td class="text-right py-2">Rp <?php echo number_format($item['harga'] * $item['quantity'], 0, ',', '.'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="py-2 font-bold">Total</td>
                        <td class="text-right py-2 font-bold">Rp <?php echo number_format($total, 0, ',', '.'); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="mt-6 text-center">
            <?php if ($order['status'] == 'pending') : ?>
                <button id="confirm-order" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded mr-4">
                    Konfirmasi Pesanan
                </button>
            <?php elseif ($order['status'] == 'processing') : ?>
                <p class="text-lg font-semibold text-green-600 mb-4">Pesanan Anda sedang diproses. Terima kasih!</p>
            <?php endif; ?>
            <a href="index.php" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                Kembali ke Beranda
            </a>
        </div>
    </div>

    <?php include '../components/footer.php' ?>
</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="../src/js/menu.js"></script>
<script>
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    const menuItems = mobileMenu.querySelectorAll('a');

    function toggleMenu() {
        mobileMenu.classList.toggle('hidden');
        if (!mobileMenu.classList.contains('hidden')) {
            mobileMenu.classList.remove('animate__fadeOutUp');
            mobileMenu.classList.add('animate__fadeInDown');
            setTimeout(() => {
                mobileMenu.classList.add('menu-enter-active');
            }, 10);
        } else {
            mobileMenu.classList.remove('animate__fadeInDown');
            mobileMenu.classList.add('animate__fadeOutUp');
            mobileMenu.classList.remove('menu-enter-active');
        }
    }

    mobileMenuButton.addEventListener('click', toggleMenu);

    menuItems.forEach(item => {
        item.addEventListener('click', (e) => {
            e.preventDefault(); // Prevent default link behavior
            toggleMenu(); // Close the menu
        });
    });
</script>
<script>
    $(document).ready(function() {
        $('#confirm-order').click(function() {
            $.ajax({
                url: '../api/order/confirm_order.php',
                method: 'POST',
                data: {
                    order_id: '<?php echo $order_id; ?>'
                },
                success: function(response) {
                    var result = JSON.parse(response);
                    if (result.success) {
                        alert('Pesanan berhasil dikonfirmasi!');
                        window.location.replace('../index.php');
                    } else {
                        alert('Terjadi kesalahan: ' + result.message);
                    }
                },
                error: function() {
                    alert('Terjadi kesalahan saat mengkonfirmasi pesanan.');
                }
            });
        });
    });
</script>


</html>