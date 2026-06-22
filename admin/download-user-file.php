<?php
// download-user-file.php - Dedicated download for user-submitted documents
session_start();
require_once 'config/database.php';

$db = new Database();
$conn = $db->getConnection();

// Get document ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if($id == 0) {
    die("Invalid document ID.");
}

// Get file information from user_documents table
$stmt = $conn->prepare("SELECT * FROM user_documents WHERE id = ?");
$stmt->execute([$id]);
$file = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$file) {
    die("Document not found in database.");
}

// ========== BUILD THE FILE PATH ==========
// The path from the test page: /home/vol13_5/infinityfree.com/if0_41586402/stellamariscollegensuube.xo.je/htdocs/assets/uploads/documents/filename.pdf
$full_path = __DIR__ . '/' . $file['file_path'];

// Debug: If file not found, try alternative
if(!file_exists($full_path)) {
    // Try with document root
    $full_path = $_SERVER['DOCUMENT_ROOT'] . '/' . $file['file_path'];
}

if(!file_exists($full_path)) {
    // Try without 'assets/' prefix
    $alt_path = str_replace('assets/', '', $file['file_path']);
    $full_path = __DIR__ . '/' . $alt_path;
}

if(!file_exists($full_path)) {
    // Try just the filename in uploads directory
    $filename_only = basename($file['file_path']);
    $full_path = __DIR__ . '/assets/uploads/documents/' . $filename_only;
}

// ========== CHECK IF FILE EXISTS ==========
if(!file_exists($full_path) || !is_readable($full_path)) {
    echo "<h2>File Not Found</h2>";
    echo "<p><strong>Document ID:</strong> " . $id . "</p>";
    echo "<p><strong>Title:</strong> " . htmlspecialchars($file['title']) . "</p>";
    echo "<p><strong>Database path:</strong> " . htmlspecialchars($file['file_path']) . "</p>";
    echo "<p><strong>Full path tried:</strong> " . htmlspecialchars($full_path) . "</p>";
    echo "<p><strong>Current directory:</strong> " . __DIR__ . "</p>";
    echo "<p><a href='admin/manage-downloads.php'>← Back to Downloads</a></p>";
    exit();
}

// ========== FORCE DOWNLOAD ==========
// Get file info
$extension = pathinfo($full_path, PATHINFO_EXTENSION);
$filename = $file['title'] . '.' . $extension;
$filesize = filesize($full_path);

// Clear ALL output buffers
while (ob_get_level()) {
    ob_end_clean();
}

// Set download headers
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . $filesize);

// Send file
readfile($full_path);
exit();
?>