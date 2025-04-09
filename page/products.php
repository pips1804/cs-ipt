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

                        <!-- Delete Button -->
                        <button type="button" class="btn btn-danger btn-sm" onclick="deleteProduct(<?= $product['productID'] ?>)">Delete</button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
// Search function
function filterProducts() {
    let input = document.getElementById('search');
    let filter = input.value.toLowerCase();
    let productList = document.getElementById('product-list');
    let products = productList.getElementsByClassName('product-item');

    for (let i = 0; i < products.length; i++) {
        let productName = products[i].getElementsByClassName('card-title')[0].textContent;
        if (productName.toLowerCase().indexOf(filter) > -1) {
            products[i].style.display = "";
        } else {
            products[i].style.display = "none";
        }
    }
}

// Function to handle the delete button
function deleteProduct(productId) {
    if (confirm("Are you sure you want to delete this product?")) {
        fetch(`http://localhost:5000/api/products/${productId}`, {
            method: 'DELETE',
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.message.includes("deleted")) {
                location.reload(); // Reload the page to reflect the changes
            }
        })
        .catch(error => {
            alert('Error deleting product.');
            console.error(error);
        });
    }
}
</script>
