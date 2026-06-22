<?php
// admission-downloads.php - Combined Admission & Downloads Page
session_start();
$page_title = "Admission & Downloads";
require_once 'config/database.php';
$db = new Database();
$conn = $db->getConnection();

// --- Handle Admission Form Submission ---
$message = '';
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_admission'])) {
    $student_name = $_POST['student_name'];
    $parent_name = $_POST['parent_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $grade_applying = $_POST['grade_applying'];
    $academic_year = $_POST['academic_year'];
    $previous_school = $_POST['previous_school'] ?? '';
    $message_text = $_POST['message'];
    
    $stmt = $conn->prepare("INSERT INTO inquiries (student_name, parent_name, email, phone, grade_applying, academic_year, previous_school, message) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    if($stmt->execute([$student_name, $parent_name, $email, $phone, $grade_applying, $academic_year, $previous_school, $message_text])) {
        $message = '<div class="alert alert-success">✅ Application submitted successfully! We will contact you soon.</div>';
    } else {
        $message = '<div class="alert alert-danger">❌ Submission failed. Please try again.</div>';
    }
}

// ========== FIXED: Document Submission - Inserts into user_documents table ==========
$doc_message = '';
$doc_message_type = '';
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_document'])) {
    $doc_title = trim($_POST['doc_title']);
    $doc_description = trim($_POST['doc_description']);
    $doc_category = $_POST['doc_category'];
    
    // Get user info if logged in
    $user_name = isset($_SESSION['user_id']) ? ($_SESSION['user_name'] ?? 'Guest User') : 'Guest User';
    $user_email = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : 'guest@stellamaris.edu.ug';
    $user_phone = isset($_SESSION['user_phone']) ? $_SESSION['user_phone'] : '';
    
    // Handle file upload
    $file_path = '';
    $file_size_display = '';
    $upload_error = '';
    
    if(isset($_FILES['doc_file']) && $_FILES['doc_file']['error'] == 0) {
        $allowed = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'jpg', 'jpeg', 'png', 'gif', 'zip', 'rar'];
        $filename = $_FILES['doc_file']['name'];
        $file_tmp = $_FILES['doc_file']['tmp_name'];
        $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $file_size = $_FILES['doc_file']['size'];
        
        // Convert file size to readable format
        if($file_size >= 1048576) {
            $file_size_display = number_format($file_size / 1048576, 2) . ' MB';
        } elseif($file_size >= 1024) {
            $file_size_display = number_format($file_size / 1024, 2) . ' KB';
        } else {
            $file_size_display = $file_size . ' bytes';
        }
        
        if(in_array($file_ext, $allowed)) {
            // Create upload directory if not exists
            $upload_dir = 'assets/uploads/documents/';
            if(!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $new_filename = 'userdoc_' . time() . '_' . uniqid() . '.' . $file_ext;
            $full_path = $upload_dir . $new_filename;
            
            if(move_uploaded_file($file_tmp, $full_path)) {
                $file_path = $full_path;
            } else {
                $upload_error = "Failed to upload file. Please check folder permissions.";
            }
        } else {
            $upload_error = "Invalid file type. Allowed: " . implode(', ', $allowed);
        }
    } else {
        if($_FILES['doc_file']['error'] != 4) {
            $upload_error = "File upload error: " . $_FILES['doc_file']['error'];
        } else {
            $upload_error = "Please select a file to upload.";
        }
    }
    
    if(empty($upload_error) && !empty($file_path)) {
        try {
            // ========== FIXED: Insert into user_documents table ==========
            $stmt = $conn->prepare("INSERT INTO user_documents (user_name, user_email, user_phone, title, description, file_path, file_size, category, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
            
            if($stmt->execute([$user_name, $user_email, $user_phone, $doc_title, $doc_description, $file_path, $file_size_display, $doc_category])) {
                $doc_message = "✅ Document submitted successfully! It will be reviewed by the admin.";
                $doc_message_type = "success";
            } else {
                $error_info = $stmt->errorInfo();
                $doc_message = "❌ Failed to save document: " . ($error_info[2] ?? "Unknown database error");
                $doc_message_type = "danger";
            }
        } catch(PDOException $e) {
            $doc_message = "❌ Database error: " . $e->getMessage();
            $doc_message_type = "danger";
        }
    } else {
        $doc_message = "❌ " . $upload_error;
        $doc_message_type = "danger";
    }
}

