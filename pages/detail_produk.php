<?php
session_start();

$product_id = $_GET['id'] ?? null;
if (!$product_id) {
    header('Location: produk.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Produk - Olshop</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web@2.1.1"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Slick CSS and JS -->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css" />
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css" />
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
</head>

<body class="font-['Kanit'] bg-gray-100">
    <?php include '../components/navbar.php' ?>
    <div class="container mx-auto px-4 py-8">
        <div id="product-detail" class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Product details will be loaded here -->
        </div>
    </div>
    <?php include '../components/footer.php' ?>
    <script src="../src/js/menu.js"></script>
    <script>
        $(document).ready(function() {
            const productId = <?php echo json_encode($product_id); ?>;

            function loadProductDetail() {
                $.ajax({
                    url: "../api/detail_produk.php?id=" + productId,
                    method: 'GET',
                    success: function(response) {
                        if (response.products.length > 0) {
                            const product = response.products[0];
                            let carouselHtml = '';
                            const allres = response.products;

                            const imageCount = allres.length;

                            if (imageCount > 1) {
                                allres.forEach((produk, index) => {
                                    carouselHtml += `<div><img src="data:image/jpeg;base64,${produk.gambar_produk}" alt="${produk.produk_nama}" class="w-full h-auto object-cover"></div>`;
                                });
                            } else {
                                carouselHtml = `<div><img src="data:image/jpeg;base64,${product.gambar_produk}" alt="${product.produk_nama}" class="w-full h-auto object-cover"></div>`;
                            }
                            const detailHtml = `
                    <div class="md:flex">
                        <div class="md:w-1/2">
                            <div class="slick-carousel">${carouselHtml}</div>
                        </div>
                        <div class="md:w-1/2 p-6">
                            <h1 class="text-3xl font-bold mb-4">${product.produk_nama}</h1>
                            <p class="text-gray-600 mb-4">${product.produk_deskripsi}</p>
                            <div class="flex items-center mb-4">
                                <span class="text-2xl font-bold text-purple-600 mr-2">Rp ${product.produk_harga}</span>
                                <span class="${product.produk_stock > 0 ? 'bg-green-500' : 'bg-red-500'} text-white px-2 py-1 rounded-full text-xs">
                                    ${product.produk_stock > 0 ? 'Tersedia' : 'Habis'}
                                </span>
                            </div>
                            <p class="mb-4">Stok: ${product.produk_stock}</p>
                            <div class="flex gap-4">
                            <button id="add-to-cart" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-full text-lg transition duration-300 ease-in-out transform hover:scale-105">
                                <i class="ph ph-shopping-cart-simple mr-2"></i>Tambah ke Keranjang
                            </button>
                            <a id="" href="checkout.php" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-full text-lg transition duration-300 ease-in-out transform hover:scale-105">Beli Sekarang</a>
                            </div>
                            </div>
                    </div>`;
                            $('#product-detail').html(detailHtml);
                            if (imageCount > 1) {
                                $('.slick-carousel').slick({
                                    dots: true,
                                    infinite: true,
                                    speed: 300,
                                    slidesToShow: 1,
                                    adaptiveHeight: true
                                });
                            }
                        } else {
                            showToast('Produk tidak ditemukan', 'error');
                        }

                        $('#add-to-cart').click(function() {
                            addToCart(productId, 1); // Gunakan productId yang sudah didefinisikan
                        });

                        $('#buy-now').click(function() {
                            buyNow(productId, 1); // Gunakan productId yang sudah didefinisikan
                        });
                    },
                    error: function(error) {
                        console.error('Error: ', error);
                        showToast('Gagal memuat detail produk', 'error');
                    }
                });
            }

            function addToCart(productId, quantity) {
                $.ajax({
                    url: "../api/add_to_cart.php",
                    method: 'POST',
                    data: {
                        product_id: productId,
                        quantity: quantity
                    },
                    success: function(response) {
                        showToast('Produk ditambahkan ke keranjang');
                    },
                    error: function(error) {
                        console.log('Error: ', error);
                        showToast('Gagal menambahkan produk ke keranjang', 'error');
                    }
                });
            }

            function buyNow(productId, quantity) {
                $.ajax({
                    url: "../api/create_order.php",
                    method: 'POST',
                    data: {
                        product_id: productId,
                        quantity: quantity
                    },
                    success: function(response) {
                        if (response.success) {
                            showToast('Pesanan berhasil dibuat');
                            // Redirect to order confirmation page
                            window.location.href = 'order_confirmation.php?order_id=' + response.order_id;
                        } else {
                            showToast('Gagal membuat pesanan', 'error');
                        }
                    },
                    error: function(error) {
                        console.error('Error: ', error);
                        showToast('Gagal membuat pesanan', 'error');
                    }
                })
            };

            function showToast(message, type = 'success') {
                Toastify({
                    text: message,
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: type === 'success' ? "#4CAF50" : "#F44336",
                }).showToast();
            }

            loadProductDetail();
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