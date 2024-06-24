<?php
session_start();

include '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
}

function generateUUID()
{
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff)
    );
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uuid = generateUUID();
    $product_id = $_POST['product_id'] ?? null;
    $quantity = $_POST['quantity'] ?? 1;
    $user_id = $_SESSION['user_id']; // Assuming you have user authentication

    if ($product_id && $user_id) {
        // Check if the item is already in the cart
        $stmt = $conn->prepare("SELECT * FROM keranjang WHERE user_id = ? AND produk_id = ?");
        $stmt->bind_param("ss", $user_id, $product_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Update quantity if the item is already in the cart
            $stmt = $conn->prepare("UPDATE keranjang SET quantity = quantity + ? WHERE user_id = ? AND produk_id = ?");
            $stmt->bind_param("iss", $quantity, $user_id, $product_id);
        } else {
            // Add new item to the keranjang
            $stmt = $conn->prepare("INSERT INTO keranjang (id, user_id, produk_id, quantity) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sssi", $uuid, $user_id, $product_id, $quantity);
        }

        if ($stmt->execute()) {
            $response['success'] = true;
            $_SESSION['cart'] + 1;
        }
    }
}

echo json_encode($response);
