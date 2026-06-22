<?php
// admin/add-inventory.php - Add inventory item
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../config/database.php';
$db = new Database();
$conn = $db->getConnection();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $item_name = $_POST['item_name'];
    $category = $_POST['category'];
    $quantity = $_POST['quantity'];
    $condition_status = $_POST['condition_status'];
    $location = $_POST['location'];
    $purchase_date = $_POST['purchase_date'];
    $purchase_price = $_POST['purchase_price'];
    $notes = $_POST['notes'];
    
    $stmt = $conn->prepare("INSERT INTO inventory (item_name, category, quantity, condition_status, location, purchase_date, purchase_price, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    if($stmt->execute([$item_name, $category, $quantity, $condition_status, $location, $purchase_date, $purchase_price, $notes])) {
        $_SESSION['message'] = "Inventory item added successfully!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Failed to add inventory item.";
        $_SESSION['message_type'] = "danger";
    }
}

header("Location: inventory.php");
exit();
?>