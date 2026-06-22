<?php
// admin/manage-alumni.php - Manage alumni with Excel export
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

// Handle delete
if(isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM alumni WHERE id = ?");
    if($stmt->execute([$id])) {
        $message = "Alumni record deleted successfully!";
        $messageType = "success";
    }
}

// Handle export to Excel
if(isset($_GET['export'])) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=alumni_export_' . date('Y-m-d') . '.csv');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Full Name', 'Email', 'Phone', 'Graduation Year', 'Marital Status', 'Profession', 'Employment Status', 'Registration Date']);
    
    $alumni = $conn->query("SELECT * FROM alumni ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
    foreach($alumni as $row) {
        fputcsv($output, [
            $row['id'],
            $row['full_name'],
            $row['email'],
            $row['phone'],
            $row['graduation_year'],
            $row['marital_status'],
            $row['profession'],
            $row['employment_status'],
            $row['created_at']
        ]);
    }
    fclose($output);
    exit();
}

$alumni = $conn->query("SELECT * FROM alumni ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Alumni - Stella Maris College Admin</title>
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
                    <h1 class="h2">Manage Alumni (Old Girls)</h1>
                    <div>
                        <a href="?export=1" class="btn btn-success">
                            <i class="fas fa-file-excel"></i> Export to Excel
                        </a>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAlumniModal">
                            <i class="fas fa-plus"></i> Add Alumni
                        </button>
                    </div>
                </div>
                
                <?php if($message): ?>
                <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-users"></i> Alumni Directory</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped" id="alumniTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Grad Year</th>
                                    <th>Profession</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($alumni as $alumnus): ?>
                                <tr>
                                    <td><?php echo $alumnus['id']; ?></td>
                                    <td><?php echo htmlspecialchars($alumnus['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($alumnus['email']); ?></td>
                                    <td><?php echo htmlspecialchars($alumnus['phone']); ?></td>
                                    <td><?php echo $alumnus['graduation_year']; ?></td>
                                    <td><?php echo htmlspecialchars($alumnus['profession'] ?: 'N/A'); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $alumnus['employment_status'] == 'employed' ? 'success' : ($alumnus['employment_status'] == 'student' ? 'info' : 'warning'); ?>">
                                            <?php echo ucfirst($alumnus['employment_status'] ?: 'N/A'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick="viewAlumni(<?php echo htmlspecialchars(json_encode($alumnus)); ?>)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <a href="?delete=<?php echo $alumnus['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this alumni record?')">
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
    
    <!-- Add Alumni Modal -->
    <div class="modal fade" id="addAlumniModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="add-alumni.php">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><i class="fas fa-user-plus"></i> Add New Alumni</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Full Name *</label>
                            <input type="text" name="full_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Email *</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Phone</label>
                            <input type="text" name="phone" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Year you left *</label>
                            <select name="graduation_year" class="form-control" required>
                                <option value="">Select Year</option>
                                <?php for($year = 1970; $year <= date('Y'); $year++): ?>
                                <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Marital Status</label>
                            <select name="marital_status" class="form-control">
                                <option value="single">Single</option>
                                <option value="married">Married</option>
                                <option value="divorced">Divorced</option>
                                <option value="widowed">Widowed</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Profession</label>
                            <input type="text" name="profession" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Employment Status</label>
                            <select name="employment_status" class="form-control">
                                <option value="employed">Employed</option>
                                <option value="unemployed">Unemployed</option>
                                <option value="self-employed">Self Employed</option>
                                <option value="student">Student</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Alumni</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- View Alumni Modal -->
    <div class="modal fade" id="viewAlumniModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title"><i class="fas fa-user-circle"></i> Alumni Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="alumniDetails"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="sendEmailToAlumni()">
                        <i class="fas fa-envelope"></i> Send Email
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
            $('#alumniTable').DataTable({
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
        
        let currentAlumni = null;
        
        function viewAlumni(alumni) {
            currentAlumni = alumni;
            const details = `
                <div class="row">
                    <div class="col-12">
                        <p><strong>Full Name:</strong> ${alumni.full_name}</p>
                        <p><strong>Email:</strong> ${alumni.email}</p>
                        <p><strong>Phone:</strong> ${alumni.phone || 'N/A'}</p>
                        <p><strong>Graduation Year:</strong> ${alumni.graduation_year}</p>
                        <p><strong>Marital Status:</strong> ${alumni.marital_status || 'N/A'}</p>
                        <p><strong>Profession:</strong> ${alumni.profession || 'N/A'}</p>
                        <p><strong>Employment Status:</strong> ${alumni.employment_status || 'N/A'}</p>
                        <p><strong>Registered:</strong> ${new Date(alumni.created_at).toLocaleDateString()}</p>
                    </div>
                </div>
            `;
            $('#alumniDetails').html(details);
            $('#viewAlumniModal').modal('show');
        }
        
        function sendEmailToAlumni() {
            if(currentAlumni) {
                window.location.href = `mailto:${currentAlumni.email}?subject=Hello from Stella Maris College Alumni Association`;
            }
        }
    </script>
</body>
</html>