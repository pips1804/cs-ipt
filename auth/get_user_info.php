<?php
require '../vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$key = "4d3c2b1a5f6e7d8c9b0a1e2f3d4c5b6a7e8d9c0b1a2f3e4d5c6b7a8d9c0e1f2";

if (!isset($_COOKIE['jwt'])) {
    echo json_encode(["success" => false, "message" => "No token found"]);
    exit;
}

$jwt = $_COOKIE['jwt'];

try {
    $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
    echo json_encode([
        "success" => true,
        "user" => [
            "id" => $decoded->id,
            "username" => $decoded->email // assuming you stored username in email
        ]
    ]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Invalid token"]);
}
?>
