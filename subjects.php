<?php
// subjects.php - Subjects Offered Page
session_start();
$page_title = "Subjects Offered";
?>
<?php include 'includes/head.php'; ?>
<?php include 'includes/sidebar.php'; ?>
<?php include 'includes/header.php'; ?>

<!-- Page Header -->
<section class="page-header" style="background: linear-gradient(rgba(26,77,140,0.8), rgba(0,0,0,0.6)), url('assets/images/subjects-bg.jpg'); background-size: cover; background-position: center;">
    <div class="container">
        <h1>Subjects Offered</h1>
        <p>Comprehensive curriculum for holistic education</p>
    </div>
</section>

<!-- Subjects Content -->
<section style="padding: 60px 0;">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="subject-category" style="background: #f5f5f5; padding: 30px; border-radius: 15px; margin-bottom: 30px;">
                    <h3 style="color: #1a4d8c; margin-bottom: 20px;"><i class="fas fa-flask"></i> Sciences</h3>
                    <ul style="list-style: none; padding: 0;">
                        <li style="padding: 8px 0;"><i class="fas fa-check-circle" style="color: #2e7d32; margin-right: 10px;"></i> Biology</li>
                        <li style="padding: 8px 0;"><i class="fas fa-check-circle" style="color: #2e7d32; margin-right: 10px;"></i> Chemistry</li>
                        <li style="padding: 8px 0;"><i class="fas fa-check-circle" style="color: #2e7d32; margin-right: 10px;"></i> Physics</li>
                        <li style="padding: 8px 0;"><i class="fas fa-check-circle" style="color: #2e7d32; margin-right: 10px;"></i> Mathematics</li>
                        <li style="padding: 8px 0;"><i class="fas fa-check-circle" style="color: #2e7d32; margin-right: 10px;"></i> Computer Science</li>
                        <li style="padding: 8px 0;"><i class="fas fa-check-circle" style="color: #2e7d32; margin-right: 10px;"></i> Agriculture</li>
                    </ul>
                </div>
            </div>
            <div class="col-md-6">
                <div class="subject-category" style="background: #f5f5f5; padding: 30px; border-radius: 15px; margin-bottom: 30px;">
                    <h3 style="color: #1a4d8c; margin-bottom: 20px;"><i class="fas fa-book"></i> Arts & Humanities</h3>
                    <ul style="list-style: none; padding: 0;">
                        <li style="padding: 8px 0;"><i class="fas fa-check-circle" style="color: #2e7d32; margin-right: 10px;"></i> English Language</li>
                        <li style="padding: 8px 0;"><i class="fas fa-check-circle" style="color: #2e7d32; margin-right: 10px;"></i> Literature in English</li>
                        <li style="padding: 8px 0;"><i class="fas fa-check-circle" style="color: #2e7d32; margin-right: 10px;"></i> History</li>
                        <li style="padding: 8px 0;"><i class="fas fa-check-circle" style="color: #2e7d32; margin-right: 10px;"></i> Geography</li>
                        <li style="padding: 8px 0;"><i class="fas fa-check-circle" style="color: #2e7d32; margin-right: 10px;"></i> Christian Religious Education</li>
                        <li style="padding: 8px 0;"><i class="fas fa-check-circle" style="color: #2e7d32; margin-right: 10px;"></i> Fine Art</li>
                    </ul>
                </div>
            </div>
            <div class="col-md-6">
                <div class="subject-category" style="background: #f5f5f5; padding: 30px; border-radius: 15px; margin-bottom: 30px;">
                    <h3 style="color: #1a4d8c; margin-bottom: 20px;"><i class="fas fa-chart-line"></i> Business & Vocational</h3>
                    <ul style="list-style: none; padding: 0;">
                        <li style="padding: 8px 0;"><i class="fas fa-check-circle" style="color: #2e7d32; margin-right: 10px;"></i> Entrepreneurship</li>
                        <li style="padding: 8px 0;"><i class="fas fa-check-circle" style="color: #2e7d32; margin-right: 10px;"></i> Commerce</li>
                        <li style="padding: 8px 0;"><i class="fas fa-check-circle" style="color: #2e7d32; margin-right: 10px;"></i> Accounts</li>
                        <li style="padding: 8px 0;"><i class="fas fa-check-circle" style="color: #2e7d32; margin-right: 10px;"></i> Economics</li>
                        <li style="padding: 8px 0;"><i class="fas fa-check-circle" style="color: #2e7d32; margin-right: 10px;"></i> Food and Nutrition</li>
                        <li style="padding: 8px 0;"><i class="fas fa-check-circle" style="color: #2e7d32; margin-right: 10px;"></i> Clothing and Textiles</li>
                    </ul>
                </div>
            </div>
            <div class="col-md-6">
                <div class="subject-category" style="background: #f5f5f5; padding: 30px; border-radius: 15px; margin-bottom: 30px;">
                    <h3 style="color: #1a4d8c; margin-bottom: 20px;"><i class="fas fa-language"></i> Languages</h3>
                    <ul style="list-style: none; padding: 0;">
                        <li style="padding: 8px 0;"><i class="fas fa-check-circle" style="color: #2e7d32; margin-right: 10px;"></i> Luganda</li>
                        <li style="padding: 8px 0;"><i class="fas fa-check-circle" style="color: #2e7d32; margin-right: 10px;"></i> French</li>
                        <li style="padding: 8px 0;"><i class="fas fa-check-circle" style="color: #2e7d32; margin-right: 10px;"></i> Kiswahili</li>
                        <li style="padding: 8px 0;"><i class="fas fa-check-circle" style="color: #2e7d32; margin-right: 10px;"></i> Latin</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>