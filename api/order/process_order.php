<?php
session_start();
include '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $payment_method = $_POST['payment_method'];

    // Start transaction
    $conn->begin_transaction();

    try {
        // Generate unique order ID
        $order_id = uniqid();

        // Insert order into orders table
        $stmt = $conn->prepare("INSERT INTO orders (id, user_id, order_date, status, payment_method) VALUES (?, ?, CURDATE(), 'pending', ?)");
        $stmt->bind_param("sss", $order_id, $user_id, $payment_method);
        $stmt->execute();

        // Get cart items
        $stmt = $conn->prepare("SELECT c.produk_id, c.quantity, p.harga FROM keranjang c JOIN produk p ON c.produk_id = p.id WHERE c.user_id = ? AND c.status = 'pending'");
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $cart_items = $result->fetch_all(MYSQLI_ASSOC);

        // Insert order items
        $stmt = $conn->prepare("INSERT INTO order_items (id, order_id, product_id, quantity, price) VALUES (?, ?, ?, ?, ?)");
        foreach ($cart_items as $item) {
            $item_id = uniqid();
            $stmt->bind_param("sssid", $item_id, $order_id, $item['produk_id'], $item['quantity'], $item['harga']);
            $stmt->execute();
        }

        // Update cart status to 'checkout'
        $stmt = $conn->prepare("UPDATE keranjang SET status = 'checkout' WHERE user_id = ? AND status = 'pending'");
        $stmt->bind_param("s", $user_id);
        $stmt->execute();

        // Commit transaction
        $conn->commit();

        echo json_encode(['success' => true, 'order_id' => $order_id]);
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
