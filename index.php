<?php
// index.php - Home page
session_start();
require_once 'config/database.php';
$db = new Database();
$conn = $db->getConnection();

// Fetch latest news
$newsQuery = "SELECT * FROM news WHERE status='published' ORDER BY published_date DESC LIMIT 3";
$latestNews = $conn->query($newsQuery)->fetchAll(PDO::FETCH_ASSOC);

// Fetch upcoming events
$eventsQuery = "SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date LIMIT 4";
$upcomingEvents = $conn->query($eventsQuery)->fetchAll(PDO::FETCH_ASSOC);

// Include header (contains all menu/sidebar code, footer, modals, scripts)
include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="hero-slider">
        <div class="hero-slide active">
            <div class="hero-bg" style="background-image: linear-gradient(rgba(26,77,140,0.7), rgba(0,0,0,0.5)), url('assets/images/hero1.jpg');">
                <div class="hero-content">
                    <h1>Empowering Young Women<br>Through Quality Education</h1>
                    <p>Excellence in O-Level and A-Level Education | Rooted in Catholic Values</p>
                    <div class="hero-buttons">
                        <a href="admission.php" class="btn-primary">Apply Now</a>
                        <a href="about.php" class="btn-outline">Learn More</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-user-graduate"></i></div>
                <h3 class="stat-number">700+</h3>
                <p>Students</p>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-chalkboard-user"></i></div>
                <h3 class="stat-number">50+</h3>
                <p>Teachers</p>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-trophy"></i></div>
                <h3 class="stat-number">98%</h3>
                <p>Pass Rate</p>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-calendar-alt"></i></div>
                <h3 class="stat-number">85+</h3>
                <p>Years of Excellence</p>
            </div>
        </div>
    </div>
</section>

<!-- Mission Vision Section -->
<section class="mv-section">
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

<!-- Programs Section -->
<section class="programs-section">
    <div class="container">
        <div class="section-header">
            <h2>Our Academic Programs</h2>
            <div class="section-line"></div>
        </div>
        <div class="programs-grid">
            <div class="program-card" onclick="location.href='olevel.php'">
                <div class="program-icon"><i class="fas fa-child"></i></div>
                <h3>O-Level</h3>
                <p>Senior 1 - Senior 4</p>
                <ul>
                    <li>Mathematics</li>
                    <li>English</li>
                    <li>Sciences</li>
                    <li>Humanities</li>
                </ul>
                <span class="btn-learn">Learn More <i class="fas fa-arrow-right"></i></span>
            </div>
            <div class="program-card" onclick="location.href='alevel.php'">
                <div class="program-icon"><i class="fas fa-female"></i></div>
                <h3>A-Level</h3>
                <p>Senior 5 - Senior 6</p>
                <ul>
                    <li>Sciences (PCM/BMC)</li>
                    <li>Arts (HEG/LKD)</li>
                    <li>Business (ECA/BAM)</li>
                </ul>
                <span class="btn-learn">Learn More <i class="fas fa-arrow-right"></i></span>
            </div>
        </div>
    </div>
</section>

<!-- Latest News Section -->
<section class="news-section">
    <div class="container">
        <div class="section-header">
            <h2>Latest News & Announcements</h2>
            <div class="section-line"></div>
            <a href="news.php" class="view-all">View All <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="news-grid">
            <?php foreach($latestNews as $news): ?>
            <div class="news-card">
                <?php if($news['featured_image']): ?>
                <div class="news-image">
                    <img src="<?php echo $news['featured_image']; ?>" alt="<?php echo htmlspecialchars($news['title']); ?>">
                </div>
                <?php endif; ?>
                <div class="news-content">
                    <div class="news-date">
                        <i class="far fa-calendar-alt"></i> <?php echo date('F j, Y', strtotime($news['published_date'])); ?>
                    </div>
                    <h3><?php echo htmlspecialchars($news['title']); ?></h3>
                    <p><?php echo htmlspecialchars(substr($news['excerpt'], 0, 120)); ?>...</p>
                    <a href="news-detail.php?id=<?php echo $news['id']; ?>" class="read-more">Read More <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Upcoming Events Section -->
<section class="events-section">
    <div class="container">
        <div class="section-header">
            <h2>Upcoming Events</h2>
            <div class="section-line"></div>
            <a href="events.php" class="view-all">View Calendar <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="events-list">
            <?php foreach($upcomingEvents as $event): ?>
            <div class="event-item">
                <div class="event-date">
                    <span class="day"><?php echo date('d', strtotime($event['event_date'])); ?></span>
                    <span class="month"><?php echo date('M', strtotime($event['event_date'])); ?></span>
                </div>
                <div class="event-details">
                    <h4><?php echo htmlspecialchars($event['title']); ?></h4>
                    <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($event['location']); ?></p>
                    <p><i class="far fa-clock"></i> <?php echo date('h:i A', strtotime($event['event_time'])); ?></p>
                </div>
                <div class="event-action">
                    <button class="btn-reminder" onclick="setReminder(<?php echo $event['id']; ?>)">Set Reminder</button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Prayer Section -->
<section class="prayer-section">
    <div class="container">
        <div class="prayer-content">
            <i class="fas fa-cross"></i>
            <h3>Daily School Prayer</h3>
            <p>"Almighty God, we thank you for the gift of Stella Maris College Nsuube. Bless our students, teachers, and staff. Grant us wisdom, knowledge, and understanding. Help us to always seek excellence in all we do. Through Christ our Lord. Amen."</p>
            <p class="patron">- St. Maria Goretti, pray for us</p>
            <button class="btn-prayer" onclick="openPrayerModal()">Pray with us <i class="fas fa-pray"></i></button>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="testimonials-section">
    <div class="container">
        <div class="section-header">
            <h2>What Our Students Say</h2>
            <div class="section-line"></div>
        </div>
        <div class="testimonials-slider">
            <div class="testimonial-card">
                <div class="testimonial-text">
                    <i class="fas fa-quote-left"></i>
                    <p>Stella Maris College has transformed my life completely. The education I received here not only prepared me academically but also spiritually. I am proud to be a Stella girl!</p>
                </div>
                <div class="testimonial-author">
                    <img src="assets/images/talent.png" alt="Student">
                    <div>
                        <h4>Kubakurungi Talent</h4>
                        <span>Former Head Girl, Class of 2025</span>
                        <span>Going to join Makerere University</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
include 'includes/footer.php'
?>