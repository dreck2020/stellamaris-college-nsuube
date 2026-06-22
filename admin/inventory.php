<?php
// admin/inventory.php - View inventory items
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
    $stmt = $conn->prepare("DELETE FROM inventory WHERE id = ?");
    if($stmt->execute([$id])) {
        $message = "Item deleted successfully!";
        $messageType = "success";
    }
}

$inventory = $conn->query("SELECT * FROM inventory ORDER BY category, item_name")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management - Stella Maris College Admin</title>
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
                    <h1 class="h2">Inventory Management</h1>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addInventoryModal">
                        <i class="fas fa-plus"></i> Add Inventory Item
                    </button>
                </div>
                
                <?php if($message): ?>
                <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-boxes"></i> School Assets Inventory</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped" id="inventoryTable">
                            <thead>
                                <tr>
                                    <th>Item Name</th>
                                    <th>Category</th>
                                    <th>Quantity</th>
                                    <th>Condition</th>
                                    <th>Location</th>
                                    <th>Purchase Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($inventory as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                                    <td><span class="badge bg-secondary"><?php echo htmlspecialchars($item['category']); ?></span></td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            echo $item['condition_status'] == 'new' ? 'success' : 
                                                ($item['condition_status'] == 'good' ? 'info' : 'danger'); 
                                        ?>">
                                            <?php echo ucfirst($item['condition_status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($item['location']); ?></td>
                                    <td><?php echo $item['purchase_date'] ? date('M d, Y', strtotime($item['purchase_date'])) : 'N/A'; ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick='editInventory(<?php echo json_encode($item); ?>)'>
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="?delete=<?php echo $item['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this item?')">
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

    <!-- Add Inventory Modal -->
    <div class="modal fade" id="addInventoryModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" action="add-inventory.php">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><i class="fas fa-plus"></i> Add Inventory Item</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Item Name *</label>
                                <input type="text" name="item_name" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Category *</label>
                                <select name="category" class="form-control" required>
                                    <option value="Furniture">Furniture</option>
                                    <option value="Technology">Technology</option>
                                    <option value="Lab Equipment">Lab Equipment</option>
                                    <option value="Sports Equipment">Sports Equipment</option>
                                    <option value="Books">Books</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Quantity *</label>
                                <input type="number" name="quantity" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Condition *</label>
                                <select name="condition_status" class="form-control">
                                    <option value="new">New</option>
                                    <option value="good">Good</option>
                                    <option value="damaged">Damaged</option>
                                    <option value="repair">Under Repair</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Location</label>
                                <input type="text" name="location" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Purchase Date</label>
                                <input type="date" name="purchase_date" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Purchase Price (USD)</label>
                                <input type="number" step="0.01" name="purchase_price" class="form-control">
                            </div>
                            <div class="col-12 mb-3">
                                <label>Notes</label>
                                <textarea name="notes" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Inventory Modal -->
    <div class="modal fade" id="editInventoryModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" action="edit-inventory.php">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Inventory Item</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Item Name *</label>
                                <input type="text" name="item_name" id="edit_item_name" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Category *</label>
                                <select name="category" id="edit_category" class="form-control" required>
                                    <option value="Furniture">Furniture</option>
                                    <option value="Technology">Technology</option>
                                    <option value="Lab Equipment">Lab Equipment</option>
                                    <option value="Sports Equipment">Sports Equipment</option>
                                    <option value="Books">Books</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Quantity *</label>
                                <input type="number" name="quantity" id="edit_quantity" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Condition *</label>
                                <select name="condition_status" id="edit_condition" class="form-control">
                                    <option value="new">New</option>
                                    <option value="good">Good</option>
                                    <option value="damaged">Damaged</option>
                                    <option value="repair">Under Repair</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Location</label>
                                <input type="text" name="location" id="edit_location" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Purchase Date</label>
                                <input type="date" name="purchase_date" id="edit_purchase_date" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Purchase Price (USD)</label>
                                <input type="number" step="0.01" name="purchase_price" id="edit_purchase_price" class="form-control">
                            </div>
                            <div class="col-12 mb-3">
                                <label>Notes</label>
                                <textarea name="notes" id="edit_notes" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
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
            $('#inventoryTable').DataTable({
                order: [[0, 'asc']],
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
        
        function editInventory(item) {
            $('#edit_id').val(item.id);
            $('#edit_item_name').val(item.item_name);
            $('#edit_category').val(item.category);
            $('#edit_quantity').val(item.quantity);
            $('#edit_condition').val(item.condition_status);
            $('#edit_location').val(item.location);
            $('#edit_purchase_date').val(item.purchase_date);
            $('#edit_purchase_price').val(item.purchase_price);
            $('#edit_notes').val(item.notes);
            $('#editInventoryModal').modal('show');
        }
    </script>
</body>
</html>