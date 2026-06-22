<?php
// admin/manage-inquiries.php - Full admission management
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

// Update status (admission_status) via AJAX or POST
if(isset($_POST['update_status'])) {
    $id = $_POST['id'];
    $status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE inquiries SET admission_status = ? WHERE id = ?");
    if($stmt->execute([$status, $id])) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit();
}

// Handle sending email with possible attachment
if(isset($_POST['send_email'])) {
    $id = $_POST['id'];
    $subject = $_POST['subject'];
    $messageBody = $_POST['message'];
    $sendCopy = isset($_POST['send_copy']);
    
    // Get applicant email
    $stmt = $conn->prepare("SELECT email, student_name FROM inquiries WHERE id = ?");
    $stmt->execute([$id]);
    $applicant = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if($applicant) {
        $to = $applicant['email'];
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: Stella Maris College Admissions <admissions@stellamaris.edu.ug>\r\n";
        
        $fullMessage = "<html><body>";
        $fullMessage .= "<h2>Dear {$applicant['student_name']},</h2>";
        $fullMessage .= "<p>" . nl2br(htmlspecialchars($messageBody)) . "</p>";
        $fullMessage .= "<br><p>Best regards,<br>Admissions Office<br>Stella Maris College Nsuube</p>";
        $fullMessage .= "</body></html>";
        
        $attachments = [];
        // Handle uploaded admission letter (if any)
        if(isset($_FILES['admission_letter']) && $_FILES['admission_letter']['error'] == 0) {
            $allowed = ['pdf'];
            $ext = strtolower(pathinfo($_FILES['admission_letter']['name'], PATHINFO_EXTENSION));
            if(in_array($ext, $allowed)) {
                $upload_dir = '../uploads/admissions/';
                if(!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
                $new_filename = 'admission_letter_' . $id . '_' . time() . '.pdf';
                if(move_uploaded_file($_FILES['admission_letter']['tmp_name'], $upload_dir . $new_filename)) {
                    $file_path = 'uploads/admissions/' . $new_filename;
                    $attachments[] = $upload_dir . $new_filename;
                    // Save path in database
                    $updateStmt = $conn->prepare("UPDATE inquiries SET admission_letter_path = ? WHERE id = ?");
                    $updateStmt->execute([$file_path, $id]);
                }
            }
        }
        
        // Send email using mail() with attachments (simplified, you may use PHPMailer)
        $boundary = md5(time());
        $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";
        
        $body = "--$boundary\r\n";
        $body .= "Content-Type: text/html; charset=UTF-8\r\n";
        $body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
        $body .= $fullMessage . "\r\n\r\n";
        
        foreach($attachments as $file) {
            if(file_exists($file)) {
                $fileData = file_get_contents($file);
                $fileData = chunk_split(base64_encode($fileData));
                $body .= "--$boundary\r\n";
                $body .= "Content-Type: application/pdf; name=\"" . basename($file) . "\"\r\n";
                $body .= "Content-Transfer-Encoding: base64\r\n";
                $body .= "Content-Disposition: attachment; filename=\"" . basename($file) . "\"\r\n\r\n";
                $body .= $fileData . "\r\n\r\n";
            }
        }
        $body .= "--$boundary--";
        
        if(mail($to, $subject, $body, $headers)) {
            $message = "Email sent successfully!";
            $messageType = "success";
        } else {
            $message = "Failed to send email. Check server configuration.";
            $messageType = "danger";
        }
    } else {
        $message = "Applicant not found.";
        $messageType = "danger";
    }
    header("Location: manage-inquiries.php?msg=" . urlencode($message) . "&type=" . $messageType);
    exit();
}

// Delete inquiry
if(isset($_POST['delete'])) {
    $id = $_POST['id'];
    $stmt = $conn->prepare("DELETE FROM inquiries WHERE id = ?");
    if($stmt->execute([$id])) {
        $message = "Inquiry deleted successfully!";
        $messageType = "success";
    }
}

$inquiries = $conn->query("SELECT * FROM inquiries ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Inquiries - Stella Maris College Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* your existing sidebar styles ... */
        .status-badge {
            font-size: 12px;
            padding: 5px 10px;
        }
        .action-btns .btn-sm {
            margin: 2px;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <div class="container-fluid">
        <div class="row">
            <?php include 'sidebar.php'; ?>
            <main class="col-md-9 col-lg-10 ms-sm-auto px-md-4 admin-main-content">
                <div class="d-flex justify-content-between pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Admission Inquiries</h1>
                    <button class="btn btn-success" onclick="window.location.href='export-inquiries.php'">
                        <i class="fas fa-file-excel"></i> Export to Excel
                    </button>
                </div>
                
                <?php if($message): ?>
                <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show"><?php echo $message; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-users"></i> All Admission Inquiries</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped" id="inquiriesTable">
                            <thead>
                                <tr><th>Date</th><th>Student Name</th><th>Parent Name</th><th>Email</th><th>Grade</th><th>Admission Status</th><th>Actions</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach($inquiries as $inquiry): ?>
                                <tr>
                                    <td><?php echo date('M d, Y', strtotime($inquiry['created_at'])); ?></td>
                                    <td><?php echo htmlspecialchars($inquiry['student_name']); ?></td>
                                    <td><?php echo htmlspecialchars($inquiry['parent_name']); ?></td>
                                    <td><?php echo htmlspecialchars($inquiry['email']); ?></td>
                                    <td><?php echo htmlspecialchars($inquiry['grade_applying']); ?></td>
                                    <td>
                                        <select class="form-select form-select-sm status-select" data-id="<?php echo $inquiry['id']; ?>" style="width:130px;">
                                            <option value="pending" <?php echo $inquiry['admission_status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="shortlisted" <?php echo $inquiry['admission_status'] == 'shortlisted' ? 'selected' : ''; ?>>Shortlisted</option>
                                            <option value="accepted" <?php echo $inquiry['admission_status'] == 'accepted' ? 'selected' : ''; ?>>Accepted</option>
                                            <option value="rejected" <?php echo $inquiry['admission_status'] == 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                            <option value="enrolled" <?php echo $inquiry['admission_status'] == 'enrolled' ? 'selected' : ''; ?>>Enrolled</option>
                                        </select>
                                    </td>
                                    <td class="action-btns">
                                        <button class="btn btn-sm btn-info" onclick="viewInquiry(<?php echo htmlspecialchars(json_encode($inquiry)); ?>)"><i class="fas fa-eye"></i></button>
                                        <button class="btn btn-sm btn-primary" onclick="openEmailModal(<?php echo $inquiry['id']; ?>, '<?php echo htmlspecialchars($inquiry['student_name']); ?>', '<?php echo htmlspecialchars($inquiry['email']); ?>')"><i class="fas fa-envelope"></i> Email</button>
                                        <form method="POST" style="display:inline;"><input type="hidden" name="id" value="<?php echo $inquiry['id']; ?>"><input type="hidden" name="delete" value="1"><button class="btn btn-sm btn-danger" onclick="return confirm('Delete this inquiry?')"><i class="fas fa-trash"></i></button></form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <!-- View Inquiry Modal (same as before) -->
    <div class="modal fade" id="viewInquiryModal" tabindex="-1">...</div>
    
    <!-- Send Email Modal with Admission Letter Upload -->
    <div class="modal fade" id="emailModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" id="email_inquiry_id">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><i class="fas fa-paper-plane"></i> Send Admission Communication</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Subject</label>
                            <input type="text" name="subject" class="form-control" required value="Update on your admission application - Stella Maris College">
                        </div>
                        <div class="mb-3">
                            <label>Message</label>
                            <textarea name="message" class="form-control" rows="6" required>Dear Student,

We are pleased to inform you about the status of your admission application. Please find attached the admission letter.

Best regards,
Admissions Office</textarea>
                        </div>
                        <div class="mb-3">
                            <label>Upload Admission Letter (PDF)</label>
                            <input type="file" name="admission_letter" class="form-control" accept=".pdf">
                            <small class="text-muted">Optional. Upload a signed admission letter (PDF) to send as attachment.</small>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="send_copy" id="sendCopy">
                            <label class="form-check-label" for="sendCopy">Send a copy to myself (admin)</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="send_email" class="btn btn-primary">Send Email</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <button class="sidebar-toggle" id="sidebarToggle"><i class="fas fa-bars"></i></button>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#inquiriesTable').DataTable({ order: [[0, 'desc']], pageLength: 15 });
            $('#sidebarToggle').on('click', function() { $('.sidebar').toggleClass('show'); });
            $('.sidebar .nav-link').on('click', function() { if ($(window).width() <= 768) $('.sidebar').removeClass('show'); });
            
            // Update status on change via AJAX
            $('.status-select').on('change', function() {
                var id = $(this).data('id');
                var status = $(this).val();
                $.post('manage-inquiries.php', { update_status: 1, id: id, status: status }, function(response) {
                    var res = JSON.parse(response);
                    if(res.success) {
                        alert('Status updated');
                    } else {
                        alert('Error updating status');
                    }
                });
            });
        });
        
        let currentInquiry = null;
        function viewInquiry(inquiry) {
            currentInquiry = inquiry;
            const details = `<div class="row">
                <div class="col-md-6"><p><strong>Date:</strong> ${new Date(inquiry.created_at).toLocaleDateString()}</p>
                <p><strong>Student:</strong> ${inquiry.student_name}</p>
                <p><strong>Parent:</strong> ${inquiry.parent_name || 'N/A'}</p>
                <p><strong>Email:</strong> ${inquiry.email}</p></div>
                <div class="col-md-6"><p><strong>Phone:</strong> ${inquiry.phone}</p>
                <p><strong>Grade:</strong> ${inquiry.grade_applying}</p>
                <p><strong>Year:</strong> ${inquiry.academic_year || 'N/A'}</p>
                <p><strong>Status:</strong> <span class="badge bg-${inquiry.admission_status === 'pending' ? 'warning' : (inquiry.admission_status === 'accepted' ? 'success' : 'secondary')}">${inquiry.admission_status}</span></p></div>
                <div class="col-12"><p><strong>Message:</strong><br>${inquiry.message || 'No message'}</p></div>
                ${inquiry.admission_letter_path ? `<div class="col-12"><p><strong>Admission Letter:</strong> <a href="../${inquiry.admission_letter_path}" target="_blank"><i class="fas fa-file-pdf"></i> Download</a></p></div>` : ''}
            </div>`;
            $('#inquiryDetails').html(details);
            $('#viewInquiryModal').modal('show');
        }
        
        function openEmailModal(id, name, email) {
            $('#email_inquiry_id').val(id);
            // Optionally pre-fill message with student name
            var msg = `Dear ${name},\n\nWe are pleased to inform you about the status of your admission application. Please find attached the admission letter.\n\nBest regards,\nAdmissions Office`;
            $('textarea[name="message"]').val(msg);
            $('#emailModal').modal('show');
        }
    </script>
</body>
</html>