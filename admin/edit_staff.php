<?php
// admin/edit-staff.php - Edit staff member
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../config/database.php';
$db = new Database();
$conn = $db->getConnection();

$id = $_GET['id'] ?? 0;
$stmt = $conn->prepare("SELECT * FROM staff WHERE id = ?");
$stmt->execute([$id]);
$staff = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$staff) {
    header("Location: manage-staff.php");
    exit();
}

$message = '';
$messageType = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $title = trim($_POST['title']);
    $position = trim($_POST['position']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $bio = trim($_POST['bio']);
    $category = $_POST['category'];
    $display_order = $_POST['display_order'] ?? 0;
    
    $image = $staff['image'];
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $filename = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if(in_array($ext, $allowed)) {
            $upload_dir = '../assets/uploads/staff/';
            if(!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            // Delete old image if not default
            if($staff['image'] != 'default.jpg' && file_exists($upload_dir . $staff['image'])) {
                unlink($upload_dir . $staff['image']);
            }
            
            $image = time() . '_' . uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image);
        } else {
            $message = "Invalid file type. Allowed: JPG, PNG, GIF, WEBP";
            $messageType = "danger";
        }
    }
    
    if(empty($message)) {
        $stmt = $conn->prepare("UPDATE staff SET name=?, title=?, position=?, email=?, phone=?, bio=?, image=?, category=?, display_order=? WHERE id=?");
        if($stmt->execute([$name, $title, $position, $email, $phone, $bio, $image, $category, $display_order, $id])) {
            header("Location: manage-staff.php?msg=updated");
            exit();
        } else {
            $message = "Failed to update staff member.";
            $messageType = "danger";
        }
    }
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
    <title>Edit Staff - Stella Maris College Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
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
        
        .image-preview {
            max-width: 200px;
            margin-top: 10px;
        }
        .image-preview img {
            width: 100%;
            border-radius: 8px;
            border: 2px solid #ddd;
        }
        
        .current-image {
            max-width: 150px;
            margin: 10px 0;
        }
        .current-image img {
            width: 100%;
            border-radius: 8px;
            border: 2px solid #1a4d8c;
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
                    <h1 class="h2"><i class="fas fa-edit"></i> Edit Staff Member</h1>
                    <a href="manage-staff.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Staff
                    </a>
                </div>
                
                <?php if($message): ?>
                <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Full Name *</label>
                                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($staff['name']) ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Title/Designation *</label>
                                    <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($staff['title']) ?>" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label>Position *</label>
                                <input type="text" name="position" class="form-control" value="<?= htmlspecialchars($staff['position']) ?>" required>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($staff['email']) ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Phone</label>
                                    <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($staff['phone']) ?>">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Category *</label>
                                    <select name="category" class="form-control" required>
                                        <?php foreach($categories as $key => $name): ?>
                                            <option value="<?= $key ?>" <?= $staff['category'] == $key ? 'selected' : '' ?>><?= $name ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Display Order</label>
                                    <input type="number" name="display_order" class="form-control" value="<?= $staff['display_order'] ?>" min="0">
                                    <small class="text-muted">Lower numbers appear first</small>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label>Current Image</label>
                                <div class="current-image">
                                    <?php 
                                    $img_path = '../assets/uploads/staff/' . $staff['image'];
                                    if(file_exists($img_path) && $staff['image']):
                                    ?>
                                        <img src="<?= $img_path ?>" alt="Current">
                                    <?php else: ?>
                                        <img src="../assets/uploads/staff/default.jpg" alt="Default">
                                    <?php endif; ?>
                                </div>
                                <label>Change Image (leave empty to keep current)</label>
                                <input type="file" name="image" class="form-control" accept="image/*" onchange="previewImage(event)">
                                <div class="image-preview" id="imagePreview"></div>
                                <small class="text-muted">Allowed: JPG, PNG, GIF, WEBP</small>
                            </div>
                            
                            <div class="mb-3">
                                <label>Bio / Description</label>
                                <textarea name="bio" class="form-control" rows="4"><?= htmlspecialchars($staff['bio']) ?></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Staff Member
                            </button>
                            <a href="manage-staff.php" class="btn btn-secondary">Cancel</a>
                        </form>
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
    
    <script>
        $(document).ready(function() {
            $('#sidebarToggle').on('click', function() {
                $('.sidebar').toggleClass('show');
            });
            
            $('.sidebar .nav-link').on('click', function() {
                if ($(window).width() <= 768) {
                    $('.sidebar').removeClass('show');
                }
            });
        });
        
        function previewImage(event) {
            const preview = document.getElementById('imagePreview');
            preview.innerHTML = '';
            if(event.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    preview.appendChild(img);
                };
                reader.readAsDataURL(event.target.files[0]);
            }
        }
    </script>
</body>
</html>