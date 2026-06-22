<?php
// includes/header.php - Complete header with working mobile menu (Self-contained)
?>
<!DOCTYPE html>
<html lang="en">
<head>
    
   <!-- ========== EXISTING TAGS (KEEP THESE) ========== -->
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<meta name="theme-color" content="#1a4d8c">
<title>Stella Maris College Nsuube - Catholic Girls' Secondary School</title>
<meta name="description" content="Stella Maris College Nsuube is a premier Catholic girls' secondary school offering O-Level and A-Level education">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">

<!-- ========== ADD THESE LINES FOR SOCIAL SHARING ========== -->
<!-- These tell Facebook, WhatsApp, Twitter what image to show when sharing -->

<!-- 1. FAVICON (Logo in browser tab) -->
<link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
<link rel="icon" type="image/png" href="assets/images/logo.jpg">

<!-- 2. OPEN GRAPH (For Facebook, WhatsApp, LinkedIn, etc.) -->
<meta property="og:title" content="Stella Maris College Nsuube - Catholic Girls' Secondary School">
<meta property="og:description" content="Stella Maris College Nsuube is a premier Catholic girls' secondary school offering O-Level and A-Level education.">
<meta property="og:image" content="https://stellamariscollegensuube.com/assets/images/logo.jpg">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:url" content="https://stellamariscollegensuube.com">
<meta property="og:type" content="website">
<meta property="og:site_name" content="Stella Maris College Nsuube">

