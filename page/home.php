<?php
include_once("../auth/jwt-auth.php");
include '../config/db_connect.php';

// Fetch categories
$categoryQuery = "SELECT * FROM categories";
$categoryResult = $conn->query($categoryQuery);

// Fetch products
$productQuery = "SELECT * FROM products";
$productResult = $conn->query($productQuery);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Inventory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="bg-light">

    <div class="container mt-5">
        <h2 class="text-center mb-4">Product Inventory</h2>

        <!-- Search Bar -->
        <div class="mb-3">
            <input type="text" id="searchInput" class="form-control" placeholder="Search for products...">
        </div>

        <!-- Category Buttons -->
        <div class="d-flex justify-content-center mb-3">
            <button class="btn btn-secondary me-2 category-btn" data-category="all">All</button>
            <?php while ($category = $categoryResult->fetch_assoc()): ?>
                <button class="btn btn-outline-primary me-2 category-btn" data-category="<?= $category['cat_id']; ?>">
                    <?= $category['name']; ?>
                </button>
            <?php endwhile; ?>
        </div>

        <!-- Product List -->
        <div class="row" id="productList">
            <?php while ($product = $productResult->fetch_assoc()): ?>
                <div class="col-md-4 mb-4 product-item" data-category="<?= $product['cat_id']; ?>">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?= $product['name']; ?></h5>
                            <p class="card-text"><?= $product['description']; ?></p>
                            <p class="text-muted">Price: $<?= number_format($product['price'], 2); ?></p>
                            <p class="text-muted">Stock: <?= $product['stock_quantity']; ?></p>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Search Functionality
            $("#searchInput").on("keyup", function() {
                let value = $(this).val().toLowerCase();
                $(".product-item").each(function() {
                    let productName = $(this).find(".card-title").text().toLowerCase();
                    $(this).toggle(productName.includes(value));
                });
            });

            // Category Filter
            $(".category-btn").on("click", function() {
                let category = $(this).data("category");
                if (category === "all") {
                    $(".product-item").show();
                } else {
                    $(".product-item").each(function() {
                        $(this).toggle($(this).data("category") == category);
                    });
                }
            });
        });
    </script>

</body>

</html>
