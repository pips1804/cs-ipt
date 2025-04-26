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

    <!-- Sidebar Navigation -->
    <div class="sidebar d-flex flex-column justify-content-between">
        <div>
            <a href="#" class="sidebar-link" data-page="dashboard">Dashboard</a>
            <a href="#" class="sidebar-link" data-page="products">Products</a>
            <a href="#" class="sidebar-link" data-page="transactions">Transactions</a>
            <a href="#" class="sidebar-link" data-page="reports">Reports</a>
            <a href="#" class="sidebar-link" data-page="qr">QR Code</a>
        </div>
        <div class="logout-btn">
            <form action="../auth/jwt-auth.php" method="GET">
                <input type="hidden" name="logout" value="true">
                <button type="submit" class="logout-button">Logout</button>
            </form>
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
            // Initially load the dashboard page
            loadPage("dashboard");

            // Sidebar link click handler
            $('.sidebar-link').on('click', function(e) {
                e.preventDefault(); // Prevent default anchor click behavior
                const page = $(this).data('page');
                loadPage(page);
            });

            function loadPage(page) {
                console.log('Loading page: ' + page);
                // Show a loading message while fetching
                $('.main-content').html('<div class="text-center">Loading...</div>');

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
