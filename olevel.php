<?php
// subjects.php - Subjects Offered Page
session_start();
require_once 'config/database.php';
$db = new Database();
$conn = $db->getConnection();

include 'includes/header.php';
?>

<!-- Page Header -->
<section class="page-header" style="background: linear-gradient(rgba(26,77,140,0.8), rgba(0,0,0,0.6)), url('assets/images/subjects-bg.jpg'); background-size: cover; background-position: center;">
    <div class="container">
        <h1>Subjects Offered</h1>
        <p>Comprehensive curriculum for holistic education</p>
    </div>
</section>

<section style="padding: 60px 0;">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="subject-category" style="background: #f5f5f5; padding: 30px; border-radius: 15px; margin-bottom: 30px;">
                    <h3><i class="fas fa-flask"></i> Sciences</h3>
                    <ul>
                        <li>Biology</li>
                        <li>Chemistry</li>
                        <li>Physics</li>
                        <li>Mathematics</li>
                        <li>Computer Science</li>
                        <li>Agriculture</li>
                    </ul>
                </div>
            </div>
            <div class="col-md-6">
                <div class="subject-category" style="background: #f5f5f5; padding: 30px; border-radius: 15px; margin-bottom: 30px;">
                    <h3><i class="fas fa-book"></i> Arts & Humanities</h3>
                    <ul>
                        <li>English Language</li>
                        <li>Literature in English</li>
                        <li>History</li>
                        <li>Geography</li>
                        <li>Christian Religious Education</li>
                        <li>Fine Art</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php' ?>