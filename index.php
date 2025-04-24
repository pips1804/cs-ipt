<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login Page</title>
    <link rel="stylesheet" href="./assets/style.css">
    <link rel="stylesheet" href="./assets/style/bootstrap.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: 'Segoe UI', sans-serif;
        }

        .login-container {
            background-color: #05445E;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 320px;
            text-align: center;
        }

        .login-container h2 {
            color: #ffffff;
            margin-bottom: 1.5rem;
            font-weight: bold;
        }

        .input-group {
            background-color: #7DA0AA;
            border-radius: 8px;
            display: flex;
            align-items: center;
            padding: 0.5rem;
            margin-bottom: 1rem;
        }

        .input-group input {
            border: none;
            background: transparent;
            color: #fff;
            flex: 1;
            outline: none;
            padding-left: 0.5rem;
            font-size: 1rem;
        }

        .input-group i {
            color: #1B1B1B;
            font-size: 1.2rem;
        }

        .login-btn {
            background-color: #A0F4EC;
            color: #000;
            border: none;
            padding: 0.6rem 1.2rem;
            font-weight: bold;
            border-radius: 10px;
            width: 100%;
            cursor: pointer;
            transition: 0.3s;
        }

        .login-btn:hover {
            background-color: #76e4da;
        }

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

    <div class="login-container">
        <h2>Log In</h2>
        <form method="post" action="controllers/login.php" onsubmit="savePassword()">
            <div class="input-group">
                <i class="fa fa-envelope"></i>
                <input type="email" placeholder="Email Address" name="email" required>
            </div>
            <div class="input-group">
                <i class="fa fa-lock"></i>
                <input type="password" name="password" id="floatingPassword" placeholder="Password" required>
                <i class="fa fa-eye-slash" onclick="togglePasswordVisibility()" style="cursor: pointer;"></i>
            </div>

            <button type="submit" class="login-btn" name="login">Log in</button>
        </form>
    </div>

    <script src="https://kit.fontawesome.com/dd50fcb824.js" crossorigin="anonymous"></script>

    <script>
        function savePassword() {
            sessionStorage.setItem("password", document.getElementById("floatingPassword").value);
        }

        function togglePasswordVisibility() {
            const passwordField = document.getElementById("floatingPassword");
            const icon = event.target;
            if (passwordField.type === "password") {
                passwordField.type = "text";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            } else {
                passwordField.type = "password";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            }
        }

        document.querySelector("form").addEventListener("submit", async function(event) {
            event.preventDefault();

            document.getElementById('loadingOverlay').style.display = 'flex';

            const formData = new FormData(this);
            const response = await fetch("controllers/login.php", {
                method: "POST",
                body: formData,
                credentials: "same-origin"
            });

            const data = await response.json();

            if (data.success) {
                window.location.href = "page/home.php";
            } else {
                document.getElementById('loadingOverlay').style.display = 'none';
                alert("Login failed: " + data.error);
            }
        });
    </script>

    <script src="./assets/script/bootstrap.min.js"></script>
</body>

</html>
