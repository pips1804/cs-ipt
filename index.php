<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login Page</title>
    <link rel="stylesheet" href="./assets/style.css">
    <link rel="stylesheet" href="./assets/style/bootstrap.min.css">
    <style>
        input:focus::placeholder,
        input:not(:placeholder-shown)::placeholder {
            color: transparent !important;
            /* Hide placeholder when typing */
        }

        /* Loader Style */
        #loadingOverlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.8);
            z-index: 9999;
            display: none;
            justify-content: center;
            align-items: center;
            font-size: 1.5rem;
        }

        #loadingOverlay .spinner-border {
            width: 3rem;
            height: 3rem;
        }
    </style>
</head>

<body class="d-flex justify-content-center align-items-center vh-100">
    <!-- Loading Overlay -->
    <div id="loadingOverlay">
        <div class="spinner-border text-primary" role="status"></div>
        <span class="ms-3">Logging in...</span>
    </div>

    <div class="card shadow-sm p-4" style="width: 300px;">
        <h2 class="text-center mb-3">Login</h2>

        <form method="post" action="controllers/login.php" onsubmit="savePassword()">
            <div class="form-floating mb-3">
                <input type="email" class="form-control" id="floatingInput" placeholder="Email address" required name="email">
                <label for="floatingInput">Email address</label>
            </div>
            <div class="form-floating mb-3">
                <input type="password" class="form-control" id="floatingPassword" placeholder="Password" required name="password">
                <label for="floatingPassword">Password</label>
            </div>
            <button type="submit" class="btn btn-primary w-100" name="login">Login</button>
        </form>
    </div>

    <script>
        // Save password before form submits
        function savePassword() {
            sessionStorage.setItem("password", document.getElementById("floatingPassword").value);
        }

        // Restore password if available
        window.onload = function () {
            if (sessionStorage.getItem("password")) {
                document.getElementById("floatingPassword").value = sessionStorage.getItem("password");
                sessionStorage.removeItem("password"); // Clear after setting
            }
        }

        document.querySelector("form").addEventListener("submit", async function (event) {
        event.preventDefault();

        // Show the loading overlay
        document.getElementById('loadingOverlay').style.display = 'flex';

        const formData = new FormData(this);
        const response = await fetch("controllers/login.php", {
            method: "POST",
            body: formData,
            credentials: "same-origin"
        });

        const data = await response.json();

        if (data.success) {
            // Don't hide overlay — let it stay while redirecting
            window.location.href = "page/home.php";
        } else {
            // Hide the overlay only if login fails
            document.getElementById('loadingOverlay').style.display = 'none';
            alert("Login failed: " + data.error);
        }
        });

    </script>

    <script src="./assets/script/bootstrap.min.js"></script>
</body>

</html>