// --- Get Downloads Data (Admin Uploads Only) ---
$category = isset($_GET['category']) ? $_GET['category'] : 'all';
$query = "SELECT * FROM downloads ORDER BY uploaded_at DESC";
if($category != 'all') {
    $query .= " WHERE category = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$category]);
} else {
    $stmt = $conn->query($query);
}
$downloads = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get categories
$categories = $conn->query("SELECT DISTINCT category FROM downloads")->fetchAll(PDO::FETCH_COLUMN);

// Get admission form from admin uploads only
$form_check = $conn->prepare("
    SELECT * FROM downloads 
    WHERE category = 'forms' 
    AND (title LIKE '%admission%' OR title LIKE '%application%') 
    ORDER BY uploaded_at DESC LIMIT 1
");
$form_check->execute();
$admission_form = $form_check->fetch(PDO::FETCH_ASSOC);

// Get active tab (default: admission)
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'admission';

// If there's a message about no form, show it
$no_form_message = '';
if(isset($_GET['no_form'])) {
    $no_form_message = '<div class="alert alert-info">📄 No admission form found in the downloads section. Please check the Downloads tab for available forms.</div>';
}
?>
<?php include 'includes/head.php'; ?>
<?php include 'includes/sidebar.php'; ?>
<?php include 'includes/header.php'; ?>

<style>
/* ====== PAGE HEADER ====== */
.page-header {
    background: linear-gradient(rgba(26,77,140,0.85), rgba(0,0,0,0.7)), url('assets/images/admission-bg.jpg');
    background-size: cover;
    background-position: center;
    padding: 60px 0;
    color: white;
    text-align: center;
}
.page-header h1 {
    font-size: 2.5rem;
    font-weight: 700;
}
.page-header p {
    font-size: 1.2rem;
    opacity: 0.9;
}

/* ====== TABS ====== */
.tabs-container {
    margin-top: -30px;
    margin-bottom: 40px;
}
.tabs-nav {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
    background: white;
    border-radius: 15px;
    padding: 8px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    max-width: 600px;
    margin: 0 auto;
}
.tab-btn {
    flex: 1;
    padding: 14px 25px;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    font-size: 16px;
    cursor: pointer;
    transition: all 0.3s;
    background: transparent;
    color: #7f8c8d;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    min-width: 120px;
}
.tab-btn:hover {
    background: #f0f0f0;
    color: #2c3e50;
}
.tab-btn.active {
    background: linear-gradient(135deg, #1a4d8c, #2e7d32);
    color: white;
    box-shadow: 0 3px 10px rgba(26,77,140,0.3);
}
.tab-btn i {
    font-size: 18px;
}
.tab-content {
    display: none;
    animation: fadeIn 0.3s ease;
}
.tab-content.active {
    display: block;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* ====== ADMISSION SECTION ====== */
.admission-wrapper {
    max-width: 1400px;
    margin: 0 auto;
}

.admission-info-card {
    background: linear-gradient(135deg, #1a4d8c, #0d3b6b);
    color: white;
    padding: 40px;
    border-radius: 20px;
    height: 100%;
}
.admission-info-card h2 {
    font-size: 28px;
    margin-bottom: 20px;
}

/* ====== DOWNLOAD ADMISSION FORM BUTTON ====== */
.download-form-box {
    background: linear-gradient(135deg, #1b5e20, #2e7d32);
    border-radius: 16px;
    padding: 22px 25px;
    margin-bottom: 28px;
    border: 2px solid #66bb6a;
    box-shadow: 0 4px 15px rgba(46, 125, 50, 0.3);
    transition: all 0.3s;
}
.download-form-box:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(46, 125, 50, 0.4);
}
.download-form-box .form-box-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 15px;
}
.download-form-box .form-box-left {
    display: flex;
    align-items: center;
    gap: 18px;
}
.download-form-box .form-icon {
    background: rgba(255,255,255,0.15);
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    color: #ff6b6b;
    flex-shrink: 0;
}
.download-form-box .form-text h4 {
    margin: 0;
    color: white;
    font-size: 18px;
    font-weight: 700;
}
.download-form-box .form-text p {
    margin: 3px 0 0;
    color: #a5d6a7;
    font-size: 14px;
}
.download-form-box .form-text .form-version {
    display: inline-block;
    background: rgba(255,255,255,0.15);
    padding: 2px 12px;
    border-radius: 20px;
    font-size: 11px;
    color: #a5d6a7;
    margin-top: 4px;
}
.btn-download-form {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    background: #ff6b6b;
    color: white;
    padding: 14px 30px;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 700;
    font-size: 16px;
    transition: all 0.3s;
    border: 2px solid #ff6b6b;
    box-shadow: 0 4px 12px rgba(255, 107, 107, 0.3);
    white-space: nowrap;
}
.btn-download-form:hover {
    background: #e74c3c;
    border-color: #e74c3c;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255, 107, 107, 0.4);
    color: white;
}
.btn-download-form .pdf-badge {
    background: rgba(255,255,255,0.2);
    padding: 2px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}
.btn-download-form i {
    font-size: 18px;
}

.btn-view-forms {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    background: #f39c12;
    color: white;
    padding: 14px 30px;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 700;
    font-size: 16px;
    transition: all 0.3s;
    border: 2px solid #f39c12;
    box-shadow: 0 4px 12px rgba(243, 156, 18, 0.3);
    white-space: nowrap;
}
.btn-view-forms:hover {
    background: #d68910;
    border-color: #d68910;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(243, 156, 18, 0.4);
    color: white;
}
.btn-view-forms .badge-count {
    background: rgba(255,255,255,0.2);
    padding: 2px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}

.btn-download-form-sm {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: #e67e22;
    color: white;
    padding: 10px 22px;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.3s;
    border: none;
    white-space: nowrap;
}
.btn-download-form-sm:hover {
    background: #d35400;
    transform: translateY(-2px);
    color: white;
}

.btn-view-forms-sm {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: #f39c12;
    color: white;
    padding: 10px 22px;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.3s;
    border: none;
    white-space: nowrap;
}
.btn-view-forms-sm:hover {
    background: #d68910;
    transform: translateY(-2px);
    color: white;
}

/* ====== STEPS ====== */
.step-item {
    display: flex;
    align-items: center;
    margin-bottom: 25px;
    padding: 15px;
    background: rgba(255,255,255,0.1);
    border-radius: 15px;
    transition: transform 0.3s;
}
.step-item:hover {
    transform: translateX(10px);
    background: rgba(255,255,255,0.15);
}
.step-number {
    width: 50px;
    height: 50px;
    background: #2e7d32;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    font-weight: bold;
    margin-right: 15px;
    flex-shrink: 0;
}
.step-content h3 {
    margin: 0 0 5px;
    font-size: 18px;
}
.step-content p {
    margin: 0;
    opacity: 0.9;
    font-size: 14px;
}

.benefits-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
    margin-top: 30px;
}
.benefit-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px;
    background: rgba(255,255,255,0.1);
    border-radius: 10px;
    font-size: 13px;
}
.benefit-item i {
    font-size: 18px;
    color: #a5d6a7;
}

/* ====== LARGE FORM ====== */
.large-form {
    background: white;
    padding: 40px;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}
.large-form h3 {
    font-size: 28px;
    color: #1a4d8c;
    margin-bottom: 10px;
}
.form-subtitle {
    color: #666;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #f0f0f0;
}
.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}
.form-group {
    margin-bottom: 25px;
}
.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #333;
    font-size: 14px;
}
.form-group label .required {
    color: #dc3545;
    margin-left: 5px;
}
.form-control-large {
    width: 100%;
    padding: 14px 16px;
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    font-size: 16px;
    transition: all 0.3s;
}
.form-control-large:focus {
    outline: none;
    border-color: #1a4d8c;
    box-shadow: 0 0 0 3px rgba(26,77,140,0.1);
}
select.form-control-large {
    appearance: none;
    background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23333' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 16px center;
    background-size: 20px;
}
textarea.form-control-large {
    resize: vertical;
    min-height: 120px;
}
.btn-submit-large {
    width: 100%;
    padding: 16px;
    background: linear-gradient(135deg, #1a4d8c, #2e7d32);
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 18px;
    font-weight: 600;
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
}
.btn-submit-large:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(26,77,140,0.3);
}

