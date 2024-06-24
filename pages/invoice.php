<?php
session_start();
include '../config/db.php';

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$order_id = $_GET['order_id'] ?? null;

if (!$order_id) {
    echo "Order ID tidak valid";
    exit;
}

// Ambil detail order
$stmt = $conn->prepare("SELECT o.id, o.order_date, o.status, u.username, u.email 
                        FROM orders o 
                        JOIN users u ON o.user_id = u.id 
                        WHERE o.id = ?");
$stmt->bind_param("s", $order_id);
$stmt->execute();
$order_result = $stmt->get_result();
$order = $order_result->fetch_assoc();

if (!$order) {
    echo "Order tidak ditemukan";
    exit;
}

// Ambil item-item dalam order
$stmt = $conn->prepare("SELECT oi.quantity, oi.price, p.nama 
                        FROM order_items oi 
                        JOIN produk p ON oi.product_id = p.id 
                        WHERE oi.order_id = ?");
$stmt->bind_param("s", $order_id);
$stmt->execute();
$items_result = $stmt->get_result();
$items = $items_result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - Order #<?php echo $order_id; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../src/css/app.css">
</head>

<body class="bg-gray-100 font-[kanit]">
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white shadow-md rounded-lg p-6">
            <h1 class="text-2xl font-bold mb-4">Invoice - Order #<?php echo $order_id; ?></h1>

            <div class="mb-4">
                <p><strong>Tanggal Order:</strong> <?php echo $order['order_date']; ?></p>
                <p><strong>Status:</strong> <?php echo ucfirst($order['status']); ?></p>
                <p><strong>Nama:</strong> <?php echo $order['username']; ?></p>
                <p><strong>Email:</strong> <?php echo $order['email']; ?></p>
            </div>

            <table class="w-full mb-4">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="p-2 text-left">Produk</th>
                        <th class="p-2 text-right">Harga</th>
                        <th class="p-2 text-right">Jumlah</th>
                        <th class="p-2 text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total = 0;
                    foreach ($items as $item) :
                        $item_total = $item['price'] * $item['quantity'];
                        $total += $item_total;
                    ?>
                        <tr>
                            <td class="p-2"><?php echo $item['nama']; ?></td>
                            <td class="p-2 text-right">Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></td>
                            <td class="p-2 text-right"><?php echo $item['quantity']; ?></td>
                            <td class="p-2 text-right">Rp <?php echo number_format($item_total, 0, ',', '.'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="font-bold">
                        <td colspan="3" class="p-2 text-right">Total:</td>
                        <td class="p-2 text-right">Rp <?php echo number_format($total, 0, ',', '.'); ?></td>
                    </tr>
                </tfoot>
            </table>

            <div class="text-center">
                <button onclick="window.print()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Cetak Invoice
                </button>
            </div>
        </div>
    </div>
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
</body>

</html>