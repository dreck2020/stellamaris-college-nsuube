<?php
// download.php - Handle file downloads with count tracking
session_start();
require_once 'config/database.php';

$db = new Database();
$conn = $db->getConnection();

// Get download ID from URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if($id > 0) {
    // Get file information
    $stmt = $conn->prepare("SELECT * FROM downloads WHERE id = ?");
    $stmt->execute([$id]);
    $file = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if($file) {
        // Update download count
        $updateStmt = $conn->prepare("UPDATE downloads SET download_count = download_count + 1 WHERE id = ?");
        $updateStmt->execute([$id]);
        
        // Get the file path
        $file_path = $file['file_path'];
        $full_path = __DIR__ . '/' . $file_path;
        
        // Check if file exists
        if(file_exists($full_path)) {
            // FIXED: Get extension from the actual stored file, not from the title
            $file_extension = pathinfo($file['file_path'], PATHINFO_EXTENSION);
            $file_name = $file['title'] . '.' . $file_extension;
            
            // Set headers for download
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $file_name . '"');
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($full_path));
            
            // Clear output buffer
            ob_clean();
            flush();
            
            // Read the file and send to output
            readfile($full_path);
            exit();
        } else {
            // File not found error
            echo '<!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>File Not Found - Stella Maris College</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
            </head>
            <body class="bg-light">
                <div class="container mt-5">
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <div class="card text-center">
                                <div class="card-body py-5">
                                    <i class="fas fa-file-excel fa-4x text-danger mb-3"></i>
                                    <h3>File Not Found</h3>
                                    <p>The requested file could not be found on the server.</p>
                                    <a href="downloads.php" class="btn btn-primary">Back to Downloads</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </body>
            </html>';
            exit();
        }
    } else {
        // Invalid download ID
        header("Location: downloads.php");
        exit();
    }
} else {
    // No ID provided
    header("Location: downloads.php");
    exit();
}
?>