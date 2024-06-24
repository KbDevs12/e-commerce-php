<?php
session_start();

// Check if the user is logged in and has an admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Olshop</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web@2.1.1"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css" />
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
</head>

<body class="font-['Kanit'] bg-gray-100">
    <nav class="bg-purple-600 text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-semibold">Olshop Admin</h1>
            <a href="../index.php" class="hover:text-gray-200">Back to Store</a>
        </div>
    </nav>

    <div class="container mx-auto mt-8">
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-2xl font-semibold mb-4">Add New Product</h2>
            <form id="addProductForm" class="space-y-4">
                <div>
                    <label for="name" class="block mb-1">Product Name</label>
                    <input type="text" id="name" name="nama" required class="w-full p-2 border rounded">
                </div>
                <div>
                    <label for="price" class="block mb-1">Price</label>
                    <input type="number" id="price" name="harga" required class="w-full p-2 border rounded">
                </div>
                <div>
                    <label for="description" class="block mb-1">Description</label>
                    <textarea id="description" name="deskripsi" required class="w-full p-2 border rounded"></textarea>
                </div>
                <div>
                    <label for="stock" class="block mb-1">Stock</label>
                    <input type="number" id="stock" name="stock" required class="w-full p-2 border rounded">
                </div>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Add Product</button>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-2xl font-semibold mb-4">Upload Product Image</h2>
            <form id="uploadImageForm" class="space-y-4">
                <div>
                    <label for="productId" class="block mb-1">Product ID</label>
                    <input type="text" id="productId" name="produk_id" required class="w-full p-2 border rounded">
                </div>
                <div>
                    <label for="image" class="block mb-1">Image (Max 1MB)</label>
                    <input type="file" id="image" name="gambar" required class="w-full p-2 border rounded" accept="image/*">
                </div>
                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Upload Image</button>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-semibold mb-4">Manage Products</h2>
            <div class="mb-4">
                <input type="text" id="searchProduct" placeholder="Search products..." class="w-full p-2 border rounded">
            </div>
            <div id="productList" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Products will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Modal for editing product -->
    <div id="editProductModal" class="fixed z-10 inset-0 overflow-y-auto hidden">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="editProductForm">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Edit Product</h3>
                        <input type="hidden" id="editProductId">
                        <div class="mb-4">
                            <label for="editName" class="block mb-1">Product Name</label>
                            <input type="text" id="editName" required class="w-full p-2 border rounded">
                        </div>
                        <div class="mb-4">
                            <label for="editPrice" class="block mb-1">Price</label>
                            <input type="number" id="editPrice" required class="w-full p-2 border rounded">
                        </div>
                        <div class="mb-4">
                            <label for="editDescription" class="block mb-1">Description</label>
                            <textarea id="editDescription" required class="w-full p-2 border rounded"></textarea>
                        </div>
                        <div class="mb-4">
                            <label for="editStock" class="block mb-1">Stock</label>
                            <input type="number" id="editStock" required class="w-full p-2 border rounded">
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Save Changes
                        </button>
                        <button type="button" id="cancelEditProduct" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 mb-8 my-20 mx-4">
        <h2 class="text-2xl font-semibold mb-4">Manage Orders</h2>
        <div class="mb-4">
            <input type="text" id="searchOrder" placeholder="Search orders..." class="w-full p-2 border rounded">
        </div>
        <div id="orderList" class="overflow-x-auto">
            <table class="w-full table-auto">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="px-4 py-2">Order ID</th>
                        <th class="px-4 py-2">User ID</th>
                        <th class="px-4 py-2">Order Date</th>
                        <th class="px-4 py-2">Status</th>
                        <th class="px-4 py-2">Payment Method</th>
                        <th class="px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody id="orderTableBody">
                    <!-- Orders will be loaded here -->
                </tbody>
            </table>
        </div>
    </div>
    </div>

    <div id="updateOrderStatusModal" class="fixed z-10 inset-0 overflow-y-auto hidden">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="updateOrderStatusForm">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Update Order Status</h3>
                        <input type="hidden" id="updateOrderId">
                        <div class="mb-4">
                            <label for="updateOrderStatus" class="block mb-1">New Status</label>
                            <select id="updateOrderStatus" required class="w-full p-2 border rounded">
                                <option value="pending">Pending</option>
                                <option value="processing">Processing</option>
                                <option value="shipped">Shipped</option>
                                <option value="delivered">Delivered</option>
                            </select>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Update Status
                        </button>
                        <button type="button" id="cancelUpdateOrderStatus" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <!-- Modal for image deletion confirmation -->
    <div id="deleteImageModal" class="fixed z-10 inset-0 overflow-y-auto hidden">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                        Delete Image
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500">
                            Are you sure you want to delete this image? This action cannot be undone.
                        </p>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" id="confirmDeleteImage" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Delete
                    </button>
                    <button type="button" id="cancelDeleteImage" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for product deletion confirmation -->
    <div id="deleteProductModal" class="fixed z-10 inset-0 overflow-y-auto hidden">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                        Delete Product
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500">
                            Are you sure you want to delete this product? This action cannot be undone.
                        </p>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" id="confirmDeleteProduct" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Delete
                    </button>
                    <button type="button" id="cancelDeleteProduct" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script src="../src/js/admin.js"></script>
    <script>
        // ... (previous code) ...

        // Function to load orders
        function loadOrders() {
            $.ajax({
                url: '../api/admin/get_orders.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        displayOrders(response.orders);
                    } else {
                        showToast(response.message, 'error');
                    }
                },
                error: function() {
                    showToast('Error loading orders', 'error');
                }
            });
        }

        // Function to display orders
        function displayOrders(orders) {
            const orderTableBody = $('#orderTableBody');
            orderTableBody.empty();

            orders.forEach(order => {
                const row = `
            <tr>
                <td class="border px-4 py-2">${order.id}</td>
                <td class="border px-4 py-2">${order.user_id}</td>
                <td class="border px-4 py-2">${order.order_date}</td>
                <td class="border px-4 py-2">${order.status}</td>
                <td class="border px-4 py-2">${order.payment_method}</td>
                <td class="border px-4 py-2">
                    <button class="bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600 updateOrderStatus" data-id="${order.id}">Update Status</button>
                </td>
            </tr>
        `;
                orderTableBody.append(row);
            });
        }

        // Event listener for updating order status
        $(document).on('click', '.updateOrderStatus', function() {
            const orderId = $(this).data('id');
            $('#updateOrderId').val(orderId);
            $('#updateOrderStatusModal').removeClass('hidden');
        });

        // Event listener for canceling order status update
        $('#cancelUpdateOrderStatus').on('click', function() {
            $('#updateOrderStatusModal').addClass('hidden');
        });

        // Event listener for submitting order status update
        $('#updateOrderStatusForm').on('submit', function(e) {
            e.preventDefault();
            const orderId = $('#updateOrderId').val();
            const newStatus = $('#updateOrderStatus').val();

            $.ajax({
                url: '../api/admin/update_order_status.php',
                type: 'POST',
                data: {
                    order_id: orderId,
                    status: newStatus
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        showToast('Order status updated successfully', 'success');
                        $('#updateOrderStatusModal').addClass('hidden');
                        loadOrders();
                    } else {
                        showToast(response.message, 'error');
                    }
                },
                error: function() {
                    showToast('Error updating order status', 'error');
                }
            });
        });

        // Search functionality for orders
        $('#searchOrder').on('input', function() {
            const searchTerm = $(this).val().toLowerCase();
            $('#orderTableBody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(searchTerm) > -1);
            });
        });

        // Load orders when the page loads
        $(document).ready(function() {
            loadOrders();
            // ... (other document ready functions) ...
        });

        // ... (previous code) ...
    </script>
</body>

</html>