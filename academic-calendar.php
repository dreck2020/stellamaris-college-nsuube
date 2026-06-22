<?php
// academic-calendar.php
session_start();
require_once 'config/database.php';
$db = new Database();
$conn = $db->getConnection();

// Include header (contains head, sidebar, and top header all in one)
include 'includes/header.php';
?>

<section class="page-header" style="background: linear-gradient(rgba(26,77,140,0.8), rgba(0,0,0,0.6)), url('assets/images/calendar-bg.jpg'); background-size: cover; background-position: center;">
    <div class="container">
        <h1>Academic Calendar</h1>
        <p>Stay updated with important dates and events</p>
    </div>
</section>

<section style="padding: 60px 0;">
    <div class="container">
        <div class="term-card" style="background: #f5f5f5; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
            <h4><i class="fas fa-calendar-alt"></i> Term 1</h4>
            <p><strong>Start:</strong> February 5, 2024</p>
            <p><strong>End:</strong> May 10, 2024</p>
        </div>
        <div class="term-card" style="background: #f5f5f5; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
            <h4><i class="fas fa-calendar-alt"></i> Term 2</h4>
            <p><strong>Start:</strong> May 27, 2024</p>
            <p><strong>End:</strong> August 23, 2024</p>
        </div>
        <div class="term-card" style="background: #f5f5f5; padding: 20px; border-radius: 10px;">
            <h4><i class="fas fa-calendar-alt"></i> Term 3</h4>
            <p><strong>Start:</strong> September 9, 2024</p>
            <p><strong>End:</strong> December 6, 2024</p>
        </div>
    </div>
</section>

<?php
include 'includes/footer.php';
    
?>