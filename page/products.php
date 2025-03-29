<?php
include '../config/db_connect.php';

// Fetch categories once and store them in an array
$categoryQuery = "SELECT * FROM categories";
$categoryResult = $conn->query($categoryQuery);

$categories = [];
while ($category = $categoryResult->fetch_assoc()) {
    $categories[] = $category; // Store each category in an array
}

// Fetch products
$productQuery = "SELECT * FROM products";
$productResult = $conn->query($productQuery);
?>

<div class="container mt-4">
    <h2 class="mb-4">Product Inventory</h2>

    <!-- Search & Add Product -->
    <div class="d-flex justify-content-between mb-3">
        <input type="text" id="searchInput" class="form-control w-50" placeholder="Search products...">
        <button class="btn btn-success px-4" id="addProductBtn">+ New Product</button>
    </div>

    <!-- Category Filter Buttons -->
    <div class="mb-3">
        <button class="btn btn-secondary me-2 category-btn" data-category="all">All</button>
        <?php foreach ($categories as $category): ?>
            <button class="btn btn-outline-primary me-2 category-btn" data-category="<?= $category['cat_id']; ?>">
                <?= $category['name']; ?>
            </button>
        <?php endforeach; ?>
    </div>

    <!-- Product List -->
    <div class="list-group" id="productList" style="max-height: 600px; overflow-y: auto; border: 1px solid #ddd; padding: 20px;">
        <?php while ($product = $productResult->fetch_assoc()): ?>
            <div class="list-group-item p-4 mb-3 shadow-sm rounded product-item" data-category="<?= $product['cat_id']; ?>">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="mb-1"><?= $product['name']; ?></h5>
                        <p class="mb-1 text-muted"><?= $product['description']; ?></p>
                        <small class="text-secondary">
                            Stock: <?= $product['stock_quantity']; ?> |
                            Price: $<?= number_format($product['price'], 2); ?>
                        </small>
                    </div>
                    <div>
                        <button class="btn btn-warning btn-sm me-2 edit-btn"
                            data-id="<?= $product['prod_id']; ?>"
                            data-name="<?= $product['name']; ?>"
                            data-description="<?= $product['description']; ?>"
                            data-stock="<?= $product['stock_quantity']; ?>"
                            data-price="<?= $product['price']; ?>"
                            data-category="<?= $product['cat_id']; ?>">Edit</button>

                        <button class="btn btn-danger btn-sm delete-btn" data-id="<?= $product['prod_id']; ?>">Delete</button>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productModalLabel">Add Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="productForm">
                    <input type="hidden" id="productId" name="productId">

                    <div class="mb-3">
                        <label for="productName" class="form-label">Product Name</label>
                        <input type="text" class="form-control" id="productName" name="name" required>
                    </div>

                    <div class="mb-3">
                        <label for="productDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="productDescription" name="description" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="productStock" class="form-label">Stock Quantity</label>
                        <input type="number" class="form-control" id="productStock" name="stock_quantity" required>
                    </div>

                    <div class="mb-3">
                        <label for="productPrice" class="form-label">Price</label>
                        <input type="number" class="form-control" id="productPrice" name="price" step="0.01" min="0" required>
                    </div>

                    <div class="mb-3">
                        <label for="productCategory" class="form-label">Category</label>
                        <select class="form-control" id="productCategory" name="category_id" required>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['cat_id']; ?>"><?= $category['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Save Product</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Search Functionality
        $("#searchInput").on("keyup", function() {
            let value = $(this).val().toLowerCase();
            $(".product-item").each(function() {
                let found = $(this).text().toLowerCase().includes(value);
                $(this).toggle(found);
            });
        });

        // Category Filter
        $(document).on("click", ".category-btn", function() {
            let category = $(this).data("category");
            $(".category-btn").removeClass("btn-secondary").addClass("btn-outline-primary");
            $(this).removeClass("btn-outline-primary").addClass("btn-secondary");

            if (category === "all") {
                $(".product-item").show();
            } else {
                $(".product-item").each(function() {
                    $(this).toggle($(this).data("category") == category);
                });
            }
        });

        // Show modal for adding a new product
        $(document).on("click", "#addProductBtn", function() {
            $("#productForm")[0].reset();
            $("#productId").val("");
            $("#productModalLabel").text("Add Product");
            $("#productModal").modal("show");
        });

        // Show modal for editing a product
        $(document).on("click", ".edit-btn", function() {
            $("#productId").val($(this).data("id"));
            $("#productName").val($(this).data("name"));
            $("#productDescription").val($(this).data("description"));
            $("#productStock").val($(this).data("stock"));
            $("#productPrice").val($(this).data("price"));
            $("#productCategory").val($(this).data("category"));
            $("#productModalLabel").text("Edit Product");
            $("#productModal").modal("show");
        });

        // Handle form submission (Add/Edit Product)
        $("#productForm").submit(function(event) {
            event.preventDefault();

            let formData = $(this).serialize();
            let actionUrl = $("#productId").val() ? "../controllers/update_product.php" : "../controllers/add_product.php";

            $.post(actionUrl, formData, function(response) {
                console.log("Response from server:", response); // Debugging log
                $("#productModal").modal("hide");
                $("#productForm")[0].reset();
                fetchProducts();
            });
        });

        // Delete product
        $(document).on("click", ".delete-btn", function() {
            let productId = $(this).data("id");

            if (confirm("Are you sure you want to delete this product?")) {
                $.post("../controllers/delete_product.php", {
                    id: productId
                }, function(response) {
                    alert(response);
                    fetchProducts();
                });
            }
        });

        // Fetch updated product list
        function fetchProducts() {
            $.get("../controllers/get_products.php", function(data) {
                console.log("Fetched Products:", data);
                $("#productList").html(data);
            });
        }
    });


    function attachEventListeners() {
        $(document).off("click", ".edit-btn").on("click", ".edit-btn", function() {
            $("#productId").val($(this).data("id"));
            $("#productName").val($(this).data("name"));
            $("#productDescription").val($(this).data("description"));
            $("#productStock").val($(this).data("stock"));
            $("#productPrice").val($(this).data("price"));
            $("#productCategory").val($(this).data("category"));
            $("#productModalLabel").text("Edit Product");
            $("#productModal").modal("show");
        });

        $(document).off("click", ".delete-btn").on("click", ".delete-btn", function() {
            let productId = $(this).data("id");
            if (confirm("Are you sure you want to delete this product?")) {
                $.post("../controllers/delete_product.php", {
                    id: productId
                }, function(response) {
                    alert(response);
                    fetchProducts();
                });
            }
        });
    }

    // Initial event listener attachment
    attachEventListeners();
</script>
