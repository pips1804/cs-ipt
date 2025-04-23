<?php
include_once("../auth/jwt-auth.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Inventory System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style/style.css">
    <style>
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            width: 200px;
            background-color: #343a40;
            color: white;
            display: flex;
            flex-direction: column;
            padding-top: 20px;
        }

        .sidebar a {
            color: white;
            padding: 10px 20px;
            display: block;
            text-decoration: none;
        }

        .sidebar a.active,
        .sidebar a:hover {
            background-color: #495057;
        }

        .logout-btn {
            margin-top: auto;
            padding: 10px 20px;
            text-align: center;
        }

        .logout-btn a {
            color: #ffc107;
            text-decoration: none;
        }

        .main-content {
            margin-left: 200px;
            padding: 20px;
        }
    </style>
</head>

<body class="bg-light">

    <!-- Loading Overlay -->
    <!-- <div id="loadingOverlay">
        <div class="loading-content text-center">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-3 fw-bold text-dark">Fetching data...</p>
        </div>
    </div> -->

    <!-- Sidebar Navigation -->
    <div class="sidebar d-flex flex-column justify-content-between">
        <div>
            <a href="home.php?page=dashboard">Dashboard</a>
            <a href="home.php?page=products">Products</a>
            <a href="home.php?page=transactions">Transactions</a>
            <a href="home.php?page=reports">Reports</a>
            <a href="home.php?page=qr">QR Code</a>
        </div>
        <div class="mb-3">
            <a href="../auth/jwt-auth.php?logout=true" class="logout-btn text-danger">
                <i class="fa-solid fa-right-from-bracket"></i>Logout
            </a>
        </div>
    </div>


    <!-- Main Content -->
    <div class="main-content">
        <?php
        // Dynamic page loading based on the URL parameter
        if (isset($_GET['page'])) {
            $page = $_GET['page'];
            $allowed_pages = ['dashboard', 'products', 'transactions', 'reports', 'qr'];

            if (in_array($page, $allowed_pages)) {
                include("$page.php"); // Only load the allowed pages
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
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

</body>

</html>
