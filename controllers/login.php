<?php
require '../vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
session_start();
include '../config/db_connect.php'; // Adjust based on your setup

$key = "4d3c2b1a5f6e7d8c9b0a1e2f3d4c5b6a7e8d9c0b1a2f3e4d5c6b7a8d9c0e1f2"; // Replace with a strong secret key

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check user in database
    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        // Generate JWT token
        $payload = [
            "id" => $user['user_id'],
            "email" => $user['username'],
            "exp" => time() + (60 * 60) // Expires in 1 hour
        ];
        $jwt = JWT::encode($payload, $key, 'HS256');

        // Store JWT in an HTTP-only cookie (secure way to store JWT)
        setcookie("jwt", $jwt, time() + (60 * 60), "/", "", false, true);

        echo json_encode(["success" => true, "message" => "Login successful"]);
    } else {
        echo json_encode(["success" => false, "error" => "Invalid credentials"]);
    }
}
?>
