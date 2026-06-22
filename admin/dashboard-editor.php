<?php
// admin/dashboard-editor.php - Limited dashboard for editors
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$isEditor = ($_SESSION['role'] == 'editor');
if(!$isEditor && $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

require_once '../config/database.php';
$db = new Database();
$conn = $db->getConnection();

// Get statistics (limited for editors)
$stats['total_news'] = $conn->query("SELECT COUNT(*) FROM news")->fetchColumn();
$stats['total_alumni'] = $conn->query("SELECT COUNT(*) FROM alumni")->fetchColumn();
$stats['total_events'] = $conn->query("SELECT COUNT(*) FROM events")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editor Dashboard - Stella Maris College</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php include 'sidebar-editor.php'; ?>
            
            <main class="col-md-9 col-lg-10 ms-sm-auto px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Editor Dashboard</h1>
                    <div class="btn-toolbar">
                        <span class="text-muted">
                            <i class="far fa-calendar-alt"></i> 
                            <?php echo date('l, F j, Y'); ?>
                        </span>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <div class="card text-white bg-primary">
                            <div class="card-body">
                                <h5 class="card-title">News Articles</h5>
                                <h2><?php echo number_format($stats['total_news']); ?></h2>
                                <a href="manage-news.php" class="text-white">Manage News →</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card text-white bg-success">
                            <div class="card-body">
                                <h5 class="card-title">Alumni Registered</h5>
                                <h2><?php echo number_format($stats['total_alumni']); ?></h2>
                                <a href="manage-alumni.php" class="text-white">View Alumni →</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card text-white bg-info">
                            <div class="card-body">
                                <h5 class="card-title">Upcoming Events</h5>
                                <h2><?php echo number_format($stats['total_events']); ?></h2>
                                <a href="manage-events.php" class="text-white">Manage Events →</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Quick Actions</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <a href="add-news.php" class="btn btn-outline-primary">
                                        <i class="fas fa-plus"></i> Add News
                                    </a>
                                    <a href="upload-gallery.php" class="btn btn-outline-success">
                                        <i class="fas fa-upload"></i> Upload to Gallery
                                    </a>
                                    <a href="manage-downloads.php" class="btn btn-outline-info">
                                        <i class="fas fa-download"></i> Manage Downloads
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0">Your Information</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Name:</strong> <?php echo htmlspecialchars($_SESSION['full_name']); ?></p>
                                <p><strong>Role:</strong> <?php echo ucfirst($_SESSION['role']); ?></p>
                                <p><strong>Permissions:</strong> Can manage content, cannot access system settings</p>
                                <a href="profile.php" class="btn btn-sm btn-secondary">Edit Profile</a>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>