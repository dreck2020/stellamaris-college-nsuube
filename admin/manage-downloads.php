<?php
// admin/manage-downloads.php - Manage downloadable files + user submissions
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

// ========== HANDLE ADMIN FILE DELETE ==========
if(isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    $stmt = $conn->prepare("SELECT file_path FROM downloads WHERE id = ?");
    $stmt->execute([$id]);
    $file = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if($file && file_exists('../' . $file['file_path'])) {
        unlink('../' . $file['file_path']);
    }
    
    $stmt = $conn->prepare("DELETE FROM downloads WHERE id = ?");
    if($stmt->execute([$id])) {
        $message = "File deleted successfully!";
        $messageType = "success";
    }
}

// ========== HANDLE USER DOCUMENT DELETE ==========
if(isset($_GET['delete_userdoc'])) {
    $id = (int)$_GET['delete_userdoc'];
    $stmt = $conn->prepare("SELECT file_path FROM user_documents WHERE id = ?");
    $stmt->execute([$id]);
    $file = $stmt->fetch(PDO::FETCH_ASSOC);
    if($file && file_exists('../' . $file['file_path'])) {
        unlink('../' . $file['file_path']);
    }
    $stmt = $conn->prepare("DELETE FROM user_documents WHERE id = ?");
    $stmt->execute([$id]);
    $message = "User document deleted successfully!";
    $messageType = "success";
}

// ========== HANDLE ADMIN FILE UPLOAD ==========
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    
    if(isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $allowed = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'zip', 'ppt', 'pptx', 'jpg', 'jpeg', 'png'];
        $filename = $_FILES['file']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $file_size = $_FILES['file']['size'];
        
        $size_mb = round($file_size / 1048576, 2);
        
        if(in_array($ext, $allowed)) {
            $new_filename = 'download_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
            $upload_path = '../uploads/downloads/';
            
            if(!file_exists($upload_path)) {
                mkdir($upload_path, 0777, true);
            }
            
            if(move_uploaded_file($_FILES['file']['tmp_name'], $upload_path . $new_filename)) {
                $file_path = 'uploads/downloads/' . $new_filename;
                
                $stmt = $conn->prepare("INSERT INTO downloads (title, description, file_path, file_size, category) VALUES (?, ?, ?, ?, ?)");
                if($stmt->execute([$title, $description, $file_path, $size_mb . ' MB', $category])) {
                    $message = "File uploaded successfully!";
                    $messageType = "success";
                    echo '<script>setTimeout(function(){ window.location.href = "manage-downloads.php"; }, 1500);</script>';
                }
            }
        } else {
            $message = "Invalid file type. Allowed: PDF, DOC, DOCX, XLS, XLSX, ZIP, PPT, PNG, JPG";
            $messageType = "danger";
        }
    } else {
        $message = "Please select a file to upload.";
        $messageType = "danger";
    }
}

// ========== HANDLE USER DOCUMENT STATUS UPDATE ==========
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_user_status'])) {
    $doc_id = (int)$_POST['doc_id'];
    $status = $_POST['status'];
    $admin_notes = trim($_POST['admin_notes']);
    
    $stmt = $conn->prepare("UPDATE user_documents SET status = ?, admin_notes = ?, reviewed_at = NOW() WHERE id = ?");
    if($stmt->execute([$status, $admin_notes, $doc_id])) {
        $message = "Document status updated to: " . ucfirst($status);
        $messageType = "success";
    } else {
        $message = "Failed to update document status.";
        $messageType = "danger";
    }
}

// ========== FETCH ADMIN DOWNLOADS ==========
$downloads = $conn->query("SELECT * FROM downloads ORDER BY uploaded_at DESC")->fetchAll(PDO::FETCH_ASSOC);

// ========== FETCH USER DOCUMENTS ==========
$userDocs = $conn->query("SELECT * FROM user_documents ORDER BY submitted_at DESC")->fetchAll(PDO::FETCH_ASSOC);