<!-- 3. TWITTER CARD (For X/Twitter sharing) -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Stella Maris College Nsuube - Catholic Girls' Secondary School">
<meta name="twitter:description" content="Stella Maris College Nsuube is a premier Catholic girls' secondary school offering O-Level and A-Level education.">
<meta name="twitter:image" content="https://stellamariscollegensuube.com/assets/images/logo.jpg">
<!-- ========== REST OF YOUR EXISTING CODE - DO NOT CHANGE ========== -->
<!-- Everything below stays exactly as it was -->

    <style>
        /* ===== RESET & BASE STYLES ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f5f5;
            color: #1a1a1a;
            overflow-x: hidden;
        }
        
        :root {
            --primary-blue: #1a4d8c;
            --primary-blue-dark: #0d3b6b;
            --primary-blue-light: #e8f0fe;
            --secondary-green: #2e7d32;
            --white: #ffffff;
            --black: #1a1a1a;
            --gray: #666666;
            --gray-light: #f5f5f5;
            --shadow-sm: 0 2px 4px rgba(0,0,0,0.1);
            --shadow-md: 0 4px 6px rgba(0,0,0,0.1);
            --shadow-lg: 0 10px 25px rgba(0,0,0,0.1);
            --transition: all 0.3s ease;
        }
        
        /* ===== TOP HEADER STYLES ===== */
        .top-header {
            background: var(--white);
            box-shadow: var(--shadow-sm);
            position: sticky;
            top: 0;
            z-index: 100;
            padding: 12px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }
        
        .header-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .header-logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .header-logo img {
            width: 40px;
            height: 40px;
        }
        
        .header-logo h2 {
            font-size: 18px;
            color: var(--primary-blue);
            margin: 0;
        }
        
        .header-logo p {
            font-size: 11px;
            color: var(--gray);
            margin: 0;
        }
        
        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .quick-contact span {
            font-size: 13px;
            margin-left: 16px;
            color: var(--gray);
        }
        
        .quick-contact i {
            color: var(--primary-blue);
            margin-right: 6px;
        }
        
        .mobile-search-btn {
            display: none;
            background: none;
            border: none;
            font-size: 20px;
            color: var(--primary-blue);
            cursor: pointer;
        }
        
        /* Mobile Search Bar */
        .mobile-search-bar {
            display: none;
            padding: 12px 16px;
            background: var(--white);
            border-bottom: 1px solid #eee;
            width: 100%;
        }
        
        .mobile-search-bar.active {
            display: block;
        }
        
        .search-container {
            position: relative;
            display: flex;
            align-items: center;
        }
        
        .search-container i {
            position: absolute;
            left: 12px;
            color: var(--gray);
        }
        
        .search-container input {
            flex: 1;
            padding: 12px 40px;
            border: 1px solid #ddd;
            border-radius: 30px;
            font-size: 14px;
            outline: none;
            width: 100%;
        }
        
        .search-container .close-search {
            position: absolute;
            right: 5px;
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
            padding: 8px;
        }
        
        /* ===== MOBILE MENU STYLES ===== */
        .menu-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.6);
            backdrop-filter: blur(3px);
            z-index: 99998;
        }
        
        .menu-overlay.active {
            display: block !important;
        }
        
        .left-sidebar {
            position: fixed;
            left: -300px;
            top: 0;
            width: 300px;
            max-width: 85%;
            height: 100%;
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            box-shadow: 5px 0 25px rgba(0,0,0,0.15);
            transition: left 0.3s ease-in-out;
            z-index: 99999;
            overflow-y: auto;
            overflow-x: hidden;
        }
        
        .left-sidebar.active {
            left: 0 !important;
        }
        
        .sidebar-header {
            padding: 25px 15px;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            color: white;
            text-align: center;
            position: relative;
        }
   
        .school-logo {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }
        
        .school-logo img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #e94560;
            padding: 3px;
            background: white;
        }
        
        .school-logo h3 {
            font-size: 16px;
            margin: 0;
            line-height: 1.3;
        }
        
        .school-logo span {
            font-size: 11px;
            font-weight: normal;
            opacity: 0.9;
        }
        
        .close-menu-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            font-size: 20px;
            cursor: pointer;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .close-menu-btn:hover {
            background: rgba(255,255,255,0.3);
        }
        
        .sidebar-search {
            padding: 15px;
            border-bottom: 1px solid rgba(0,0,0,0.08);
        }
        
        .search-box {
            display: flex;
            align-items: center;
            background: white;
            border-radius: 10px;
            padding: 8px 15px;
            border: 1px solid rgba(0,0,0,0.1);
        }
        
        .search-box i {
            color: #e94560;
            margin-right: 10px;
        }
        
        .search-box input {
            border: none;
            outline: none;
            width: 100%;
            font-size: 14px;
        }
        
        .sidebar-nav {
            padding: 10px 0;
        }
        
        .nav-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .nav-item {
            margin: 5px 0;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 18px;
            margin: 0 10px;
            color: #2c3e50;
            text-decoration: none;
            gap: 12px;
            border-radius: 10px;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.2s;
        }
        
        .nav-link:hover {
            background: rgba(233,69,96,0.1);
            color: #e94560;
        }
        
        .nav-link.active {
            background: #e94560;
            color: white;
        }
        
        .nav-link i:first-child {
            width: 20px;
            font-size: 16px;
        }
        
        .arrow {
            margin-left: auto;
            transition: transform 0.2s;
            font-size: 12px;
        }
        
        .has-submenu.submenu-open .arrow {
            transform: rotate(180deg);
        }
        
        .submenu {
            list-style: none;
            padding: 0;
            margin: 0;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            background: rgba(0,0,0,0.02);
        }
        
        .has-submenu.submenu-open .submenu {
            max-height: 300px;
        }
        
        .submenu li a {
            display: flex;
            align-items: center;
            padding: 10px 18px 10px 50px;
            color: #666;
            text-decoration: none;
            font-size: 13px;
            gap: 10px;
        }
        
        .submenu li a:hover {
            background: rgba(233,69,96,0.08);
            color: #e94560;
        }
        
        .submenu li a i {
            width: 18px;
            font-size: 13px;
        }
        
        .sidebar-footer {
            padding: 15px;
            border-top: 1px solid rgba(0,0,0,0.08);
            margin-top: 20px;
        }
        
        .contact-info p {
            margin: 8px 0;
            font-size: 11px;
            color: #555;
            display: flex;
            align-items: center;
        }
        
        .contact-info i {
            width: 25px;
            color: #e94560;
            font-size: 12px;
        }
        
        .social-links {
            display: flex;
            gap: 12px;
            margin: 15px 0;
        }
        
        .social-links a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            background: white;
            color: #1a1a2e;
            border-radius: 50%;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.2s;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .social-links a:hover {
            background: #e94560;
            color: white;
        }
        
        .btn-admin {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px;
            background: #1a1a2e;
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-size: 12px;
            transition: all 0.2s;
        }
        
        .btn-admin:hover {
            background: #e94560;
        }
        
        .left-sidebar::-webkit-scrollbar {
            width: 4px;
        }
        
        .left-sidebar::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        .left-sidebar::-webkit-scrollbar-thumb {
            background: #e94560;
            border-radius: 10px;
        }
        
        .menu-toggle-btn {
            background: var(--primary-blue);
            border: none;
            width: 42px;
            height: 42px;
            border-radius: 8px;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }
        
        .menu-toggle-btn:hover {
            background: var(--primary-blue-dark);
        }
        
        /* ===== CONTAINER ===== */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            width: 100%;
        }
        
        .main-wrapper {
            min-height: 100vh;
            background: var(--gray-light);
            width: 100%;
            transition: margin-left 0.3s ease;
        }
        
        /* ===== RESPONSIVE DESIGN ===== */
        @media (max-width: 768px) {
            .quick-contact {
                display: none;
            }
            
            .mobile-search-btn {
                display: block;
            }
            
            .left-sidebar {
                left: -300px;
            }
            
            .menu-toggle-btn {
                display: flex !important;
            }
        }
        
        @media (min-width: 769px) {
            .left-sidebar {
                left: 0 !important;
            }
            
            .menu-overlay {
                display: none !important;
            }
            
            .main-wrapper {
                margin-left: 300px;
                width: calc(100% - 300px);
            }
            
            .menu-toggle-btn {
                display: none !important;
            }
        }
        
        /* ===== FOOTER STYLES ===== */
        .footer {
            background: var(--black);
            color: var(--white);
            padding: 60px 0 20px;
            width: 100%;
        }
        
        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }
        
        .footer-about img {
            width: 60px;
            margin-bottom: 15px;
        }
        
        .footer-about h3 {
            font-size: 18px;
            margin-bottom: 15px;
        }
        
        .footer-about p {
            font-size: 14px;
            color: #aaa;
            line-height: 1.6;
        }
        
        .footer-social {
            margin-top: 20px;
        }
        
        .footer-social a {
            display: inline-block;
            width: 35px;
            height: 35px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            text-align: center;
            line-height: 35px;
            margin-right: 10px;
            color: var(--white);
            transition: var(--transition);
        }
        
        .footer-links h4,
        .footer-contact h4,
        .footer-newsletter h4 {
            font-size: 18px;
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 10px;
        }
        
        .footer-links h4:after,
        .footer-contact h4:after,
        .footer-newsletter h4:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 2px;
            background: var(--secondary-green);
        }
        
        .footer-links ul,
        .footer-contact ul {
            list-style: none;
        }
        
        .footer-links li,
        .footer-contact li {
            margin-bottom: 12px;
        }
        
        .footer-links a {
            color: #aaa;
            text-decoration: none;
            font-size: 14px;
            transition: var(--transition);
        }
        
        .footer-links a:hover {
            color: var(--white);
            padding-left: 5px;
        }
        
        .footer-contact li {
            font-size: 14px;
            color: #aaa;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .footer-contact i {
            width: 20px;
            color: var(--secondary-green);
        }
        
        .newsletter-group {
            display: flex;
            margin-top: 15px;
        }
        
        .newsletter-group input {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 8px 0 0 8px;
            outline: none;
        }
        
        .newsletter-group button {
            background: var(--secondary-green);
            border: none;
            padding: 0 15px;
            border-radius: 0 8px 8px 0;
            color: var(--white);
            cursor: pointer;
            transition: var(--transition);
        }
        
        .footer-bottom {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        
        .footer-bottom p {
            font-size: 12px;
            color: #aaa;
        }
        
        /* ===== UTILITY CLASSES ===== */
        .text-center { text-align: center; }
        .mt-4 { margin-top: 40px; }
        .mb-4 { margin-bottom: 40px; }
        
        .row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -15px;
        }
        
        .col-lg-6 { width: 50%; padding: 0 15px; }
        .col-md-4 { width: 33.333%; padding: 0 15px; }
        
        @media (max-width: 768px) {
            .col-lg-6, .col-md-4 { width: 100%; margin-bottom: 20px; }
        }
        
        .btn-primary {
            display: inline-block;
            padding: 12px 30px;
            background: var(--primary-blue);
            color: white;
            text-decoration: none;
            border-radius: 30px;
            font-weight: 600;
            transition: var(--transition);
        }
        
        .btn-primary:hover {
            background: var(--primary-blue-dark);
            transform: translateY(-2px);
        }
        
        /* Page Header */
        .page-header {
            padding: 60px 0;
            text-align: center;
            color: var(--white);
            background-size: cover;
            background-position: center;
            width: 100%;
        }
        
        .page-header h1 {
            font-size: 48px;
            margin-bottom: 15px;
        }
        
        .page-header p {
            font-size: 18px;
            opacity: 0.9;
        }
        
        @media (max-width: 768px) {
            .page-header {
                padding: 40px 0;
            }
            .page-header h1 {
                font-size: 32px;
            }
        }
    </style>
