// assets/js/main.js - Complete website functionality (No sidebar/hamburger)
$(document).ready(function() {
    // Mobile Search Toggle
    $('#mobileSearchBtn').on('click', function() {
        $('#mobileSearchBar').toggleClass('active');
        $('#globalSearch').focus();
    });
    
    $('.close-search').on('click', function() {
        $('#mobileSearchBar').removeClass('active');
    });
    
    // Global Search
    let searchTimeout;
    $('#globalSearch').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            const searchTerm = $(this).val().toLowerCase();
            performGlobalSearch(searchTerm);
        }, 300);
    });
    
    function performGlobalSearch(term) {
        if (term.length < 2) {
            $('.search-results').remove();
            return;
        }
        
        $.ajax({
            url: 'ajax/search.php',
            method: 'POST',
            data: {search: term},
            success: function(response) {
                if ($('.search-results').length) {
                    $('.search-results').remove();
                }
                if (response.trim()) {
                    $('body').append('<div class="search-results">' + response + '</div>');
                }
            }
        });
    }
    
    // Close search results when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#globalSearch, .search-results').length) {
            $('.search-results').remove();
        }
    });
    
    // Smooth scrolling
    $('a[href^="#"]').on('click', function(e) {
        e.preventDefault();
        const target = $(this.hash);
        if (target.length) {
            $('html, body').animate({
                scrollTop: target.offset().top - 70
            }, 800);
        }
    });
    
    // Stats Counter Animation
    function animateNumbers() {
        $('.stat-number').each(function() {
            const $this = $(this);
            const target = parseInt($this.text());
            if (!isNaN(target) && !$this.hasClass('animated')) {
                $this.addClass('animated');
                let current = 0;
                const increment = target / 50;
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        $this.text(target);
                        clearInterval(timer);
                    } else {
                        $this.text(Math.floor(current));
                    }
                }, 20);
            }
        });
    }
    
    // Trigger counter when stats section is visible
    const statsSection = $('.stats-section');
    if (statsSection.length) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateNumbers();
                    observer.unobserve(entry.target);
                }
            });
        });
        observer.observe(statsSection[0]);
    }
    
    // Lazy loading images
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    const dataSrc = img.getAttribute('data-src');
                    if (dataSrc) {
                        img.src = dataSrc;
                        img.removeAttribute('data-src');
                    }
                    imageObserver.unobserve(img);
                }
            });
        });
        
        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }
    
    // Newsletter subscription
    $('#footerNewsletterForm').on('submit', function(e) {
        e.preventDefault();
        const email = $(this).find('input[type="email"]').val();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (!emailRegex.test(email)) {
            showNotification('Please enter a valid email address', 'error');
            return;
        }
        
        $.ajax({
            url: 'ajax/subscribe.php',
            method: 'POST',
            data: {email: email},
            success: function(response) {
                showNotification('Successfully subscribed to newsletter!', 'success');
                $('#footerNewsletterForm input[type="email"]').val('');
            },
            error: function() {
                showNotification('Error subscribing. Please try again.', 'error');
            }
        });
    });
    
    // Set reminder for event
    window.setReminder = function(eventId) {
        if (confirm('Set reminder for this event? We will notify you 1 day before.')) {
            $.ajax({
                url: 'ajax/set-reminder.php',
                method: 'POST',
                data: {event_id: eventId},
                success: function() {
                    showNotification('Reminder set successfully!', 'success');
                }
            });
        }
    };
    
    // Open prayer modal
    window.openPrayerModal = function() {
        $('#prayerModal').addClass('show');
    };
    
    window.closePrayerModal = function() {
        $('#prayerModal').removeClass('show');
    };
    
    // Close modal on outside click
    $(document).on('click', function(e) {
        if ($(e.target).is('.modal')) {
            $('.modal').removeClass('show');
        }
    });
    
    $('.close-modal').on('click', function() {
        $('.modal').removeClass('show');
    });
    
    // Notification system
    function showNotification(message, type) {
        const notification = $(`
            <div class="notification notification-${type}">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
                <span>${message}</span>
            </div>
        `);
        
        $('body').append(notification);
        
        setTimeout(() => {
            notification.addClass('show');
        }, 100);
        
        setTimeout(() => {
            notification.removeClass('show');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
    
    // Add notification styles
    const notificationStyles = `
        .notification {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: white;
            padding: 12px 20px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 10000;
            transform: translateX(400px);
            transition: transform 0.3s ease;
            font-size: 14px;
            max-width: 350px;
        }
        
        .notification.show {
            transform: translateX(0);
        }
        
        .notification-success {
            background: #2e7d32;
            color: white;
        }
        
        .notification-error {
            background: #dc3545;
            color: white;
        }
        
        @media (max-width: 768px) {
            .notification {
                left: 20px;
                right: 20px;
                transform: translateY(100px);
                max-width: none;
            }
            
            .notification.show {
                transform: translateY(0);
            }
        }
        
        .search-results {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            max-height: 400px;
            overflow-y: auto;
            z-index: 1000;
            margin-top: 5px;
        }
        
        .search-result-item {
            padding: 12px 16px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .search-result-item:hover {
            background: var(--gray-light);
        }
        
        .search-result-item h4 {
            font-size: 14px;
            margin-bottom: 4px;
        }
        
        .search-result-item p {
            font-size: 12px;
            color: var(--gray);
        }
    `;
    
    $('<style>').text(notificationStyles).appendTo('head');
    
    // Preloader
    $(window).on('load', function() {
        $('#preloader').fadeOut();
    });
    
    // Back to top button
    const backToTop = $(`
        <button class="back-to-top" id="backToTop">
            <i class="fas fa-arrow-up"></i>
        </button>
    `);
    
    $('body').append(backToTop);
    
    $(window).on('scroll', function() {
        if ($(window).scrollTop() > 300) {
            $('#backToTop').addClass('show');
        } else {
            $('#backToTop').removeClass('show');
        }
    });
    
    $('#backToTop').on('click', function() {
        $('html, body').animate({scrollTop: 0}, 500);
    });
    
    // Add back to top styles
    const backToTopStyles = `
        .back-to-top {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 45px;
            height: 45px;
            background: var(--primary-blue);
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            display: none;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
            z-index: 999;
        }
        
        .back-to-top.show {
            display: flex;
        }
        
        .back-to-top:hover {
            background: var(--secondary-green);
            transform: translateY(-2px);
        }
    `;
    
    $('<style>').text(backToTopStyles).appendTo('head');
});
// Mobile Menu Toggle - FIXED
$(document).ready(function() {
    const menuBtn = document.getElementById('menuToggleBtn');
    const sidebar = document.getElementById('leftSidebar');
    const overlay = document.getElementById('menuOverlay');
    const closeBtn = document.getElementById('closeMenuBtn');
    
    if (menuBtn && sidebar && overlay) {
        menuBtn.onclick = function(e) {
            e.preventDefault();
            sidebar.classList.add('active');
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        };
        
        if (closeBtn) {
            closeBtn.onclick = function() {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            };
        }
        
        overlay.onclick = function() {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        };
    }
});