// ========== GET STATISTICS ==========
$total_downloads = count($downloads);
$total_user_docs = count($userDocs);
$pending_docs = $conn->query("SELECT COUNT(*) FROM user_documents WHERE status = 'pending'")->fetchColumn();
$approved_docs = $conn->query("SELECT COUNT(*) FROM user_documents WHERE status = 'approved'")->fetchColumn();
$rejected_docs = $conn->query("SELECT COUNT(*) FROM user_documents WHERE status = 'rejected'")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Downloads - Stella Maris College Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
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
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .stat-card .number {
            font-size: 2rem;
            font-weight: bold;
        }
        .stat-card .label {
            color: #7f8c8d;
            font-size: 0.85rem;
        }
        .stat-card.total .number { color: #3498db; }
        .stat-card.pending .number { color: #f39c12; }
        .stat-card.approved .number { color: #27ae60; }
        .stat-card.rejected .number { color: #e74c3c; }
        
        .section-admin { border-top: 4px solid #3498db; }
        .section-users { border-top: 4px solid #27ae60; }
        .badge-admin { background: #3498db; }
        .badge-user { background: #27ae60; }
        
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
            .sidebar.show { left: 0; }
            .sidebar-toggle { display: flex; }
            .admin-main-content { padding-top: 70px !important; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
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
                    <h1 class="h2"><i class="fas fa-download"></i> Manage Downloads</h1>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFileModal">
                        <i class="fas fa-plus"></i> Add New File
                    </button>
                </div>
                
                <?php if($message): ?>
                <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <!-- ========== STATISTICS ========== -->
                <div class="stats-grid">
                    <div class="stat-card total">
                        <div class="number"><?= $total_downloads ?></div>
                        <div class="label"><i class="fas fa-file"></i> Admin Files</div>
                    </div>
                    <div class="stat-card pending">
                        <div class="number"><?= $pending_docs ?></div>
                        <div class="label"><i class="fas fa-clock"></i> Pending Review</div>
                    </div>
                    <div class="stat-card approved">
                        <div class="number"><?= $approved_docs ?></div>
                        <div class="label"><i class="fas fa-check-circle"></i> Approved</div>
                    </div>
                    <div class="stat-card rejected">
                        <div class="number"><?= $rejected_docs ?></div>
                        <div class="label"><i class="fas fa-times-circle"></i> Rejected</div>
                    </div>
                </div>
                
                <!-- ========== ADMIN UPLOADED FILES ========== -->
                <div class="card section-admin">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-download"></i> Admin Uploaded Files</h5>
                        <small class="text-light">Files uploaded by administrators</small>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="downloadsTable">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Category</th>
                                        <th>Size</th>
                                        <th>Downloads</th>
                                        <th>Uploaded</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($downloads as $file): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($file['title']); ?></td>
                                        <td><span class="badge badge-admin"><?php echo ucfirst($file['category']); ?></span></td>
                                        <td><?php echo $file['file_size']; ?></td>
                                        <td><?php echo number_format($file['download_count']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($file['uploaded_at'])); ?></td>
                                        <td>
                                            <a href="../download.php?id=<?php echo $file['id']; ?>" class="btn btn-sm btn-success" target="_blank">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <a href="?delete=<?php echo $file['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this file?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- ========== USER SUBMITTED DOCUMENTS ========== -->
                <div class="card mt-4 section-users">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-users"></i> User‑Submitted Documents</h5>
                        <small class="text-light">Documents uploaded by users for review</small>
                    </div>
                    <div class="card-body">
                        <?php if(empty($userDocs)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-file-upload" style="font-size: 3rem; color: #dee2e6;"></i>
                                <p class="mt-2 text-muted">No user submissions yet.</p>
                            </div>
                        <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped" id="userDocsTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>User</th>
                                        <th>Title</th>
                                        <th>File</th>
                                        <th>Status</th>
                                        <th>Submitted</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($userDocs as $doc): ?>
                                    <tr>
                                        <td><?php echo $doc['id']; ?></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($doc['user_name']); ?></strong>
                                            <br><small class="text-muted"><?php echo htmlspecialchars($doc['user_email']); ?></small>
                                            <?php if($doc['user_phone']): ?>
                                                <br><small class="text-muted"><i class="fas fa-phone"></i> <?php echo htmlspecialchars($doc['user_phone']); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($doc['title']); ?></strong>
                                            <?php if($doc['description']): ?>
                                                <br><small class="text-muted"><?php echo htmlspecialchars(substr($doc['description'], 0, 50)); ?>...</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="../download-user-file.php?id=<?php echo $doc['id']; ?>" class="btn btn-sm btn-info" target="_blank">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="../download-user-file.php?id=<?php echo $doc['id']; ?>" class="btn btn-sm btn-success">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo $doc['status']=='pending'?'warning':($doc['status']=='approved'?'success':'danger'); ?>">
                                                <?php echo ucfirst($doc['status']); ?>
                                            </span>
                                            <?php if($doc['admin_notes']): ?>
                                                <br><small class="text-muted" title="<?php echo htmlspecialchars($doc['admin_notes']); ?>">
                                                    <i class="fas fa-sticky-note"></i> <?php echo htmlspecialchars(substr($doc['admin_notes'], 0, 30)); ?>...
                                                </small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo date('M d, Y', strtotime($doc['submitted_at'])); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#reviewUserDocModal" 
                                                data-id="<?php echo $doc['id']; ?>" 
                                                data-status="<?php echo $doc['status']; ?>" 
                                                data-notes="<?php echo htmlspecialchars($doc['admin_notes']); ?>"
                                                data-title="<?php echo htmlspecialchars($doc['title']); ?>"
                                                data-user="<?php echo htmlspecialchars($doc['user_name']); ?>">
                                                <i class="fas fa-edit"></i> Review
                                            </button>
                                            <a href="?delete_userdoc=<?php echo $doc['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this document?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="mt-4 text-center text-muted small">
                    <i class="fas fa-info-circle"></i> 
                    <span class="text-primary">Admin Files: <?= $total_downloads ?></span> | 
                    <span class="text-success">User Submissions: <?= $total_user_docs ?></span> | 
                    <span class="text-warning">Pending Review: <?= $pending_docs ?></span>
                </div>
            </main>
        </div>
    </div>
    
    <!-- ========== ADD FILE MODAL ========== -->
    <div class="modal fade" id="addFileModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><i class="fas fa-plus"></i> Upload File</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>File Title *</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Category *</label>
                            <select name="category" class="form-control" required>
                                <option value="forms">Forms</option>
                                <option value="newsletters">Newsletters</option>
                                <option value="academic">Academic</option>
                                <option value="policies">Policies</option>
                                <option value="sports">Sports</option>
                                <option value="spiritual">Spiritual</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Description</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label>Select File *</label>
                            <input type="file" name="file" class="form-control" required>
                            <small class="text-muted">Allowed: PDF, DOC, DOCX, XLS, XLSX, ZIP, PPT, PNG, JPG (Max 10MB)</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- ========== REVIEW USER DOCUMENT MODAL ========== -->
    <div class="modal fade" id="reviewUserDocModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="doc_id" id="review_doc_id">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><i class="fas fa-edit"></i> Review Document</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>User</label>
                            <p class="form-control-plaintext" id="review_user_name">-</p>
                        </div>
                        <div class="mb-3">
                            <label>Document Title</label>
                            <p class="form-control-plaintext" id="review_doc_title">-</p>
                        </div>
                        <div class="mb-3">
                            <label>Status *</label>
                            <select name="status" id="review_doc_status" class="form-control">
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Admin Notes</label>
                            <textarea name="admin_notes" id="review_doc_notes" class="form-control" rows="3" placeholder="Add notes about this document..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="update_user_status" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Status
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- ========== SIDEBAR TOGGLE ========== -->
    <button class="sidebar-toggle" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#downloadsTable').DataTable({
                order: [[4, 'desc']],
                pageLength: 10,
                language: { search: "Search admin files:" }
            });
            
            $('#userDocsTable').DataTable({
                order: [[0, 'desc']],
                pageLength: 10,
                language: { search: "Search user documents:" },
                columnDefs: [{ orderable: false, targets: [3, 6] }]
            });
            
            $('#sidebarToggle').on('click', function() {
                $('.sidebar').toggleClass('show');
            });
            
            $('.sidebar .nav-link').on('click', function() {
                if ($(window).width() <= 768) {
                    $('.sidebar').removeClass('show');
                }
            });
            
            $('#reviewUserDocModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var modal = $(this);
                modal.find('#review_doc_id').val(button.data('id'));
                modal.find('#review_doc_status').val(button.data('status'));
                modal.find('#review_doc_notes').val(button.data('notes'));
                modal.find('#review_doc_title').text(button.data('title'));
                modal.find('#review_user_name').text(button.data('user'));
            });
        });
    </script>
</body>
</html>