</head>
<body>
    <!-- Menu Overlay -->
    <div class="menu-overlay" id="menuOverlay"></div>
    
    <!-- Sidebar Menu -->
    <aside class="left-sidebar" id="leftSidebar">
        <div class="sidebar-header">
            <div class="school-logo">
                <img src="assets/images/logo.jpg" alt="logo">
                <h3>Stella Maris<br><span>College Nsuube</span></h3>
            </div>
            <button class="close-menu-btn" id="closeMenuBtn">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="sidebar-search">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="menuSearchInput" placeholder="Search menu...">
            </div>
        </div>
        
        <nav class="sidebar-nav">
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="index.php" class="nav-link">
                        <i class="fas fa-home"></i>
                        <span>Home</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="about.php" class="nav-link">
                        <i class="fas fa-info-circle"></i>
                        <span>About Us</span>
                    </a>
                </li>
                <li class="nav-item has-submenu">
                    <a href="javascript:void(0)" class="nav-link">
                        <i class="fas fa-book"></i>
                        <span>Academics</span>
                        <i class="fas fa-chevron-down arrow"></i>
                    </a>
                    <ul class="submenu">
                        <li><a href="olevel.php"><i class="fas fa-chalkboard-user"></i> O-Level (S1-S4)</a></li>
                        <li><a href="alevel.php"><i class="fas fa-graduation-cap"></i> A-Level (S5-S6)</a></li>
                        <li><a href="subjects.php"><i class="fas fa-flask"></i> Subjects Offered</a></li>
                        <li><a href="departments.php"><i class="fas fa-building"></i> Departments</a></li>
                        <li><a href="academic-calendar.php"><i class="fas fa-calendar-alt"></i> Academic Calendar</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="gallery.php" class="nav-link">
                        <i class="fas fa-images"></i>
                        <span>Gallery</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="news.php" class="nav-link">
                        <i class="fas fa-newspaper"></i>
                        <span>News & Updates</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="events.php" class="nav-link">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Events</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="alumni.php" class="nav-link">
                        <i class="fas fa-users"></i>
                        <span>Old Girls (Alumni)</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="admission-downloads.php" class="nav-link">
                        <i class="fas fa-graduation-cap"></i>
                        <span>Admission and Downloads</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="contact.php" class="nav-link">
                        <i class="fas fa-envelope"></i>
                        <span>Contact Us</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="spiritual-life.php" class="nav-link">
                        <i class="fas fa-cross"></i>
                        <span>Spiritual Life</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="sports.php" class="nav-link">
                        <i class="fas fa-futbol"></i>
                        <span>Sports & Co-curricular</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="staff.php" class="nav-link">
                        <i class="fas fa-chalkboard-user"></i>
                        <span>Our Staff</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="faq.php" class="nav-link">
                        <i class="fas fa-question-circle"></i>
                        <span>FAQ</span>
                    </a>
                </li>
            </ul>
        </nav>
        
        <div class="sidebar-footer">
            <div class="contact-info">
                <p><i class="fas fa-phone-alt"></i> +25677094664/+256782394058</p>
                <p><i class="fas fa-envelope"></i> stellamariscollege2025@gmail.com</p>
                <p><i class="fas fa-map-marker-alt"></i> P.O. Box 51, mukono, Uganda</p>
            </div>
            <div class="social-links">
                <a href="#"><i class="fa-brands fa-tiktok"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-youtube"></i></a>
            </div>
            <a href="admin/login.php" class="btn-admin">
                <i class="fas fa-lock"></i> Admin Login
            </a>
        </div>
    </aside>
    
    <!-- Main Content Wrapper -->
    <div class="main-wrapper">
        <!-- Top Header -->
        <header class="top-header">
            <div class="header-left">
                <button class="menu-toggle-btn" id="menuToggleBtn">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="header-logo">
                    <img src="assets/images/logo.jpg" alt="Logo">
                    <div class="logo-text">
                        <h2>Stella Maris College</h2>
                        <p>Nsuube - Empowering Young Women</p>
                    </div>
                </div>
            </div>
            <div class="header-right">
                <div class="quick-contact">
                    <span><i class="fas fa-phone-alt"></i> +256779094664/+256782394058</span>
                    <span><i class="fas fa-envelope"></i> stellamariscollege2025@gmail.com</span>
                </div>
                <button class="mobile-search-btn" id="mobileSearchBtn">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </header>
        
        <!-- Mobile Search Bar -->
        <div class="mobile-search-bar" id="mobileSearchBar">
            <div class="search-container">
                <i class="fas fa-search"></i>
                <input type="text" id="globalSearch" placeholder="Search news, events, pages...">
                <button class="close-search"><i class="fas fa-times"></i></button>
            </div>
        </div>

