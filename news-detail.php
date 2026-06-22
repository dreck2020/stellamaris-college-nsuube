<?php
// news-detail.php - Display single news article with proper structure
session_start();
require_once 'config/database.php';
$db = new Database();
$conn = $db->getConnection();

// Get news ID from URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if($id > 0) {
    // Get news article
    $stmt = $conn->prepare("SELECT * FROM news WHERE id = ? AND status = 'published'");
    $stmt->execute([$id]);
    $news = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if(!$news) {
        header("Location: news.php");
        exit();
    }
    
    // Update view count
    $updateStmt = $conn->prepare("UPDATE news SET views = views + 1 WHERE id = ?");
    $updateStmt->execute([$id]);
    
    // Set page title for the head
    $page_title = $news['title'];
    
    // Get related news (same category, excluding current)
    $relatedStmt = $conn->prepare("SELECT * FROM news WHERE category = ? AND id != ? AND status = 'published' ORDER BY published_date DESC LIMIT 3");
    $relatedStmt->execute([$news['category'], $id]);
    $relatedNews = $relatedStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get recent news for sidebar
    $recentStmt = $conn->query("SELECT * FROM news WHERE status = 'published' ORDER BY published_date DESC LIMIT 5");
    $recentNews = $recentStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get categories for sidebar
    $categories = $conn->query("SELECT category, COUNT(*) as count FROM news WHERE status='published' AND category IS NOT NULL AND category != '' GROUP BY category ORDER BY count DESC")->fetchAll(PDO::FETCH_ASSOC);
} else {
    header("Location: news.php");
    exit();
}
?>
<?php include 'includes/head.php'; ?>
<?php include 'includes/sidebar.php'; ?>
<?php include 'includes/header.php'; ?>

<style>
.news-detail-container {
    padding: 40px 0;
}

.news-header {
    margin-bottom: 30px;
}

.news-category {
    display: inline-block;
    background: #1a4d8c;
    color: white;
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 12px;
    margin-bottom: 15px;
}

.news-title {
    font-size: 36px;
    color: #1a4d8c;
    margin-bottom: 20px;
    line-height: 1.3;
}

.news-meta {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
    padding-bottom: 20px;
    border-bottom: 1px solid #eee;
    margin-bottom: 30px;
    color: #666;
    font-size: 14px;
}

.news-meta i {
    margin-right: 5px;
    color: #2e7d32;
}

.news-featured-image {
    margin-bottom: 30px;
    border-radius: 15px;
    overflow: hidden;
}

.news-featured-image img {
    width: 100%;
    max-height: 500px;
    object-fit: cover;
}

.news-content {
    font-size: 16px;
    line-height: 1.8;
    color: #333;
}

.news-content p {
    margin-bottom: 20px;
}

.news-content h2, .news-content h3 {
    color: #1a4d8c;
    margin-top: 30px;
    margin-bottom: 15px;
}

.news-content ul, .news-content ol {
    margin-bottom: 20px;
    padding-left: 20px;
}

.news-content li {
    margin-bottom: 10px;
}

.news-content img {
    max-width: 100%;
    height: auto;
    border-radius: 10px;
    margin: 20px 0;
}

.share-section {
    margin-top: 40px;
    padding-top: 20px;
    border-top: 1px solid #eee;
}

.share-title {
    font-weight: 600;
    margin-bottom: 15px;
}

.share-buttons {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.share-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    border-radius: 25px;
    color: white;
    text-decoration: none;
    font-size: 14px;
    transition: transform 0.2s;
}

.share-btn:hover {
    transform: translateY(-2px);
    color: white;
}

