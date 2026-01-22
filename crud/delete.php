<?php
require_once '../config/database.php';

// Get product ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = intval($_GET['id']);

// Get database connection
$database = new Database();
$conn = $database->getConnection();

// Get product to check if image exists
$stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $stmt->close();
    $database->closeConnection();
    header("Location: index.php");
    exit();
}

$product = $result->fetch_assoc();
$image_path = $product['image'];
$stmt->close();

// Delete product
$stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    // Delete image file if exists
    if ($image_path && file_exists('../' . $image_path)) {
        unlink('../' . $image_path);
    }
    
    $stmt->close();
    $database->closeConnection();
    header("Location: index.php?success=deleted");
    exit();
} else {
    $stmt->close();
    $database->closeConnection();
    header("Location: index.php?error=delete_failed");
    exit();
}
?>
