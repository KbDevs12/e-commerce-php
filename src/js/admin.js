$(document).ready(function () {
  let allProducts = [];

  function showToast(message, type = "success") {
    Toastify({
      text: message,
      duration: 3000,
      close: true,
      gravity: "top",
      position: "right",
      backgroundColor: type === "success" ? "#4CAF50" : "#F44336",
    }).showToast();
  }

  function loadProducts() {
    $.get("../api/admin/produk.php", function (products) {
      allProducts = products; // Store all products
      renderProducts(products);
    });
  }

  function renderProducts(products) {
    let html = "";
    products.forEach(function (product) {
      html += `
        <div class="border rounded-lg p-4 flex flex-col justify-between">
            <div class="product-images group/item" data-product-id="${product.id}">
                <!-- Images will be loaded here -->
            </div>
            <div>
                <h3 class="text-lg font-semibold">${product.nama}</h3>
                <p class="text-gray-600">ID: ${product.id}</p>
                <p class="text-gray-600">$${product.harga}</p>
                <p>${product.deskripsi}</p>
                <p>Stock: ${product.stock}</p>
            </div>
            <div class="mt-4 space-y-2">
                <button class="editProduct bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600 w-full" data-id="${product.id}">Edit</button>
                <button class="deleteProduct bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 w-full" data-id="${product.id}">Delete</button>
            </div>
        </div>
    `;
    });
    $("#productList").html(html);

    // Load images for each product
    products.forEach(function (product) {
      loadProductImages(product.id);
    });
  }

  $("#searchProduct").on("input", function () {
    let searchTerm = $(this).val().trim().toLowerCase();
    if (searchTerm.length >= 3) {
      let filteredProducts = allProducts.filter(function (product) {
        return product.nama.toLowerCase().includes(searchTerm);
      });
      renderProducts(filteredProducts);
    } else if (searchTerm.length === 0) {
      renderProducts(allProducts);
    }
  });

  function loadProductImages(productId) {
    $.get(
      `../api/admin/gambar_produk.php?produk_id=${productId}`,
      function (images) {
        let imageHtml = "";
        images.forEach(function (image) {
          imageHtml += `
                <div class="relative group/image">
                    <img src="data:image/jpeg;base64,${image.gambar}" alt="Product Image" class="w-full h-48 object-cover">
                    <button class="deleteImage absolute top-2 right-2 bg-red-500 text-white p-2 rounded-full invisible group-hover/image:visible" data-image-id="${image.id}">
                        <i class="ph ph-trash"></i>
                    </button>
                </div>
            `;
        });
        $(`.product-images[data-product-id="${productId}"]`)
          .html(imageHtml)
          .slick({
            dots: true,
            infinite: true,
            speed: 300,
            slidesToShow: 1,
            adaptiveHeight: true,
          });
      }
    );
  }

  loadProducts();

  // Add product
  $("#addProductForm").submit(function (e) {
    e.preventDefault();
    $.ajax({
      url: "../api/admin/produk.php",
      method: "POST",
      contentType: "application/json",
      data: JSON.stringify({
        nama: $("#name").val(),
        harga: $("#price").val(),
        deskripsi: $("#description").val(),
        stock: $("#stock").val(),
      }),
      success: function (response) {
        showToast("Product added successfully");
        loadProducts();
        $("#addProductForm")[0].reset();
      },
      error: function (xhr, status, error) {
        showToast("Error adding product: " + error, "error");
      },
    });
  });

  // Upload image
  $("#uploadImageForm").submit(function (e) {
    e.preventDefault();
    var fileInput = $("#image")[0];
    var file = fileInput.files[0];

    if (!file) {
      showToast("No file selected", "error");
      return;
    }

    if (file && file.size > 1024 * 1024) {
      showToast("Image size should not exceed 1MB", "error");
      return;
    }

    var fileType = file.type;
    if (fileType !== "image/jpeg" && fileType !== "image/png") {
      showToast("Only JPG/JPEG and PNG files are accepted", "error");
      return;
    }

    var formData = new FormData(this);
    $.ajax({
      url: "../api/admin/gambar_produk.php",
      method: "POST",
      data: formData,
      contentType: false,
      processData: false,
      success: function (response) {
        showToast("Image uploaded successfully");
        loadProducts();
        $("#uploadImageForm")[0].reset();
      },
      error: function (xhr, status, error) {
        showToast("Error uploading image: " + error, "error");
      },
    });
  });

  // Delete product
  $(document).on("click", ".deleteProduct", function () {
    var productId = $(this).data("id");
    $("#confirmDeleteProduct").data("id", productId);
    $("#deleteProductModal").removeClass("hidden");
  });

  $("#confirmDeleteProduct").click(function () {
    var productId = $(this).data("id");
    $.ajax({
      url: "../api/admin/produk.php?id=" + productId,
      method: "DELETE",
      success: function (response) {
        showToast("Product deleted successfully");
        loadProducts();
        $("#deleteProductModal").addClass("hidden");
      },
      error: function (xhr, status, error) {
        showToast("Error deleting product: " + error, "error");
      },
    });
  });

  $("#cancelDeleteProduct").click(function () {
    $("#deleteProductModal").addClass("hidden");
  });

  // Edit product
  $(document).on("click", ".editProduct", function () {
    var productId = $(this).data("id");
    var product = allProducts.find((p) => p.id === productId);
    if (product) {
      $("#editProductId").val(product.id);
      $("#editName").val(product.nama);
      $("#editPrice").val(product.harga);
      $("#editDescription").val(product.deskripsi);
      $("#editStock").val(product.stock);
      $("#editProductModal").removeClass("hidden");
    }
  });

  $("#editProductForm").submit(function (e) {
    e.preventDefault();
    var productId = $("#editProductId").val();
    $.ajax({
      url: "../api/admin/produk.php",
      method: "PUT",
      contentType: "application/json",
      data: JSON.stringify({
        id: productId,
        nama: $("#editName").val(),
        harga: $("#editPrice").val(),
        deskripsi: $("#editDescription").val(),
        stock: $("#editStock").val(),
      }),
      success: function (response) {
        showToast("Product updated successfully");
        $("#editProductModal").addClass("hidden");
        loadProducts();
      },
      error: function (xhr, status, error) {
        showToast("Error updating product: " + error, "error");
      },
    });
  });

  $("#cancelEditProduct").click(function () {
    $("#editProductModal").addClass("hidden");
  });

  // Delete image
  $(document).on("click", ".deleteImage", function (e) {
    e.preventDefault();
    e.stopPropagation();
    const imageId = $(this).data("image-id");
    $("#deleteImageModal").removeClass("hidden");
    $("#confirmDeleteImage").data("image-id", imageId);
  });

  $("#confirmDeleteImage").click(function () {
    const imageId = $(this).data("image-id");
    $.ajax({
      url: `../api/admin/gambar_produk.php?id=${imageId}`,
      method: "DELETE",
      success: function (response) {
        showToast("Image deleted successfully");
        loadProducts();
        $("#deleteImageModal").addClass("hidden");
      },
      error: function (xhr, status, error) {
        showToast("Error deleting image: " + error, "error");
      },
    });
  });

  $("#cancelDeleteImage").click(function () {
    $("#deleteImageModal").addClass("hidden");
  });
});
