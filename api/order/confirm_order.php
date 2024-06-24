<?php
session_start();
include '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $order_id = $_POST['order_id'];

    // Periksa apakah pesanan milik user yang sedang login
    $stmt = $conn->prepare("SELECT id FROM orders WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ss", $order_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Pesanan tidak ditemukan']);
        exit;
    }

    // Update status pesanan menjadi 'processing'
    $stmt = $conn->prepare("UPDATE orders SET status = 'processing' WHERE id = ?");
    $stmt->bind_param("s", $order_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal mengupdate status pesanan']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
