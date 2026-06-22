<?php
// admin/manage-news.php - Manage all news articles
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../config/database.php';
$db = new Database();
$conn = $db->getConnection();

$message = '';
$messageType = '';

// Handle delete
if(isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM news WHERE id = ?");
    if($stmt->execute([$id])) {
        $message = "News article deleted successfully!";
        $messageType = "success";
    } else {
        $message = "Failed to delete news article.";
        $messageType = "danger";
    }
}

// Handle publish/unpublish
if(isset($_GET['toggle'])) {
    $id = $_GET['toggle'];
    $stmt = $conn->prepare("UPDATE news SET status = IF(status='published', 'draft', 'published') WHERE id = ?");
    $stmt->execute([$id]);
    $message = "Status updated!";
    $messageType = "success";
}

// Get all news
$news = $conn->query("SELECT * FROM news ORDER BY published_date DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage News - Stella Maris College Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Sidebar styles */
        .sidebar {
            background: #1a1a2e !important;
            min-height: 100vh;
            transition: transform 0.3s ease;
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            transition: all 0.3s;
            border-radius: 8px;
            margin: 4px 8px;
        }
        
        .sidebar .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        
        .sidebar .nav-link.active {
            background: #1a4d8c;
            color: white;
        }
        
        .sidebar .nav-link i {
            width: 24px;
            margin-right: 12px;
        }
        
        /* Mobile sidebar toggle */
        .sidebar-toggle {
            display: none;
            position: fixed;
            top: 15px;
            left: 15px;
            width: 45px;
            height: 45px;
            background: #1a4d8c;
            color: white;
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            z-index: 1060;
            cursor: pointer;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                top: 0;
                left: -280px;
                width: 280px;
                height: 100vh;
                z-index: 1050;
                overflow-y: auto;
            }
            
            .sidebar.show {
                left: 0;
            }
            
            .sidebar-toggle {
                display: flex;
            }
            
            .admin-main-content {
                padding-top: 70px !important;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php include 'sidebar.php'; ?>
            
            <main class="col-md-9 col-lg-10 ms-sm-auto px-md-4 admin-main-content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Manage News & Announcements</h1>
                    <a href="add-news.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New News
                    </a>
                </div>
                
                <?php if($message): ?>
                <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-newspaper"></i> All News Articles</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped" id="newsTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Image</th>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Date</th>
                                    <th>Views</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($news as $item): ?>
                                <tr>
                                    <td><?php echo $item['id']; ?></td>
                                    <td>
                                        <?php if($item['featured_image']): ?>
                                            <img src="../<?php echo $item['featured_image']; ?>" width="50" height="50" style="object-fit: cover; border-radius: 5px;">
                                        <?php else: ?>
                                            <i class="fas fa-image fa-2x text-muted"></i>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars(substr($item['title'], 0, 50)); ?>...</td>
                                    <td><span class="badge bg-info"><?php echo ucfirst($item['category'] ?: 'General'); ?></span></td>
                                    <td><?php echo date('M d, Y', strtotime($item['published_date'])); ?></td>
                                    <td><?php echo number_format($item['views']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $item['status'] == 'published' ? 'success' : 'warning'; ?>">
                                            <?php echo ucfirst($item['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="edit-news.php?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="?toggle=<?php echo $item['id']; ?>" class="btn btn-sm btn-secondary" onclick="return confirm('Change status?')">
                                            <i class="fas fa-<?php echo $item['status'] == 'published' ? 'eye-slash' : 'eye'; ?>"></i>
                                        </a>
                                        <a href="?delete=<?php echo $item['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this news article?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <!-- Sidebar Toggle Button -->
    <button class="sidebar-toggle" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#newsTable').DataTable({
                order: [[0, 'desc']],
                pageLength: 10
            });
            
            // Sidebar toggle for mobile
            $('#sidebarToggle').on('click', function() {
                $('.sidebar').toggleClass('show');
            });
            
            // Close sidebar when clicking a link on mobile
            $('.sidebar .nav-link').on('click', function() {
                if ($(window).width() <= 768) {
                    $('.sidebar').removeClass('show');
                }
            });
        });
    </script>
</body>
</html>