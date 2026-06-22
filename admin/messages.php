<?php
// admin/messages.php - View contact messages
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

// Mark as read
if(isset($_GET['read'])) {
    $id = $_GET['read'];
    $stmt = $conn->prepare("UPDATE contact_messages SET status = 'read' WHERE id = ?");
    $stmt->execute([$id]);
    $message = "Message marked as read!";
    $messageType = "success";
}

// Reply to message
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reply'])) {
    $id = $_POST['id'];
    $reply = $_POST['reply'];
    
    $stmt = $conn->prepare("UPDATE contact_messages SET admin_reply = ?, replied_at = NOW(), status = 'replied' WHERE id = ?");
    if($stmt->execute([$reply, $id])) {
        $message = "Reply sent successfully!";
        $messageType = "success";
    }
}

// Delete message
if(isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM contact_messages WHERE id = ?");
    $stmt->execute([$id]);
    $message = "Message deleted!";
    $messageType = "success";
}

$messages = $conn->query("SELECT * FROM contact_messages ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Messages - Stella Maris College Admin</title>
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
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php include 'sidebar.php'; ?>
            
            <main class="col-md-9 col-lg-10 ms-sm-auto px-md-4 admin-main-content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Contact Messages</h1>
                </div>
                
                <?php if($message): ?>
                <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-envelope"></i> All Messages</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped" id="messagesTable">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Subject</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($messages as $msg): ?>
                                <tr>
                                    <td><?php echo date('M d, Y h:i A', strtotime($msg['created_at'])); ?></td>
                                    <td><?php echo htmlspecialchars($msg['name']); ?></td>
                                    <td><?php echo htmlspecialchars($msg['email']); ?></td>
                                    <td><?php echo htmlspecialchars(substr($msg['subject'], 0, 30)); ?>...</td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            echo $msg['status'] == 'unread' ? 'danger' : ($msg['status'] == 'read' ? 'warning' : 'success'); 
                                        ?>">
                                            <?php echo ucfirst($msg['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick='viewMessage(<?php echo json_encode($msg); ?>)'>
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <a href="?delete=<?php echo $msg['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this message?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
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
    
    <!-- View Message Modal -->
    <div class="modal fade" id="viewMessageModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title"><i class="fas fa-envelope-open"></i> Message Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="messageDetails"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="replyToMessage()">
                        <i class="fas fa-reply"></i> Reply
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Sidebar Toggle Button -->
    <button class="sidebar-toggle" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#messagesTable').DataTable({
                order: [[0, 'desc']],
                pageLength: 15
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
        
        let currentMessage = null;
        
        function viewMessage(message) {
            currentMessage = message;
            const details = `
                <div class="row">
                    <div class="col-12">
                        <p><strong>From:</strong> ${message.name} (${message.email})</p>
                        <p><strong>Phone:</strong> ${message.phone || 'N/A'}</p>
                        <p><strong>Subject:</strong> ${message.subject}</p>
                        <p><strong>Date:</strong> ${new Date(message.created_at).toLocaleString()}</p>
                        <hr>
                        <p><strong>Message:</strong></p>
                        <p>${message.message}</p>
                        ${message.admin_reply ? `<hr><p><strong>Your Reply:</strong><br>${message.admin_reply}</p>` : ''}
                    </div>
                </div>
            `;
            $('#messageDetails').html(details);
            $('#viewMessageModal').modal('show');
            
            // Mark as read
            if(message.status === 'unread') {
                window.location.href = `?read=${message.id}`;
            }
        }
        
        function replyToMessage() {
            if(currentMessage) {
                const reply = prompt('Enter your reply:', currentMessage.admin_reply || '');
                if(reply !== null) {
                    $('<form method="POST">' +
                        '<input type="hidden" name="id" value="' + currentMessage.id + '">' +
                        '<input type="hidden" name="reply" value="' + reply.replace(/['"]/g, '\\"') + '">' +
                        '</form>').appendTo('body').submit();
                }
            }
        }
    </script>
</body>
</html>