<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login Page</title>
    <link rel="stylesheet" href="./assets/style/bootstrap.min.css">
    <link rel="stylesheet" href="./assets/style.css">
    <script src="https://kit.fontawesome.com/dd50fcb824.js" crossorigin="anonymous"></script>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: whitesmoke;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }

        .login-wrapper {
            display: flex;
            width: 900px;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .login-banner {
            flex: 1;
            background: #05445E;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            color: white;
            flex-direction: column;
        }

        .login-banner h1 {
            font-size: 2.5rem;
            font-weight: bold;
        }

        .login-form-container {
            flex: 1;
            padding: 3rem;
            background-color: #ffffff;
        }

        .login-form-container h2 {
            color: #05445E;
            margin-bottom: 2rem;
            font-weight: bold;
            text-align: center;
        }

        .input-group-custom {
            position: relative;
            background-color: #7DA0AA;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
        }

        .input-group-custom i {
            padding: 0.75rem;
            color: #1B1B1B;
            font-size: 1.2rem;
        }

        .input-group-custom input {
            border: none;
            outline: none;
            background: transparent;
            padding: 0.75rem;
            color: #fff;
            font-size: 1rem;
            flex: 1;
        }

        .toggle-password {
            cursor: pointer;
            padding: 0 0.75rem;
            color: #1B1B1B;
        }

        .login-btn {
            background-color: #A0F4EC;
            color: #000;
            border: none;
            padding: 0.75rem;
            font-weight: bold;
            border-radius: 10px;
            width: 100%;
            transition: 0.3s ease;
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
        }

        #loadingOverlay .spinner-border {
            width: 3rem;
            height: 3rem;
        }

        @media screen and (max-width: 768px) {
            .login-wrapper {
                flex-direction: column;
                width: 90%;
            }

            .login-banner {
                display: none;
            }

            .login-form-container {
                padding: 2rem;
            }
        }
    </style>
</head>

<body>
    <!-- Loading Overlay -->
    <div id="loadingOverlay">
        <div class="spinner-border text-primary" role="status"></div>
        <span class="ms-3">Logging in...</span>
    </div>

    <div class="login-wrapper">
        <div class="login-banner">
            <h1>Welcome Back!</h1>
            <p style="max-width: 250px; text-align: center;">Log in to continue and manage your account.</p>
        </div>

        <div class="login-form-container">
            <h2>Login</h2>
            <form method="post" action="controllers/login.php" onsubmit="savePassword()">
                <div class="input-group-custom">
                    <i class="fa fa-envelope"></i>
                    <input type="email" name="email" placeholder="Email Address" required>
                </div>
                <div class="input-group-custom">
                    <i class="fa fa-lock"></i>
                    <input type="password" name="password" id="floatingPassword" placeholder="Password" required>
                    <i class="fa fa-eye-slash toggle-password" onclick="togglePasswordVisibility()"></i>
                </div>
                <button type="submit" class="login-btn" name="login">Log In</button>
            </form>
        </div>
    </div>

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
