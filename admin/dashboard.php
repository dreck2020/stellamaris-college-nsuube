<?php
// admin/dashboard.php - Updated with logout header
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Store the requested URL to redirect after login
    $_SESSION['redirect_after_login'] = 'dashboard.php';
    header("Location: login.php");
    exit();
}

require_once '../config/database.php';
$db = new Database();
$conn = $db->getConnection();

// Get statistics
$stats = [];
$stats['total_students'] = $conn->query("SELECT COUNT(*) FROM alumni")->fetchColumn();
$stats['total_news'] = $conn->query("SELECT COUNT(*) FROM news")->fetchColumn();
$stats['total_alumni'] = $conn->query("SELECT COUNT(*) FROM alumni")->fetchColumn();
$stats['total_messages'] = $conn->query("SELECT COUNT(*) FROM contact_messages WHERE status='unread'")->fetchColumn();
$stats['total_inventory'] = $conn->query("SELECT SUM(quantity) FROM inventory")->fetchColumn();

// Get recent activity
$recentNews = $conn->query("SELECT * FROM news ORDER BY published_date DESC LIMIT 5")->fetchAll();
$recentMessages = $conn->query("SELECT * FROM contact_messages WHERE status='unread' ORDER BY created_at DESC LIMIT 5")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Stella Maris College Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary-color: #1a4d8c;
            --primary-dark: #0d3b6b;
            --success: #28a745;
            --warning: #ffc107;
            --danger: #dc3545;
            --info: #17a2b8;
        }
        
        body {
            background: #f0f2f5;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        
        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 15px;
        }
        
        .stat-icon.primary { background: rgba(26,77,140,0.1); color: var(--primary-color); }
        .stat-icon.success { background: rgba(40,167,69,0.1); color: var(--success); }
        .stat-icon.warning { background: rgba(255,193,7,0.1); color: var(--warning); }
        .stat-icon.danger { background: rgba(220,53,69,0.1); color: var(--danger); }
        
        .stat-value {
            font-size: 32px;
            font-weight: 700;
            margin: 10px 0 5px;
        }
        
        .stat-label {
            color: #666;
            font-size: 14px;
            margin: 0;
        }
        
        .stat-change {
            font-size: 12px;
            margin-top: 10px;
        }
        
        .stat-change.positive { color: var(--success); }
        .stat-change.negative { color: var(--danger); }
        
        .content-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .content-card h5 {
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .btn-action {
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 12px;
        }
        
        @media (max-width: 768px) {
            .stat-value {
                font-size: 24px;
            }
            
            .content-card {
                padding: 16px;
            }
        }
        
        /* Mobile sidebar toggle */
        .sidebar-toggle {
            display: none;
            position: fixed;
            right: 20px;
            width: 50px;
            height: 50px;
            top: 15px;  
           left: 15px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 50%;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 1000;
            cursor: pointer;
        }
        
        @media (max-width: 768px) {
            .sidebar-toggle {
                display: flex;
                align-items: center;
                justify-content: center;
            }
        }
        
        .admin-main-content {
            transition: margin-left 0.3s;
        }
        
        @media (max-width: 768px) {
            .admin-main-content {
                margin-left: 0 !important;
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
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
                    <h1 class="h2">Dashboard</h1>
                    <div class="btn-toolbar">
                        <span class="text-muted">
                            <i class="far fa-calendar-alt"></i> 
                            <?php echo date('l, F j, Y'); ?>
                        </span>
                    </div>
                </div>
                
                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3 col-6 mb-3">
                        <div class="stat-card" onclick="window.location.href='students.php'">
                            <div class="stat-icon primary">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <div class="stat-value"><?php echo number_format($stats['total_students']); ?></div>
                            <p class="stat-label">Total Students</p>
                            <div class="stat-change positive">
                                <i class="fas fa-arrow-up"></i> 12% from last year
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="stat-card" onclick="window.location.href='manage-news.php'">
                            <div class="stat-icon success">
                                <i class="fas fa-newspaper"></i>
                            </div>
                            <div class="stat-value"><?php echo $stats['total_news']; ?></div>
                            <p class="stat-label">News Posts</p>
                            <div class="stat-change positive">
                                <i class="fas fa-arrow-up"></i> 5 this month
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="stat-card" onclick="window.location.href='manage-alumni.php'">
                            <div class="stat-icon warning">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-value"><?php echo number_format($stats['total_alumni']); ?></div>
                            <p class="stat-label">Alumni Registered</p>
                            <div class="stat-change positive">
                                <i class="fas fa-arrow-up"></i> 28 new this year
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="stat-card" onclick="window.location.href='messages.php'">
                            <div class="stat-icon danger">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="stat-value"><?php echo $stats['total_messages']; ?></div>
                            <p class="stat-label">Unread Messages</p>
                            <div class="stat-change">
                                <i class="fas fa-clock"></i> Needs attention
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Charts and Recent Activity -->
                <div class="row">
                    <div class="col-lg-8 mb-4">
                        <div class="content-card">
                            <h5>Website Analytics</h5>
                            <canvas id="visitorChart" height="300"></canvas>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-4">
                        <div class="content-card">
                            <h5>Quick Actions</h5>
                            <div class="d-grid gap-2">
                                <button class="btn btn-primary" onclick="window.location.href='add-news.php'">
                                    <i class="fas fa-plus"></i> Add News
                                </button>
                                <button class="btn btn-success" onclick="window.location.href='upload-gallery.php'">
                                    <i class="fas fa-upload"></i> Upload to Gallery
                                </button>
                                <button class="btn btn-info" onclick="window.location.href='add-inventory.php'">
                                    <i class="fas fa-box"></i> Add Inventory
                                </button>
                                <button class="btn btn-warning" onclick="window.location.href='export-report.php'">
                                    <i class="fas fa-file-excel"></i> Export Report
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent News -->
                <div class="row">
                    <div class="col-md-7 mb-4">
                        <div class="content-card">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">Recent News & Updates</h5>
                                <a href="manage-news.php" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($recentNews as $news): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars(substr($news['title'], 0, 40)); ?>…</td>
                                            <td><?php echo date('M d, Y', strtotime($news['published_date'])); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $news['status'] == 'published' ? 'success' : 'warning'; ?>">
                                                    <?php echo $news['status']; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-info btn-action" onclick="editNews(<?php echo $news['id']; ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger btn-action" onclick="deleteNews(<?php echo $news['id']; ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent Messages -->
                    <div class="col-md-5 mb-4">
                        <div class="content-card">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">Recent Messages</h5>
                                <a href="messages.php" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                            <?php foreach($recentMessages as $message): ?>
                            <div class="message-item mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between">
                                    <strong><?php echo htmlspecialchars($message['name']); ?></strong>
                                    <small class="text-muted"><?php echo time_ago($message['created_at']); ?></small>
                                </div>
                                <p class="mb-1 small"><?php echo htmlspecialchars(substr($message['message'], 0, 60)); ?>…</p>
                                <button class="btn btn-sm btn-link p-0" onclick="viewMessage(<?php echo $message['id']; ?>)">
                                    View Message
                                </button>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <button class="sidebar-toggle" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    
    <script>
        // Visitor Chart
        const ctx = document.getElementById('visitorChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Website Visitors',
                    data: [1200, 1500, 1800, 2200, 2500, 2800, 3000, 3200, 3500, 3800, 4000, 4500],
                    borderColor: '#1a4d8c',
                    backgroundColor: 'rgba(26, 77, 140, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                }
            }
        });
        
        // Sidebar toggle for mobile
        $('#sidebarToggle').on('click', function() {
            $('.sidebar').toggleClass('show');
        });
        
        function editNews(id) {
            window.location.href = `edit-news.php?id=${id}`;
        }
        
        function deleteNews(id) {
            if(confirm('Are you sure you want to delete this news item?')) {
                $.post('ajax/delete-news.php', {id: id}, function(response) {
                    if(response.success) {
                        location.reload();
                    } else {
                        alert('Error deleting news');
                    }
                }, 'json');
            }
        }
        
        function viewMessage(id) {
            window.location.href = `view-message.php?id=${id}`;
        }
    </script>
</body>
</html>

<?php
// Helper function for time ago
function time_ago($datetime) {
    $timestamp = strtotime($datetime);
    $difference = time() - $timestamp;
    
    if ($difference < 60) return 'Just now';
    if ($difference < 3600) return round($difference / 60) . ' minutes ago';
    if ($difference < 86400) return round($difference / 3600) . ' hours ago';
    if ($difference < 2592000) return round($difference / 86400) . ' days ago';
    return date('M d, Y', $timestamp);
}
?>