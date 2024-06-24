<?php
$url_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$current_page = basename($url_path, ".php");
$is_in_pages = strpos($url_path, '/ecoms/pages/') !== false;
?>

<nav class="bg-blue-500 p-4">
    <div class="container mx-auto flex justify-between items-center">
        <div class="text-white font-bold text-xl">Logo</div>

        <div class="hidden md:flex space-x-4">
            <a href="<?php echo $is_in_pages ? '../index.php' : 'index.php'; ?>" class="text-white hover:text-gray-300 <?php echo $current_page == 'index' ? 'active' : ''; ?>">Home</a>
            <a href="<?php echo $is_in_pages ? 'produk.php' : 'pages/produk.php'; ?>" class="text-white hover:text-gray-300 <?php echo $current_page == 'produk' ? 'active' : ''; ?>">Produk</a>
            <a href="<?php echo $is_in_pages ? 'about.php' : 'pages/about.php'; ?>" class="text-white hover:text-gray-300 <?php echo $current_page == 'about' ? 'active' : ''; ?>">Tentang</a>
            <a href="<?php echo $is_in_pages ? 'services.php' : 'pages/services.php'; ?>" class="text-white hover:text-gray-300 <?php echo $current_page == 'services' ? 'active' : ''; ?>">Layanan</a>
        </div>

        <div class="hidden md:block">
            <form id="searchForm" action="<?php echo $is_in_pages ? 'search_result.php' : 'pages/search_result.php'; ?>" method="GET">
                <input type="text" name="keyword" placeholder="Search..." class="px-3 py-1 rounded-md" required />
                <button type="submit" class="ml-2 bg-white text-blue-500 px-3 py-1 rounded-md">Search</button>
            </form>
        </div>

        <div class="flex items-center space-x-4">
            <?php if (isset($_SESSION['user_id'])) : ?>
                <div class="relative">
                    <button id="user-menu-button" class="text-white">
                        <i class="ph ph-user-circle text-4xl"></i>
                    </button>
                    <div id="user-menu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 animate__animated">
                        <a href="<?php echo $is_in_pages ? 'profile.php' : 'pages/profile.php'; ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                        <?php if ($_SESSION['role'] == 'admin') : ?>
                            <a href="<?php echo $is_in_pages ? 'admin.php' : 'pages/admin.php'; ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Admin Menu</a>
                        <?php endif; ?>
                        <a href="<?php echo $is_in_pages ? '../api/logout.php' : 'api/logout.php'; ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</a>
                    </div>
                </div>
            <?php else : ?>
                <a href="<?php echo $is_in_pages ? 'login.php' : 'pages/login.php'; ?>" class="text-blue-500 px-4 font-semibold rounded-full py-2 bg-white hover:text-gray-300">Login</a>
            <?php endif; ?>
        </div>

        <button id="mobile-menu-button" class="md:hidden text-white">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>

    <div id="mobile-menu" class="hidden md:hidden mt-4 animate__animated">
        <a href="<?php echo $is_in_pages ? '../index.php' : 'index.php'; ?>" class="block text-white py-2 px-4 hover:bg-gray-700 <?php echo $current_page == 'index' ? 'active' : ''; ?>">Home</a>
        <a href="<?php echo $is_in_pages ? 'produk.php' : 'pages/produk.php'; ?>" class="block text-white py-2 px-4 hover:bg-gray-700 <?php echo $current_page == 'produk' ? 'active' : ''; ?>">Produk</a>
        <a href="<?php echo $is_in_pages ? 'about.php' : 'pages/about.php'; ?>" class="block text-white py-2 px-4 hover:bg-gray-700 <?php echo $current_page == 'about' ? 'active' : ''; ?>">Tentang</a>
        <a href="<?php echo $is_in_pages ? 'services.php' : 'pages/services.php'; ?>" class="block text-white py-2 px-4 hover:bg-gray-700 <?php echo $current_page == 'services' ? 'active' : ''; ?>">Layanan</a>
        <div class="p-4">
            <form id="MsearchForm" action="<?php echo $is_in_pages ? 'search_result.php' : 'pages/search_result.php'; ?>" method="GET">
                <input type="text" name="keyword" placeholder="Search..." class="px-3 py-1 rounded-md" required />
                <button type="submit" class="ml-2 bg-white text-blue-500 px-3 py-1 rounded-md">Search</button>
            </form>
        </div>
    </div>
</nav>