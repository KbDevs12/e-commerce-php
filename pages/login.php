<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Olshop</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../src/css/app.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/zod@3.21.4/lib/index.umd.js"></script>
</head>

<body class="font-['Kanit'] bg-gray-100">
    <?php include_once '../components/navbar.php' ?>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-purple-500 to-blue-500 py-4 px-6">
                <h2 class="text-2xl font-bold text-white">Login to Your Account</h2>
            </div>
            <div class="p-6">
                <div id="message" class="hidden mb-4 p-4 rounded"></div>

                <form id="loginForm">
                    <div class="mb-4">
                        <label for="username" class="block text-gray-700 text-sm mb-2">Username</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                <i class="ph-user text-gray-400"></i>
                            </span>
                            <input type="text" id="username" name="username" class="w-full pl-10 pr-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500" required>
                        </div>
                    </div>
                    <div class="mb-6">
                        <label for="password" class="block text-gray-700 text-sm mb-2">Password</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                <i class="ph-lock text-gray-400"></i>
                            </span>
                            <input type="password" id="password" name="password" class="w-full pl-10 pr-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500" required>
                            <span class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer" id="togglePassword">
                                <i class="ph ph-eye text-gray-400"></i>
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <button type="submit" class="bg-gradient-to-r from-purple-500 to-blue-500 text-white py-2 px-4 rounded-lg hover:opacity-90 transition duration-300">
                            Login
                        </button>
                        <a href="register.php" class="inline-block align-baseline text-sm text-blue-500 hover:text-blue-800">
                            Don't have an account?
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include_once('../components/footer.php') ?>

    <script src="../src/js/menu.js"></script>
    <script>
        $(document).ready(function() {
            const {
                z
            } = Zod;

            const schema = z.object({
                username: z.string().min(3, {
                    message: "username harus minimal 3 karakter"
                }).max(50).trim(),
                password: z.string().min(6, {
                    message: "Password harus minimal 6 karakter"
                }).max(100, {
                    message: "Password maksimal 100 karakter"
                })
            });

            $('#loginForm').on('submit', function(e) {
                e.preventDefault();

                const formData = {
                    username: $('#username').val().trim(),
                    password: $('#password').val()
                };

                try {
                    schema.parse(formData);

                    $.ajax({
                        url: '../api/login.php',
                        type: 'POST',
                        data: formData,
                        dataType: 'json',
                        success: function(response) {
                            $('#message').removeClass('hidden');
                            if (response.success) {
                                $('#message').removeClass('bg-red-100 text-red-700').addClass('bg-green-100 text-green-700');
                                $('#message').text(response.message);
                                setTimeout(function() {
                                    window.location.href = '../index.php?status=berhasil_login';
                                }, 1500);
                            } else {
                                $('#message').removeClass('bg-green-100 text-green-700').addClass('bg-red-100 text-red-700');
                                $('#message').text(response.message);
                            }
                        },
                        error: function() {
                            $('#message').removeClass('hidden bg-green-100 text-green-700').addClass('bg-red-100 text-red-700');
                            $('#message').text('An error occurred. Please try again later.');
                        }
                    });
                } catch (error) {
                    $('#message').removeClass('hidden bg-green-100 text-green-700').addClass('bg-red-100 text-red-700');
                    $('#message').text(error.errors[0].message);
                }
            });

            $('#togglePassword').on('click', function() {
                const passwordField = $('#password');
                const passwordFieldType = passwordField.attr('type');
                passwordField.attr('type', passwordFieldType === 'password' ? 'text' : 'password');
                $(this).find('i').toggleClass('ph-eye ph-eye-slash');
            });
        });

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