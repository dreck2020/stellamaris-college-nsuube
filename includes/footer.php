<?php
// includes/footer.php - Simple Responsive Footer
?>
        <!-- Footer -->
        <footer class="footer">
            <div class="container">
                <div class="footer-grid">
                    <!-- About Section -->
                    <div class="footer-col">
                        <img src="assets/images/logo.png" alt="Stella Maris College" style="width: 50px; margin-bottom: 15px;">
                        <h3>Stella Maris College</h3>
                        <p>Empowering young women through quality Catholic education since 1950's.</p>
                        <div class="footer-social">
                           <a href="#"><i class="fa-brands fa-tiktok"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-instagram"></i></a>
                            <a href="#"><i class="fab fa-youtube"></i></a>
                        </div>
                    </div>
                    
                    <!-- Quick Links -->
                    <div class="footer-col">
                        <h4>Quick Links</h4>
                        <ul>
                            <li><a href="index.php">Home</a></li>
                            <li><a href="about.php">About Us</a></li>
                            <li><a href="admission.php">Admission</a></li>
                            <li><a href="gallery.php">Gallery</a></li>
                            <li><a href="contact.php">Contact</a></li>
                        </ul>
                    </div>
                    
                    <!-- Academics -->
                    <div class="footer-col">
                        <h4>Academics</h4>
                        <ul>
                            <li><a href="olevel.php">O-Level (S1-S4)</a></li>
                            <li><a href="alevel.php">A-Level (S5-S6)</a></li>
                            <li><a href="subjects.php">Subjects Offered</a></li>
                            <li><a href="academic-calendar.php">Academic Calendar</a></li>
                        </ul>
                    </div>
                    
                    <!-- Contact Info -->
                    <div class="footer-col">
                        <h4>Contact Us</h4>
                        <ul class="contact-info">
                            <li><i class="fas fa-map-marker-alt"></i> P.O. Box 51,mukono, Uganda</li>
                            <li><i class="fas fa-phone-alt"></i> +256779094664/+256782394058</li>
                            <li><i class="fas fa-envelope"></i> stellamariscollege2025@gmail.com</li>
                        </ul>
                    </div>
                </div>
                
                <div class="footer-bottom">
                    <p>&copy; <?php echo date('Y'); ?> Stella Maris College Nsuube. All rights reserved.</p>
                </div>
            </div>
        </footer>

        <!-- Back to Top Button -->
        <button class="back-to-top" id="backToTop">
            <i class="fas fa-arrow-up"></i>
        </button>

        <style>
        /* Footer Styles */
        .footer {
            background: #1a1a1a;
            color: #fff;
            padding: 50px 0 20px;
            width: 100%;
        }
        
        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .footer-col h3 {
            font-size: 18px;
            margin: 10px 0;
        }
        
        .footer-col h4 {
            font-size: 16px;
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 10px;
        }
        
        .footer-col h4:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 2px;
            background: #2e7d32;
        }
        
        .footer-col p {
            font-size: 13px;
            line-height: 1.6;
            color: #aaa;
            margin-bottom: 15px;
        }
        
        .footer-social a {
            display: inline-block;
            width: 32px;
            height: 32px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            text-align: center;
            line-height: 32px;
            margin-right: 8px;
            color: #fff;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .footer-social a:hover {
            background: #2e7d32;
            transform: translateY(-2px);
        }
        
        .footer-col ul {
            list-style: none;
        }
        
        .footer-col ul li {
            margin-bottom: 10px;
        }
        
        .footer-col ul li a {
            color: #aaa;
            text-decoration: none;
            font-size: 13px;
            transition: all 0.3s;
        }
        
        .footer-col ul li a:hover {
            color: #fff;
            padding-left: 5px;
        }
        
        .contact-info li {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #aaa;
            font-size: 13px;
            margin-bottom: 12px;
        }
        
        .contact-info i {
            width: 20px;
            color: #2e7d32;
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
        
        /* Back to Top Button */
        .back-to-top {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 40px;
            height: 40px;
            background: #1a4d8c;
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            display: none;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
            z-index: 999;
        }
        
        .back-to-top.show {
            display: flex;
        }
        
        .back-to-top:hover {
            background: #2e7d32;
            transform: translateY(-2px);
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .footer {
                padding: 40px 0 20px;
            }
            
            .footer-grid {
                grid-template-columns: 1fr;
                gap: 25px;
                text-align: center;
            }
            
            .footer-col h4:after {
                left: 50%;
                transform: translateX(-50%);
            }
            
            .contact-info li {
                justify-content: center;
            }
            
            .back-to-top {
                bottom: 15px;
                right: 15px;
                width: 35px;
                height: 35px;
            }
        }
        </style>

        <script>
        // Back to Top Button
        var backToTop = document.getElementById('backToTop');
        if (backToTop) {
            window.addEventListener('scroll', function() {
                if (window.scrollY > 300) {
                    backToTop.classList.add('show');
                } else {
                    backToTop.classList.remove('show');
                }
            });
            
            backToTop.addEventListener('click', function() {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        }
        </script>

    </div> <!-- Close main-wrapper -->
</body>
</html>