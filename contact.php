<?php
// contact.php - Contact Us Page (Improved with Larger Form)
session_start();
$page_title = "Contact Us";
require_once 'config/database.php';
$db = new Database();
$conn = $db->getConnection();

$message = '';
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $subject = $_POST['subject'];
    $message_text = $_POST['message'];
    
    $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
    if($stmt->execute([$name, $email, $phone, $subject, $message_text])) {
        $message = '<div class="alert alert-success">✅ Message sent successfully! We will respond within 24 hours.</div>';
    } else {
        $message = '<div class="alert alert-danger">❌ Failed to send message. Please try again.</div>';
    }
}
?>
<?php include 'includes/header.php'; ?>

<style>
/* Contact Page Styles */
.contact-wrapper {
    max-width: 1400px;
    margin: 0 auto;
}

/* Contact Info Cards */
.contact-info-card {
    background: linear-gradient(135deg, #1a4d8c, #0d3b6b);
    color: white;
    padding: 35px;
    border-radius: 20px;
    height: 100%;
}

.contact-info-card h3 {
    font-size: 28px;
    margin-bottom: 10px;
}

.contact-info-card > p {
    opacity: 0.9;
    margin-bottom: 30px;
}

.info-item-large {
    display: flex;
    align-items: flex-start;
    gap: 15px;
    margin-bottom: 25px;
    padding: 15px;
    background: rgba(255,255,255,0.1);
    border-radius: 15px;
    transition: transform 0.3s;
}

.info-item-large:hover {
    transform: translateX(5px);
    background: rgba(255,255,255,0.15);
}

.info-icon {
    width: 50px;
    height: 50px;
    background: #2e7d32;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}

.info-details h4 {
    margin: 0 0 5px;
    font-size: 18px;
}

.info-details p {
    margin: 0;
    opacity: 0.9;
    font-size: 12px;
}

.info-details a {
    color: white;
    text-decoration: none;
}

/* Map Container */
.map-container {
    margin-top: 20px;
    border-radius: 15px;
    overflow: hidden;
    height: 200px;
}

.map-placeholder {
    background: rgba(255,255,255,0.1);
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    border-radius: 15px;
}

/* Large Form Styles */
.contact-large-form {
    background: white;
    padding: 40px;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.contact-large-form h3 {
    font-size: 28px;
    color: #1a4d8c;
    margin-bottom: 10px;
}

.form-description {
    color: #666;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #f0f0f0;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 25px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #333;
    font-size: 14px;
}

.form-group label .required {
    color: #dc3545;
    margin-left: 5px;
}

.form-control-large {
    width: 100%;
    padding: 14px 16px;
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    font-size: 16px;
    transition: all 0.3s;
}

.form-control-large:focus {
    outline: none;
    border-color: #1a4d8c;
    box-shadow: 0 0 0 3px rgba(26,77,140,0.1);
}

textarea.form-control-large {
    resize: vertical;
    min-height: 150px;
}

.btn-submit-large {
    width: 100%;
    padding: 16px;
    background: linear-gradient(135deg, #1a4d8c, #2e7d32);
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 18px;
    font-weight: 600;
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
}

.btn-submit-large:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(26,77,140,0.3);
}

/* Office Hours */
.office-hours {
    margin-top: 30px;
    padding: 20px;
    background: rgba(255,255,255,0.1);
    border-radius: 15px;
}

.office-hours h4 {
    margin: 0 0 10px;
    font-size: 18px;
}

.office-hours p {
    margin: 5px 0;
    font-size: 14px;
    display: flex;
    justify-content: space-between;
}

/* Social Links Large */
.social-links-large {
    display: flex;
    gap: 15px;
    margin-top: 20px;
}

.social-link {
    width: 45px;
    height: 45px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s;
}

.social-link:hover {
    background: #2e7d32;
    transform: translateY(-3px);
}

.social-link i {
    font-size: 20px;
    color: white;
}

/* Responsive */
@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
        gap: 0;
    }
    
    .contact-large-form {
        padding: 25px;
        margin-top: 30px;
    }
    
    .contact-large-form h3 {
        font-size: 22px;
    }
    
    .contact-info-card {
        padding: 25px;
    }
    
    .info-item-large {
        padding: 12px;
    }
    
    .office-hours p {
        flex-direction: column;
    }
}

/* Alert Styles */
.alert {
    padding: 15px 20px;
    border-radius: 12px;
    margin-bottom: 25px;
    font-size: 14px;
}

.alert-success {
    background: #e8f5e9;
    color: #2e7d32;
    border: 1px solid #c8e6c9;
}

.alert-danger {
    background: #ffebee;
    color: #c62828;
    border: 1px solid #ffcdd2;
}

/* Loading State */
.btn-submit-large.loading {
    opacity: 0.7;
    cursor: not-allowed;
}

.btn-submit-large.loading i {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>

<!-- Page Header -->
<section class="page-header" style="background: linear-gradient(rgba(26,77,140,0.8), rgba(0,0,0,0.6)), url('assets/images/contact-bg.jpg'); background-size: cover; background-position: center;">
    <div class="container">
        <h1>Contact Us</h1>
        <p>Get in touch with Stella Maris College</p>
    </div>
</section>

<!-- Contact Content -->
<section style="padding: 60px 0;">
    <div class="container">
        <div class="contact-wrapper">
            <div class="row">
                <!-- Left Column - Contact Info -->
                <div class="col-lg-5 mb-4">
                    <div class="contact-info-card">
                        <h3><i class="fas fa-phone-alt"></i> Get in Touch</h3>
                        <p>We'd love to hear from you. Reach out to us through any of these channels.</p>
                        
                        <div class="info-item-large">
                            <div class="info-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="info-details">
                                <h4>Visit Us</h4>
                                <p>P.O. Box 51,Mukono District, Uganda<br>Nkonkonjeru town council</p>
                            </div>
                        </div>
                        
                        <div class="info-item-large">
                            <div class="info-icon">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <div class="info-details">
                                <h4>Call Us</h4>
                                <p>+256779094664<br>+256782394058</p>
                            </div>
                        </div>
                        
                        <div class="info-item-large">
                            <div class="info-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="info-details">
                                <h4>Email Us</h4>
                                <p><a href="mailto:stellamariscollege2025@gmail.com">stellamariscollege2025@gmail.com</a><br><a href="mailto:stellamariscollege2025@gmail.com">stellamariscollege2025@gmail.com</a></p>
                            </div>
                        </div>
                        
                        <div class="office-hours">
                            <h4><i class="fas fa-clock"></i> Office Hours</h4>
                            <p><span>Monday - Friday:</span> <span>8:00 AM - 5:00 PM</span></p>
                            <p><span>Saturday:</span> <span>9:00 AM - 1:00 PM</span></p>
                            <p><span>Sunday:</span> <span>Closed</span></p>
                        </div>
                        
                        <div class="social-links-large">
                            <a href="#" class="social-link"><i class="fa-brands fa-tiktok"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-youtube"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-whatsapp"></i></a>
                        </div>
                    </div>
                </div>
                
                <!-- Right Column - Large Contact Form -->
                <div class="col-lg-7">
                    <div class="contact-large-form">
                        <h3><i class="fas fa-paper-plane"></i> Send us a Message</h3>
                        <div class="form-description">
                            Please fill out the form below and we will respond within 24 hours.
                        </div>
                        
                        <?php echo $message; ?>
                        
                        <form method="POST" id="contactForm">
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Your Full Name <span class="required">*</span></label>
                                    <input type="text" name="name" class="form-control-large" required placeholder="e.g., Mary Akello">
                                </div>
                                <div class="form-group">
                                    <label>Email Address <span class="required">*</span></label>
                                    <input type="email" name="email" class="form-control-large" required placeholder="example@email.com">
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Phone Number</label>
                                    <input type="tel" name="phone" class="form-control-large" placeholder="+256 XXX XXX XXX">
                                </div>
                                <div class="form-group">
                                    <label>Subject <span class="required">*</span></label>
                                    <input type="text" name="subject" class="form-control-large" required placeholder="What is this regarding?">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Your Message <span class="required">*</span></label>
                                <textarea name="message" class="form-control-large" required placeholder="Please provide details about your inquiry..."></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                                    <input type="checkbox" id="copySelf" style="width: 18px; height: 18px;">
                                    <span>Send a copy of this message to my email</span>
                                </label>
                            </div>
                            
                            <button type="submit" class="btn-submit-large" id="submitBtn">
                                <i class="fas fa-paper-plane"></i> Send Message
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Form validation and submission
document.getElementById('contactForm').addEventListener('submit', function(e) {
    const name = document.querySelector('input[name="name"]').value;
    const email = document.querySelector('input[name="email"]').value;
    const subject = document.querySelector('input[name="subject"]').value;
    const message = document.querySelector('textarea[name="message"]').value;
    const phone = document.querySelector('input[name="phone"]').value;
    
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const phoneRegex = /^[\+]?[0-9]{10,13}$/;
    
    // Validate name
    if (name.trim().length < 2) {
        e.preventDefault();
        alert('Please enter your full name');
        return false;
    }
    
    // Validate email
    if (!emailRegex.test(email)) {
        e.preventDefault();
        alert('Please enter a valid email address');
        return false;
    }
    
    // Validate subject
    if (subject.trim().length < 5) {
        e.preventDefault();
        alert('Please enter a subject (minimum 5 characters)');
        return false;
    }
    
    // Validate message
    if (message.trim().length < 10) {
        e.preventDefault();
        alert('Please enter a message (minimum 10 characters)');
        return false;
    }
    
    // Validate phone if provided
    if (phone && phone.trim() !== '' && !phoneRegex.test(phone.replace(/\s/g, ''))) {
        e.preventDefault();
        alert('Please enter a valid phone number');
        return false;
    }
    
    // Show loading state
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
    submitBtn.classList.add('loading');
    
    return true;
});

// Character counter for message field
const messageField = document.querySelector('textarea[name="message"]');
if (messageField) {
    const counter = document.createElement('div');
    counter.style.cssText = 'font-size: 12px; color: #666; margin-top: 5px; text-align: right;';
    messageField.parentNode.appendChild(counter);
    
    messageField.addEventListener('input', function() {
        const remaining = 500 - this.value.length;
        if (remaining >= 0) {
            counter.textContent = remaining + ' characters remaining';
            if (remaining < 50) {
                counter.style.color = '#ff9800';
            } else {
                counter.style.color = '#666';
            }
        }
    });
    
    messageField.dispatchEvent(new Event('input'));
}

// Copy to self functionality
document.getElementById('copySelf')?.addEventListener('change', function() {
    if (this.checked) {
        const email = document.querySelector('input[name="email"]').value;
        if (email && email.includes('@')) {
            console.log('Will send copy to: ' + email);
        }
    }
});
</script>

<?php include 'includes/footer.php'; ?>