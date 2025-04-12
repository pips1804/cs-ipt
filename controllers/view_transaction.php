<?php
require '../config/db_connect.php'; // This should define $conn as mysqli connection

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_all') {
    $query = "SELECT * FROM transactions ORDER BY timestamp DESC";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $transactions = [];

        while ($row = mysqli_fetch_assoc($result)) {
            $transactions[] = $row;
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

