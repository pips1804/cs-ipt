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

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style/style.css">
    <style>
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            width: 220px;
            background: linear-gradient(to bottom, #00394f, #001f2d);
            color: white;
            padding: 20px 15px;
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.2);
        }

        .sidebar-header h4 {
            font-weight: bold;
            color: #00d1b2;
        }

        .sidebar-link {
            color: #ffffff;
            padding: 12px 15px;
            display: flex;
            align-items: center;
            text-decoration: none;
            font-weight: 500;
            border-radius: 8px;
            margin-bottom: 10px;
            transition: background 0.3s ease;
        }

        .sidebar-link:hover,
        .sidebar-link.active {
            background-color: #00d1b2;
            color: #1a1f2e;
            font-weight: 600;
        }

        .logout-btn {
            padding-top: 20px;
        }

        .logout-button {
            display: flex;
            align-items: center;
            width: 100%;
            padding: 12px;
            background: transparent;
            border: none;
            color: #ffffff;
            font-weight: 500;
            font-size: 1rem;
            border-radius: 8px;
            transition: background 0.3s ease;
        }

        .logout-button:hover {
            background: #dc3545;
            color: white;
        }

        .main-content {
            background-color: whitesmoke;
        }
    </style>
</head>

<body class="bg-light">

    <!-- Sidebar Navigation -->
    <div class="sidebar d-flex flex-column">
        <div class="sidebar-header text-center mb-4">
            <h4>🛒 Inventory</h4>
        </div>
        <nav class="flex-grow-1">
            <a href="#" class="sidebar-link" data-page="dashboard"><i class="fas fa-home me-2"></i> Dashboard</a>
            <a href="#" class="sidebar-link" data-page="products"><i class="fas fa-box-open me-2"></i> Products</a>
            <a href="#" class="sidebar-link" data-page="transactions"><i class="fas fa-exchange-alt me-2"></i> Transactions</a>
            <a href="#" class="sidebar-link" data-page="reports"><i class="fas fa-chart-bar me-2"></i> Reports</a>
            <a href="#" class="sidebar-link" data-page="qr"><i class="fas fa-qrcode me-2"></i> QR Code</a>
        </nav>
        <div class="logout-btn">
            <button type="button" class="logout-button" data-bs-toggle="modal" data-bs-target="#logoutModal">
                <i class="fas fa-sign-out-alt me-2"></i> Logout
            </button>
        </div>
    </div>

    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="logoutModalLabel">Confirm Logout</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to logout?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="../auth/jwt-auth.php" method="GET">
                        <input type="hidden" name="logout" value="true">
                        <button type="submit" class="btn btn-danger">Yes, Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- The content will be loaded dynamically here -->
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/dd50fcb824.js" crossorigin="anonymous"></script>
    <script src="../assets/script/script.js" defer></script>
    <script src="../assets/script/product.js" defer></script>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        $(document).ready(function() {
            // Load the last visited page or default to 'dashboard'
            const lastPage = localStorage.getItem('lastPage') || 'dashboard';
            loadPage(lastPage);

            // Sidebar link click handler
            $('.sidebar-link').on('click', function(e) {
                e.preventDefault();
                const page = $(this).data('page');
                localStorage.setItem('lastPage', page); // Save selected page
                loadPage(page);
            });

            function loadPage(page) {
                console.log('Loading page: ' + page);
                // Show a loading message while fetching
                $('.main-content').html(`
        <div class="d-flex flex-column justify-content-center align-items-center" style="height: 80vh;">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <div class="mt-3 text-primary">Loading content...</div>
        </div>
    `);

                // Use AJAX to load the page content dynamically
                $.ajax({
                    url: page + '.php', // Dynamically load the page from the server
                    type: 'GET',
                    success: function(data) {
                        console.log('Successfully loaded: ' + page);
                        $('.main-content').html(data); // Insert the page content into the main-content div
                        if (page === "dashboard") {
                            initializeChart(); // Initialize chart when the dashboard is loaded
                        }
                    },
                    error: function() {
                        console.error('Failed to load content for: ' + page);
                        $('.main-content').html('<div class="text-center text-danger">Failed to load content</div>');
                    }
                });
            }

            function initializeChart() {
                const ctx = document.getElementById('stockChart');
                if (!ctx) return;

                // Destroy existing chart if it exists
                if (window.stockChart instanceof Chart) {
                    window.stockChart.destroy();
                }

                // Fetch product data via AJAX
                $.ajax({
                    url: 'http://localhost:5000/api/products', // Your API endpoint
                    type: 'GET',
                    success: function(response) {
                        const productNames = response.map(product => product.itemName);
                        const productStocks = response.map(product => product.stock);

                        const context = ctx.getContext('2d');
                        const gradient = context.createLinearGradient(0, 0, 0, 400);
                        gradient.addColorStop(0, '#002e45');
                        gradient.addColorStop(1, '#004e66');

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
                    },
                    error: function() {
                        console.error('Failed to fetch products.');
                    }
                });
            }

        });
    </script>

</body>

</html>
