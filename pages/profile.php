<?php
session_start();

// Periksa apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include '../config/db.php';

$user_id = $_SESSION['user_id'];

// Ambil data user
$stmt = $conn->prepare("SELECT u.id, u.username, u.email, u.image, du.alamat, du.telepon 
                        FROM users u 
                        LEFT JOIN detail_user du ON u.id = du.id_user 
                        WHERE u.id = ?");
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Proses form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $alamat = $_POST['alamat'];
    $telepon = $_POST['telepon'];

    // Update user data
    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
    $stmt->bind_param("sss", $username, $email, $user_id);
    $stmt->execute();

    // Update or insert detail_user
    $stmt = $conn->prepare("INSERT INTO detail_user (id_user, alamat, telepon) 
                            VALUES (?, ?, ?) 
                            ON DUPLICATE KEY UPDATE alamat = VALUES(alamat), telepon = VALUES(telepon)");
    $stmt->bind_param("sss", $user_id, $alamat, $telepon);
    $stmt->execute();

    // Handle image upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $image = file_get_contents($_FILES['profile_image']['tmp_name']);
        $stmt = $conn->prepare("UPDATE users SET image = ? WHERE id = ?");
        $stmt->bind_param("ss", $image, $user_id);
        $stmt->execute();
    }

    // Redirect to refresh the page and show updated data
    header('Location: profile.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pengguna - Olshop</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../src/css/app.css">
</head>

<body class="bg-gray-100 font-[kanit]">
    <?php include '../components/navbar.php' ?>

    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6">Profil Pengguna</h1>

        <div class="bg-white shadow-md rounded-lg p-6">
            <form action="profile.php" method="POST" enctype="multipart/form-data">

                <div class="mb-4">
                    <label for="profile_image" class="block text-gray-700 text-sm font-bold mb-2">Foto Profil</label>
                    <?php if (!empty($user['image'])) : ?>
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($user['image']); ?>" alt="Profile Image" class="w-32 h-32 object-cover rounded-full mb-2">
                    <?php endif; ?>
                    <input type="file" id="profile_image" name="profile_image" accept="image/*" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="mb-4">
                    <label for="username" class="block text-gray-700 text-sm font-bold mb-2">Username</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>

                <div class="mb-4">
                    <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>

                <div class="mb-4">
                    <label for="alamat" class="block text-gray-700 text-sm font-bold mb-2">Alamat</label>
                    <textarea id="alamat" name="alamat" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" rows="3"><?php echo htmlspecialchars($user['alamat']); ?></textarea>
                </div>

                <div class="mb-4">
                    <label for="telepon" class="block text-gray-700 text-sm font-bold mb-2">Nomor Telepon</label>
                    <input type="tel" id="telepon" name="telepon" value="<?php echo htmlspecialchars($user['telepon']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="flex items-center justify-between">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php include '../components/footer.php' ?>
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