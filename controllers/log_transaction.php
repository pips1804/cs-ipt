<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include '../config/db_connect.php'; // Make sure this connects to the right DB

    $action = $_POST['action'];
    $description = $_POST['description'];
    $user = $_POST['username'];

    $stmt = $conn->prepare("INSERT INTO transactions (user, action, description, timestamp) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("sss", $user, $action, $description);

    if ($stmt->execute()) {
        echo json_encode(['message' => 'Log inserted']);
    } else {
        echo json_encode(['message' => 'Log insert failed']);
    }

    $stmt->close();
    $conn->close();
}
?>
