<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Olshop</title>
    
    <!-- Include the same CSS and JS files as in your main page -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../src/css/app.css">
    <!-- Add other necessary stylesheets and scripts -->
</head>
<body class="font-['Kanit']">
    <?php include_once '../components/navbar.php' ?>

    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-4">About Us</h1>
        <p class="mb-4">Welcome to Olshop, your one-stop destination for all your shopping needs.</p>
        <p class="mb-4">We are committed to providing high-quality products and excellent customer service.</p>
        <!-- Add more content about your company, history, mission, etc. -->
    </div>

    <?php include_once('../components/footer.php')?>

    <!-- Include your JavaScript files -->
    <script src="../src/js/menu.js"></script>
</body>
</html>