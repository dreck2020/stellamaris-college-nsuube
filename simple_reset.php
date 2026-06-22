<?php
// simple_reset.php - No token required
// Access: http://localhost/stella%20website/simple_reset.php

$host = 'localhost';
$dbname = 'stella_maris_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Reset to default
    $newUsername = 'admin';
    $newPassword = 'Admin@123';
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    
    $checkStmt = $pdo->prepare("SELECT id FROM users WHERE role = 'admin' LIMIT 1");
    $checkStmt->execute();
    $admin = $checkStmt->fetch();
    
    if ($admin) {
        $stmt = $pdo->prepare("UPDATE users SET username = ?, password_hash = ? WHERE id = ?");
        $stmt->execute([$newUsername, $hashedPassword, $admin['id']]);
        echo "<h2 style='color:green'>✅ Admin credentials reset successfully!</h2>";
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, full_name, role) VALUES (?, ?, ?, ?, 'admin')");
        $stmt->execute([$newUsername, 'admin@stellamaris.edu.ug', $hashedPassword, 'System Administrator']);
        echo "<h2 style='color:green'>✅ Admin account created successfully!</h2>";
    }
    
    echo "<div style='background:#f0f0f0; padding:20px; border-radius:10px; margin-top:20px'>";
    echo "<h3>New Login Credentials:</h3>";
    echo "<p><strong>Username:</strong> admin</p>";
    echo "<p><strong>Password:</strong> Admin@123</p>";
    echo "<p><strong>Login URL:</strong> <a href='admin/login.php'>http://localhost/stella%20website/admin/login.php</a></p>";
    echo "</div>";
    echo "<p style='margin-top:20px'><strong>⚠️ IMPORTANT: Delete this file now!</strong></p>";
    
} catch(PDOException $e) {
    echo "<h2 style='color:red'>Error: " . $e->getMessage() . "</h2>";
    echo "<p>Make sure MySQL is running and database exists.</p>";
}
?>