<?php
// admin/manage-staff.php - Manage staff members
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

// Handle Delete
if(isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // Get image path to delete file
    $img_stmt = $conn->prepare("SELECT image FROM staff WHERE id = ?");
    $img_stmt->execute([$id]);
    $staff = $img_stmt->fetch(PDO::FETCH_ASSOC);
    
    if($staff && $staff['image'] != 'default.jpg') {
        $file_path = '../assets/uploads/staff/' . $staff['image'];
        if(file_exists($file_path)) {
            unlink($file_path);
        }
    }
    
    $stmt = $conn->prepare("DELETE FROM staff WHERE id = ?");
    if($stmt->execute([$id])) {
        $message = "Staff member deleted successfully!";
        $messageType = "success";
    } else {
        $message = "Failed to delete staff member.";
        $messageType = "danger";
    }
}

// Handle Status Toggle
if(isset($_GET['toggle'])) {
    $id = $_GET['toggle'];
    $stmt = $conn->prepare("UPDATE staff SET is_active = NOT is_active WHERE id = ?");
    if($stmt->execute([$id])) {
        $message = "Staff status updated successfully!";
        $messageType = "success";
    } else {
        $message = "Failed to update staff status.";
        $messageType = "danger";
    }
}

// Fetch all staff
$staff = $conn->query("SELECT * FROM staff ORDER BY category, display_order, name");
$staff_list = $staff->fetchAll(PDO::FETCH_ASSOC);

// Group staff by category
$grouped = [
    'administration' => [],
    'department_head' => [],
    'teaching' => [],
    'support' => []
];

foreach($staff_list as $s) {
    $grouped[$s['category']][] = $s;
}

$categories = [
    'administration' => 'Administration',
    'department_head' => 'Department Heads',
    'teaching' => 'Teaching Staff',
    'support' => 'Support Staff'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Staff - Stella Maris College Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
        
        .staff-card {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            transition: all 0.3s;
            height: 100%;
        }
        .staff-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        
        .staff-card-img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background: #ecf0f1;
        }
        
        .staff-card-body {
            padding: 20px;
        }
        
        .staff-card-body h5 {
            margin: 0 0 5px;
            color: #1a4d8c;
        }
        
        .staff-card-body .position {
            color: #2e7d32;
            font-weight: 500;
            font-size: 0.9rem;
        }
        
        .staff-card-body .email {
            color: #7f8c8d;
            font-size: 0.85rem;
            margin: 5px 0;
        }
        
        .category-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 0.7rem;
            font-weight: 600;
            margin: 8px 0;
        }
        .badge-admin { background: #3498db; color: white; }
        .badge-head { background: #9b59b6; color: white; }
        .badge-teaching { background: #27ae60; color: white; }
        .badge-support { background: #f39c12; color: white; }
        
        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .status-active { background: #d4edda; color: #155724; }
        .status-inactive { background: #f8d7da; color: #721c24; }
        
        .staff-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #ecf0f1;
        }
        
        .category-section {
            margin-bottom: 40px;
        }
        .category-title {
            font-size: 1.5rem;
            color: #1a4d8c;
            border-bottom: 3px solid #1a4d8c;
            padding-bottom: 10px;
            margin-bottom: 20px;
            display: inline-block;
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
                    <h1 class="h2"><i class="fas fa-users"></i> Manage Staff</h1>
                    <a href="add_staff.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Staff
                    </a>
                </div>
                
                <?php if($message): ?>
                <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <?php foreach($categories as $cat_key => $cat_name): ?>
                    <?php if(count($grouped[$cat_key]) > 0): ?>
                        <div class="category-section">
                            <h2 class="category-title"><?= $cat_name ?></h2>
                            <div class="row g-4">
                                <?php foreach($grouped[$cat_key] as $staff_member): ?>
                                    <div class="col-md-4 col-lg-3">
                                        <div class="staff-card card">
                                            <?php 
                                            $image_path = '../assets/uploads/staff/' . $staff_member['image'];
                                            if(!file_exists($image_path) || !$staff_member['image']) {
                                                $image_path = '../assets/uploads/staff/default.jpg';
                                            }
                                            ?>
                                            <img src="<?= $image_path ?>" alt="<?= htmlspecialchars($staff_member['name']) ?>" class="staff-card-img card-img-top">
                                            <div class="staff-card-body card-body">
                                                <h5><?= htmlspecialchars($staff_member['name']) ?></h5>
                                                <div class="position"><?= htmlspecialchars($staff_member['position']) ?></div>
                                                <?php if($staff_member['email']): ?>
                                                    <div class="email"><i class="fas fa-envelope"></i> <?= htmlspecialchars($staff_member['email']) ?></div>
                                                <?php endif; ?>
                                                <?php if($staff_member['phone']): ?>
                                                    <div class="email"><i class="fas fa-phone"></i> <?= htmlspecialchars($staff_member['phone']) ?></div>
                                                <?php endif; ?>
                                                <div>
                                                    <span class="category-badge badge-<?= $cat_key == 'administration' ? 'admin' : ($cat_key == 'department_head' ? 'head' : ($cat_key == 'teaching' ? 'teaching' : 'support')) ?>">
                                                        <?= $cat_name ?>
                                                    </span>
                                                    <span class="status-badge status-<?= $staff_member['is_active'] ? 'active' : 'inactive' ?>">
                                                        <?= $staff_member['is_active'] ? 'Active' : 'Inactive' ?>
                                                    </span>
                                                </div>
                                                <div class="staff-actions">
                                                    <a href="edit_staff.php?id=<?= $staff_member['id'] ?>" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="?toggle=<?= $staff_member['id'] ?>" class="btn btn-sm <?= $staff_member['is_active'] ? 'btn-secondary' : 'btn-success' ?>">
                                                        <i class="fas <?= $staff_member['is_active'] ? 'fa-eye-slash' : 'fa-eye' ?>"></i>
                                                    </a>
                                                    <a href="?delete=<?= $staff_member['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this staff member?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
                
                <?php if(count($staff_list) == 0): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-users" style="font-size: 4rem; color: #dee2e6;"></i>
                        <p class="mt-3" style="font-size: 1.2rem; color: #7f8c8d;">No staff members added yet.</p>
                        <a href="add-staff.php" class="btn btn-primary">Add Your First Staff Member</a>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>
    
    <!-- Sidebar Toggle Button -->
    <button class="sidebar-toggle" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        $(document).ready(function() {
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