<?php

if (isset($_GET['logout'])) {
    setcookie("jwt", "", time() - 3600, "/", "", false, true); // Destroy cookie
    header("Location: ../index.php");
    exit();
}
require '../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$key = "4d3c2b1a5f6e7d8c9b0a1e2f3d4c5b6a7e8d9c0b1a2f3e4d5c6b7a8d9c0e1f2";

// Logout handler

// Check if JWT cookie exists
if (!isset($_COOKIE['jwt']) || empty($_COOKIE['jwt'])) {
    header("Location: ../index.php");
    exit();
}

$token = $_COOKIE['jwt'];

try {
    // Decode the JWT
    $decoded = JWT::decode($token, new Key($key, 'HS256'));

    // Check expiration
    if ($decoded->exp < time()) {
        setcookie("jwt", "", time() - 3600, "/", "", false, true); // Delete expired token
        header("Location: ../index.php");
        exit();
    }

    // Extract user email
    $user_email = $decoded->email;
} catch (Exception $e) {
    // If decoding fails, redirect to login
    setcookie("jwt", "", time() - 3600, "/", "", false, true); // Remove invalid token
    header("Location: ../index.php");
    exit();
}
