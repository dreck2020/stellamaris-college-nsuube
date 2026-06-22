<?php
// news.php - News & Updates Page with Proper Structure
session_start();
$page_title = "News & Updates";
require_once 'config/database.php';
$db = new Database();
$conn = $db->getConnection();

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 9;
$offset = ($page - 1) * $limit;

// Get category filter and search
$category = isset($_GET['category']) ? $_GET['category'] : 'all';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$archive = isset($_GET['archive']) ? $_GET['archive'] : '';

// Build queries based on filters
if($search != '') {
    $countQuery = "SELECT COUNT(*) FROM news WHERE status='published' AND (title LIKE :search OR content LIKE :search OR excerpt LIKE :search)";
    $stmt = $conn->prepare($countQuery);
    $stmt->execute(['search' => "%$search%"]);
    $totalNews = $stmt->fetchColumn();
    
    $newsQuery = "SELECT * FROM news WHERE status='published' AND (title LIKE :search OR content LIKE :search OR excerpt LIKE :search) ORDER BY published_date DESC LIMIT $limit OFFSET $offset";
    $stmt = $conn->prepare($newsQuery);
    $stmt->execute(['search' => "%$search%"]);
    $news = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $totalPages = ceil($totalNews / $limit);
} 
elseif($archive != '') {
    $countStmt = $conn->prepare("SELECT COUNT(*) FROM news WHERE status='published' AND DATE_FORMAT(published_date, '%Y-%m') = ?");
    $countStmt->execute([$archive]);
    $totalNews = $countStmt->fetchColumn();
    
    $newsQuery = "SELECT * FROM news WHERE status='published' AND DATE_FORMAT(published_date, '%Y-%m') = ? ORDER BY published_date DESC LIMIT $limit OFFSET $offset";
    $stmt = $conn->prepare($newsQuery);
    $stmt->execute([$archive]);
    $news = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $totalPages = ceil($totalNews / $limit);
}
elseif($category != 'all') {
    $countStmt = $conn->prepare("SELECT COUNT(*) FROM news WHERE status='published' AND category = ?");
    $countStmt->execute([$category]);
    $totalNews = $countStmt->fetchColumn();
    
    $newsQuery = "SELECT * FROM news WHERE status='published' AND category = ? ORDER BY published_date DESC LIMIT $limit OFFSET $offset";
    $stmt = $conn->prepare($newsQuery);
    $stmt->execute([$category]);
    $news = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $totalPages = ceil($totalNews / $limit);
} 
else {
    $totalNews = $conn->query("SELECT COUNT(*) FROM news WHERE status='published'")->fetchColumn();
    $newsQuery = "SELECT * FROM news WHERE status='published' ORDER BY published_date DESC LIMIT $limit OFFSET $offset";
    $stmt = $conn->prepare($newsQuery);
    $stmt->execute();
    $news = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $totalPages = ceil($totalNews / $limit);
}

// Get categories with counts
$categories = $conn->query("SELECT category, COUNT(*) as count FROM news WHERE status='published' AND category IS NOT NULL AND category != '' GROUP BY category ORDER BY count DESC")->fetchAll(PDO::FETCH_ASSOC);

// Get popular news
$popularNews = $conn->query("SELECT * FROM news WHERE status='published' ORDER BY views DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

// Get archives
$archives = array();
$archiveYears = $conn->query("SELECT DISTINCT YEAR(published_date) as year FROM news WHERE status='published' ORDER BY year DESC")->fetchAll(PDO::FETCH_ASSOC);
foreach($archiveYears as $yearData) {
    $year = $yearData['year'];
    $months = $conn->prepare("SELECT DISTINCT MONTH(published_date) as month FROM news WHERE status='published' AND YEAR(published_date) = ? ORDER BY month DESC");
    $months->execute([$year]);
    $monthData = $months->fetchAll(PDO::FETCH_ASSOC);
    foreach($monthData as $monthInfo) {
        $month = $monthInfo['month'];
        $monthName = date('F', mktime(0, 0, 0, $month, 1));
        $yearMonth = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);
        $countStmt = $conn->prepare("SELECT COUNT(*) FROM news WHERE status='published' AND YEAR(published_date) = ? AND MONTH(published_date) = ?");
        $countStmt->execute([$year, $month]);
        $count = $countStmt->fetchColumn();
        if($count > 0) {
            $archives[] = array('year_month' => $yearMonth, 'month_year' => $monthName . ' ' . $year, 'cnt' => $count);
        }
    }
}
$archives = array_slice($archives, 0, 12);
?>
<?php include 'includes/head.php'; ?>
<?php include 'includes/sidebar.php'; ?>
<?php include 'includes/header.php'; ?>

