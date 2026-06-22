<?php
// about.php - About Us Page
session_start();
require_once 'config/database.php';
$db = new Database();
$conn = $db->getConnection();

// Fetch about content from database
$stmt = $conn->prepare("SELECT * FROM pages WHERE page_name = 'about'");
$stmt->execute();
$aboutContent = $stmt->fetch(PDO::FETCH_ASSOC);

// Include header (contains all menu/sidebar code)
include 'includes/header.php';
?>

<!-- Page Header -->
<section class="page-header" style="background: linear-gradient(rgba(26,77,140,0.8), rgba(0,0,0,0.6)), url('assets/images/about-bg.jpg'); background-size: cover; background-position: center; padding: 80px 0; color: white; text-align: center;">
    <div class="container">
        <h1>About Us</h1>
        <p>Discover the story of Stella Maris College Nsuube</p>
    </div>
</section>

<!-- History Section -->
<section class="history-section" style="padding: 80px 0;">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="history-content">
                    <h2 style="color: var(--primary-blue); margin-bottom: 20px;">Our History</h2>
                    <p style="line-height: 1.8; margin-bottom: 15px;">Stella Maris College Nsuube was established in 1999 with a vision to provide quality Catholic education to young women in Uganda. Founded by the Sisters of the Immaculate Heart of Mary, the school has grown from humble beginnings to become one of the leading girls' secondary schools in the region.</p>
                    <p style="line-height: 1.8; margin-bottom: 15px;">The school is named after Stella Maris (Star of the Sea), a title for the Blessed Virgin Mary. Our patroness, St. Maria Goretti, inspires our students to live lives of purity, faith, and courage.</p>
                    <p style="line-height: 1.8;">Over the past 25 years, we have educated thousands of young women who have gone on to become leaders in various fields including medicine, law, education, business, and public service.</p>
                </div>
            </div>
            <div class="col-lg-6">
                <img src="assets/images/school-history.jpg" alt="School History" style="width: 100%; border-radius: 15px; box-shadow: var(--shadow-lg);">
            </div>
        </div>
    </div>
</section>

<!-- Mission Vision Section -->
<section class="mv-section" style="background: var(--gray-light); padding: 80px 0;">
    <div class="container">
        <div class="section-header">
            <h2>Our Foundation</h2>
            <div class="section-line"></div>
        </div>
        <div class="mv-grid">
            <div class="mv-card mission">
                <div class="mv-icon"><i class="fas fa-star"></i></div>
                <h3>Our Mission</h3>
                <p>To provide holistic Catholic education that empowers girls to become responsible, godly, and productive members of society.</p>
            </div>
            <div class="mv-card vision">
                <div class="mv-icon"><i class="fas fa-eye"></i></div>
                <h3>Our Vision</h3>
                <p>To be a center of excellence in girls' education, producing future leaders grounded in Christian values.</p>
            </div>
        </div>
    </div>
</section>

<!-- Core Values Section -->
<section class="values-section" style="padding: 80px 0;">
    <div class="container">
        <div class="section-header">
            <h2>Our Core Values</h2>
            <div class="section-line"></div>
            <p style="margin-top: 15px; color: var(--gray);">The principles that guide everything we do</p>
        </div>
        <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-cross"></i></div>
                <h3 class="stat-number" style="font-size: 24px;">Faith</h3>
                <p>Deepening our relationship with God through prayer and service</p>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-gem"></i></div>
                <h3 class="stat-number" style="font-size: 24px;">Excellence</h3>
                <p>Striving for the highest standards in all endeavors</p>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-hand-peace"></i></div>
                <h3 class="stat-number" style="font-size: 24px;">Integrity</h3>
                <p>Being honest and morally upright in all situations</p>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-heart"></i></div>
                <h3 class="stat-number" style="font-size: 24px;">Compassion</h3>
                <p>Showing kindness and care for others</p>
            </div>
        </div>
    </div>
</section>

<!-- School Anthem Section -->
<section class="prayer-section" style="padding: 80px 0;">
    <div class="container">
        <div class="prayer-content">
            <i class="fas fa-music"></i>
            <h3>Our School Anthem</h3>
            <p style="font-style: italic; max-width: 700px; margin: 20px auto;">"Stella Maris, Star of the Sea,<br>
            Guide our girls to victory.<br>
            With faith and knowledge, hand in hand,<br>
            Building a better Uganda's land."</p>
            <button class="btn-prayer" onclick="playAnthem()">Listen to Anthem <i class="fas fa-play"></i></button>
        </div>
    </div>
</section>

<script>
// Simple function for anthem button
function playAnthem() {
    alert("School anthem feature coming soon!");
}
</script>

<?php
// Footer is already included in header.php, but if you have a separate footer file:
include 'includes/footer.php';
// The main-wrapper div is closed in header.php
?>