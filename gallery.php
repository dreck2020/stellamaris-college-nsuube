<?php
// gallery.php
session_start();
$page_title = "Gallery";
require_once 'config/database.php';
$db = new Database();
$conn = $db->getConnection();

$gallery = $conn->query("SELECT * FROM gallery ORDER BY uploaded_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include 'includes/head.php'; ?>
<?php include 'includes/sidebar.php'; ?>
<?php include 'includes/header.php'; ?>

<style>
    /* ===== GALLERY PAGE STYLES ===== */
    .gallery-page-header {
        background: linear-gradient(rgba(26,77,140,0.85), rgba(0,0,0,0.7)), url('assets/images/gallery-bg.jpg');
        background-size: cover;
        background-position: center;
        padding: 60px 0;
        color: white;
        text-align: center;
    }
    .gallery-page-header h1 {
        font-size: 2.5rem;
        font-weight: 700;
    }
    .gallery-page-header p {
        font-size: 1.2rem;
        opacity: 0.9;
    }
    
    .gallery-section {
        padding: 60px 0;
        background: #f5f5f5;
    }
    
    /* ===== TWO COLUMN GALLERY GRID ===== */
    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 25px;
        max-width: 1200px;
        margin: 0 auto;
    }
    
    .gallery-item {
        position: relative;
        overflow: hidden;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        cursor: pointer;
        transition: all 0.3s ease;
        background: #fff;
        aspect-ratio: 4/3;
    }
    
    .gallery-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.2);
    }
    
    .gallery-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    
    .gallery-item:hover img {
        transform: scale(1.05);
    }
    
    /* ===== OVERLAY ===== */
    .gallery-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(transparent, rgba(0,0,0,0.8));
        padding: 20px 15px 15px;
        transform: translateY(100%);
        transition: transform 0.3s ease;
    }
    
    .gallery-item:hover .gallery-overlay {
        transform: translateY(0);
    }
    
    .gallery-overlay h4 {
        color: white;
        margin: 0;
        font-size: 1rem;
        font-weight: 600;
    }
    
    .gallery-overlay p {
        color: rgba(255,255,255,0.8);
        margin: 5px 0 0;
        font-size: 0.85rem;
    }
    
    /* ===== LIGHTBOX ===== */
    .lightbox {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.92);
        z-index: 99999;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    
    .lightbox.active {
        display: flex;
    }
    
    .lightbox-content {
        max-width: 90%;
        max-height: 85vh;
        border-radius: 8px;
        box-shadow: 0 10px 50px rgba(0,0,0,0.5);
        object-fit: contain;
    }
    
    .lightbox-close {
        position: absolute;
        top: 25px;
        right: 35px;
        color: white;
        font-size: 40px;
        cursor: pointer;
        transition: all 0.3s;
        background: none;
        border: none;
        z-index: 999999;
    }
    
    .lightbox-close:hover {
        color: #e94560;
        transform: rotate(90deg);
    }
    
    .lightbox-caption {
        position: absolute;
        bottom: 30px;
        left: 50%;
        transform: translateX(-50%);
        color: white;
        background: rgba(0,0,0,0.6);
        padding: 10px 25px;
        border-radius: 30px;
        font-size: 1rem;
        text-align: center;
    }
    
    .lightbox-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        color: white;
        font-size: 40px;
        cursor: pointer;
        background: rgba(255,255,255,0.1);
        padding: 15px 20px;
        border-radius: 50%;
        transition: all 0.3s;
        border: none;
    }
    
    .lightbox-nav:hover {
        background: rgba(255,255,255,0.3);
    }
    
    .lightbox-prev {
        left: 20px;
    }
    
    .lightbox-next {
        right: 20px;
    }
    
    /* ===== COUNTER ===== */
    .gallery-counter {
        text-align: center;
        color: #7f8c8d;
        margin-bottom: 25px;
        font-size: 0.95rem;
    }
    
    .gallery-counter span {
        font-weight: 700;
        color: #1a4d8c;
    }
    
    /* ===== EMPTY STATE ===== */
    .empty-gallery {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 15px;
        grid-column: 1 / -1;
    }
    
    .empty-gallery i {
        font-size: 4rem;
        color: #dee2e6;
        margin-bottom: 20px;
    }
    
    .empty-gallery h3 {
        color: #2c3e50;
        margin-bottom: 10px;
    }
    
    .empty-gallery p {
        color: #7f8c8d;
    }
    
    /* ===== RESPONSIVE ===== */
    @media (max-width: 992px) {
        .gallery-grid {
            gap: 20px;
        }
    }
    
    @media (max-width: 768px) {
        .gallery-grid {
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .gallery-page-header h1 {
            font-size: 1.8rem;
        }
        
        .gallery-page-header p {
            font-size: 1rem;
        }
        
        .lightbox-content {
            max-width: 95%;
            max-height: 70vh;
        }
        
        .lightbox-nav {
            font-size: 25px;
            padding: 10px 15px;
        }
        
        .lightbox-prev {
            left: 10px;
        }
        
        .lightbox-next {
            right: 10px;
        }
        
        .lightbox-caption {
            font-size: 0.85rem;
            padding: 8px 18px;
            bottom: 15px;
        }
    }
    
    @media (max-width: 480px) {
        .gallery-grid {
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        
        .gallery-item {
            border-radius: 8px;
        }
        
        .gallery-overlay h4 {
            font-size: 0.8rem;
        }
        
        .lightbox-close {
            top: 15px;
            right: 20px;
            font-size: 30px;
        }
    }
</style>

<!-- ===== PAGE HEADER ===== -->
<section class="gallery-page-header">
    <div class="container">
        <h1>📸 Gallery</h1>
        <p>Moments and memories at Stella Maris College</p>
    </div>
</section>

<!-- ===== GALLERY SECTION ===== -->
<section class="gallery-section">
    <div class="container">
        <?php if(count($gallery) > 0): ?>
            <div class="gallery-counter">
                <i class="fas fa-images"></i> 
                <span><?= count($gallery) ?></span> photos in our gallery
            </div>
            
            <div class="gallery-grid" id="galleryGrid">
                <?php foreach($gallery as $index => $item): ?>
                    <div class="gallery-item" data-index="<?= $index ?>" onclick="openLightbox(<?= $index ?>)">
                        <img src="<?php echo $item['file_path']; ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" loading="lazy">
                        <div class="gallery-overlay">
                            <h4><?php echo htmlspecialchars($item['title']); ?></h4>
                            <?php if(!empty($item['description'])): ?>
                                <p><?php echo htmlspecialchars($item['description']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-gallery">
                <i class="fas fa-images"></i>
                <h3>No Photos Yet</h3>
                <p>Check back soon for photos from our school events and activities.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- ===== LIGHTBOX ===== -->
<div class="lightbox" id="lightbox" onclick="closeLightbox(event)">
    <button class="lightbox-close" onclick="closeLightbox()">&times;</button>
    <button class="lightbox-nav lightbox-prev" onclick="prevImage(event)">&#10094;</button>
    <button class="lightbox-nav lightbox-next" onclick="nextImage(event)">&#10095;</button>
    <img class="lightbox-content" id="lightboxImage" src="" alt="">
    <div class="lightbox-caption" id="lightboxCaption"></div>
</div>

<script>
// ===== LIGHTBOX JAVASCRIPT =====
var galleryImages = [];
var currentIndex = 0;

// Load all gallery images
document.addEventListener('DOMContentLoaded', function() {
    var items = document.querySelectorAll('.gallery-item img');
    items.forEach(function(img) {
        var caption = img.closest('.gallery-item').querySelector('.gallery-overlay h4');
        galleryImages.push({
            src: img.src,
            title: caption ? caption.textContent : 'Gallery Image'
        });
    });
});

function openLightbox(index) {
    currentIndex = index;
    var lightbox = document.getElementById('lightbox');
    var image = document.getElementById('lightboxImage');
    var caption = document.getElementById('lightboxCaption');
    
    image.src = galleryImages[index].src;
    caption.textContent = galleryImages[index].title;
    
    lightbox.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeLightbox(event) {
    // Close only if clicking on the background or close button
    if (event && event.target !== event.currentTarget && !event.target.closest('.lightbox-close')) {
        return;
    }
    document.getElementById('lightbox').classList.remove('active');
    document.body.style.overflow = '';
}

function nextImage(event) {
    event.stopPropagation();
    currentIndex = (currentIndex + 1) % galleryImages.length;
    updateLightbox();
}

function prevImage(event) {
    event.stopPropagation();
    currentIndex = (currentIndex - 1 + galleryImages.length) % galleryImages.length;
    updateLightbox();
}

function updateLightbox() {
    var image = document.getElementById('lightboxImage');
    var caption = document.getElementById('lightboxCaption');
    image.src = galleryImages[currentIndex].src;
    caption.textContent = galleryImages[currentIndex].title;
}

// Keyboard navigation
document.addEventListener('keydown', function(e) {
    var lightbox = document.getElementById('lightbox');
    if (!lightbox.classList.contains('active')) return;
    
    if (e.key === 'Escape') {
        closeLightbox();
    } else if (e.key === 'ArrowRight') {
        nextImage(e);
    } else if (e.key === 'ArrowLeft') {
        prevImage(e);
    }
});
</script>

<?php include 'includes/footer.php'; ?>