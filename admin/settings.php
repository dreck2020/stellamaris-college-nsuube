<?php
// admin/settings.php - System settings with user management
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
require_once '../config/database.php';
$db = new Database();
$conn = $db->getConnection();

$message = '';
$messageType = '';

// Get all users
$users = $conn->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

// Handle Add User
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_user'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $full_name = trim($_POST['full_name']);
    $password = $_POST['password'];
    $role = $_POST['role'];
    
    $errors = [];
    
    // Validate
    if(empty($username)) $errors[] = "Username is required";
    if(empty($email)) $errors[] = "Email is required";
    if(empty($full_name)) $errors[] = "Full name is required";
    if(strlen($password) < 6) $errors[] = "Password must be at least 6 characters";
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format";
    
    // Check if username or email exists
    $checkStmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $checkStmt->execute([$username, $email]);
    if($checkStmt->fetch()) {
        $errors[] = "Username or email already exists";
    }
    
    if(empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash, full_name, role) VALUES (?, ?, ?, ?, ?)");
        if($stmt->execute([$username, $email, $hashedPassword, $full_name, $role])) {
            $message = "User created successfully!";
            $messageType = "success";
            // Refresh users list
            $users = $conn->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $message = "Failed to create user.";
            $messageType = "danger";
        }
    } else {
        $message = implode("<br>", $errors);
        $messageType = "danger";
    }
}

// Handle Edit User
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_user'])) {
    $user_id = $_POST['user_id'];
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    
    $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ?, role = ? WHERE id = ?");
    if($stmt->execute([$full_name, $email, $role, $user_id])) {
        $message = "User updated successfully!";
        $messageType = "success";
        $users = $conn->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $message = "Failed to update user.";
        $messageType = "danger";
    }
}

// Handle Reset Password
if(isset($_GET['reset_password'])) {
    $user_id = $_GET['reset_password'];
    $new_password = 'password123';
    $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
    if($stmt->execute([$hashedPassword, $user_id])) {
        $message = "Password reset to: password123";
        $messageType = "success";
    } else {
        $message = "Failed to reset password.";
        $messageType = "danger";
    }
}

// Handle Delete User
if(isset($_GET['delete_user'])) {
    $user_id = $_GET['delete_user'];
    
    // Prevent deleting own account
    if($user_id == $_SESSION['user_id']) {
        $message = "You cannot delete your own account!";
        $messageType = "danger";
    } else {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        if($stmt->execute([$user_id])) {
            $message = "User deleted successfully!";
            $messageType = "success";
            $users = $conn->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $message = "Failed to delete user.";
            $messageType = "danger";
        }
    }
}

// Update general settings
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_general'])) {
    $school_name = $_POST['school_name'];
    $school_email = $_POST['school_email'];
    $school_phone = $_POST['school_phone'];
    $school_address = $_POST['school_address'];
    $school_motto = $_POST['school_motto'];
    
    $stmt = $conn->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
    $stmt->execute([$school_name, 'school_name']);
    $stmt->execute([$school_email, 'school_email']);
    $stmt->execute([$school_phone, 'school_phone']);
    $stmt->execute([$school_address, 'school_address']);
    $stmt->execute([$school_motto, 'school_motto']);
    
    $message = "General settings updated!";
    $messageType = "success";
}

// Update social media settings
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_social'])) {
    $facebook = $_POST['facebook_url'];
    $twitter = $_POST['twitter_url'];
    $instagram = $_POST['instagram_url'];
    $youtube = $_POST['youtube_url'];
    
    $stmt = $conn->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
    $stmt->execute([$facebook, 'facebook_url']);
    $stmt->execute([$twitter, 'twitter_url']);
    $stmt->execute([$instagram, 'instagram_url']);
    $stmt->execute([$youtube, 'youtube_url']);
    
    $message = "Social media settings updated!";
    $messageType = "success";
}

// Change own password
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    $stmt = $conn->prepare("SELECT password_hash FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    if(password_verify($current_password, $user['password_hash'])) {
        if($new_password === $confirm_password && strlen($new_password) >= 6) {
            $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
            $stmt->execute([$new_hash, $_SESSION['user_id']]);
            $message = "Password changed successfully!";
            $messageType = "success";
        } else {
            $message = "Passwords do not match or is too short (min 6 characters)";
            $messageType = "danger";
        }
    } else {
        $message = "Current password is incorrect";
        $messageType = "danger";
    }
}

