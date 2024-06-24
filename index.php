<?php
session_start();

$id = isset($_SESSION['user_id']);

if ($id) {
    $ids = $_SESSION['user_id'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Olshop</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Rubik+Mono+One&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="src/css/app.css">
    <script src="https://unpkg.com/@phosphor-icons/web@2.1.1"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>

    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>


    <!-- Slick CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css" />
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css" />

</head>

<body class="font-['Kanit']">
    <?php include 'components/navbar.php' ?>

    <?php if (isset($_SESSION['user_id'])) : ?>
        <div id="cart-button" class="fixed bottom-4 right-4 bg-black text-white rounded-full px-4 p-3 shadow-lg cursor-pointer hover:bg-blue-600 transition-colors duration-300" style="z-index: 1000;">
            <a href="pages/keranjang.php"><i class="ph ph-shopping-cart text-2xl"></i></a>
            <?php require_once('config/db.php');

            $cart_sql = "SELECT SUM(quantity) as total_quantity FROM keranjang WHERE user_id = ? AND status= 'pending'";
            $cart_stmt = $conn->prepare($cart_sql);
            $cart_stmt->bind_param("s", $ids);
            $cart_stmt->execute();
            $cart_result = $cart_stmt->get_result();
            $cart_data = $cart_result->fetch_assoc();
            $total_quantity = $cart_data['total_quantity'] ?? 0;
            ?>
            <?php if ($total_quantity > 0) : ?>
                <span class="absolute top-0 right-0 bg-red-500 text-white rounded-full text-sm w-5 h-5 flex items-center justify-center">
                    <?= ($total_quantity > 99) ? '99+' : $total_quantity ?>
                </span>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="mt-2 text-center text-2xl">
        <p>Selamat Datang Di Olshop</p>
    </div>

    <div class="container mx-auto mt-4 justify-center content-center align-center">
        <div class="carousel">
            <div class="carousel-item"><img src="https://via.placeholder.com/800x400?text=Slide+1" alt="Slide 1"></div>
            <div class="carousel-item"><img src="https://via.placeholder.com/800x400?text=Slide+2" alt="Slide 2"></div>
            <div class="carousel-item"><img src="https://via.placeholder.com/800x400?text=Slide+3" alt="Slide 3"></div>
            <div class="carousel-item"><img src="https://via.placeholder.com/800x400?text=Slide+4" alt="Slide 4"></div>
        </div>
    </div>
    <!-- list produk -->
    <div class="flex justify-between px-8">
        <p class="font-semibold text-xl">List Produk</p>
        <a href="pages/produk" class="text-gray-400 hover:text-blue-500">lihat selengkapnya...</a>
    </div>
    <div id="list-produk" class="container mx-auto px-4 mt-8 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        <!-- di isi otomatis dari api -->
    </div>

    <?php include 'components/footer.php' ?>

    <!-- Slick JS -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
    <script src="src/js/menu.js"></script>
    <script src="src/js/index.js"></script>
    <script>
        $(document).ready(function() {

            $('.carousel').slick({
                dots: true,
                infinite: true,
                speed: 300,
                slidesToShow: 1,
                adaptiveHeight: true,
                slidesToScroll: 1,
                autoplay: true,
                autoplaySpeed: 2000,
                centerMode: true,
                centerPadding: '0px'
            });

            function formatRupiah(angka, prefix) {
                var number_string = angka.toString().replace(/[^,\d]/g, ''),
                    split = number_string.split(','),
                    sisa = split[0].length % 3,
                    rupiah = split[0].substr(0, sisa),
                    ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                if (ribuan) {
                    separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }

                rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
                return prefix === undefined ? rupiah : (rupiah ? 'Rp ' + rupiah : '');
            }

            function loadProducts() {
                $.ajax({
                    url: 'api/produk.php',
                    method: 'GET',
                    success: function(data) {

                        const shuffleArray = (array) => {
                            const shuffledArray = array.slice(); // Copy array
                            for (let i = shuffledArray.length - 1; i > 0; i--) {
                                const j = Math.floor(Math.random() * (i + 1));
                                [shuffledArray[i], shuffledArray[j]] = [shuffledArray[j], shuffledArray[i]];
                            }
                            return shuffledArray;
                        };

                        var produk = shuffleArray(data);
                        var randomProduct = produk.slice(0, 8);

                        var container = $('#list-produk');
                        container.empty();

                        randomProduct.forEach(function(product) {
                            var card = `
                                        <div class="bg-white rounded-lg shadow-2xl overflow-hidden p-4 my-4 transition duration-300 ease-in-out transform hover:scale-105 cursor-pointer">
                                            <a id="product-card" class="relative" href="pages/detail_produk.php?id=${product.produk_id}">
                                                <div class="mb-4 rounded-lg overflow-hidden">
                                                    <div class="p-4 shadow-xl rounded-md shadow">
                                                    <img src="data:image/jpeg;base64,${product.gambar_produk}" alt="${product.produk_nama}" class="w-full h-48 object-cover">
                                                    </div>
                                                </div>
                                            </a>
                                                <div class="p-4">
                                                    <h3 class=" text-lg mb-2">${product.produk_nama}</h3>
                                                    <div class="flex justify-between">
                                                    <p class="text-black">${formatRupiah(product.produk_harga, 'Rp ')}</p>
                                                    <a href="pages/detail_produk.php?id=${product.produk_id}" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-full text-sm transition duration-300 ease-in-out transform hover:scale-105">
                                                Detail
                                            </a>
                                                    </div>
                                                </div>
                                        </div>`;
                            container.append(card);
                        });

                        $('#product-card').on('click', function() {
                            $(this).addClass('animate-click');
                            setTimeout(() => {
                                $(this).removeClass('animate-click');
                            }, 300);
                        });
                    },
                    error: function(error) {
                        console.error('Error: ', error)
                    }
                });
            }
            loadProducts();
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