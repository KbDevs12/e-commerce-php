<?php
session_start();
include '../../config/db.php';

if (isset($_POST['id']) && isset($_POST['quantity'])) {
    $id = $_POST['id'];
    $quantity = $_POST['quantity'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("UPDATE keranjang SET quantity = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("iss", $quantity, $id, $user_id);
    $stmt->execute();

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
