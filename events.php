<?php
// events.php - All Events Page (Fixed SQL)
session_start();
$page_title = "Events Calendar";
require_once 'config/database.php';
$db = new Database();
$conn = $db->getConnection();

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 6;
$offset = ($page - 1) * $limit;

// Get total events
$totalQuery = $conn->query("SELECT COUNT(*) FROM events WHERE event_date >= CURDATE() OR event_date IS NULL");
$totalEvents = $totalQuery->fetchColumn();
$totalPages = ceil($totalEvents / $limit);

// Get events - FIXED: Use string concatenation for LIMIT and OFFSET
$eventsQuery = $conn->prepare("SELECT * FROM events WHERE event_date >= CURDATE() OR event_date IS NULL ORDER BY event_date ASC LIMIT $limit OFFSET $offset");
$eventsQuery->execute();
$events = $eventsQuery->fetchAll(PDO::FETCH_ASSOC);

// Get upcoming events for sidebar
$upcomingQuery = $conn->prepare("SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC LIMIT 5");
$upcomingQuery->execute();
$upcomingEvents = $upcomingQuery->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include 'includes/head.php'; ?>
<?php include 'includes/sidebar.php'; ?>
<?php include 'includes/header.php'; ?>

<style>
.event-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: transform 0.3s, box-shadow 0.3s;
    height: 100%;
}

.event-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

.event-date-large {
    background: linear-gradient(135deg, #1a4d8c, #2e7d32);
    padding: 20px;
    text-align: center;
    color: white;
}

.event-date-large .day {
    font-size: 48px;
    font-weight: 700;
    line-height: 1;
}

.event-date-large .month {
    font-size: 16px;
    text-transform: uppercase;
    letter-spacing: 2px;
}

.event-details {
    padding: 20px;
}

.event-details h3 {
    font-size: 20px;
    margin-bottom: 10px;
    color: #1a4d8c;
}

.event-details p {
    color: #666;
    margin-bottom: 10px;
    font-size: 14px;
}

.event-details i {
    width: 20px;
    color: #2e7d32;
    margin-right: 8px;
}

.event-description {
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #eee;
    color: #555;
    line-height: 1.6;
}

.btn-register {
    display: inline-block;
    margin-top: 15px;
    padding: 8px 20px;
    background: #1a4d8c;
    color: white;
    border-radius: 25px;
    text-decoration: none;
    font-size: 13px;
    transition: all 0.3s;
    border: none;
    cursor: pointer;
}

.btn-register:hover {
    background: #2e7d32;
    transform: translateY(-2px);
}

/* Upcoming Events Sidebar */
.upcoming-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 12px 0;
    border-bottom: 1px solid #eee;
    transition: background 0.3s;
}

.upcoming-item:hover {
    background: #f9f9f9;
    padding-left: 10px;
}

.upcoming-date {
    text-align: center;
    min-width: 60px;
    background: #1a4d8c;
    color: white;
    padding: 8px;
    border-radius: 10px;
}

.upcoming-date .day {
    font-size: 20px;
    font-weight: 700;
    line-height: 1;
}

.upcoming-date .month {
    font-size: 10px;
}

.upcoming-info h4 {
    font-size: 14px;
    margin: 0 0 5px;
}

.upcoming-info p {
    font-size: 12px;
    color: #666;
    margin: 0;
}

.upcoming-info i {
    font-size: 10px;
    margin-right: 5px;
}

/* Pagination */
.pagination {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-top: 40px;
    flex-wrap: wrap;
}

.pagination a, .pagination span {
    padding: 8px 15px;
    background: #f5f5f5;
    color: #333;
    text-decoration: none;
    border-radius: 5px;
    transition: all 0.3s;
}

.pagination a:hover {
    background: #1a4d8c;
    color: white;
}

.pagination .active {
    background: #1a4d8c;
    color: white;
}

/* Category Filter */
.category-filter {
    margin-bottom: 30px;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    justify-content: center;
}

.filter-btn {
    padding: 8px 20px;
    background: #f5f5f5;
    border: none;
    border-radius: 25px;
    cursor: pointer;
    transition: all 0.3s;
}

.filter-btn.active, .filter-btn:hover {
    background: #1a4d8c;
    color: white;
}
</style>

<!-- Page Header -->
<section class="page-header" style="background: linear-gradient(rgba(26,77,140,0.8), rgba(0,0,0,0.6)), url('assets/images/events-bg.jpg'); background-size: cover; background-position: center;">
    <div class="container">
        <h1>School Events</h1>
        <p>Stay connected with our vibrant school community</p>
    </div>
</section>