.share-facebook { background: #3b5998; }
.share-twitter { background: #1da1f2; }
.share-whatsapp { background: #25d366; }
.share-linkedin { background: #0077b5; }
.share-email { background: #666; }

.related-news-card {
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: transform 0.3s;
    margin-bottom: 20px;
}

.related-news-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.15);
}

.related-news-image {
    height: 150px;
    overflow: hidden;
}

.related-news-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
}

.related-news-card:hover .related-news-image img {
    transform: scale(1.05);
}

.related-news-content {
    padding: 15px;
}

.related-news-title {
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 10px;
}

.related-news-title a {
    color: #1a4d8c;
    text-decoration: none;
}

.related-news-title a:hover {
    color: #2e7d32;
}

.related-news-date {
    font-size: 11px;
    color: #999;
}

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

.recent-news-item {
    display: flex;
    gap: 15px;
    padding: 10px 0;
    border-bottom: 1px solid #ddd;
}

.recent-news-item:last-child {
    border-bottom: none;
}

.recent-news-img {
    width: 60px;
    height: 60px;
    border-radius: 5px;
    overflow: hidden;
    flex-shrink: 0;
}

.recent-news-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.recent-news-info h4 {
    font-size: 13px;
    margin: 0 0 5px;
}

.recent-news-info h4 a {
    color: #333;
    text-decoration: none;
}

.recent-news-info h4 a:hover {
    color: #1a4d8c;
}

.recent-news-info .date {
    font-size: 10px;
    color: #999;
}

.category-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.category-list li {
    padding: 10px 0;
    border-bottom: 1px solid #ddd;
}

.category-list li:last-child {
    border-bottom: none;
}

.category-list a {
    display: flex;
    justify-content: space-between;
    color: #333;
    text-decoration: none;
    transition: color 0.3s;
}

.category-list a:hover {
    color: #1a4d8c;
}

.category-list span {
    background: #ddd;
    padding: 2px 8px;
    border-radius: 20px;
    font-size: 11px;
}

.back-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #1a4d8c;
    color: white;
    padding: 10px 25px;
    border-radius: 30px;
    text-decoration: none;
    transition: all 0.3s;
    margin-top: 30px;
}

.back-btn:hover {
    background: #2e7d32;
    color: white;
    gap: 12px;
}

@media (max-width: 768px) {
    .news-title {
        font-size: 28px;
    }
    .news-detail-container {
        padding: 20px;
    }
    .share-buttons {
        justify-content: center;
    }
}
</style>

<!-- Main Content -->
<div class="container">
    <div class="row">
        <!-- Main Content Column -->
        <div class="col-lg-8">
            <div class="news-detail-container">
                <article class="news-article">
                    <div class="news-header">
                        <span class="news-category"><?php echo ucfirst($news['category'] ?: 'General'); ?></span>
                        <h1 class="news-title"><?php echo htmlspecialchars($news['title']); ?></h1>
                        <div class="news-meta">
                            <span><i class="far fa-calendar-alt"></i> <?php echo date('F j, Y', strtotime($news['published_date'])); ?></span>
                            <span><i class="far fa-clock"></i> <?php echo date('g:i A', strtotime($news['published_date'])); ?></span>
                            <span><i class="far fa-eye"></i> <?php echo number_format($news['views']); ?> views</span>
                        </div>
                    </div>
                    
                    <?php if($news['featured_image']): ?>
                    <div class="news-featured-image">
                        <img src="<?php echo $news['featured_image']; ?>" alt="<?php echo htmlspecialchars($news['title']); ?>">
                    </div>
                    <?php endif; ?>
                    
                    <div class="news-content">
                        <?php echo htmlspecialchars_decode($news['content']); ?>
                    </div>
                    
                    <div class="share-section">
                        <div class="share-title"><i class="fas fa-share-alt"></i> Share this article:</div>
                        <div class="share-buttons">
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" target="_blank" class="share-btn share-facebook">
                                <i class="fab fa-facebook-f"></i> Facebook
                            </a>
                            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>&text=<?php echo urlencode($news['title']); ?>" target="_blank" class="share-btn share-twitter">
                                <i class="fab fa-twitter"></i> Twitter
                            </a>
                            <a href="https://wa.me/?text=<?php echo urlencode($news['title'] . ' - http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" target="_blank" class="share-btn share-whatsapp">
                                <i class="fab fa-whatsapp"></i> WhatsApp
                            </a>
                            <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>&title=<?php echo urlencode($news['title']); ?>" target="_blank" class="share-btn share-linkedin">
                                <i class="fab fa-linkedin-in"></i> LinkedIn
                            </a>
                            <a href="mailto:?subject=<?php echo urlencode($news['title']); ?>&body=Check out this article: <?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" class="share-btn share-email">
                                <i class="fas fa-envelope"></i> Email
                            </a>
                        </div>
                    </div>
                </article>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Search Widget -->
            <div class="sidebar-widget">
                <h3><i class="fas fa-search"></i> Search News</h3>
                <form method="GET" action="news.php">
                    <div class="search-box" style="position: relative;">
                        <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #999;"></i>
                        <input type="text" name="search" placeholder="Search articles..." style="width: 100%; padding: 10px 15px 10px 40px; border: 1px solid #ddd; border-radius: 8px;">
                    </div>
                </form>
            </div>
            
            <!-- Recent News Widget -->
            <div class="sidebar-widget">
                <h3><i class="fas fa-clock"></i> Recent News</h3>
                <?php foreach($recentNews as $recent): ?>
                <div class="recent-news-item">
                    <?php if($recent['featured_image']): ?>
                    <div class="recent-news-img">
                        <img src="<?php echo $recent['featured_image']; ?>" alt="<?php echo htmlspecialchars($recent['title']); ?>">
                    </div>
                    <?php endif; ?>
                    <div class="recent-news-info">
                        <h4><a href="news-detail.php?id=<?php echo $recent['id']; ?>"><?php echo htmlspecialchars(substr($recent['title'], 0, 40)); ?>...</a></h4>
                        <div class="date"><i class="far fa-calendar-alt"></i> <?php echo date('M d, Y', strtotime($recent['published_date'])); ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Categories Widget -->
            <div class="sidebar-widget">
                <h3><i class="fas fa-tags"></i> Categories</h3>
                <ul class="category-list">
                    <li><a href="news.php">All News <span><?php echo $conn->query("SELECT COUNT(*) FROM news WHERE status='published'")->fetchColumn(); ?></span></a></li>
                    <?php foreach($categories as $cat): ?>
                    <li><a href="news.php?category=<?php echo urlencode($cat['category']); ?>"><?php echo ucfirst($cat['category']); ?> <span><?php echo $cat['count']; ?></span></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <!-- Back Button -->
            <a href="news.php" class="back-btn">
                <i class="fas fa-arrow-left"></i> Back to All News
            </a>
        </div>
    </div>
    
    <!-- Related News Section -->
    <?php if(!empty($relatedNews)): ?>
    <div class="row mt-5">
        <div class="col-12">
            <h3 style="color: #1a4d8c; margin-bottom: 20px;"><i class="fas fa-newspaper"></i> Related News</h3>
            <div class="row">
                <?php foreach($relatedNews as $related): ?>
                <div class="col-md-4">
                    <div class="related-news-card">
                        <?php if($related['featured_image']): ?>
                        <div class="related-news-image">
                            <img src="<?php echo $related['featured_image']; ?>" alt="<?php echo htmlspecialchars($related['title']); ?>">
                        </div>
                        <?php endif; ?>
                        <div class="related-news-content">
                            <h4 class="related-news-title">
                                <a href="news-detail.php?id=<?php echo $related['id']; ?>"><?php echo htmlspecialchars(substr($related['title'], 0, 50)); ?></a>
                            </h4>
                            <div class="related-news-date">
                                <i class="far fa-calendar-alt"></i> <?php echo date('M d, Y', strtotime($related['published_date'])); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// Auto-submit search on input
let searchTimeout;
$('input[name="search"]').on('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        $(this).closest('form').submit();
    }, 500);
});

// Add reading time indicator
const content = document.querySelector('.news-content');
if(content) {
    const text = content.innerText;
    const wordCount = text.split(/\s+/).length;
    const readingTime = Math.ceil(wordCount / 200);
    const readingTimeElement = document.createElement('div');
    readingTimeElement.className = 'reading-time';
    readingTimeElement.innerHTML = '<i class="fas fa-clock"></i> ' + readingTime + ' min read';
    readingTimeElement.style.cssText = 'margin-bottom: 15px; color: #666; font-size: 14px;';
    document.querySelector('.news-meta').appendChild(readingTimeElement);
}
</script>

<?php include 'includes/footer.php'; ?>