<?php
// admin/edit-inventory.php - Edit inventory item
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../config/database.php';
$db = new Database();
$conn = $db->getConnection();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $item_name = $_POST['item_name'];
    $category = $_POST['category'];
    $quantity = $_POST['quantity'];
    $condition_status = $_POST['condition_status'];
    $location = $_POST['location'];
    $purchase_date = $_POST['purchase_date'];
    $purchase_price = $_POST['purchase_price'];
    $notes = $_POST['notes'];
    
    $stmt = $conn->prepare("UPDATE inventory SET item_name=?, category=?, quantity=?, condition_status=?, location=?, purchase_date=?, purchase_price=?, notes=? WHERE id=?");
    if($stmt->execute([$item_name, $category, $quantity, $condition_status, $location, $purchase_date, $purchase_price, $notes, $id])) {
        $_SESSION['message'] = "Inventory item updated successfully!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Failed to update inventory item.";
        $_SESSION['message_type'] = "danger";
    }
}

header("Location: inventory.php");
exit();
?>