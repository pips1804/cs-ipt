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
                        <p class="card-text"><small>Stock: <?= $product['stock'] ?> | Price: ₱<?= number_format($product['unitPrice'], 2) ?></small></p>
                        <button type="button" class="btn btn-danger btn-sm" onclick="deleteProduct(<?= $product['productID'] ?>)">Delete</button>
                        <button type="button" class="btn btn-warning btn-sm" onclick='openEditModal(<?= json_encode($product) ?>)'>Edit</button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Item Number</label>
                    <input type="text" id="addItemNumber" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Item Name</label>
                    <input type="text" id="addItemName" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea id="addDescription" class="form-control" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Price</label>
                    <input type="number" id="addUnitPrice" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Stock</label>
                    <input type="number" id="addStock" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Discount</label>
                    <input type="number" id="addDiscount" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select id="addStatus" class="form-select">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <!-- Image Upload -->
                <div class="mb-3">
                    <label class="form-label">Product Image</label>
                    <input type="file" id="addImage" class="form-control" accept="image/*">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary" onclick="addProduct()">Add Product</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Product Modal -->
<div class="modal fade" id="editProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editProductId">
                <div class="mb-3">
                    <label class="form-label">Item Name</label>
                    <input type="text" id="editItemName" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea id="editDescription" class="form-control" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Price</label>
                    <input type="number" id="editUnitPrice" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Stock</label>
                    <input type="number" id="editStock" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary" onclick="saveProductChanges()">Save Changes</button>
            </div>
        </div>
    </div>
</div>



<script>

let currentUsername = "";

fetch('../auth/get_user_info.php')
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            currentUsername = data.user.username;
            console.log("Logged in as:", currentUsername);
        } else {
            console.warn("User info not found:", data.message);
        }
    });

function togglePriceFilter() {
    let value = document.getElementById('priceFilterToggle').value;
    let section = document.getElementById('priceFilterSection');
    section.style.display = value === "on" ? "block" : "none";
    filterProducts();
}

function updatePriceDisplay() {
    let priceVal = document.getElementById("priceRange").value;
    document.getElementById("priceValue").innerText = priceVal;
}

function filterProducts() {
    let searchValue = document.getElementById('search').value.toLowerCase();
    let stockFilter = document.getElementById('stockFilter').value;
    let priceFilterActive = document.getElementById('priceFilterToggle').value === "on";
    let maxPrice = parseFloat(document.getElementById('priceRange').value) || Infinity;

    let products = document.querySelectorAll('.product-item');

    products.forEach(product => {
        let title = product.querySelector('.card-title').textContent.toLowerCase();
        let description = product.querySelector('.card-text').textContent.toLowerCase();
        let stockText = product.querySelectorAll('.card-text')[1].textContent;
        let priceMatch = stockText.match(/Price: ₱([\d,\.]+)/);
        let stockMatch = stockText.match(/Stock: (\d+)/);

        let stock = stockMatch ? parseInt(stockMatch[1]) : 0;
        let price = priceMatch ? parseFloat(priceMatch[1].replace(/,/g, '')) : 0;

        let isVisible = true;
        if (!title.includes(searchValue) && !description.includes(searchValue)) isVisible = false;
        if (stockFilter === 'in' && stock <= 0) isVisible = false;
        if (stockFilter === 'out' && stock > 0) isVisible = false;
        if (priceFilterActive && price > maxPrice) isVisible = false;

        product.style.display = isVisible ? '' : 'none';
    });
}

function deleteProduct(productId) {
    if (confirm("Are you sure you want to delete this product?")) {
        fetch(`http://localhost:5000/api/products/${productId}`, { method: 'DELETE' })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.message.includes("deleted")) {
                    const logDescription = `Deleted product with ID: ${productId}`;

                    fetch('../controllers/log_transaction.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: new URLSearchParams({
                            action: 'Deleted product',
                            username: currentUsername,
                            description: logDescription
                        })
                    })
                    .then(response => response.json())
                    .then(logData => {
                        console.log('Log:', logData.message);
                        location.reload();
                    });
                }
            })
            .catch(error => {
                alert('Error deleting product.');
                console.error(error);
            });
    }
}


function openEditModal(product) {
    document.getElementById('editProductId').value = product.productID;
    document.getElementById('editItemName').value = product.itemName;
    document.getElementById('editDescription').value = product.description;
    document.getElementById('editUnitPrice').value = product.unitPrice;
    document.getElementById('editStock').value = product.stock;
    let modal = new bootstrap.Modal(document.getElementById('editProductModal'));
    modal.show();
}

function saveProductChanges() {
    const id = document.getElementById('editProductId').value;
    const updatedData = {
        itemName: document.getElementById('editItemName').value,
        description: document.getElementById('editDescription').value,
        unitPrice: parseFloat(document.getElementById('editUnitPrice').value),
        stock: parseInt(document.getElementById('editStock').value)
    };

    fetch(`http://localhost:5000/api/products/${id}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(updatedData)
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        if (data.message === 'Product updated successfully') {
            const logDescription = `
                Updated product ID: ${id},
                New Name: ${updatedData.itemName},
                New Description: ${updatedData.description},
                New Price: ₱${updatedData.unitPrice.toFixed(2)},
                New Stock: ${updatedData.stock}
            `.trim();

            fetch('../controllers/log_transaction.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    action: 'Edited product',
                    username: currentUsername,
                    description: logDescription
                })
            })
            .then(response => response.json())
            .then(logData => {
                console.log('Log:', logData.message);
                bootstrap.Modal.getInstance(document.getElementById('editProductModal')).hide();
                location.reload();
            });
        }
    })
    .catch(err => {
        alert('Update failed.');
        console.error(err);
    });
}


function addProduct() {
    const newProductData = {
        itemNumber: document.getElementById('addItemNumber').value,
        itemName: document.getElementById('addItemName').value,
        description: document.getElementById('addDescription').value,
        unitPrice: parseFloat(document.getElementById('addUnitPrice').value),
        stock: parseInt(document.getElementById('addStock').value),
        discount: parseFloat(document.getElementById('addDiscount').value) || 0,
        status: document.getElementById('addStatus').value
    };

    const formData = new FormData();
    formData.append('itemNumber', newProductData.itemNumber);
    formData.append('itemName', newProductData.itemName);
    formData.append('description', newProductData.description);
    formData.append('unitPrice', newProductData.unitPrice);
    formData.append('stock', newProductData.stock);
    formData.append('discount', newProductData.discount);
    formData.append('status', newProductData.status);

    const imageInput = document.getElementById('addImage');
    if (imageInput.files.length > 0) {
        formData.append('itemImage', imageInput.files[0]);
    }

    fetch('http://localhost:5000/api/add_product', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.message === 'Item added successfully!') {
            // Create log description
            const logDescription = `
                Item Number: ${newProductData.itemNumber},
                Name: ${newProductData.itemName},
                Description: ${newProductData.description},
                Price: ₱${newProductData.unitPrice.toFixed(2)},
                Stock: ${newProductData.stock},
                Discount: ${newProductData.discount}%, 
                Status: ${newProductData.status}
            `.trim();

            // Log the transaction
            fetch('../controllers/log_transaction.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    action: 'Added product',
                    description: logDescription,
                    username: currentUsername
                })
            })
            .then(response => response.json())
            .then(logData => {
                console.log('Log:', logData.message);
                location.reload();
            });
        }
    })
    .catch(error => {
        alert('Error adding product.');
        console.error(error);
    });
}




</script>