/* ====== DOWNLOADS SECTION ====== */
.downloads-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #f0f0f0;
}
.downloads-header h2 {
    color: #1a4d8c;
    margin: 0;
}

.categories-filter {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}
.btn-category {
    display: inline-block;
    padding: 6px 18px;
    background: #f5f5f5;
    color: #333;
    text-decoration: none;
    border-radius: 25px;
    font-size: 14px;
    transition: all 0.3s;
}
.btn-category:hover, .btn-category.active {
    background: #1a4d8c;
    color: white;
}

.downloads-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
}
.download-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 3px 15px rgba(0,0,0,0.08);
    transition: all 0.3s;
    border-left: 4px solid #1a4d8c;
}
.download-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.12);
}
.download-card .doc-icon {
    font-size: 35px;
    color: #dc3545;
    margin-bottom: 10px;
}
.download-card h4 {
    margin: 0 0 5px;
    color: #1a4d8c;
    font-size: 18px;
}
.download-card p {
    color: #666;
    font-size: 14px;
    margin-bottom: 10px;
}
.download-card .doc-meta {
    display: flex;
    gap: 15px;
    font-size: 12px;
    color: #999;
    margin-bottom: 15px;
    flex-wrap: wrap;
}
.download-card .doc-meta span {
    display: flex;
    align-items: center;
    gap: 5px;
}
.btn-download {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #1a4d8c;
    color: white;
    padding: 8px 20px;
    text-decoration: none;
    border-radius: 25px;
    font-size: 14px;
    transition: all 0.3s;
}
.btn-download:hover {
    background: #2e7d32;
    transform: translateY(-2px);
    color: white;
}

