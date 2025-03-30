<?php
include_once("../auth/jwt-auth.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style/style.css">
</head>

<body class="bg-light">

    <!-- Sidebar Navigation -->
    <div class="sidebar">
        <a href="home.php?page=dashboard"><i class="fa-solid fa-chart-simple"></i>Dashboard</a>
        <a href="home.php?page=products"><i class="fa-solid fa-box-archive"></i>Products</a>
        <a href="home.php?page=transactions"><i class="fa-solid fa-arrows-turn-to-dots"></i>Transactions</a>
        <a href="home.php?page=reports"> <i class="fa-solid fa-book-open"></i>Reports</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <?php
        // Dynamic page loading
        if (isset($_GET['page'])) {
            $page = $_GET['page'];
            $allowed_pages = ['dashboard', 'products', 'transactions', 'reports'];

            if (in_array($page, $allowed_pages)) {
                include("$page.php");
            } else {
                echo "<h2>Page Not Found</h2>";
            }
        } else {
            include("dashboard.php"); // Default page
        }
        ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/dd50fcb824.js" crossorigin="anonymous"></script>
    <script src="../assets/script/script.js" defer></script>
    <script src="../assets/script/product.js" defer></script>
</body>

</html>
