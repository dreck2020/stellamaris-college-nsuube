<?php
// download-user-file.php - Dedicated download for user-submitted documents
session_start();
require_once 'config/database.php';

$db = new Database();
$conn = $db->getConnection();

// Get document ID from URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if($id == 0) {
    die("Invalid document ID. Please go back and try again.");
}

// ========== FETCH DOCUMENT FROM DATABASE ==========
$stmt = $conn->prepare("SELECT * FROM user_documents WHERE id = ?");
$stmt->execute([$id]);
$file = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$file) {
    die("Document not found in database. ID: " . $id);
}

// ========== BUILD THE FILE PATH ==========
// Try multiple path combinations
$paths_to_try = [
    __DIR__ . '/' . $file['file_path'],
    $_SERVER['DOCUMENT_ROOT'] . '/' . $file['file_path'],
    __DIR__ . '/' . str_replace('assets/', '', $file['file_path']),
    __DIR__ . '/assets/uploads/documents/' . basename($file['file_path'])
];

$found_path = null;
foreach($paths_to_try as $path) {
    if(file_exists($path) && is_readable($path)) {
        $found_path = $path;
        break;
    }
}

// ========== CHECK IF FILE EXISTS ==========
if(!$found_path) {
    echo "<h2>File Not Found</h2>";
    echo "<p><strong>Document ID:</strong> " . $id . "</p>";
    echo "<p><strong>Title:</strong> " . htmlspecialchars($file['title']) . "</p>";
    echo "<p><strong>User:</strong> " . htmlspecialchars($file['user_name']) . "</p>";
    echo "<p><strong>Database path:</strong> " . htmlspecialchars($file['file_path']) . "</p>";
    echo "<p><strong>Paths tried:</strong></p><ul>";
    foreach($paths_to_try as $path) {
        echo "<li><code>" . htmlspecialchars($path) . "</code> - " . (file_exists($path) ? 'exists' : 'not found') . "</li>";
    }
    echo "</ul>";
    echo "<p><a href='admin/manage-downloads.php'>← Back to Downloads</a></p>";
    exit();
}

// ========== FORCE DOWNLOAD ==========
// Get file info
$extension = pathinfo($found_path, PATHINFO_EXTENSION);
$filename = $file['title'] . '.' . $extension;
$filesize = filesize($found_path);

// Clear ALL output buffers
while (ob_get_level()) {
    ob_end_clean();
}

// Set headers
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . $filesize);

// Send file
readfile($found_path);
exit();
?>