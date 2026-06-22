<?php
// admin/edit-news.php - Edit existing news article
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../config/database.php';
$db = new Database();
$conn = $db->getConnection();

$id = isset($_GET['id']) ? $_GET['id'] : 0;
$news = null;

// Get news data
$stmt = $conn->prepare("SELECT * FROM news WHERE id = ?");
$stmt->execute([$id]);
$news = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$news) {
    header("Location: manage-news.php");
    exit();
}

$message = '';
$messageType = '';

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $excerpt = $_POST['excerpt'];
    $category = $_POST['category'];
    $status = $_POST['status'];
    
    $featured_image = $news['featured_image'];
    
    // Handle image upload
    if(isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $filename = $_FILES['featured_image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if(in_array($ext, $allowed)) {
            // Delete old image
            if($featured_image && file_exists('../' . $featured_image)) {
                unlink('../' . $featured_image);
            }
            
            $new_filename = 'news_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
            $upload_path = '../uploads/news/';
            
            if(!file_exists($upload_path)) {
                mkdir($upload_path, 0777, true);
            }
            
            if(move_uploaded_file($_FILES['featured_image']['tmp_name'], $upload_path . $new_filename)) {
                $featured_image = 'uploads/news/' . $new_filename;
            }
        }
    }
    
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
    
    $stmt = $conn->prepare("UPDATE news SET title=?, slug=?, content=?, excerpt=?, featured_image=?, category=?, status=? WHERE id=?");
    if($stmt->execute([$title, $slug, $content, $excerpt, $featured_image, $category, $status, $id])) {
        $message = "News article updated successfully!";
        $messageType = "success";
        // Refresh news data
        $stmt = $conn->prepare("SELECT * FROM news WHERE id = ?");
        $stmt->execute([$id]);
        $news = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $message = "Failed to update news article.";
        $messageType = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit News - Stella Maris College Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <style>
        .ql-container {
            min-height: 400px;
        }
        .preview-image {
            max-width: 200px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php include 'sidebar.php'; ?>
            
            <main class="col-md-9 col-lg-10 ms-sm-auto px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Edit News Article</h1>
                    <a href="manage-news.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to News
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
                        <h5 class="mb-0"><i class="fas fa-edit"></i> Edit: <?php echo htmlspecialchars($news['title']); ?></h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label">Title *</label>
                                <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($news['title']); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Category</label>
                                <select name="category" class="form-control">
                                    <option value="General" <?php echo $news['category'] == 'General' ? 'selected' : ''; ?>>General</option>
                                    <option value="Academics" <?php echo $news['category'] == 'Academics' ? 'selected' : ''; ?>>Academics</option>
                                    <option value="Events" <?php echo $news['category'] == 'Events' ? 'selected' : ''; ?>>Events</option>
                                    <option value="Sports" <?php echo $news['category'] == 'Sports' ? 'selected' : ''; ?>>Sports</option>
                                    <option value="Spiritual" <?php echo $news['category'] == 'Spiritual' ? 'selected' : ''; ?>>Spiritual</option>
                                    <option value="Alumni" <?php echo $news['category'] == 'Alumni' ? 'selected' : ''; ?>>Alumni</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Featured Image</label>
                                <?php if($news['featured_image']): ?>
                                <div class="mb-2">
                                    <img src="../<?php echo $news['featured_image']; ?>" class="preview-image img-thumbnail">
                                </div>
                                <?php endif; ?>
                                <input type="file" name="featured_image" class="form-control" accept="image/*" onchange="previewImage(this)">
                                <div id="imagePreview"></div>
                                <small class="text-muted">Leave empty to keep current image</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Excerpt (Short Description)</label>
                                <textarea name="excerpt" class="form-control" rows="3"><?php echo htmlspecialchars($news['excerpt']); ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Full Content</label>
                                <div id="editor" style="min-height: 400px;"><?php echo htmlspecialchars_decode($news['content']); ?></div>
                                <textarea name="content" id="content" style="display:none;"><?php echo htmlspecialchars_decode($news['content']); ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-control">
                                    <option value="published" <?php echo $news['status'] == 'published' ? 'selected' : ''; ?>>Published</option>
                                    <option value="draft" <?php echo $news['status'] == 'draft' ? 'selected' : ''; ?>>Draft</option>
                                </select>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update News
                                </button>
                                <a href="manage-news.php" class="btn btn-secondary">Cancel</a>
                                <a href="../news-detail.php?id=<?php echo $news['id']; ?>" class="btn btn-info" target="_blank">
                                    <i class="fas fa-eye"></i> Preview
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    
    <script>
        var quill = new Quill('#editor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'color': [] }, { 'background': [] }],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['link', 'image', 'video'],
                    ['clean']
                ]
            }
        });
        
        var form = document.querySelector('form');
        form.onsubmit = function() {
            document.querySelector('#content').value = quill.root.innerHTML;
        };
        
        function previewImage(input) {
            if(input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#imagePreview').html('<img src="' + e.target.result + '" class="preview-image img-thumbnail mt-2">');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>