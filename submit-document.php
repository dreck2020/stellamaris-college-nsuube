<?php
// submit-document.php - Users can upload documents for admin review
session_start();
$page_title = "Submit Document";
require_once 'config/database.php';

$db = new Database();
$conn = $db->getConnection();
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_doc'])) {
    $user_name = trim($_POST['user_name']);
    $email = trim($_POST['email']);
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    
    if (isset($_FILES['document']) && $_FILES['document']['error'] == 0) {
        $allowed = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'zip', 'rar', 'xls', 'xlsx', 'jpg', 'jpeg', 'png'];
        $filename = $_FILES['document']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $file_size = $_FILES['document']['size'];
        $size_mb = round($file_size / 1048576, 2);
        
        if (in_array($ext, $allowed)) {
            $new_filename = 'userdoc_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
            $upload_path = 'uploads/user_documents/';
            if (!file_exists($upload_path)) mkdir($upload_path, 0777, true);
            
            if (move_uploaded_file($_FILES['document']['tmp_name'], $upload_path . $new_filename)) {
                $file_path = 'uploads/user_documents/' . $new_filename;
                $stmt = $conn->prepare("INSERT INTO user_documents (user_name, email, title, description, file_path, file_size) VALUES (?, ?, ?, ?, ?, ?)");
                if ($stmt->execute([$user_name, $email, $title, $description, $file_path, $size_mb . ' MB'])) {
                    $message = "Document submitted successfully! Admin will review it shortly.";
                    $messageType = "success";
                } else {
                    $message = "Database error. Please try again.";
                    $messageType = "danger";
                }
            } else {
                $message = "Failed to upload file.";
                $messageType = "danger";
            }
        } else {
            $message = "Invalid file type. Allowed: PDF, DOC, DOCX, PPT, PPTX, ZIP, RAR, XLS, XLSX, JPG, PNG";
            $messageType = "danger";
        }
    } else {
        $message = "Please select a file.";
        $messageType = "danger";
    }
}
?>
<?php include 'includes/head.php'; ?>
<?php include 'includes/sidebar.php'; ?>
<?php include 'includes/header.php'; ?>

<style>
.submit-page-header {
    background: linear-gradient(rgba(26,77,140,0.8), rgba(0,0,0,0.6)), url('assets/images/submit-bg.jpg');
    background-size: cover;
    padding: 60px 0;
    color: white;
    text-align: center;
}
.submit-form {
    background: white;
    border-radius: 15px;
    padding: 40px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    max-width: 800px;
    margin: 0 auto;
}
.btn-submit {
    background: #1a4d8c;
    color: white;
    padding: 12px 30px;
    border: none;
    border-radius: 30px;
    font-weight: 600;
}
.btn-submit:hover { background: #2e7d32; transform: translateY(-2px); }
</style>

<section class="submit-page-header">
    <div class="container">
        <h1>Submit a Document</h1>
        <p>Upload applications, assignments, or any file for admin review</p>
    </div>
</section>

<section style="padding: 60px 0;">
    <div class="container">
        <div class="submit-form">
            <?php if($message): ?>
            <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label>Your Full Name *</label>
                    <input type="text" name="user_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Email Address *</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Document Title *</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Description (optional)</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>
                <div class="mb-3">
                    <label>Select Document *</label>
                    <input type="file" name="document" class="form-control" required>
                    <small class="text-muted">Allowed: PDF, DOC, DOCX, PPT, PPTX, ZIP, RAR, XLS, XLSX, JPG, PNG (Max 10MB)</small>
                </div>
                <div class="text-center">
                    <button type="submit" name="submit_doc" class="btn-submit">
                        <i class="fas fa-cloud-upload-alt"></i> Submit Document
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>