<style>
.news-page-header {
    background: linear-gradient(rgba(26,77,140,0.8), rgba(0,0,0,0.6)), url('assets/images/news-bg.jpg');
    background-size: cover;
    background-position: center;
    padding: 60px 0;
    color: white;
    text-align: center;
}

.news-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 30px;
    margin-bottom: 40px;
}

.news-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.news-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

.news-image {
    height: 200px;
    overflow: hidden;
    position: relative;
}

.news-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.news-card:hover .news-image img {
    transform: scale(1.05);
}

.news-category {
    position: absolute;
    top: 15px;
    right: 15px;
    background: #1a4d8c;
    color: white;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 500;
}

.news-content {
    padding: 20px;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.news-date {
    font-size: 12px;
    color: #999;
    margin-bottom: 10px;
}
.news-date i { margin-right: 5px; color: #2e7d32; }

.news-title {
    font-size: 18px;
    margin-bottom: 10px;
    line-height: 1.4;
}
.news-title a { color: #1a4d8c; text-decoration: none; transition: color 0.3s; }
.news-title a:hover { color: #2e7d32; }

.news-excerpt {
    color: #666;
    font-size: 14px;
    line-height: 1.6;
    margin-bottom: 15px;
    flex: 1;
}

.read-more {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    color: #1a4d8c;
    text-decoration: none;
    font-weight: 500;
    font-size: 14px;
    transition: gap 0.3s;
}
.read-more:hover { gap: 10px; color: #2e7d32; }

.news-meta {
    display: flex;
    gap: 15px;
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #eee;
    font-size: 12px;
    color: #999;
}
.news-meta i { margin-right: 5px; color: #2e7d32; }

.sidebar-widget {
    background: #f5f5f5;
    border-radius: 15px;
    padding: 25px;
    margin-bottom: 30px;
}
.sidebar-widget h3 {
    font-size: 18px;
    margin-bottom: 20px;
    color: #1a4d8c;
    border-left: 3px solid #2e7d32;
    padding-left: 10px;
}

.search-box { position: relative; }
.search-box input {
    width: 100%;
    padding: 12px 15px 12px 45px;
    border: 1px solid #ddd;
    border-radius: 10px;
    font-size: 14px;
}
.search-box i {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #999;
}

.popular-news-item {
    display: flex;
    gap: 15px;
    padding: 12px 0;
    border-bottom: 1px solid #ddd;
    transition: all 0.3s;
}
.popular-news-item:hover { background: #fff; padding-left: 10px; }
.popular-news-img {
    width: 70px;
    height: 70px;
    border-radius: 10px;
    overflow: hidden;
    flex-shrink: 0;
}
.popular-news-img img { width: 100%; height: 100%; object-fit: cover; }
.popular-news-info h4 { font-size: 14px; margin: 0 0 5px; line-height: 1.4; }
.popular-news-info h4 a { color: #333; text-decoration: none; }
.popular-news-info h4 a:hover { color: #1a4d8c; }
.popular-news-info .views { font-size: 11px; color: #999; }

.category-list, .archive-list { list-style: none; padding: 0; margin: 0; }
.category-list li, .archive-list li { padding: 10px 0; border-bottom: 1px solid #ddd; }
.category-list li:last-child, .archive-list li:last-child { border-bottom: none; }
.category-list a, .archive-list a {
    display: flex;
    justify-content: space-between;
    color: #333;
    text-decoration: none;
    transition: color 0.3s;
}
.category-list a:hover, .archive-list a:hover { color: #1a4d8c; }
.category-list span, .archive-list span {
    background: #ddd;
    padding: 2px 8px;
    border-radius: 20px;
    font-size: 11px;
}

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
.pagination a:hover { background: #1a4d8c; color: white; }
.pagination .active { background: #1a4d8c; color: white; }

.newsletter-widget {
    background: linear-gradient(135deg, #1a4d8c, #2e7d32);
    color: white;
    text-align: center;
}
.newsletter-widget input {
    width: 100%;
    padding: 12px;
    border: none;
    border-radius: 8px;
    margin-bottom: 10px;
}
.newsletter-widget button {
    width: 100%;
    padding: 12px;
    background: white;
    color: #1a4d8c;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
}
.newsletter-widget button:hover { transform: translateY(-2px); }

.no-results {
    text-align: center;
    padding: 60px;
    background: #f5f5f5;
    border-radius: 15px;
}
.no-results i { font-size: 60px; color: #999; margin-bottom: 20px; }

@media (max-width: 768px) {
    .news-grid { grid-template-columns: 1fr; gap: 20px; }
    .sidebar-widget { padding: 20px; }
}
</style>

<!-- Page Header -->
<section class="news-page-header">
    <div class="container">
        <h1>News & Updates</h1>
        <p>Latest happenings at Stella Maris College</p>
        <?php if($search): ?>
        <p class="mt-2">Search results for: "<?php echo htmlspecialchars($search); ?>"</p>
        <?php endif; ?>
        <?php if($category != 'all'): ?>
        <p class="mt-2">Category: <?php echo ucfirst(htmlspecialchars($category)); ?></p>
        <?php endif; ?>
        <?php if($archive): ?>
        <p class="mt-2">Archive: <?php echo date('F Y', strtotime($archive . '-01')); ?></p>
        <?php endif; ?>
    </div>
</section>

<!-- Main Content -->
<section style="padding: 60px 0;">
    <div class="container">
        <div class="row">
            <!-- Main News Column -->
            <div class="col-lg-8">
                <?php if(empty($news)): ?>
                    <div class="no-results">
                        <i class="fas fa-newspaper"></i>
                        <h3>No News Found</h3>
                        <p>No news articles match your search criteria. Please try different keywords or browse all news.</p>
                        <a href="news.php" class="btn btn-primary" style="display: inline-block; margin-top: 20px; padding: 10px 25px; background: #1a4d8c; color: white; text-decoration: none; border-radius: 25px;">
                            <i class="fas fa-arrow-left"></i> View All News
                        </a>
                    </div>
                <?php else: ?>
                    <div class="news-grid">
                        <?php foreach($news as $item): ?>
                        <div class="news-card">
                            <?php if($item['featured_image']): ?>
                            <div class="news-image">
                                <img src="<?php echo $item['featured_image']; ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" loading="lazy">
                                <?php if($item['category']): ?>
                                <span class="news-category"><?php echo ucfirst($item['category']); ?></span>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                            <div class="news-content">
                                <div class="news-date">
                                    <i class="far fa-calendar-alt"></i> <?php echo date('F j, Y', strtotime($item['published_date'])); ?>
                                </div>
                                <h3 class="news-title">
                                    <a href="news-detail.php?id=<?php echo $item['id']; ?>"><?php echo htmlspecialchars($item['title']); ?></a>
                                </h3>
                                <div class="news-excerpt">
                                    <?php 
                                    $excerpt = !empty($item['excerpt']) ? $item['excerpt'] : strip_tags($item['content']);
                                    echo htmlspecialchars(substr($excerpt, 0, 120)); 
                                    ?>...
                                </div>
                                <a href="news-detail.php?id=<?php echo $item['id']; ?>" class="read-more">
                                    Read More <i class="fas fa-arrow-right"></i>
                                </a>
                                <div class="news-meta">
                                    <span><i class="far fa-eye"></i> <?php echo number_format($item['views']); ?> views</span>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php if($totalPages > 1): ?>
                    <div class="pagination">
                        <?php if($page > 1): ?>
                            <a href="?page=<?php echo $page-1; ?><?php echo $category != 'all' ? '&category=' . urlencode($category) : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $archive ? '&archive=' . urlencode($archive) : ''; ?>">
                                <i class="fas fa-chevron-left"></i> Previous
                            </a>
                        <?php endif; ?>
                        <?php
                        $start = max(1, $page - 2);
                        $end = min($totalPages, $page + 2);
                        ?>
                        <?php if($start > 1): ?>
                            <a href="?page=1<?php echo $category != 'all' ? '&category=' . urlencode($category) : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $archive ? '&archive=' . urlencode($archive) : ''; ?>">1</a>
                            <?php if($start > 2): ?><span>...</span><?php endif; ?>
                        <?php endif; ?>
                        <?php for($i = $start; $i <= $end; $i++): ?>
                            <?php if($i == $page): ?>
                                <span class="active"><?php echo $i; ?></span>
                            <?php else: ?>
                                <a href="?page=<?php echo $i; ?><?php echo $category != 'all' ? '&category=' . urlencode($category) : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $archive ? '&archive=' . urlencode($archive) : ''; ?>"><?php echo $i; ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>
                        <?php if($end < $totalPages): ?>
                            <?php if($end < $totalPages - 1): ?><span>...</span><?php endif; ?>
                            <a href="?page=<?php echo $totalPages; ?><?php echo $category != 'all' ? '&category=' . urlencode($category) : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $archive ? '&archive=' . urlencode($archive) : ''; ?>"><?php echo $totalPages; ?></a>
                        <?php endif; ?>
                        <?php if($page < $totalPages): ?>
                            <a href="?page=<?php echo $page+1; ?><?php echo $category != 'all' ? '&category=' . urlencode($category) : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $archive ? '&archive=' . urlencode($archive) : ''; ?>">
                                Next <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="sidebar-widget">
                    <h3><i class="fas fa-search"></i> Search News</h3>
                    <form method="GET" action="news.php">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" name="search" placeholder="Search articles..." value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                    </form>
                </div>
                
                <?php if(!empty($popularNews)): ?>
                <div class="sidebar-widget">
                    <h3><i class="fas fa-fire"></i> Popular News</h3>
                    <?php foreach($popularNews as $popular): ?>
                    <div class="popular-news-item">
                        <?php if($popular['featured_image']): ?>
                        <div class="popular-news-img">
                            <img src="<?php echo $popular['featured_image']; ?>" alt="<?php echo htmlspecialchars($popular['title']); ?>">
                        </div>
                        <?php endif; ?>
                        <div class="popular-news-info">
                            <h4><a href="news-detail.php?id=<?php echo $popular['id']; ?>"><?php echo htmlspecialchars(substr($popular['title'], 0, 50)); ?></a></h4>
                            <div class="views"><i class="far fa-eye"></i> <?php echo number_format($popular['views']); ?> views</div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                
                <?php if(!empty($categories)): ?>
                <div class="sidebar-widget">
                    <h3><i class="fas fa-tags"></i> Categories</h3>
                    <ul class="category-list">
                        <li><a href="news.php">All News <span><?php echo $conn->query("SELECT COUNT(*) FROM news WHERE status='published'")->fetchColumn(); ?></span></a></li>
                        <?php foreach($categories as $cat): ?>
                        <li><a href="?category=<?php echo urlencode($cat['category']); ?>"><?php echo ucfirst($cat['category']); ?> <span><?php echo $cat['count']; ?></span></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
                
                <?php if(!empty($archives)): ?>
                <div class="sidebar-widget">
                    <h3><i class="fas fa-archive"></i> Archives</h3>
                    <ul class="archive-list">
                        <?php foreach($archives as $arch): ?>
                        <li><a href="?archive=<?php echo $arch['year_month']; ?>"><?php echo $arch['month_year']; ?> <span><?php echo $arch['cnt']; ?></span></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
                
                <div class="sidebar-widget newsletter-widget">
                    <i class="fas fa-envelope" style="font-size: 40px; margin-bottom: 15px;"></i>
                    <h3 style="color: white; border-left-color: white;">Newsletter</h3>
                    <p style="margin-bottom: 15px;">Subscribe to get the latest news delivered to your inbox</p>
                    <form id="newsletterForm">
                        <input type="email" id="newsletterEmail" placeholder="Your email address" required>
                        <button type="submit"><i class="fas fa-paper-plane"></i> Subscribe</button>
                    </form>
                    <div id="newsletterMsg" style="margin-top: 10px; font-size: 12px;"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
let searchTimeout;
$('input[name="search"]').on('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => { $(this).closest('form').submit(); }, 500);
});

$('#newsletterForm').on('submit', function(e) {
    e.preventDefault();
    const email = $('#newsletterEmail').val();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        $('#newsletterMsg').html('<span style="color: #ffcc00;">Please enter a valid email</span>');
        return;
    }
    $('#newsletterMsg').html('<span style="color: #a5d6a7;">✓ Subscribed successfully!</span>');
    $('#newsletterEmail').val('');
    setTimeout(() => $('#newsletterMsg').html(''), 3000);
});
</script>

<?php include 'includes/footer.php'; ?>