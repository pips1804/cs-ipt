<?php
$apiUrl = "http://localhost:5000/api/products";
$response = file_get_contents($apiUrl);
$products = json_decode($response, true);

if (!$products) {
    echo "Failed to fetch or decode products.";
    exit;
}

$totalProducts = count($products);
$totalStock = 0;
$lowStockCount = 0;
$totalValue = 0;

foreach ($products as $product) {
    $stock = (int)$product['stock'];
    $price = (float)$product['unitPrice'];

    $totalStock += $stock;
    $totalValue += $stock * $price;

    if ($stock <= 5) {
        $lowStockCount++;
    }
}
?>

<div class="container mt-5">
    <h1 class="mb-4">Dashboard</h1>

    <div class="row">
        <!-- Total Products -->
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-primary shadow">
                <div class="card-body">
                    <h5 class="card-title">Total Products</h5>
                    <p class="fs-4"><?= $totalProducts ?></p>
                </div>
            </div>
        </div>

        <!-- Total Stock -->
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-success shadow">
                <div class="card-body">
                    <h5 class="card-title">Total Stock</h5>
                    <p class="fs-4"><?= $totalStock ?></p>
                </div>
            </div>
        </div>

        <!-- Low Stock -->
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-danger shadow">
                <div class="card-body">
                    <h5 class="card-title">Low Stock (≤ 5)</h5>
                    <p class="fs-4"><?= $lowStockCount ?></p>
                </div>
            </div>
        </div>

        <!-- Inventory Value -->
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-secondary shadow">
                <div class="card-body">
                    <h5 class="card-title">Inventory Value</h5>
                    <p class="fs-4">₱<?= number_format($totalValue, 2) ?></p>
                </div>
            </div>
        </div>
    </div>
</div>
