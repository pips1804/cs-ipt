<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login Page</title>
    <link rel="stylesheet" href="./assets/style.css">
    <link rel="stylesheet" href="./assets/bootstrap.min.css">
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
                    id="floatingInput" placeholder="Email address" required name="email"
                    style="background-color: #303841 !important; color: #EEEEEE;">
                <label for="floatingInput">Email address</label>
            </div>
            <div class="form-floating mb-3">
                <input type="password" class="form-control "
                    id="floatingPassword" placeholder="Password" required name="password"
                    style="background-color: #303841 !important; color: #EEEEEE;">
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
    </script>
    <script src="./assets/bootstrap.min.js"></script>



</body>

</html>