<script>
// ===== COMPLETE WORKING JAVASCRIPT =====
(function() {
    // Mobile Menu Toggle
    function initMobileMenu() {
        var menuBtn = document.getElementById('menuToggleBtn');
        var sidebar = document.getElementById('leftSidebar');
        var overlay = document.getElementById('menuOverlay');
        var closeBtn = document.getElementById('closeMenuBtn');
        
        if (menuBtn && sidebar && overlay) {
            function openMenu() {
                sidebar.classList.add('active');
                overlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
            
            function closeMenu() {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            }
            
            menuBtn.onclick = function(e) {
                e.preventDefault();
                openMenu();
            };
            
            if (closeBtn) {
                closeBtn.onclick = function(e) {
                    e.preventDefault();
                    closeMenu();
                };
            }
            
            overlay.onclick = closeMenu;
            
            document.onkeydown = function(e) {
                if (e.key === 'Escape' && sidebar.classList.contains('active')) {
                    closeMenu();
                }
            };
            
            var allLinks = sidebar.querySelectorAll('a');
            for (var i = 0; i < allLinks.length; i++) {
                allLinks[i].onclick = function() {
                    if (!this.closest('.has-submenu')) {
                        setTimeout(closeMenu, 200);
                    }
                };
            }
        }
    }
    
    // Submenu Toggle
    function initSubmenu() {
        var toggles = document.querySelectorAll('.has-submenu > a');
        for (var i = 0; i < toggles.length; i++) {
            toggles[i].onclick = function(e) {
                e.preventDefault();
                var parent = this.parentElement;
                parent.classList.toggle('submenu-open');
            };
        }
    }
    
    // Search Functionality
    function initSearch() {
        var searchInput = document.getElementById('menuSearchInput');
        if (searchInput) {
            searchInput.onkeyup = function() {
                var filter = this.value.toLowerCase();
                var items = document.querySelectorAll('.nav-item');
                for (var i = 0; i < items.length; i++) {
                    var text = items[i].textContent.toLowerCase();
                    items[i].style.display = text.indexOf(filter) > -1 ? '' : 'none';
                }
            };
        }
    }
    
    // Mobile Search Toggle
    function initMobileSearch() {
        var searchBtn = document.getElementById('mobileSearchBtn');
        var searchBar = document.getElementById('mobileSearchBar');
        var closeSearch = document.querySelector('.close-search');
        
        if (searchBtn && searchBar) {
            searchBtn.onclick = function() {
                searchBar.classList.toggle('active');
                var searchInput = document.getElementById('globalSearch');
                if (searchInput) setTimeout(function() { searchInput.focus(); }, 100);
            };
        }
        
        if (closeSearch) {
            closeSearch.onclick = function() {
                searchBar.classList.remove('active');
            };
        }
    }
    
    // Run all when DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            initMobileMenu();
            initSubmenu();
            initSearch();
            initMobileSearch();
        });
    } else {
        initMobileMenu();
        initSubmenu();
        initSearch();
        initMobileSearch();
    }
})();
</script>