<?php
// departments.php
session_start();
$page_title = "Departments";
?>
<?php include 'includes/head.php'; ?>
<?php include 'includes/sidebar.php'; ?>
<?php include 'includes/header.php'; ?>

<section class="page-header" style="background: linear-gradient(rgba(26,77,140,0.8), rgba(0,0,0,0.6)), url('assets/images/departments-bg.jpg'); background-size: cover;">
    <div class="container">
        <h1>Academic Departments</h1>
        <p>Organized for excellence in teaching and learning</p>
    </div>
</section>

<section style="padding: 60px 0;">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="department-card" style="background: #f5f5f5; padding: 25px; border-radius: 15px; margin-bottom: 30px; text-align: center;">
                    <i class="fas fa-flask" style="font-size: 50px; color: #1a4d8c;"></i>
                    <h3>Science Department</h3>
                    <p>Head: Mrs. Jane Mukasa</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="department-card" style="background: #f5f5f5; padding: 25px; border-radius: 15px; margin-bottom: 30px; text-align: center;">
                    <i class="fas fa-calculator" style="font-size: 50px; color: #1a4d8c;"></i>
                    <h3>Mathematics Department</h3>
                    <p>Head: Mr. Robert Ssebunya</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="department-card" style="background: #f5f5f5; padding: 25px; border-radius: 15px; margin-bottom: 30px; text-align: center;">
                    <i class="fas fa-book" style="font-size: 50px; color: #1a4d8c;"></i>
                    <h3>Languages Department</h3>
                    <p>Head: Ms. Sarah Nalwoga</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>