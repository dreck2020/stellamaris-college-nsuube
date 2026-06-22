<?php
// admin/upload-gallery.php - Upload images to gallery
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

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $uploaded_files = [];
    
    // Handle multiple file uploads
    $files = $_FILES['images'];
    for($i = 0; $i < count($files['name']); $i++) {
        if($files['error'][$i] == 0) {
            $filename = $files['name'][$i];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if(in_array($ext, $allowed)) {
                $new_filename = 'gallery_' . time() . '_' . rand(1000, 9999) . '_' . $i . '.' . $ext;
                $upload_path = '../uploads/gallery/';
                
                if(!file_exists($upload_path)) {
                    mkdir($upload_path, 0777, true);
                }
                
                if(move_uploaded_file($files['tmp_name'][$i], $upload_path . $new_filename)) {
                    $file_path = 'uploads/gallery/' . $new_filename;
                    
                    $stmt = $conn->prepare("INSERT INTO gallery (title, description, file_path, file_type, category) VALUES (?, ?, ?, 'image', ?)");
                    if($stmt->execute([$title, $description, $file_path, $category])) {
                        $uploaded_files[] = $filename;
                    }
                }
            }
        }
    }
    
    if(count($uploaded_files) > 0) {
        $message = count($uploaded_files) . " image(s) uploaded successfully!";
        $messageType = "success";
        echo '<script>setTimeout(function(){ window.location.href = "manage-gallery.php"; }, 2000);</script>';
    } else {
        $message = "Failed to upload images.";
        $messageType = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Gallery Images - Stella Maris College Admin</title>
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
                    <h1 class="h2">Upload Gallery Images</h1>
                    <a href="manage-gallery.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Gallery
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
                        <h5 class="mb-0"><i class="fas fa-upload"></i> Upload Images</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label">Gallery Title</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Category</label>
                                <select name="category" class="form-control" required>
                                    <option value="events">Events</option>
                                    <option value="sports">Sports</option>
                                    <option value="academics">Academics</option>
                                    <option value="spiritual">Spiritual</option>
                                    <option value="campus">Campus Life</option>
                                    <option value="alumni">Alumni</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="3"></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Select Images (Multiple allowed)</label>
                                <input type="file" name="images[]" class="form-control" accept="image/*" multiple required>
                                <small class="text-muted">You can select multiple images at once. Supported formats: JPG, PNG, GIF, WEBP</small>
                            </div>
                            
                            <div id="selectedImages" class="mb-3"></div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-upload"></i> Upload Images
                                </button>
                                <button type="reset" class="btn btn-secondary">
                                    <i class="fas fa-undo"></i> Clear
                                </button>
                            </div>
                        </form>
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
        
        $('input[type="file"]').on('change', function() {
            const files = this.files;
            const preview = $('#selectedImages');
            preview.html('<h6>Selected Images:</h6><div class="row">');
            
            for(let i = 0; i < files.length; i++) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.find('.row').append(`
                        <div class="col-md-3 mb-2">
                            <img src="${e.target.result}" class="img-thumbnail" style="height: 100px; width: 100%; object-fit: cover;">
                        </div>
                    `);
                };
                reader.readAsDataURL(files[i]);
            }
        });
    </script>
</body>
</html>