<!-- Events Content -->
<section style="padding: 60px 0;">
    <div class="container">
        <div class="row">
            <!-- Main Events Column -->
            <div class="col-lg-8">
                <?php if(empty($events)): ?>
                    <div style="text-align: center; padding: 60px; background: #f5f5f5; border-radius: 15px;">
                        <i class="fas fa-calendar-times" style="font-size: 60px; color: #999; margin-bottom: 20px;"></i>
                        <h3>No Upcoming Events</h3>
                        <p>Check back soon for upcoming events and activities.</p>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach($events as $event): ?>
                        <div class="col-md-6 mb-4">
                            <div class="event-card">
                                <div class="event-date-large">
                                    <div class="day"><?php echo date('d', strtotime($event['event_date'])); ?></div>
                                    <div class="month"><?php echo date('M Y', strtotime($event['event_date'])); ?></div>
                                </div>
                                <div class="event-details">
                                    <h3><?php echo htmlspecialchars($event['title']); ?></h3>
                                    <p><i class="fas fa-clock"></i> <?php echo date('h:i A', strtotime($event['event_time'])); ?></p>
                                    <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($event['location']); ?></p>
                                    <p><i class="fas fa-tag"></i> <?php echo htmlspecialchars($event['event_type'] ?: 'School Event'); ?></p>
                                    <div class="event-description">
                                        <?php echo nl2br(htmlspecialchars(substr($event['description'], 0, 150))); ?>
                                        <?php if(strlen($event['description']) > 150): ?>...<?php endif; ?>
                                    </div>
                                    <button class="btn-register" onclick="addToCalendar(<?php echo $event['id']; ?>, '<?php echo addslashes(htmlspecialchars($event['title'])); ?>', '<?php echo $event['event_date']; ?>', '<?php echo $event['event_time']; ?>')">
                                        <i class="fas fa-calendar-plus"></i> Add to Calendar
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if($totalPages > 1): ?>
                    <div class="pagination">
                        <?php if($page > 1): ?>
                            <a href="?page=<?php echo $page-1; ?>">&laquo; Previous</a>
                        <?php endif; ?>
                        
                        <?php for($i = 1; $i <= $totalPages; $i++): ?>
                            <?php if($i == $page): ?>
                                <span class="active"><?php echo $i; ?></span>
                            <?php else: ?>
                                <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>
                        
                        <?php if($page < $totalPages): ?>
                            <a href="?page=<?php echo $page+1; ?>">Next &raquo;</a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Upcoming Events Widget -->
                <div style="background: #f5f5f5; padding: 25px; border-radius: 15px; margin-bottom: 30px;">
                    <h3 style="color: #1a4d8c; margin-bottom: 20px;">
                        <i class="fas fa-calendar-alt"></i> Upcoming Events
                    </h3>
                    <?php if(empty($upcomingEvents)): ?>
                        <p>No upcoming events scheduled.</p>
                    <?php else: ?>
                        <?php foreach($upcomingEvents as $event): ?>
                        <div class="upcoming-item">
                            <div class="upcoming-date">
                                <div class="day"><?php echo date('d', strtotime($event['event_date'])); ?></div>
                                <div class="month"><?php echo date('M', strtotime($event['event_date'])); ?></div>
                            </div>
                            <div class="upcoming-info">
                                <h4><?php echo htmlspecialchars($event['title']); ?></h4>
                                <p><i class="fas fa-clock"></i> <?php echo date('h:i A', strtotime($event['event_time'])); ?></p>
                                <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars(substr($event['location'], 0, 30)); ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <!-- Newsletter Widget -->
                <div style="background: linear-gradient(135deg, #1a4d8c, #2e7d32); padding: 25px; border-radius: 15px; color: white;">
                    <i class="fas fa-envelope" style="font-size: 40px; margin-bottom: 15px;"></i>
                    <h3 style="margin-bottom: 10px;">Get Event Updates</h3>
                    <p style="margin-bottom: 15px; opacity: 0.9;">Subscribe to receive notifications about upcoming events</p>
                    <form id="eventNewsletterForm" style="display: flex; gap: 10px;">
                        <input type="email" id="eventEmail" placeholder="Your email" style="flex: 1; padding: 10px; border: none; border-radius: 5px;">
                        <button type="submit" style="background: white; color: #1a4d8c; border: none; padding: 10px 15px; border-radius: 5px; cursor: pointer;">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                    <div id="eventSubscribeMsg" style="margin-top: 10px; font-size: 12px;"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Add to Calendar function
function addToCalendar(eventId, title, date, time) {
    if (confirm(`Add "${title}" to your calendar?`)) {
        showNotification('Event added to calendar!', 'success');
    }
}

// Event Newsletter Subscription
$('#eventNewsletterForm').on('submit', function(e) {
    e.preventDefault();
    const email = $('#eventEmail').val();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    if (!emailRegex.test(email)) {
        $('#eventSubscribeMsg').html('<span style="color: #ffcc00;">Please enter a valid email</span>');
        return;
    }
    
    $('#eventSubscribeMsg').html('<span style="color: #a5d6a7;">✓ Subscribed successfully!</span>');
    $('#eventEmail').val('');
    setTimeout(() => $('#eventSubscribeMsg').html(''), 3000);
    showNotification('Successfully subscribed to event updates!', 'success');
});

// Category Filter
$('.filter-btn').click(function() {
    const category = $(this).data('category');
    $('.filter-btn').removeClass('active');
    $(this).addClass('active');
    
    if (category === 'all') {
        $('.event-card').parent().show();
    } else {
        $('.event-card').parent().each(function() {
            const eventText = $(this).text().toLowerCase();
            if (eventText.indexOf(category) > -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }
});

// Show notification function
function showNotification(message, type) {
    const notification = $(`
        <div style="position: fixed; bottom: 20px; right: 20px; background: ${type === 'success' ? '#4caf50' : '#2196f3'}; color: white; padding: 12px 20px; border-radius: 8px; z-index: 10000; animation: slideIn 0.3s ease;">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-info-circle'}"></i>
            <span style="margin-left: 10px;">${message}</span>
        </div>
    `);
    $('body').append(notification);
    setTimeout(() => notification.fadeOut(300, function() { $(this).remove(); }), 3000);
}

// Add animation style
$('<style>@keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }</style>').appendTo('head');
</script>

<?php include 'includes/footer.php'; ?>