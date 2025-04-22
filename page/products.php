<?php
$apiUrl = "http://localhost:5000/api/products";
$response = file_get_contents($apiUrl);
$products = json_decode($response, true);

if (!$products) {
    echo "Failed to fetch or decode products.";
    exit;
}
?>

<div class="container mt-5">
    <h2 class="mb-4">PRODUCTS</h2>

    <!-- Search Bar -->
    <div class="mb-3">
        <input type="text" id="search" class="form-control" placeholder="Search Products..." onkeyup="filterProducts()">
    </div>

    <!-- Filters -->
    <div class="row mb-4">
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

    <!-- Product List -->
<div class="row" id="product-list">
    <?php foreach ($products as $product): ?>
        <div class="col-md-4 mb-4 product-item">
            <div class="card h-100 shadow-sm">
                <?php if (!empty($product['imageFullURL'])): ?>
                    <img src="<?= htmlspecialchars($product['imageFullURL']) ?>" class="card-img-top product-img" alt="Product Image">
                <?php else: ?>
                    <img src="https://via.placeholder.com/150" class="card-img-top product-img" alt="No Image">
                <?php endif; ?>

                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($product['itemName']) ?></h5>
                    <p class="card-text"><?= htmlspecialchars($product['description']) ?></p>
                    <p class="card-text">
                        <small>Stock: <?= $product['stock'] ?> | Price: ₱<?= number_format($product['unitPrice'], 2) ?></small>
                    </p>

                    <!-- Buttons -->
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
    <?php endforeach; ?>
</div>


<!-- QR Code Modal -->
<div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="qrModalLabel">Product QR Code</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
        <img id="qrImage" src="" alt="QR Code" class="img-fluid">
      </div>
    </div>
  </div>
</div>

<?php include '../modals/add_product.php'; ?>
<?php include '../modals/edit_product.php'; ?>

<script src="../assets/script/product.js"></script>