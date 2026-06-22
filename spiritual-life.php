<?php
// spiritual-life.php - Spiritual Life Page
session_start();
$page_title = "Spiritual Life";
?>
<?php include 'includes/head.php'; ?>
<?php include 'includes/sidebar.php'; ?>
<?php include 'includes/header.php'; ?>

<!-- Page Header -->
<section class="page-header" style="background: linear-gradient(rgba(26,77,140,0.8), rgba(0,0,0,0.6)), url('assets/images/spiritual-bg.jpg'); background-size: cover; background-position: center;">
    <div class="container">
        <h1>Spiritual Life</h1>
        <p>Nurturing Faith and Character</p>
    </div>
</section>

<!-- Spiritual Content -->
<section style="padding: 60px 0;">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <h2 style="color: #1a4d8c;">Faith Formation at Stella Maris</h2>
                <p>As a Catholic institution, spiritual development is at the heart of our education. We provide numerous opportunities for students to grow in their faith and develop strong moral character.</p>
                
                <h3 style="color: #2e7d32; margin-top: 30px;">Daily Spiritual Activities</h3>
                <ul style="list-style: none; padding: 0;">
                    <li style="padding: 10px 0;"><i class="fas fa-praying-hands" style="color: #1a4d8c; margin-right: 10px;"></i> Morning Prayer - 7:45 AM</li>
                    <li style="padding: 10px 0;"><i class="fas fa-church" style="color: #1a4d8c; margin-right: 10px;"></i> Daily Mass - 7:00 AM</li>
                    <li style="padding: 10px 0;"><i class="fas fa-book" style="color: #1a4d8c; margin-right: 10px;"></i> Scripture Reading during assembly</li>
                    <li style="padding: 10px 0;"><i class="fas fa-cross" style="color: #1a4d8c; margin-right: 10px;"></i> Angelus Prayer at noon</li>
                    <li style="padding: 10px 0;"><i class="fas fa-candle" style="color: #1a4d8c; margin-right: 10px;"></i> Evening Rosary - 5:30 PM</li>
                </ul>
                
                <h3 style="color: #2e7d32; margin-top: 30px;">Sacraments & Retreats</h3>
                <ul style="list-style: none; padding: 0;">
                    <li style="padding: 10px 0;"><i class="fas fa-check-circle" style="color: #2e7d32; margin-right: 10px;"></i> Annual School Retreat</li>
                    <li style="padding: 10px 0;"><i class="fas fa-check-circle" style="color: #2e7d32; margin-right: 10px;"></i> Sacrament of Reconciliation (Monthly)</li>
                    <li style="padding: 10px 0;"><i class="fas fa-check-circle" style="color: #2e7d32; margin-right: 10px;"></i> Baptism & Confirmation preparation</li>
                    <li style="padding: 10px 0;"><i class="fas fa-check-circle" style="color: #2e7d32; margin-right: 10px;"></i> Eucharistic Adoration (Every Friday)</li>
                    <li style="padding: 10px 0;"><i class="fas fa-check-circle" style="color: #2e7d32; margin-right: 10px;"></i> Pilgrimages to holy sites</li>
                </ul>
            </div>
            <div class="col-lg-4">
                <div class="prayer-corner" style="background: #f5f5f5; padding: 30px; border-radius: 15px;">
                    <h3 style="color: #1a4d8c;">Weekly Prayer Schedule</h3>
                    <div style="border-bottom: 1px solid #ddd; padding: 12px 0;"><strong>Monday:</strong> Rosary for Peace</div>
                    <div style="border-bottom: 1px solid #ddd; padding: 12px 0;"><strong>Tuesday:</strong> Divine Mercy Chaplet</div>
                    <div style="border-bottom: 1px solid #ddd; padding: 12px 0;"><strong>Wednesday:</strong> Novena to Our Lady</div>
                    <div style="border-bottom: 1px solid #ddd; padding: 12px 0;"><strong>Thursday:</strong> Adoration & Benediction</div>
                    <div style="border-bottom: 1px solid #ddd; padding: 12px 0;"><strong>Friday:</strong> Stations of the Cross</div>
                    <div style="padding: 12px 0;"><strong>Sunday:</strong> Holy Mass (8:00 AM & 10:00 AM)</div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>