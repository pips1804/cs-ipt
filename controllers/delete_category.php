<?php
include '../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoryId = $_POST['category_id'];

    // Check if any products belong to this category (optional step)
    $checkQuery = "SELECT COUNT(*) as count FROM products WHERE cat_id = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("i", $categoryId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if ($result['count'] > 0) {
        echo json_encode(['success' => false, 'message' => 'Category cannot be deleted because products exist under it.']);
        exit;
    }

    // Delete the category
    $deleteQuery = "DELETE FROM categories WHERE cat_id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $categoryId);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete category.']);
    }

    $stmt->close();
    $conn->close();
}
