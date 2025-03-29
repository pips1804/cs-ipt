<?php
include '../config/db_connect.php';

$productQuery = "SELECT * FROM products";
$productResult = $conn->query($productQuery);

while ($product = $productResult->fetch_assoc()): ?>
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
