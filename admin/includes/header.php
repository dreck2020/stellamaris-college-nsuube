<?php
// admin/includes/header.php - Updated
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$full_name = $_SESSION['full_name'] ?? 'Admin';
$role = $_SESSION['role'] ?? 'editor';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Stella Maris College</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .admin-header {
            background: linear-gradient(135deg, #1a4d8c, #0d3b6b);
            color: white;
            padding: 15px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .user-dropdown {
            position: relative;
            cursor: pointer;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 16px;
            background: rgba(255,255,255,0.1);
            border-radius: 30px;
            transition: background 0.3s;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 18px;
        }
        
        .dropdown-menu-custom {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            min-width: 200px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            margin-top: 10px;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s;
            z-index: 1001;
        }
        
        .user-dropdown:hover .dropdown-menu-custom {
            opacity: 1;
            visibility: visible;
        }
        
        .dropdown-menu-custom a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: #333;
            text-decoration: none;
            transition: background 0.3s;
            border-bottom: 1px solid #eee;
        }
        
        .dropdown-menu-custom a:last-child {
            border-bottom: none;
        }
        
        .dropdown-menu-custom a:hover {
            background: #f5f5f5;
        }
        
        .dropdown-menu-custom i {
            width: 20px;
            color: #1a4d8c;
        }
        
        .logout-btn {
            color: #dc3545 !important;
        }
        
        .logout-btn i {
            color: #dc3545 !important;
        }
        
        @media (max-width: 768px) {
            .user-details {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col">
                    <div class="d-flex align-items-center">
                        <img src="../assets/images/logo.png" alt="Logo" height="40" class="me-3">
                        <div>
                            <h5 class="mb-0">Stella Maris College</h5>
                            <small>Admin Dashboard</small>
                        </div>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="user-dropdown">
                        <div class="user-info">
                            <div class="user-avatar">
                                <?php echo strtoupper(substr($full_name, 0, 1)); ?>
                            </div>
                            <div class="user-details">
                                <div class="user-name"><?php echo htmlspecialchars($full_name); ?></div>
                                <div class="user-role"><?php echo ucfirst($role); ?></div>
                            </div>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="dropdown-menu-custom">
                            <a href="profile.php">
                                <i class="fas fa-user-circle"></i> My Profile
                            </a>
                            <a href="settings.php">
                                <i class="fas fa-cog"></i> Settings
                            </a>
                            <hr class="my-1">
                            <a href="../index.php" target="_blank">
                                <i class="fas fa-globe"></i> View Website
                            </a>
                            <a href="logout.php" class="logout-btn">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>