// Get current settings
$settings = [];
$stmt = $conn->query("SELECT setting_key, setting_value FROM settings");
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $settings[$row['setting_key']] = $row['setting_value'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Stella Maris College Admin</title>
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
        
        .nav-tabs .nav-link {
            color: #1a4d8c;
            font-weight: 500;
        }
        
        .nav-tabs .nav-link.active {
            background-color: #1a4d8c;
            color: white;
            border-color: #1a4d8c;
        }
        
        .user-avatar-sm {
            width: 35px;
            height: 35px;
            background: #1a4d8c;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }
        
        .role-badge {
            font-size: 11px;
            padding: 3px 8px;
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
                    <h1 class="h2">System Settings</h1>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                        <i class="fas fa-user-plus"></i> Add New User
                    </button>
                </div>
                
                <?php if($message): ?>
                <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <!-- Tabs -->
                <ul class="nav nav-tabs mb-4" id="settingsTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab">
                            <i class="fas fa-users"></i> User Management
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab">
                            <i class="fas fa-building"></i> General Settings
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="social-tab" data-bs-toggle="tab" data-bs-target="#social" type="button" role="tab">
                            <i class="fab fa-facebook"></i> Social Media
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button" role="tab">
                            <i class="fas fa-key"></i> Change Password
                        </button>
                    </li>
                </ul>
                
                <div class="tab-content">
                    <!-- User Management Tab -->
                    <div class="tab-pane fade show active" id="users" role="tabpanel">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="fas fa-users"></i> System Users</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped" id="usersTable">
                                        <thead>
                                            <tr>
                                                <th>User</th>
                                                <th>Username</th>
                                                <th>Email</th>
                                                <th>Role</th>
                                                <th>Joined</th>
                                                <th>Last Login</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($users as $user): ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="user-avatar-sm">
                                                            <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                                                        </div>
                                                        <div>
                                                            <strong><?php echo htmlspecialchars($user['full_name']); ?></strong>
                                                        </div>
                                                    </div>
                                                 </div>
                                                <td><?php echo htmlspecialchars($user['username']); ?></div>
                                                <td><?php echo htmlspecialchars($user['email']); ?></div>
                                                <td>
                                                    <span class="badge bg-<?php echo $user['role'] == 'admin' ? 'danger' : 'info'; ?> role-badge">
                                                        <?php echo ucfirst($user['role']); ?>
                                                    </span>
                                                 </div>
                                                <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></div>
                                                <td><?php echo $user['last_login'] ? date('M d, Y', strtotime($user['last_login'])) : 'Never'; ?></div>
                                                <td>
                                                    <button class="btn btn-sm btn-info" onclick='editUser(<?php echo json_encode($user); ?>)'>
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <a href="?reset_password=<?php echo $user['id']; ?>" class="btn btn-sm btn-warning" onclick="return confirm('Reset password for <?php echo addslashes($user['full_name']); ?>? New password will be: password123')">
                                                        <i class="fas fa-key"></i>
                                                    </a>
                                                    <?php if($user['id'] != $_SESSION['user_id']): ?>
                                                    <a href="?delete_user=<?php echo $user['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete user <?php echo addslashes($user['full_name']); ?>?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                    <?php endif; ?>
                                                 </div>
                                             </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- General Settings Tab -->
                    <div class="tab-pane fade" id="general" role="tabpanel">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="fas fa-building"></i> General Settings</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <div class="mb-3">
                                        <label>School Name</label>
                                        <input type="text" name="school_name" class="form-control" value="<?php echo htmlspecialchars($settings['school_name'] ?? 'Stella Maris College Nsuube'); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label>School Motto</label>
                                        <input type="text" name="school_motto" class="form-control" value="<?php echo htmlspecialchars($settings['school_motto'] ?? 'Empowering Young Women Through Quality Education'); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label>School Email</label>
                                        <input type="email" name="school_email" class="form-control" value="<?php echo htmlspecialchars($settings['school_email'] ?? 'info@stellamaris.edu.ug'); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label>School Phone</label>
                                        <input type="text" name="school_phone" class="form-control" value="<?php echo htmlspecialchars($settings['school_phone'] ?? '+256 123 456 789'); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label>School Address</label>
                                        <textarea name="school_address" class="form-control" rows="3"><?php echo htmlspecialchars($settings['school_address'] ?? 'P.O. Box 123, Nsuube, Uganda'); ?></textarea>
                                    </div>
                                    <button type="submit" name="update_general" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Save Settings
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Social Media Tab -->
                    <div class="tab-pane fade" id="social" role="tabpanel">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="fas fa-share-alt"></i> Social Media Links</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <div class="mb-3">
                                        <label><i class="fab fa-facebook text-primary"></i> Facebook URL</label>
                                        <input type="url" name="facebook_url" class="form-control" value="<?php echo htmlspecialchars($settings['facebook_url'] ?? ''); ?>" placeholder="https://facebook.com/yourpage">
                                    </div>
                                    <div class="mb-3">
                                        <label><i class="fab fa-twitter text-info"></i> Twitter URL</label>
                                        <input type="url" name="twitter_url" class="form-control" value="<?php echo htmlspecialchars($settings['twitter_url'] ?? ''); ?>" placeholder="https://twitter.com/yourhandle">
                                    </div>
                                    <div class="mb-3">
                                        <label><i class="fab fa-instagram text-danger"></i> Instagram URL</label>
                                        <input type="url" name="instagram_url" class="form-control" value="<?php echo htmlspecialchars($settings['instagram_url'] ?? ''); ?>" placeholder="https://instagram.com/yourprofile">
                                    </div>
                                    <div class="mb-3">
                                        <label><i class="fab fa-youtube text-danger"></i> YouTube URL</label>
                                        <input type="url" name="youtube_url" class="form-control" value="<?php echo htmlspecialchars($settings['youtube_url'] ?? ''); ?>" placeholder="https://youtube.com/yourchannel">
                                    </div>
                                    <button type="submit" name="update_social" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Save Social Links
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Change Password Tab -->
                    <div class="tab-pane fade" id="password" role="tabpanel">
                        <div class="card">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0"><i class="fas fa-key"></i> Change Your Password</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <div class="mb-3">
                                        <label>Current Password</label>
                                        <input type="password" name="current_password" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label>New Password</label>
                                        <input type="password" name="new_password" class="form-control" required>
                                        <small class="text-muted">Minimum 6 characters</small>
                                    </div>
                                    <div class="mb-3">
                                        <label>Confirm New Password</label>
                                        <input type="password" name="confirm_password" class="form-control" required>
                                    </div>
                                    <button type="submit" name="change_password" class="btn btn-warning">
                                        <i class="fas fa-key"></i> Change Password
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <!-- Sidebar Toggle Button -->
    <button class="sidebar-toggle" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>
    
    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="add_user" value="1">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><i class="fas fa-user-plus"></i> Add New User</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Full Name *</label>
                            <input type="text" name="full_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Username *</label>
                            <input type="text" name="username" class="form-control" required>
                            <small class="text-muted">Letters, numbers, and underscore only</small>
                        </div>
                        <div class="mb-3">
                            <label>Email *</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Password *</label>
                            <input type="password" name="password" class="form-control" required>
                            <small class="text-muted">Minimum 6 characters</small>
                        </div>
                        <div class="mb-3">
                            <label>Role *</label>
                            <select name="role" class="form-control" required>
                                <option value="editor">Editor (Can manage content)</option>
                                <option value="admin">Administrator (Full access)</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="edit_user" value="1">
                    <input type="hidden" name="user_id" id="edit_user_id">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title"><i class="fas fa-user-edit"></i> Edit User</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Full Name</label>
                            <input type="text" name="full_name" id="edit_full_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" id="edit_email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Role</label>
                            <select name="role" id="edit_role" class="form-control" required>
                                <option value="editor">Editor (Can manage content)</option>
                                <option value="admin">Administrator (Full access)</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#usersTable').DataTable({
                order: [[4, 'desc']],
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
        
        function editUser(user) {
            $('#edit_user_id').val(user.id);
            $('#edit_full_name').val(user.full_name);
            $('#edit_email').val(user.email);
            $('#edit_role').val(user.role);
            $('#editUserModal').modal('show');
        }
    </script>
</body>
</html>