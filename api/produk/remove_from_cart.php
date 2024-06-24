<?php
session_start();
include '../../config/db.php';

if (isset($_POST['id'])) {
    $id = $_POST['id'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("DELETE FROM keranjang WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ss", $id, $user_id);
    $stmt->execute();

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
