<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require '../config/db_connect.php'; // This should define $conn as mysqli connection

// Encryption setup (use .env for production)
define('ENCRYPTION_KEY', 'Z9fQw7YtRk1pM2vNcH4aXsVbJ6eLgTqW');
define('ENCRYPTION_IV', '1234567890123456');

function decrypt($data) {
    $decrypted = openssl_decrypt($data, 'aes-256-cbc', ENCRYPTION_KEY, 0, ENCRYPTION_IV);
    if ($decrypted === false) {
        error_log('Decryption failed for: ' . $data);
        return 'Decryption error'; // Prevent breaking JSON
    }
    return $decrypted;
}


header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_all') {
    $query = "SELECT * FROM transactions ORDER BY timestamp DESC";
    $result = mysqli_query($conn, $query);

    if ($result) {
    $transactions = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $transactions[] = [
            'user' => $row['user'],
            'action' => $row['action'],
            'description' => $row['description'],
            'timestamp' => $row['timestamp'],
            'tx_hash' => $row['tx_hash']
        ];
    }

    echo json_encode([
        'success' => true,
        'transactions' => $transactions
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . mysqli_error($conn)
    ]);
}


    exit;
}
?>
