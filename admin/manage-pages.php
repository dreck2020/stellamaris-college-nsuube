<?php
// admin/manage-pages.php - Manage all frontend pages content
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

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $page_name = $_POST['page_name'];
    $title = $_POST['title'];
    $content = $_POST['content'];
    
    $stmt = $conn->prepare("UPDATE pages SET title = ?, content = ?, updated_at = NOW() WHERE page_name = ?");
    if($stmt->execute([$title, $content, $page_name])) {
        $message = "Page updated successfully!";
        $messageType = "success";
    } else {
        $message = "Failed to update page.";
        $messageType = "danger";
    }
}

// Get all pages
$pages = $conn->query("SELECT * FROM pages ORDER BY page_name")->fetchAll(PDO::FETCH_ASSOC);

// Get specific page for editing
$editPage = null;
if(isset($_GET['edit'])) {
    $stmt = $conn->prepare("SELECT * FROM pages WHERE page_name = ?");
    $stmt->execute([$_GET['edit']]);
    $editPage = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Pages - Stella Maris College Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
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
        
        .ql-container {
            min-height: 400px;
            font-size: 14px;
        }
        
        .ql-editor {
            min-height: 400px;
        }
        
        .page-card {
            transition: transform 0.2s;
            cursor: pointer;
        }
        
        .page-card:hover {
            transform: translateY(-5px);
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
                    <h1 class="h2">Manage Website Pages</h1>
                    <button class="btn btn-primary" onclick="window.location.href='../index.php'" target="_blank">
                        <i class="fas fa-eye"></i> View Website
                    </button>
                </div>
                
                <?php if($message): ?>
                <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <div class="row">
                    <!-- Pages List -->
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="fas fa-list"></i> Available Pages</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="list-group list-group-flush">
                                    <a href="?edit=home" class="list-group-item list-group-item-action <?php echo isset($_GET['edit']) && $_GET['edit'] == 'home' ? 'active' : ''; ?>">
                                        <i class="fas fa-home"></i> Home Page
                                    </a>
                                    <a href="?edit=about" class="list-group-item list-group-item-action <?php echo isset($_GET['edit']) && $_GET['edit'] == 'about' ? 'active' : ''; ?>">
                                        <i class="fas fa-info-circle"></i> About Us
                                    </a>
                                    <a href="?edit=mission_vision" class="list-group-item list-group-item-action <?php echo isset($_GET['edit']) && $_GET['edit'] == 'mission_vision' ? 'active' : ''; ?>">
                                        <i class="fas fa-star"></i> Mission & Vision
                                    </a>
                                </div>
                            </div>
                            <div class="card-footer">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i> Click on any page to edit its content
                                </small>
                            </div>
                        </div>
                        
                        <!-- Quick Links -->
                        <div class="card mt-4">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="fas fa-link"></i> Quick Actions</h5>
                            </div>
                            <div class="card-body">
                                <a href="manage-news.php" class="btn btn-outline-primary btn-sm w-100 mb-2">
                                    <i class="fas fa-newspaper"></i> Manage News
                                </a>
                                <a href="manage-events.php" class="btn btn-outline-primary btn-sm w-100 mb-2">
                                    <i class="fas fa-calendar"></i> Manage Events
                                </a>
                                <a href="manage-gallery.php" class="btn btn-outline-primary btn-sm w-100 mb-2">
                                    <i class="fas fa-images"></i> Manage Gallery
                                </a>
                                <a href="manage-downloads.php" class="btn btn-outline-primary btn-sm w-100">
                                    <i class="fas fa-download"></i> Manage Downloads
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Page Editor -->
                    <div class="col-md-8">
                        <?php if($editPage): ?>
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-edit"></i> Editing: <?php echo ucwords(str_replace('_', ' ', $editPage['page_name'])); ?>
                                </h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" id="pageForm">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="page_name" value="<?php echo $editPage['page_name']; ?>">
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Page Title</label>
                                        <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($editPage['title']); ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Page Content</label>
                                        <div id="editor" style="min-height: 400px;"><?php echo htmlspecialchars_decode($editPage['content']); ?></div>
                                        <textarea name="content" id="content" style="display:none;"><?php echo htmlspecialchars_decode($editPage['content']); ?></textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Meta Description (for SEO)</label>
                                        <textarea name="meta_description" class="form-control" rows="2"><?php echo htmlspecialchars($editPage['meta_description']); ?></textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Meta Keywords</label>
                                        <input type="text" name="meta_keywords" class="form-control" value="<?php echo htmlspecialchars($editPage['meta_keywords']); ?>">
                                    </div>
                                    
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Save Changes
                                        </button>
                                        <a href="?edit=<?php echo $editPage['page_name']; ?>" class="btn btn-secondary">
                                            <i class="fas fa-refresh"></i> Reset
                                        </a>
                                        <a href="../index.php" class="btn btn-success" target="_blank">
                                            <i class="fas fa-eye"></i> Preview
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="card">
                            <div class="card-body text-center py-5">
                                <i class="fas fa-file-alt" style="font-size: 64px; color: #ccc;"></i>
                                <h4 class="mt-3">Select a page to edit</h4>
                                <p class="text-muted">Choose a page from the list on the left to edit its content.</p>
                            </div>
                        </div>
                        <?php endif; ?>
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
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    
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
        
        <?php if($editPage): ?>
        // Initialize Quill editor
        var quill = new Quill('#editor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'color': [] }, { 'background': [] }],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'align': [] }],
                    ['link', 'image', 'video'],
                    ['clean']
                ]
            },
            placeholder: 'Enter page content here...'
        });
        
        // Update hidden textarea on form submit
        var form = document.getElementById('pageForm');
        form.onsubmit = function() {
            var content = document.querySelector('#content');
            content.value = quill.root.innerHTML;
        };
        <?php endif; ?>
    </script>
</body>
</html>