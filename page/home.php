<?php
include_once("../auth/jwt-auth.php");

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
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
            background: linear-gradient(to bottom, #00394f, #001f2d);
            color: white;
            display: flex;
            flex-direction: column;
            padding-top: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .sidebar a {
            color: white;
            padding: 12px 20px;
            display: block;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s ease, border-radius 0.3s;
            border-radius: 6px 0 0 6px;
            margin-right: 10px;
        }

        .sidebar a.active,
        .sidebar a:hover {
            background-color: #2b7a8b;
            border-radius: 6px 0 0 6px;
        }

        .sidebar form {
            margin: 0;
        }

        .logout-btn {
            margin-top: auto;
            padding: 20px;
            text-align: left;
        }

        .logout-button {
            display: flex;
            align-items: center;
            gap: 10px;
            background: none;
            border: none;
            color: #ffffff;
            font-weight: bold;
            cursor: pointer;
            font-size: 1rem;
            text-decoration: none;
            transition: opacity 0.2s ease-in-out;
        }

        .logout-button:hover {
            opacity: 0.8;
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
        <div class="logout-btn">
            <form action="../auth/jwt-auth.php" method="GET">
                <input type="hidden" name="logout" value="true">
                <button type="submit" class="logout-button">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    Logout
                </button>
            </form>
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
