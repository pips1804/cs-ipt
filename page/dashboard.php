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

<style>
    .dashboard-cards {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
        margin-top: 20px;
    }

    .dashboard-card {
        background: linear-gradient(to bottom, #002e45, #004e66);
        color: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        flex: 1;
        min-width: 200px;
        text-align: left;
    }

    .dashboard-card h5 {
        margin: 0 0 10px;
        font-size: 1rem;
        font-weight: 500;
    }

    .dashboard-card p {
        font-size: 1.5rem;
        font-weight: bold;
        margin: 0;
    }

    .dashboard-title {
        font-size: 2rem;
        font-weight: 700;
        color: #333;
    }
</style>

<div class="dashboard-section">
    <h1 class="dashboard-title mb-4">Dashboard</h1>

    <div class="dashboard-cards">
        <div class="dashboard-card">
            <h5>Total Products</h5>
            <p><?= $totalProducts ?></p>
        </div>
        <div class="dashboard-card">
            <h5>Total Stocks</h5>
            <p><?= $totalStock ?></p>
        </div>
        <div class="dashboard-card">
            <h5>Low Stocks</h5>
            <p><?= $lowStockCount ?></p>
        </div>
        <div class="dashboard-card">
            <h5>Inventory Value</h5>
            <p>₱<?= number_format($totalValue, 2) ?></p>
        </div>
    </div>
</div>

<div class="mt-5">
    <h2 class="dashboard-title mb-3">Stock Overview</h2>
    <canvas id="stockChart" style="max-height: 400px;"></canvas>
</div>


<script>
    window.addEventListener('load', function() {
        const ctx = document.getElementById('stockChart');
        if (!ctx) return;

        if (window.stockChart instanceof Chart) {
            window.stockChart.destroy();
        }

        const context = ctx.getContext('2d');
        const gradient = context.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, '#002e45');
        gradient.addColorStop(1, '#004e66');

        const productNames = <?= json_encode(array_column($products, 'itemName')) ?>;
        const productStocks = <?= json_encode(array_column($products, 'stock')) ?>;

        console.log(productNames);
        console.log(productStocks);

        if (productNames.length === 0 || productStocks.length === 0) {
            console.error("Product data is empty.");
            return;
        }

        window.stockChart = new Chart(context, {
            type: 'bar',
            data: {
                labels: productNames,
                datasets: [{
                    label: 'Stock per Product',
                    data: productStocks,
                    backgroundColor: gradient,
                    borderColor: '#002e45',
                    borderWidth: 1,
                    borderRadius: 5
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>