/* ====== SUBMIT DOCUMENT SECTION ====== */
.submit-doc-section {
    background: white;
    border-radius: 15px;
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: 0 3px 15px rgba(0,0,0,0.08);
    border-left: 4px solid #2e7d32;
}
.submit-doc-section h3 {
    color: #1a4d8c;
    margin-bottom: 10px;
}
.submit-doc-section .subtitle {
    color: #666;
    margin-bottom: 20px;
}
.submit-doc-section .btn-submit-doc {
    background: linear-gradient(135deg, #2e7d32, #1b5e20);
    color: white;
    padding: 12px 28px;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 600;
    font-size: 16px;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    border: none;
    cursor: pointer;
}
.submit-doc-section .btn-submit-doc:hover {
    background: linear-gradient(135deg, #1b5e20, #0a3d0e);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}
.submit-doc-section .doc-form-group {
    margin-bottom: 20px;
}
.submit-doc-section .doc-form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #333;
    font-size: 14px;
}
.submit-doc-section .doc-form-group input,
.submit-doc-section .doc-form-group select,
.submit-doc-section .doc-form-group textarea {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    font-size: 14px;
    transition: all 0.3s;
}
.submit-doc-section .doc-form-group input:focus,
.submit-doc-section .doc-form-group select:focus,
.submit-doc-section .doc-form-group textarea:focus {
    outline: none;
    border-color: #1a4d8c;
    box-shadow: 0 0 0 3px rgba(26,77,140,0.1);
}
.submit-doc-section .doc-form-group textarea {
    min-height: 100px;
    resize: vertical;
}
.submit-doc-section .doc-form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}
.submit-doc-section .file-input-wrapper input[type="file"] {
    padding: 12px;
    background: #f8f9fa;
    border: 2px dashed #ddd;
    cursor: pointer;
}
.submit-doc-section .file-input-wrapper input[type="file"]:hover {
    background: #e8f0fe;
    border-color: #1a4d8c;
}
.submit-doc-section .allowed-formats {
    font-size: 12px;
    color: #999;
    margin-top: 5px;
}

/* ====== SIDEBAR ====== */
.sidebar-widget {
    background: white;
    border-radius: 15px;
    padding: 25px;
    margin-bottom: 25px;
    box-shadow: 0 3px 15px rgba(0,0,0,0.08);
}
.sidebar-widget h3 {
    font-size: 18px;
    margin-bottom: 20px;
    color: #1a4d8c;
    border-left: 3px solid #2e7d32;
    padding-left: 10px;
}
.category-list {
    list-style: none;
    padding: 0;
    margin: 0;
}
.category-list li {
    padding: 10px 0;
    border-bottom: 1px solid #eee;
}
.category-list li:last-child { border-bottom: none; }
.category-list a {
    display: flex;
    justify-content: space-between;
    color: #333;
    text-decoration: none;
    transition: color 0.3s;
}
.category-list a:hover { color: #1a4d8c; }
.category-list span {
    background: #f0f0f0;
    padding: 2px 10px;
    border-radius: 20px;
    font-size: 12px;
}

/* ====== ALERTS ====== */
.alert {
    padding: 15px 20px;
    border-radius: 12px;
    margin-bottom: 25px;
    font-size: 14px;
}
.alert-success {
    background: #e8f5e9;
    color: #2e7d32;
    border: 1px solid #c8e6c9;
}
.alert-danger {
    background: #ffebee;
    color: #c62828;
    border: 1px solid #ffcdd2;
}
.alert-info {
    background: #e3f2fd;
    color: #1565c0;
    border: 1px solid #bbdefb;
}

/* ====== RESPONSIVE ====== */
@media (max-width: 992px) {
    .download-form-box .form-box-content {
        flex-direction: column;
        align-items: stretch;
        text-align: center;
    }
    .download-form-box .form-box-left {
        flex-direction: column;
        text-align: center;
    }
    .btn-download-form, .btn-view-forms {
        justify-content: center;
    }
}

@media (max-width: 768px) {
    .page-header h1 { font-size: 1.8rem; }
    .page-header p { font-size: 1rem; }
    
    .tabs-nav { flex-direction: column; max-width: 100%; }
    .tab-btn { justify-content: center; padding: 12px 20px; }
    
    .form-row { grid-template-columns: 1fr; gap: 0; }
    .large-form { padding: 25px; }
    .large-form h3 { font-size: 22px; }
    
    .admission-info-card { padding: 25px; margin-bottom: 30px; }
    .benefits-grid { grid-template-columns: 1fr; }
    
    .downloads-grid { grid-template-columns: 1fr; }
    .downloads-header { flex-direction: column; text-align: center; }
    .categories-filter { justify-content: center; }
    
    .step-item { padding: 12px; }
    
    .submit-doc-section .doc-form-row {
        grid-template-columns: 1fr;
        gap: 0;
    }
    
    .download-form-box {
        padding: 18px 15px;
    }
    .download-form-box .form-icon {
        width: 50px;
        height: 50px;
        font-size: 22px;
    }
    .btn-download-form, .btn-view-forms {
        padding: 12px 20px;
        font-size: 14px;
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .download-form-box .form-text h4 {
        font-size: 16px;
    }
    .btn-download-form, .btn-view-forms {
        font-size: 13px;
        padding: 10px 16px;
    }
    .btn-download-form .pdf-badge,
    .btn-view-forms .badge-count {
        display: none;
    }
}
</style>

<!-- ====== PAGE HEADER ====== -->
<section class="page-header">
    <div class="container">
        <h1>Admission & Downloads</h1>
        <p>Join the Stella Maris College Family • Access Important Documents</p>
    </div>
</section>

<!-- ====== TABS ====== -->
<section style="padding: 40px 0 60px;">
    <div class="container">
        <div class="tabs-container">
            <div class="tabs-nav">
                <button class="tab-btn <?php echo $active_tab == 'admission' ? 'active' : ''; ?>" data-tab="admission">
                    <i class="fas fa-graduation-cap"></i> Admission
                </button>
                <button class="tab-btn <?php echo $active_tab == 'downloads' ? 'active' : ''; ?>" data-tab="downloads">
                    <i class="fas fa-download"></i> Downloads
                </button>
            </div>
        </div>

        <!-- ====== TAB 1: ADMISSION ====== -->
        <div class="tab-content <?php echo $active_tab == 'admission' ? 'active' : ''; ?>" id="tab-admission">
            <div class="admission-wrapper">
                <div class="row">
                    <!-- Left Column - Info -->
                    <div class="col-lg-5 mb-4">
                        <div class="admission-info-card">
                            <h2><i class="fas fa-graduation-cap"></i> Admission Process</h2>
                            <p style="margin-bottom: 20px;">Follow these simple steps to join Stella Maris College</p>
                            
                            <div class="download-form-box">
                                <div class="form-box-content">
                                    <div class="form-box-left">
                                        <div class="form-icon">
                                            <i class="fas fa-file-pdf"></i>
                                        </div>
                                        <div class="form-text">
                                            <?php if($admission_form): ?>
                                                <h4>📄 Admission Application Form</h4>
                                                <p>Download and fill the official admission form</p>
                                                <span class="form-version"><i class="fas fa-clock"></i> Updated: <?php echo date('M Y', strtotime($admission_form['uploaded_at'] ?? 'now')); ?></span>
                                            <?php else: ?>
                                                <h4>📄 Admission Forms</h4>
                                                <p>No specific admission form available</p>
                                                <span class="form-version"><i class="fas fa-info-circle"></i> Check Downloads tab</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php if($admission_form): ?>
                                        <a href="download.php?id=<?= $admission_form['id'] ?>" class="btn-download-form">
                                            <i class="fas fa-download"></i> Download Form
                                            <span class="pdf-badge">PDF</span>
                                        </a>
                                    <?php else: ?>
                                        <a href="?tab=downloads" class="btn-view-forms">
                                            <i class="fas fa-folder-open"></i> View Forms
                                        </a>
                                    <?php endif; ?>
                                </div>
                                <?php if($admission_form): ?>
                                    <p style="margin: 12px 0 0; color: #a5d6a7; font-size: 12px; text-align: center;">
                                        <i class="fas fa-info-circle"></i> Fill the form and submit to the admissions office
                                    </p>
                                <?php else: ?>
                                    <p style="margin: 12px 0 0; color: #ffd54f; font-size: 12px; text-align: center;">
                                        <i class="fas fa-exclamation-triangle"></i> No admission form found. 
                                        <a href="?tab=downloads" style="color: #ffd54f; text-decoration: underline;">Check downloads section</a>
                                    </p>
                                <?php endif; ?>
                            </div>
                            
                            <div class="step-item">
                                <div class="step-number">1</div>
                                <div class="step-content">
                                    <h3>Submit Application</h3>
                                    <p>Fill out the admission inquiry form with your details</p>
                                </div>
                            </div>
                            
                            <div class="step-item">
                                <div class="step-number">2</div>
                                <div class="step-content">
                                    <h3>Entrance Examination</h3>
                                    <p>Take our placement test to assess academic level</p>
                                </div>
                            </div>
                            
                            <div class="step-item">
                                <div class="step-number">3</div>
                                <div class="step-content">
                                    <h3>Interview</h3>
                                    <p>Meet with our admissions team for a personal interview</p>
                                </div>
                            </div>
                            
                            <div class="step-item">
                                <div class="step-number">4</div>
                                <div class="step-content">
                                    <h3>Admission Offer</h3>
                                    <p>Receive admission letter and complete enrollment</p>
                                </div>
                            </div>
                            
                            <div class="benefits-grid">
                                <div class="benefit-item">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Quality Education</span>
                                </div>
                                <div class="benefit-item">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Experienced Teachers</span>
                                </div>
                                <div class="benefit-item">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Modern Facilities</span>
                                </div>
                                <div class="benefit-item">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Spiritual Growth</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Column - Form -->
                    <div class="col-lg-7">
                        <div style="background: <?php echo $admission_form ? '#fff8e1' : '#fff3e0'; ?>; border-radius: 12px; padding: 15px 20px; margin-bottom: 25px; border: 1px solid <?php echo $admission_form ? '#ffe0b2' : '#ffcc80'; ?>; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <i class="fas fa-file-pdf" style="font-size: 24px; color: <?php echo $admission_form ? '#e74c3c' : '#f39c12'; ?>;"></i>
                                <div>
                                    <span style="font-weight: 600; color: #1a4d8c; font-size: 14px;">
                                        <?php echo $admission_form ? 'Admission Form' : 'Forms Available'; ?>
                                    </span>
                                    <p style="margin: 0; font-size: 12px; color: #666;">
                                        <?php echo $admission_form ? 'Download the application form' : 'Check downloads for available forms'; ?>
                                    </p>
                                </div>
                            </div>
                            <?php if($admission_form): ?>
                                <a href="download.php?id=<?= $admission_form['id'] ?>" class="btn-download-form-sm">
                                    <i class="fas fa-download"></i> Download
                                </a>
                            <?php else: ?>
                                <a href="?tab=downloads" class="btn-view-forms-sm">
                                    <i class="fas fa-folder-open"></i> View Forms
                                </a>
                            <?php endif; ?>
                        </div>
                        
                        <div class="large-form">
                            <h3><i class="fas fa-edit"></i> Admission Inquiry Form</h3>
                            <div class="form-subtitle">
                                Please fill out all required fields marked with <span class="required">*</span>
                            </div>
                            
                            <?php echo $message; ?>
                            
                            <form method="POST" id="admissionForm">
                                <input type="hidden" name="submit_admission" value="1">
                                
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Student's Full Name <span class="required">*</span></label>
                                        <input type="text" name="student_name" class="form-control-large" required placeholder="e.g., Mary Akello">
                                    </div>
                                    <div class="form-group">
                                        <label>Parent/Guardian Name</label>
                                        <input type="text" name="parent_name" class="form-control-large" placeholder="e.g., John Akello">
                                    </div>
                                </div>
                                
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Email Address <span class="required">*</span></label>
                                        <input type="email" name="email" class="form-control-large" required placeholder="example@email.com">
                                    </div>
                                    <div class="form-group">
                                        <label>Phone Number <span class="required">*</span></label>
                                        <input type="tel" name="phone" class="form-control-large" required placeholder="+256 XXX XXX XXX">
                                    </div>
                                </div>
                                
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Applying for Grade <span class="required">*</span></label>
                                        <select name="grade_applying" class="form-control-large" required>
                                            <option value="">Select Grade</option>
                                            <option value="S1">Senior 1 (S1) - O-Level</option>
                                            <option value="S2">Senior 2 (S2) - O-Level</option>
                                            <option value="S3">Senior 3 (S3) - O-Level</option>
                                            <option value="S4">Senior 4 (S4) - O-Level</option>
                                            <option value="S5">Senior 5 (S5) - A-Level</option>
                                            <option value="S6">Senior 6 (S6) - A-Level</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Academic Year</label>
                                        <select name="academic_year" class="form-control-large">
                                            <option value="2024">2024 Academic Year</option>
                                            <option value="2025">2025 Academic Year</option>
                                            <option value="2026">2026 Academic Year</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label>Previous School</label>
                                    <input type="text" name="previous_school" class="form-control-large" placeholder="Name of previous/current school">
                                </div>
                                
                                <div class="form-group">
                                    <label>Additional Information / Message</label>
                                    <textarea name="message" class="form-control-large" placeholder="Tell us anything else you'd like us to know..."></textarea>
                                </div>
                                
                                <button type="submit" class="btn-submit-large">
                                    <i class="fas fa-paper-plane"></i> Submit Application
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ====== TAB 2: DOWNLOADS ====== -->
        <div class="tab-content <?php echo $active_tab == 'downloads' ? 'active' : ''; ?>" id="tab-downloads">
            <div class="row">
                <div class="col-lg-8">
                    <!-- ====== SUBMIT DOCUMENT SECTION ====== -->
                    <div class="submit-doc-section" id="submit-doc">
                        <h3><i class="fas fa-cloud-upload-alt"></i> Submit a Document</h3>
                        <p class="subtitle">Upload applications, assignments, or any file for admin review.</p>
                        
                        <?php if($doc_message): ?>
                            <div class="alert alert-<?php echo $doc_message_type; ?> alert-dismissible fade show" role="alert">
                                <?php echo $doc_message; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="submit_document" value="1">
                            
                            <div class="doc-form-row">
                                <div class="doc-form-group">
                                    <label>Document Title <span class="required">*</span></label>
                                    <input type="text" name="doc_title" placeholder="e.g., Application Form 2024" required>
                                </div>
                                <div class="doc-form-group">
                                    <label>Category <span class="required">*</span></label>
                                    <select name="doc_category" required>
                                        <option value="">Select Category</option>
                                        <option value="forms">Forms</option>
                                        <option value="academic">Academic</option>
                                        <option value="administration">Administration</option>
                                        <option value="sports">Sports</option>
                                        <option value="spiritual">Spiritual</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="doc-form-group">
                                <label>Description</label>
                                <textarea name="doc_description" placeholder="Brief description of the document..."></textarea>
                            </div>
                            
                            <div class="doc-form-group">
                                <label>Choose File <span class="required">*</span></label>
                                <div class="file-input-wrapper">
                                    <input type="file" name="doc_file" required>
                                </div>
                                <div class="allowed-formats">
                                    <i class="fas fa-info-circle"></i> Allowed formats: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, JPG, PNG, GIF, ZIP, RAR (Max: 10MB)
                                </div>
                            </div>
                            
                            <button type="submit" class="btn-submit-doc">
                                <i class="fas fa-upload"></i> Submit Document
                            </button>
                        </form>
                    </div>
                    
                    <!-- ====== DOWNLOADS LIST ====== -->
                    <div class="downloads-header">
                        <h2><i class="fas fa-download"></i> Available Downloads</h2>
                        <div class="categories-filter">
                            <a href="?tab=downloads&category=all" class="btn-category <?php echo $category == 'all' ? 'active' : ''; ?>">All</a>
                            <?php foreach($categories as $cat): ?>
                            <a href="?tab=downloads&category=<?php echo urlencode($cat); ?>" class="btn-category <?php echo $category == $cat ? 'active' : ''; ?>"><?php echo ucfirst($cat); ?></a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <?php if(empty($downloads)): ?>
                        <div class="text-center py-5" style="background: white; border-radius: 15px;">
                            <i class="fas fa-folder-open" style="font-size: 4rem; color: #dee2e6;"></i>
                            <p class="mt-3" style="font-size: 1.1rem; color: #7f8c8d;">No documents available in this category.</p>
                        </div>
                    <?php else: ?>
                        <div class="downloads-grid">
                            <?php foreach($downloads as $download): ?>
                            <div class="download-card">
                                <div class="doc-icon">
                                    <i class="fas fa-file-pdf"></i>
                                </div>
                                <h4><?php echo htmlspecialchars($download['title']); ?></h4>
                                <p><?php echo htmlspecialchars($download['description']); ?></p>
                                <div class="doc-meta">
                                    <span><i class="fas fa-download"></i> <?php echo number_format($download['download_count']); ?></span>
                                    <span><i class="fas fa-file"></i> <?php echo $download['file_size']; ?></span>
                                    <span><i class="fas fa-tag"></i> <?php echo ucfirst($download['category']); ?></span>
                                </div>
                                <a href="download.php?id=<?php echo $download['id']; ?>" class="btn-download">
                                    <i class="fas fa-download"></i> Download
                                </a>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Sidebar -->
                <div class="col-lg-4">
                    <div class="sidebar-widget">
                        <h3><i class="fas fa-folder"></i> Categories</h3>
                        <ul class="category-list">
                            <li><a href="?tab=downloads&category=all">All Files <span><?php echo $conn->query("SELECT COUNT(*) FROM downloads")->fetchColumn(); ?></span></a></li>
                            <?php foreach($categories as $cat): ?>
                            <?php
                            $countStmt = $conn->prepare("SELECT COUNT(*) FROM downloads WHERE category = ?");
                            $countStmt->execute([$cat]);
                            $count = $countStmt->fetchColumn();
                            ?>
                            <li><a href="?tab=downloads&category=<?php echo urlencode($cat); ?>"><?php echo ucfirst($cat); ?> <span><?php echo $count; ?></span></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    
                    <div class="sidebar-widget">
                        <h3><i class="fas fa-info-circle"></i> Need Help?</h3>
                        <p>If you can't find what you're looking for, please contact us:</p>
                        <p><i class="fas fa-envelope"></i> stellamariscollege2025@gmail.com</p>
                        <p><i class="fas fa-phone"></i> +256779094664</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ====== JAVASCRIPT ====== -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            tabBtns.forEach(b => b.classList.remove('active'));
            tabContents.forEach(c => c.classList.remove('active'));

            this.classList.add('active');
            const tabId = this.dataset.tab;
            document.getElementById('tab-' + tabId).classList.add('active');

            const url = new URL(window.location);
            url.searchParams.set('tab', tabId);
            window.history.pushState({}, '', url);
        });
    });

    const form = document.getElementById('admissionForm');
    if(form) {
        form.addEventListener('submit', function(e) {
            const email = document.querySelector('input[name="email"]').value;
            const phone = document.querySelector('input[name="phone"]').value;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const phoneRegex = /^[\+]?[0-9]{10,13}$/;
            
            if (!emailRegex.test(email)) {
                e.preventDefault();
                alert('Please enter a valid email address');
                return false;
            }
            
            if (!phoneRegex.test(phone.replace(/\s/g, ''))) {
                e.preventDefault();
                alert('Please enter a valid phone number (10-13 digits)');
                return false;
            }
            
            return true;
        });
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const tab = urlParams.get('tab');
    if(tab) {
        const btn = document.querySelector(`.tab-btn[data-tab="${tab}"]`);
        if(btn) btn.click();
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });
});
</script>

<?php include 'includes/footer.php'; ?>