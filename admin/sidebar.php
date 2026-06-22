<?php
// admin/sidebar.php - Updated with all page management links
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$current_page = basename($_SERVER['PHP_SELF']);
?>

<nav class="col-md-3 col-lg-2 d-md-block bg-dark sidebar" style="min-height: 100vh;">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            
            <!-- Content Management Section -->
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'manage-pages.php' ? 'active' : ''; ?>" href="manage-pages.php">
                    <i class="fas fa-file-alt"></i> Manage Pages
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'manage-news.php' ? 'active' : ''; ?>" href="manage-news.php">
                    <i class="fas fa-newspaper"></i> Manage News
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'manage-gallery.php' ? 'active' : ''; ?>" href="manage-gallery.php">
                    <i class="fas fa-images"></i> Gallery
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'manage-downloads.php' ? 'active' : ''; ?>" href="manage-downloads.php">
                    <i class="fas fa-download"></i> Downloads
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'manage-events.php' ? 'active' : ''; ?>" href="manage-events.php">
                    <i class="fas fa-calendar-alt"></i> Manage Events
                </a>
            </li>
            
            <!-- User Management Section -->
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'manage-alumni.php' ? 'active' : ''; ?>" href="manage-alumni.php">
                    <i class="fas fa-users"></i> Alumni
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'manage-inquiries.php' ? 'active' : ''; ?>" href="manage-inquiries.php">
                    <i class="fas fa-question-circle"></i> Inquiries
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'messages.php' ? 'active' : ''; ?>" href="messages.php">
                    <i class="fas fa-envelope"></i> Contact Messages
                </a>
            </li>
            
            <!-- Inventory Section -->
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'inventory.php' ? 'active' : ''; ?>" href="inventory.php">
                    <i class="fas fa-boxes"></i> Inventory
                </a>
            </li>
            <!--staff section -->
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'manage_staff.php' ? 'active' : ''; ?>" href="manage_staff.php">
                    <i class="fas fa-boxes"></i> Manage staff
                </a>
            </li>
            
            <!-- System Section -->
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'settings.php' ? 'active' : ''; ?>" href="settings.php">
                    <i class="fas fa-cog"></i> Settings
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'profile.php' ? 'active' : ''; ?>" href="profile.php">
                    <i class="fas fa-user-circle"></i> My Profile
                </a>
            </li>
            <li class="nav-item mt-4">
                <hr class="border-secondary">
            </li>
            <li class="nav-item">
                <a class="nav-link text-danger" href="logout.php" id="sidebarLogoutBtn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>
    </div>
</nav>

<style>
.sidebar {
    background: #1a1a2e !important;
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

@media (max-width: 768px) {
    .sidebar {
        position: fixed;
        top: 0;
        left: -280px;
        width: 280px;
        height: 100vh;
        z-index: 1050;
        transition: left 0.3s ease;
        overflow-y: auto;
    }
    
    .sidebar.show {
        left: 0;
    }
}
</style>