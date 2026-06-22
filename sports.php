<?php
// sports.php - Sports & Co-curricular Page
session_start();
$page_title = "Sports & Co-curricular";
?>
<?php include 'includes/head.php'; ?>
<?php include 'includes/sidebar.php'; ?>
<?php include 'includes/header.php'; ?>

<!-- Page Header -->
<section class="page-header" style="background: linear-gradient(rgba(26,77,140,0.8), rgba(0,0,0,0.6)), url('assets/images/sports-bg.jpg'); background-size: cover; background-position: center;">
    <div class="container">
        <h1>Sports & Co-curricular</h1>
        <p>Developing Talents Beyond the Classroom</p>
    </div>
</section>

<!-- Sports Content -->
<section style="padding: 60px 0;">
    <div class="container">
        <h2 class="text-center" style="color: #1a4d8c; margin-bottom: 40px;">Sports Offered</h2>
        <div class="row">
            <div class="col-md-4">
                <div class="sport-card" style="text-align: center; padding: 30px; background: #f5f5f5; border-radius: 15px; margin-bottom: 30px;">
                    <i class="fas fa-futbol" style="font-size: 50px; color: #1a4d8c;"></i>
                    <h3 style="margin-top: 15px;">Football/Soccer</h3>
                    <p>School team competes nationally</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="sport-card" style="text-align: center; padding: 30px; background: #f5f5f5; border-radius: 15px; margin-bottom: 30px;">
                    <i class="fas fa-basketball-ball" style="font-size: 50px; color: #1a4d8c;"></i>
                    <h3 style="margin-top: 15px;">Basketball</h3>
                    <p>Junior and Senior teams</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="sport-card" style="text-align: center; padding: 30px; background: #f5f5f5; border-radius: 15px; margin-bottom: 30px;">
                    <i class="fas fa-volleyball-ball" style="font-size: 50px; color: #1a4d8c;"></i>
                    <h3 style="margin-top: 15px;">Volleyball</h3>
                    <p>Regular tournaments</p>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-4">
                <div class="sport-card" style="text-align: center; padding: 30px; background: #f5f5f5; border-radius: 15px; margin-bottom: 30px;">
                    <i class="fas fa-running" style="font-size: 50px; color: #1a4d8c;"></i>
                    <h3 style="margin-top: 15px;">Athletics</h3>
                    <p>Track and field events</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="sport-card" style="text-align: center; padding: 30px; background: #f5f5f5; border-radius: 15px; margin-bottom: 30px;">
                    <i class="fas fa-table-tennis" style="font-size: 50px; color: #1a4d8c;"></i>
                    <h3 style="margin-top: 15px;">Table Tennis</h3>
                    <p>Indoor sports facility</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="sport-card" style="text-align: center; padding: 30px; background: #f5f5f5; border-radius: 15px; margin-bottom: 30px;">
                    <i class="fas fa-chess" style="font-size: 50px; color: #1a4d8c;"></i>
                    <h3 style="margin-top: 15px;">Chess Club</h3>
                    <p>Strategic thinking development</p>
                </div>
            </div>
        </div>
        
        <h2 class="text-center" style="color: #1a4d8c; margin: 50px 0 40px;">Clubs & Societies</h2>
        <div class="row">
            <div class="col-md-3">
                <div class="club-card" style="background: #f5f5f5; padding: 25px; border-radius: 10px; text-align: center; margin-bottom: 20px;">
                    <i class="fas fa-microphone-alt" style="font-size: 40px; color: #2e7d32;"></i>
                    <h4 style="margin-top: 10px;">Debate Club</h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="club-card" style="background: #f5f5f5; padding: 25px; border-radius: 10px; text-align: center; margin-bottom: 20px;">
                    <i class="fas fa-music" style="font-size: 40px; color: #2e7d32;"></i>
                    <h4 style="margin-top: 10px;">Choir</h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="club-card" style="background: #f5f5f5; padding: 25px; border-radius: 10px; text-align: center; margin-bottom: 20px;">
                    <i class="fas fa-drama" style="font-size: 40px; color: #2e7d32;"></i>
                    <h4 style="margin-top: 10px;">Drama Club</h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="club-card" style="background: #f5f5f5; padding: 25px; border-radius: 10px; text-align: center; margin-bottom: 20px;">
                    <i class="fas fa-leaf" style="font-size: 40px; color: #2e7d32;"></i>
                    <h4 style="margin-top: 10px;">Environment Club</h4>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>