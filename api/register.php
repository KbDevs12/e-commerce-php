<?php
require_once '../config/db.php';

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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uuid = generateUUID();
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password =  md5($_POST['password']);;
    
    
    // Default image URL
    $defaultImageUrl = "https://pizza-bucketss12.s3.amazonaws.com/2n4luiyezbh.png";
    
    // Fetch image content
    $imageContent = file_get_contents($defaultImageUrl);
    
    if ($imageContent === false) {
        echo json_encode(["success" => false, "message" => "Failed to fetch default image"]);
        exit;
    }

    if(strcmp($username, 'Admin')) {
        echo json_encode(["success" => false, "message" => "Jangan ngide jadi admin ya!"]);
        exit;
    }

    // Check if username or email already exists
    $check_sql = "SELECT * FROM users WHERE username = ? OR email = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ss", $username, $email);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "Username or email already in use"]);
    } else {
        $sql = "INSERT INTO users (id, username, email, password, image) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $uuid, $username, $email, $password, $imageContent);
        
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Registration successful"]);
        } else {
            echo json_encode(["success" => false, "message" => "Registration failed"]);
        }
        
        $stmt->close();
    }
    
    $check_stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
}

$conn->close();
?>