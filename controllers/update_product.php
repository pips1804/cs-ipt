<?php
include '../config/db_connect.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $product_id = $_POST['productId'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $stock = $_POST['stock_quantity'];
    $price = round($_POST['price'], 2); // Ensures only 2 decimal places
    $category_id = $_POST['category_id'];

    // Debugging Logs
    error_log("Updating Product: ID=$product_id, Name=$name, Desc=$description, Stock=$stock, Price=$price, Cat=$category_id");

    $stmt = $conn->prepare("UPDATE products SET name=?, description=?, stock_quantity=?, price=?, cat_id=? WHERE prod_id=?");
    $stmt->bind_param("ssiddi", $name, $description, $stock, $price, $category_id, $product_id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Product updated successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error updating product: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
}
