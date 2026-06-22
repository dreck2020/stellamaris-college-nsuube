<?php
// admin/export-report.php - Export various reports
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

// Handle report generation
if(isset($_POST['generate_report'])) {
    $report_type = $_POST['report_type'];
    $format = $_POST['format'];
    
    if($report_type == 'inventory') {
        $data = $conn->query("SELECT * FROM inventory ORDER BY category, item_name")->fetchAll(PDO::FETCH_ASSOC);
        $filename = 'inventory_report_' . date('Y-m-d');
        $headers = ['Item Name', 'Category', 'Quantity', 'Condition', 'Location', 'Purchase Date', 'Purchase Price', 'Notes'];
        
        if($format == 'csv') {
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=' . $filename . '.csv');
            
            $output = fopen('php://output', 'w');
            fputcsv($output, $headers);
            
            foreach($data as $row) {
                fputcsv($output, [
                    $row['item_name'],
                    $row['category'],
                    $row['quantity'],
                    $row['condition_status'],
                    $row['location'],
                    $row['purchase_date'],
                    $row['purchase_price'],
                    $row['notes']
                ]);
            }
            fclose($output);
            exit();
        }
    }
    elseif($report_type == 'alumni') {
        $data = $conn->query("SELECT * FROM alumni ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
        $filename = 'alumni_report_' . date('Y-m-d');
        $headers = ['ID', 'Full Name', 'Email', 'Phone', 'Graduation Year', 'Marital Status', 'Profession', 'Employment Status', 'Registration Date'];
        
        if($format == 'csv') {
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=' . $filename . '.csv');
            
            $output = fopen('php://output', 'w');
            fputcsv($output, $headers);
            
            foreach($data as $row) {
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
    }
    elseif($report_type == 'messages') {
        $data = $conn->query("SELECT * FROM contact_messages ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
        $filename = 'contact_messages_report_' . date('Y-m-d');
        $headers = ['Date', 'Name', 'Email', 'Phone', 'Subject', 'Message', 'Status', 'Reply'];
        
        if($format == 'csv') {
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=' . $filename . '.csv');
            
            $output = fopen('php://output', 'w');
            fputcsv($output, $headers);
            
            foreach($data as $row) {
                fputcsv($output, [
                    $row['created_at'],
                    $row['name'],
                    $row['email'],
                    $row['phone'],
                    $row['subject'],
                    $row['message'],
                    $row['status'],
                    $row['admin_reply']
                ]);
            }
            fclose($output);
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export Reports - Stella Maris College Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php include 'sidebar.php'; ?>
            
            <main class="col-md-9 col-lg-10 ms-sm-auto px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Export Reports</h1>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="fas fa-file-export"></i> Generate Report</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <div class="mb-3">
                                        <label>Report Type</label>
                                        <select name="report_type" class="form-control" required>
                                            <option value="inventory">Inventory Report</option>
                                            <option value="alumni">Alumni Report</option>
                                            <option value="messages">Contact Messages Report</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label>Export Format</label>
                                        <select name="format" class="form-control" required>
                                            <option value="csv">CSV (Excel Compatible)</option>
                                        </select>
                                    </div>
                                    
                                    <button type="submit" name="generate_report" class="btn btn-primary">
                                        <i class="fas fa-download"></i> Generate & Download Report
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Report Information</h5>
                            </div>
                            <div class="card-body">
                                <ul>
                                    <li><strong>Inventory Report:</strong> Complete list of all school assets including quantity, condition, and location</li>
                                    <li><strong>Alumni Report:</strong> Complete alumni database with contact information and employment status</li>
                                    <li><strong>Contact Messages Report:</strong> All contact form submissions with replies</li>
                                </ul>
                                <p class="text-muted mt-3">All reports are exported in CSV format, compatible with Microsoft Excel and Google Sheets.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>