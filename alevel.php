<?php
// alevel.php - A-Level Program Page
session_start();
require_once 'config/database.php';
$db = new Database();
$conn = $db->getConnection();

// Include header (contains head, sidebar, and top header all in one)
include 'includes/header.php';
?>

<!-- Page Header -->
<section class="page-header" style="background: linear-gradient(rgba(26,77,140,0.8), rgba(0,0,0,0.6)), url('assets/images/alevel-bg.jpg'); background-size: cover; background-position: center;">
    <div class="container">
        <h1>A-Level Program</h1>
        <p>Senior 5 to Senior 6 - Excellence in Advanced Education</p>
    </div>
</section>

<!-- Program Details -->
<section style="padding: 60px 0;">
    <div class="container">
        <h2 class="text-center" style="margin-bottom: 40px;">Choose Your Career Path</h2>
        <div class="row">
            <div class="col-md-4">
                <div class="combination-card" style="background: #f5f5f5; padding: 30px; border-radius: 15px; margin-bottom: 30px; text-align: center; transition: transform 0.3s;">
                    <i class="fas fa-flask" style="font-size: 50px; color: #1a4d8c; margin-bottom: 15px;"></i>
                    <h3 style="color: #1a4d8c;">Sciences (PCM)</h3>
                    <p><strong>Subjects:</strong> Physics, Chemistry, Mathematics</p>
                    <p><strong>Careers:</strong> Engineering, Medicine, Pharmacy, Computer Science</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="combination-card" style="background: #f5f5f5; padding: 30px; border-radius: 15px; margin-bottom: 30px; text-align: center; transition: transform 0.3s;">
                    <i class="fas fa-microscope" style="font-size: 50px; color: #1a4d8c; margin-bottom: 15px;"></i>
                    <h3 style="color: #1a4d8c;">Biological Sciences (BCM)</h3>
                    <p><strong>Subjects:</strong> Biology, Chemistry, Mathematics</p>
                    <p><strong>Careers:</strong> Medicine, Nursing, Pharmacy, Biotechnology</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="combination-card" style="background: #f5f5f5; padding: 30px; border-radius: 15px; margin-bottom: 30px; text-align: center; transition: transform 0.3s;">
                    <i class="fas fa-landmark" style="font-size: 50px; color: #1a4d8c; margin-bottom: 15px;"></i>
                    <h3 style="color: #1a4d8c;">Arts (HEG)</h3>
                    <p><strong>Subjects:</strong> History, Economics, Geography</p>
                    <p><strong>Careers:</strong> Law, Teaching, Journalism, Public Administration</p>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-4">
                <div class="combination-card" style="background: #f5f5f5; padding: 30px; border-radius: 15px; margin-bottom: 30px; text-align: center;">
                    <i class="fas fa-chart-line" style="font-size: 50px; color: #1a4d8c; margin-bottom: 15px;"></i>
                    <h3 style="color: #1a4d8c;">Business (ECA)</h3>
                    <p><strong>Subjects:</strong> Entrepreneurship, Commerce, Accounts</p>
                    <p><strong>Careers:</strong> Business, Accounting, Finance, Marketing</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="combination-card" style="background: #f5f5f5; padding: 30px; border-radius: 15px; margin-bottom: 30px; text-align: center;">
                    <i class="fas fa-language" style="font-size: 50px; color: #1a4d8c; margin-bottom: 15px;"></i>
                    <h3 style="color: #1a4d8c;">Languages (LKD)</h3>
                    <p><strong>Subjects:</strong> Literature, Kiswahili, Divinity</p>
                    <p><strong>Careers:</strong> Teaching, Translation, Journalism, Law</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="combination-card" style="background: #f5f5f5; padding: 30px; border-radius: 15px; margin-bottom: 30px; text-align: center;">
                    <i class="fas fa-calculator" style="font-size: 50px; color: #1a4d8c; margin-bottom: 15px;"></i>
                    <h3 style="color: #1a4d8c;">Mathematics & Economics</h3>
                    <p><strong>Subjects:</strong> Mathematics, Economics, Geography</p>
                    <p><strong>Careers:</strong> Economics, Statistics, Actuarial Science</p>
                </div>
            </div>
        </div>
        <div class="text-center mt-4">
            <a href="admission.php" class="btn-primary" style="display: inline-block;">Apply for A-Level</a>
        </div>
    </div>
</section>

<?php
include 'includes/footer.php'
?>