<?php
// admin/add-alumni.php - Add new alumni record
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../config/database.php';
$db = new Database();
$conn = $db->getConnection();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $graduation_year = $_POST['graduation_year'];
    $marital_status = $_POST['marital_status'];
    $profession = $_POST['profession'];
    $employment_status = $_POST['employment_status'];
    
    $stmt = $conn->prepare("INSERT INTO alumni (full_name, email, phone, graduation_year, marital_status, profession, employment_status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if($stmt->execute([$full_name, $email, $phone, $graduation_year, $marital_status, $profession, $employment_status])) {
        $_SESSION['message'] = "Alumni added successfully!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Failed to add alumni.";
        $_SESSION['message_type'] = "danger";
    }
}

header("Location: manage-alumni.php");
exit();
?>