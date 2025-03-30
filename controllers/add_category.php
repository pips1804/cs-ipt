<?php
include '../config/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $categoryName = $_POST['category_name'];

    if (!empty($categoryName)) {
        $query = "INSERT INTO categories (name) VALUES (?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $categoryName);

        if ($stmt->execute()) {
            echo "Category added successfully!";
        } else {
            echo "Error adding category!";
        }
    } else {
        echo "Category name cannot be empty!";
    }
}
