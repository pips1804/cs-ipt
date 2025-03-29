<?php
include '../config/db_connect.php';

$name = $_POST['name'];
$description = $_POST['description'];
$stock = $_POST['stock_quantity'];
$price = $_POST['price'];
$category_id = $_POST['category_id'];

$query = "INSERT INTO products (name, description, stock_quantity, price, cat_id) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("ssidi", $name, $description, $stock, $price, $category_id);
$stmt->execute();

echo "Product added successfully";
