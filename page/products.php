<?php
require '../auth/verify_token.php';

if (!isset($_COOKIE['jwt'])) {
    // No token, redirect to login
    header("Location: ../index.php");
    exit();
}

$decodedToken = verifyJWT($_COOKIE['jwt']);

if (!$decodedToken || $decodedToken['exp'] < time()) {
    // Invalid or expired token
    setcookie("jwt", "", time() - 3600, "/", "", false, true);
    header("Location: ../index.php");
    exit();
}

// Optionally access user data
$user_id = $decodedToken['id'];
$user_email = $decodedToken['email'];
?>

<?php

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$apiUrl = "http://localhost:5000/api/products";
$response = file_get_contents($apiUrl);
$products = json_decode($response, true);

if (!$products) {
    echo "Failed to fetch or decode products.";
    exit;
}
?>

<style>
    .sticky-top {
        background-color: white;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    }

    #product-list::-webkit-scrollbar {
        width: 6px;
    }

    #product-list::-webkit-scrollbar-thumb {
        background-color: #ccc;
        border-radius: 4px;
    }

    .form-control,
    .form-select {
        background: linear-gradient(135deg, #04364A, #0D5975);
        color: white;
        border: none;
        border-radius: 6px;
        padding: 10px 14px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }

    .form-control::placeholder {
        color: #cfd8dc;
    }

    .form-control:focus,
    .form-select:focus {
        outline: none;
        box-shadow: 0 0 0 2px #4fc3f7;
    }

    .form-select option {
        background-color: #04364A;
        color: white;
    }

    .btn-primary {
        background-color: #0D5975;
        border: none;
    }

    .btn-primary:hover {
        background-color: #127291;
    }

    .btn-primary {
        height: 44px;
        padding: 8px 20px;
        font-size: 0.95rem;
        border-radius: 6px;
    }

    /* PRODUCT CARD STYLES */
    .product-item {
        display: flex;
        justify-content: center;
    }

    .product-card {
        width: 100%;
        max-width: 300px;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .product-img {
        width: 100%;
        height: 220px;
        object-fit: cover;
        border-top-left-radius: 0.375rem;
        border-top-right-radius: 0.375rem;
    }

    .card-body {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .card-text {
        margin-bottom: 0.5rem;
    }

    .card-body .btn {
        margin-top: 0.25rem;
        width: 100%;
    }

    @media (max-width: 768px) {
        .col-md-4 {
            flex: 0 0 100%;
            max-width: 100%;
        }
    }

    #qrImage:hover {
        transform: scale(1.05);
    }

    .form-control:focus,
    .form-select:focus {
        color: #ffffff;
        /* Change text color to white when typing */
        box-shadow: 0 0 0 2px #4fc3f7;
        /* Optional, focus glow */
    }

    /* Optional: Change text color of un-focused input fields */
    .form-control,
    .form-select {
        color: #cfd8dc;
        /* Default text color */
    }
</style>

<div class="container">
    <div class="sticky-top bg-white py-3 z-3">

        <!-- Search Bar -->
        <div class="mb-3">
            <input type="text" id="search" class="form-control" placeholder="Search Products..." onkeyup="filterProducts()">
        </div>

        <!-- Filters -->
        <div class="row mb-3">
            <div class="col-md-4 mb-2">
                <select id="stockFilter" class="form-select" onchange="filterProducts()">
                    <option value="">All Stock Status</option>
                    <option value="in">In Stock</option>
                    <option value="out">Out of Stock</option>
                </select>
            </div>

            <div class="col-md-4 mb-2">
                <select id="priceFilterToggle" class="form-select" onchange="togglePriceFilter()">
                    <option value="">No Price Filter</option>
                    <option value="on">Filter by Price</option>
                </select>
            </div>

            <div class="col-md-4 mb-2" id="priceFilterSection" style="display: none;">
                <label for="priceRange" class="form-label">Max Price: ₱<span id="priceValue">5000</span></label>
                <input type="range" class="form-range" min="0" max="5000" step="50" id="priceRange" oninput="updatePriceDisplay(); filterProducts();">
            </div>
        </div>

        <!-- Add Product Button -->
        <div class="mb-3">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">Add Product</button>
        </div>
    </div>

    <!-- Scrollable Product List -->
    <div style="max-height: calc(100vh - 280px); overflow-y: auto; padding-right: 10px;">
        <div class="row" id="product-list">
            <?php foreach ($products as $product): ?>
                <div class="col-sm-6 col-md-4 col-lg-3 mb-3 product-item">
                    <div class="card shadow-sm product-card">
                        <?php if (!empty($product['imageFullURL'])): ?>
                            <img src="<?= htmlspecialchars($product['imageFullURL']) ?>" class="card-img-top product-img" alt="Product Image">
                        <?php else: ?>
                            <img src="https://via.placeholder.com/150" class="card-img-top product-img" alt="No Image">
                        <?php endif; ?>

                        <div class="card-body">
                            <div>
                                <h5 class="card-title"><?= htmlspecialchars($product['itemName']) ?></h5>
                                <p class="card-text"><?= htmlspecialchars($product['description']) ?></p>
                                <p class="card-text">
                                    <small>Stock: <?= $product['stock'] ?> | Price: ₱<?= number_format($product['unitPrice'], 2) ?></small>
                                </p>
                            </div>

                            <div>
                                <button type="button" class="btn btn-danger btn-sm" onclick="deleteProduct(<?= $product['productID'] ?>)">Delete</button>
                                <button type="button" class="btn btn-warning btn-sm" onclick='openEditModal(<?= json_encode($product) ?>)'>Edit</button>

                                <?php if (!empty($product['qrFullURL'])): ?>
                                    <button type="button" class="btn btn-info btn-sm mt-2" onclick="showQRModal('<?= htmlspecialchars($product['qrFullURL']) ?>')">View QR</button>
                                <?php else: ?>
                                    <button type="button" class="btn btn-secondary btn-sm mt-2" disabled>No QR</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- ✅ QR Modal moved outside scroll container -->
    <!-- QR Code Modal -->
    <!-- QR Code Modal -->
    <div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 shadow-lg border-0">
                <div class="modal-header text-white rounded-top-4" style="background: linear-gradient(135deg, #04364A, #0D5975);">
                    <h5 class="modal-title" id="qrModalLabel">Product QR Code</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="qrImage" src="" alt="QR Code" class="img-fluid rounded shadow-sm" style="max-width: 80%; transition: transform 0.3s;">
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>



    <?php include '../modals/add_product.php'; ?>
    <?php include '../modals/edit_product.php'; ?>

    <script src="../assets/script/product.js"></script>
</div>
