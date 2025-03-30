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
        <button class="btn btn-success " id="addProductBtn">New Product</button>
    </div>

    <!-- Category Filter Buttons -->
    <!-- Category Filter Buttons -->
    <div class="dropdown mb-3">
        <button class="btn btn-secondary dropdown-toggle" type="button" id="categoryDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            Select Category
        </button>
        <ul class="dropdown-menu" aria-labelledby="categoryDropdown">
            <li><button class="dropdown-item category-btn" data-category="all">All</button></li>
            <?php foreach ($categories as $category): ?>
                <li class="d-flex justify-content-between align-items-center px-2">
                    <button class="dropdown-item category-btn flex-grow-1 text-start" data-category="<?= $category['cat_id']; ?>">
                        <?= $category['name']; ?>
                    </button>
                    <button class="btn btn-sm text-danger delete-category border-0 ms-2" data-id="<?= $category['cat_id']; ?>" style="background: none;">
                        <i class="fas fa-times"></i>
                    </button>
                </li>
            <?php endforeach; ?>
        </ul>
        <button class="btn btn-primary " id="addCategoryBtn">New Category</button>
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

<div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryModalLabel">Add Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="categoryForm">
                    <div class="mb-3">
                        <label for="categoryName" class="form-label">Category Name</label>
                        <input type="text" class="form-control" id="categoryName" name="category_name" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Category</button>
                </form>
            </div>
        </div>
    </div>
</div>
