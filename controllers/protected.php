<?php
include 'verify_token.php';

$headers = apache_request_headers();
$token = $headers['Authorization'] ?? '';

if (!$token || !verifyJWT($token)) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

echo json_encode(["message" => "Welcome to the protected route!"]);
?>
