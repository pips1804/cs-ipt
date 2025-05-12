<?php
include '../config/db_connect.php';
define('ENCRYPTION_KEY', 'Z9fQw7YtRk1pM2vNcH4aXsVbJ6eLgTqW');
define('ENCRYPTION_IV', '1234567890123456');

// Remove the encryption here
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action']; // Don't encrypt here
    $description = $_POST['description']; // Don't encrypt here
    $user = $_POST['username']; // Don't encrypt here
    $timestamp = date('Y-m-d H:i:s');

    $data_to_hash = "user=$user;action=$action;description=$description;timestamp=$timestamp;";
    $transaction_hash = hash('sha256', $data_to_hash);

    $stmt = $conn->prepare("INSERT INTO transactions (user, action, description, timestamp, tx_hash) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $user, $action, $description, $timestamp, $transaction_hash);

    if ($stmt->execute()) {
        echo json_encode(['message' => 'Log inserted']);
    } else {
        echo json_encode(['message' => 'Insert failed']);
    }

    $stmt->close();
    $conn->close();
}
?>
