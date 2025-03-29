<?php
require '../vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$key = "4d3c2b1a5f6e7d8c9b0a1e2f3d4c5b6a7e8d9c0b1a2f3e4d5c6b7a8d9c0e1f2"; // Same secret key used in login.php

function verifyJWT($token) {
    global $key;
    try {
        $decoded = JWT::decode($token, new Key($key, 'HS256'));
        return (array) $decoded; // Return decoded user data
    } catch (Exception $e) {
        return null; // Invalid token
    }
}
?>
