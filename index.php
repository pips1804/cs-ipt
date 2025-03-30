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
    </style>
</head>

<body class="d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow-sm p-4" style="width: 300px;">
        <h2 class="text-center mb-3">Login</h2>


        <form method="post" action="controllers/login.php" onsubmit="savePassword()">
            <div class="form-floating mb-3">
                <input type="email" class="form-control"
                    id="floatingInput" placeholder="Email address" required name="email">
                <label for="floatingInput">Email address</label>
            </div>
            <div class="form-floating mb-3">
                <input type="password" class="form-control "
                    id="floatingPassword" placeholder="Password" required name="password">
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
        window.onload = function() {
            if (sessionStorage.getItem("password")) {
                document.getElementById("floatingPassword").value = sessionStorage.getItem("password");
                sessionStorage.removeItem("password"); // Clear after setting
            }
        }

        document.querySelector("form").addEventListener("submit", async function(event) {
            event.preventDefault();

            const formData = new FormData(this);
            const response = await fetch("controllers/login.php", {
                method: "POST",
                body: formData,
                credentials: "same-origin" // Ensure cookies are sent
            });

            const data = await response.json();

            if (data.success) {
                alert("Login successful!");
                window.location.href = "page/home.php"; // Redirect after login
            } else {
                alert("Login failed: " + data.error); // Show specific error
            }
        });
    </script>
    <script src="./assets/script/bootstrap.min.js"></script>



</body>

</html>
