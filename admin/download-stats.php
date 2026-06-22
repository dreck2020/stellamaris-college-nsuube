<?php
// admin/download-stats.php - View download statistics
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../config/database.php';
$db = new Database();
$conn = $db->getConnection();

// Get all downloads with statistics
$downloads = $conn->query("SELECT * FROM downloads ORDER BY download_count DESC")->fetchAll(PDO::FETCH_ASSOC);
$total_downloads = array_sum(array_column($downloads, 'download_count'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download Statistics - Stella Maris College Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php include 'sidebar.php'; ?>
            
            <main class="col-md-9 col-lg-10 ms-sm-auto px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Download Statistics</h1>
                    <a href="manage-downloads.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Downloads
                    </a>
                </div>
                
                <!-- Summary Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card text-white bg-primary">
                            <div class="card-body">
                                <h5 class="card-title">Total Files</h5>
                                <h2><?php echo count($downloads); ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-success">
                            <div class="card-body">
                                <h5 class="card-title">Total Downloads</h5>
                                <h2><?php echo number_format($total_downloads); ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-info">
                            <div class="card-body">
                                <h5 class="card-title">Most Downloaded</h5>
                                <h6 class="mb-0"><?php echo !empty($downloads) ? htmlspecialchars($downloads[0]['title']) : 'N/A'; ?></h6>
                                <small><?php echo !empty($downloads) ? number_format($downloads[0]['download_count']) . ' downloads' : ''; ?></small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-warning">
                            <div class="card-body">
                                <h5 class="card-title">Average per File</h5>
                                <h2><?php echo count($downloads) > 0 ? round($total_downloads / count($downloads)) : 0; ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Download Statistics by File</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="downloadsChart" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Distribution</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="pieChart" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card mt-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-table"></i> Detailed File Statistics</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped" id="statsTable">
                            <thead>
                                <tr>
                                    <th>File Title</th>
                                    <th>Category</th>
                                    <th>File Size</th>
                                    <th>Downloads</th>
                                    <th>Uploaded</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($downloads as $file): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($file['title']); ?></td>
                                    <td><span class="badge bg-info"><?php echo ucfirst($file['category']); ?></span></td>
                                    <td><?php echo $file['file_size']; ?></td>
                                    <td>
                                        <strong><?php echo number_format($file['download_count']); ?></strong>
                                        <div class="progress mt-1" style="height: 5px;">
                                            <div class="progress-bar bg-success" style="width: <?php echo $total_downloads > 0 ? ($file['download_count'] / $total_downloads * 100) : 0; ?>%"></div>
                                        </div>
                                     </td>
                                    <td><?php echo date('M d, Y', strtotime($file['uploaded_at'])); ?></td>
                                    <td>
                                        <a href="../download.php?id=<?php echo $file['id']; ?>" class="btn btn-sm btn-success" target="_blank">
                                            <i class="fas fa-download"></i> Test
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
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#statsTable').DataTable({
                order: [[3, 'desc']],
                pageLength: 10
            });
        });
        
        // Bar Chart
        const ctx = document.getElementById('downloadsChart').getContext('2d');
        const fileNames = <?php echo json_encode(array_slice(array_column($downloads, 'title'), 0, 10)); ?>;
        const downloadCounts = <?php echo json_encode(array_slice(array_column($downloads, 'download_count'), 0, 10)); ?>;
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: fileNames,
                datasets: [{
                    label: 'Number of Downloads',
                    data: downloadCounts,
                    backgroundColor: 'rgba(26, 77, 140, 0.8)',
                    borderColor: '#1a4d8c',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Downloads Count'
                        }
                    },
                    x: {
                        ticks: {
                            callback: function(value, index, values) {
                                return fileNames[index].length > 15 ? fileNames[index].substring(0, 15) + '...' : fileNames[index];
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `Downloads: ${context.raw.toLocaleString()}`;
                            }
                        }
                    }
                }
            }
        });
        
        // Pie Chart
        const pieCtx = document.getElementById('pieChart').getContext('2d');
        const categories = {};
        <?php foreach($downloads as $file): ?>
            categories['<?php echo $file['category']; ?>'] = (categories['<?php echo $file['category']; ?>'] || 0) + <?php echo $file['download_count']; ?>;
        <?php endforeach; ?>
        
        new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode(array_keys($categories)); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_values($categories)); ?>,
                    backgroundColor: ['#1a4d8c', '#2e7d32', '#ffc107', '#dc3545', '#17a2b8', '#6c757d']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = <?php echo $total_downloads; ?>;
                                const percentage = ((context.raw / total) * 100).toFixed(1);
                                return `${context.label}: ${context.raw.toLocaleString()} downloads (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>