<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk - Olshop</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web@2.1.1"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="font-['Kanit'] bg-gray-100">
    <?php include('../components/navbar.php') ?>

    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-8 text-center">Semua Produk</h1>

        <div id="product-list" class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <!-- Products will be loaded here -->
        </div>

        <div id="pagination" class="mt-8 flex justify-center space-x-2">
            <!-- Pagination buttons will be added here -->
        </div>
    </div>

    <?php include('../components/footer.php') ?>

    <script src="../src/js/menu.js"></script>
    <script>
        $(document).ready(function() {
            let currentPage = 1;
            const productsPerPage = 8;
            let totalProducts = 0;

            function loadProducts(page) {
                $.ajax({
                    url: `../api/total_produk.php?page=${page}&limit=${productsPerPage}`,
                    method: 'GET',
                    success: function(response) {
                        var container = $('#product-list');
                        container.empty();

                        response.products.forEach(function(product) {
                            var card = `
                                <div class="bg-white rounded-lg shadow-lg overflow-hidden transition duration-300 ease-in-out transform hover:scale-105">
                                    <div class="relative">
                                        <img src="data:image/jpeg;base64,${product.gambar_produk}" alt="${product.produk_nama}" class="w-full h-48 object-cover">
                                        <div class="absolute top-0 right-0 ${product.produk_stock > 0 ? 'bg-green-500' : 'bg-red-500'} text-white px-2 py-1 m-2 rounded-full text-xs font-bold">
                                            ${product.produk_stock > 0 ? 'Tersedia' : 'Habis'}
                                        </div>
                                    </div>
                                    <div class="p-4">
                                        <h3 class="font-semibold text-lg mb-2 truncate">${product.produk_nama}</h3>
                                        <p class="text-gray-600 text-sm mb-2 truncate">${product.produk_deskripsi}</p>
                                        <div class="flex justify-between items-center">
                                            <p class="text-purple-600 font-bold">Rp ${product.produk_harga}</p>
                                            <a href="detail_produk.php?id=${product.produk_id}" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-full text-sm transition duration-300 ease-in-out transform hover:scale-105">
                                                Detail
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            `;
                            container.append(card);
                        });

                        totalProducts = response.total;
                        updatePagination();
                    },
                    error: function(error) {
                        console.error('Error: ', error);
                        showToast('Gagal memuat produk', 'error');
                    }
                });
            }

            function updatePagination() {
                const totalPages = Math.ceil(totalProducts / productsPerPage);
                let paginationHtml = '';

                for (let i = 1; i <= totalPages; i++) {
                    paginationHtml += `
                        <button class="pagination-button ${i === currentPage ? 'bg-blue-500 text-white' : 'bg-white text-blue-500'} border border-blue-500 px-3 py-1 rounded-md" data-page="${i}">
                            ${i}
                        </button>
                    `;
                }

                $('#pagination').html(paginationHtml);

                $('.pagination-button').click(function() {
                    currentPage = parseInt($(this).data('page'));
                    loadProducts(currentPage);
                });
            }

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

            loadProducts(currentPage);
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