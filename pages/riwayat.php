<?php
session_start();
include '../config/db.php';

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fungsi untuk membatalkan pesanan
if (isset($_POST['cancel_order'])) {
    $order_id = $_POST['order_id'];
    $stmt = $conn->prepare("UPDATE orders SET status = 'cancelled' WHERE id = ? AND user_id = ? AND status NOT IN ('shipped', 'delivered', 'cancelled')");
    $stmt->bind_param("ss", $order_id, $user_id);
    $stmt->execute();
    // Redirect untuk refresh halaman
    header("Location: riwayat.php");
    exit;
}

// Ambil semua pesanan user
$stmt = $conn->prepare("SELECT o.id, o.order_date, o.status, 
                        (SELECT SUM(oi.price * oi.quantity) FROM order_items oi WHERE oi.order_id = o.id) as total
                        FROM orders o 
                        WHERE o.user_id = ?
                        ORDER BY o.order_date DESC");
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);

// Kelompokkan pesanan berdasarkan status
$grouped_orders = [
    'pending' => [],
    'processing' => [],
    'shipped' => [],
    'delivered' => [],
    'cancelled' => []
];

foreach ($orders as $order) {
    $grouped_orders[$order['status']][] = $order;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pesanan - Olshop</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="bg-gray-100 font-sans">
    <?php include '../components/navbar.php' ?>

    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6">Riwayat Pesanan</h1>

        <div class="space-y-6">
            <?php
            $status_labels = [
                'pending' => 'Menunggu Pembayaran',
                'processing' => 'Diproses',
                'shipped' => 'Dikirim',
                'delivered' => 'Diterima',
                'cancelled' => 'Dibatalkan'
            ];

            foreach ($grouped_orders as $status => $orders) :
                if (empty($orders)) continue;
            ?>
                <div>
                    <h2 class="text-2xl font-semibold mb-4"><?php echo $status_labels[$status]; ?></h2>
                    <div class="space-y-4">
                        <?php foreach ($orders as $order) : ?>
                            <div class="bg-white shadow-md rounded-lg p-6">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-xl font-semibold">Order #<?php echo $order['id']; ?></h3>
                                    <span class="text-sm text-gray-500"><?php echo $order['order_date']; ?></span>
                                </div>
                                <p class="mb-2">Total: Rp <?php echo number_format($order['total'], 0, ',', '.'); ?></p>
                                <div class="flex justify-between items-center">
                                    <a href="invoice.php?order_id=<?php echo $order['id']; ?>" class="text-blue-500 hover:underline">Lihat Detail</a>
                                    <?php if ($status == 'pending' || $status == 'processing') : ?>
                                        <form method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?');">
                                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                            <button type="submit" name="cancel_order" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded">
                                                Batalkan Pesanan
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php include '../components/footer.php' ?>

</body>

</html>