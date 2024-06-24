<?php
require_once '../../config/db.php';

header('Content-Type: application/json');

ini_set('upload_max_filesize', '1M');
ini_set('post_max_size', '1M');

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

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if (isset($_GET['produk_id'])) {
            $produk_id = $conn->real_escape_string($_GET['produk_id']);
            $result = $conn->query("SELECT * FROM gambar_produk WHERE produk_id = '$produk_id'");
            $images = [];
            while ($row = $result->fetch_assoc()) {
                $base64Image = base64_encode($row['gambar']);
                $images[] = [
                    'id' => $row['id'],
                    'produk_id' => $row['produk_id'],
                    'gambar' => $base64Image,
                ];
            }
            echo json_encode($images);
        } else {
            echo json_encode(['error' => 'Product ID is required']);
        }
        break;

    case 'POST':
        if (isset($_FILES['gambar'])) {

            if ($_FILES['gambar']['size'] > 1048576) { // 1MB = 1048576 bytes
                echo json_encode(['error' => 'File is too large. Maximum size is 1MB.']);
                exit;
            }

            $produk_id = $conn->real_escape_string($_POST['produk_id']);
            $image = file_get_contents($_FILES['gambar']['tmp_name']);
            $id = generateUUID();

            $info = getimagesize($_FILES['gambar']['tmp_name']);
            if ($info['mime'] == 'image/jpeg') {
                $image = imagecreatefromjpeg($_FILES['gambar']['tmp_name']);
            } elseif ($info['mime'] == 'image/png') {
                $image = imagecreatefrompng($_FILES['gambar']['tmp_name']);
            } else {
                echo json_encode(['error' => 'Invalid image format. Only JPEG and PNG are allowed.']);
                exit;
            }

            ob_start();

            imagejpeg($image, null, 75);

            $image_data = ob_get_contents();

            ob_end_clean();

            $stmt = $conn->prepare("INSERT INTO gambar_produk (id, produk_id, gambar) VALUES (?, ?, ?)");
            $null = null;
            $stmt->bind_param("ssb", $id, $produk_id, $null);
            $stmt->send_long_data(2, $image_data);

            if ($stmt->execute()) {
                echo json_encode(['message' => 'Image uploaded', 'id' => $id]);
            } else {
                echo json_encode(['error' => 'Failed to upload image']);
            }
            $stmt->close();
        } else {
            echo json_encode(['error' => 'No image file uploaded']);
        }
        break;

    case 'DELETE':
        $id = $conn->real_escape_string($_GET['id']);
        if ($conn->query("DELETE FROM gambar_produk WHERE id = '$id'")) {
            echo json_encode(['message' => 'Image deleted']);
        } else {
            echo json_encode(['error' => 'Failed to delete image']);
        }
        break;
}

$conn->close();
