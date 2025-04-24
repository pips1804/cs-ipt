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

$totalProducts = count($products);
$totalStock = 0;
$lowStockCount = 0;

foreach ($products as $product) {
    $stock = (int)$product['stock'];
    $totalStock += $stock;
    if ($stock <= 5) {
        $lowStockCount++;
    }
}
?>

<div class="container mt-5">
    <h2 class="mb-4">STOCK REPORTS</h2>

    <!-- Detailed Table -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Product Name</th>
                    <th>Description</th>
                    <th>Stock</th>
                    <th>Price</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product):
                    $stock = (int)$product['stock'];
                    $status = $stock <= 5 ? "Low" : "Sufficient";
                    $statusClass = $stock <= 5 ? "text-danger" : "text-success";
                ?>
                    <tr>
                        <td><?= htmlspecialchars($product['itemName']) ?></td>
                        <td><?= htmlspecialchars($product['description']) ?></td>
                        <td><?= $stock ?></td>
                        <td>₱<?= number_format($product['unitPrice'], 2) ?></td>
                        <td class="<?= $statusClass ?> fw-bold"><?= $status ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
