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

<style>
    .table-container {
        max-height: 500px;
        /* adjust height if needed */
        overflow-y: auto;
        overflow-x: auto;
    }

    .table thead th {
        position: sticky;
        top: 0;
        background: linear-gradient(135deg, #04364A, #0D5975);
        color: white;
        z-index: 2;
        text-align: center;
    }

    th,
    td {
        vertical-align: middle;
        white-space: nowrap;
    }

    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(0, 0, 0, 0.05);
    }

    .dashboard-title {
        font-size: 3rem;
        font-weight: 700;
        color: #333;
    }
</style>

<div class="container mt-5">
    <h2 class="mb-4 fw-bold text-dark text-center dashboard-title">Stock Reports</h2>

    <!-- Detailed Table -->
    <div class="table-container">
        <table class="table table-bordered table-striped">
            <thead>
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
