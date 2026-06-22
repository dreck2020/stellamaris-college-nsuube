<?php
// admin/sidebar-editor.php - Limited sidebar for editors
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$current_page = basename($_SERVER['PHP_SELF']);
?>

<nav class="col-md-3 col-lg-2 d-md-block bg-dark sidebar" style="min-height: 100vh;">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'dashboard-editor.php' ? 'active' : ''; ?>" href="dashboard-editor.php">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
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
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'manage-alumni.php' ? 'active' : ''; ?>" href="manage-alumni.php">
                    <i class="fas fa-users"></i> Alumni
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'messages.php' ? 'active' : ''; ?>" href="messages.php">
                    <i class="fas fa-envelope"></i> Contact Messages
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
                <a class="nav-link text-danger" href="logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>
    </div>
</nav>

<style>
.sidebar {
    background: #1a1a2e !important;
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
</style>