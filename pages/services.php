<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Services - Olshop</title>

    <!-- Include the same CSS and JS files as in your main page -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../src/css/app.css">
    <!-- Add other necessary stylesheets and scripts -->
</head>
<body class="font-['Kanit']">
    <?php include_once '../components/navbar.php'?>

    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-4">Our Services</h1>
        <ul class="list-disc pl-5 mb-4">
            <li>Wide range of products</li>
            <li>Fast and reliable shipping</li>
            <li>24/7 customer support</li>
            <li>Easy returns and exchanges</li>
        </ul>
        <!-- Add more details about your services -->
    </div>

    <?php include_once '../components/footer.php'?>

    <!-- Include your JavaScript files -->
    <script src="../src/js/menu.js"></script>
    <script>
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        const menuItems = mobileMenu.querySelectorAll('a');

        function toggleMenu() {
            mobileMenu.classList.toggle('hidden');
            if (!mobileMenu.classList.contains('hidden')) {
                mobileMenu.classList.remove('animate__fadeOutUp');
                mobileMenu.classList.add('animate__fadeInDown');
                setTimeout(() => {
                    mobileMenu.classList.add('menu-enter-active');
                }, 10);
            } else {
                mobileMenu.classList.remove('animate__fadeInDown');
                mobileMenu.classList.add('animate__fadeOutUp');
                mobileMenu.classList.remove('menu-enter-active');
            }
        }

        mobileMenuButton.addEventListener('click', toggleMenu);

        menuItems.forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault(); // Prevent default link behavior
                toggleMenu(); // Close the menu
            });
        });
    </script>
</body>
</html>