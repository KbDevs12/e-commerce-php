<?php
require_once '../../config/db.php';

header('Content-Type: application/json');


function generateUUID() {
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if (isset($_GET['id'])) {
            $id = $conn->real_escape_string($_GET['id']);
            $result = $conn->query("SELECT * FROM produk WHERE id = '$id'");
            $product = $result->fetch_assoc();
            echo json_encode($product);
        } else {
            $result = $conn->query("SELECT * FROM produk");
            $products = [];
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
            echo json_encode($products);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        $id = generateUUID();
        $nama = $conn->real_escape_string($data['nama']);
        $deskripsi = $conn->real_escape_string($data['deskripsi']);
        $stock = $conn->real_escape_string($data['stock']);
        $harga = floatval($data['harga']);
        
        $stmt = $conn->prepare("INSERT INTO produk (id, nama, deskripsi, stock, harga, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("sssid", $id, $nama, $deskripsi, $stock, $harga);
        
        if ($stmt->execute()) {
            echo json_encode(['message' => 'Product created', 'id' => $id]);
        } else {
            echo json_encode(['error' => 'Failed to create product']);
        }
        $stmt->close();
        break;

    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $conn->real_escape_string($data['id']);
        $nama = $conn->real_escape_string($data['nama']);
        $deskripsi = $conn->real_escape_string($data['deskripsi']);
        $stock = $conn->real_escape_string($data['stock']);
        $harga = floatval($data['harga']);
        
        $stmt = $conn->prepare("UPDATE produk SET nama = ?, deskripsi = ?, stock= ?, harga = ? WHERE id = ?");

        $stmt->bind_param("ssids", $nama, $deskripsi, $stock, $harga, $id);
        
        if ($stmt->execute()) {
            echo json_encode(['message' => 'Product updated']);
        } else {
            echo json_encode(['error' => 'Failed to update product']);
        }
        $stmt->close();
        break;

    case 'DELETE':
        $id = $conn->real_escape_string($_GET['id']);
        if ($conn->query("DELETE FROM produk WHERE id = '$id'")) {
            echo json_encode(['message' => 'Product deleted']);
        } else {
            echo json_encode(['error' => 'Failed to delete product']);
        }
        break;
}

$conn->close();